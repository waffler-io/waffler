<?php

namespace Waffler\Waffler\Implementation\Factory;

use ArrayObject;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use ReflectionClass;
use ReflectionMethod;
use TypeError;
use Waffler\Waffler\Attributes\Auth\Basic;
use Waffler\Waffler\Attributes\Auth\Bearer;
use Waffler\Waffler\Attributes\Auth\Digest;
use Waffler\Waffler\Attributes\Auth\Ntml;
use Waffler\Waffler\Attributes\Contracts\Verb;
use Waffler\Waffler\Attributes\Request\Body;
use Waffler\Waffler\Attributes\Request\FormParam;
use Waffler\Waffler\Attributes\Request\Headers;
use Waffler\Waffler\Attributes\Request\Json;
use Waffler\Waffler\Attributes\Request\Multipart;
use Waffler\Waffler\Attributes\Request\Path;
use Waffler\Waffler\Attributes\Request\Produces;
use Waffler\Waffler\Attributes\Request\Query;
use Waffler\Waffler\Attributes\Request\Timeout;
use Waffler\Waffler\Attributes\Utils\Batch;
use Waffler\Waffler\Attributes\Utils\NestedResource;
use Waffler\Waffler\Attributes\Utils\Suppress;
use Waffler\Waffler\Attributes\Utils\Unwrap;
use Waffler\Waffler\Implementation\Attributes\ImplHash;
use Waffler\Waffler\Implementation\Exceptions\NotAnInterfaceException;
use Waffler\Waffler\Implementation\MethodValidator;
use Waffler\Waffler\Implementation\PathParser;
use Waffler\Waffler\Implementation\Traits\BuildsImplementationFileName;
use Waffler\Waffler\Implementation\Traits\InteractsWithAttributes;
use Waffler\Waffler\Implementation\Traits\WafflerImplConstructor;

readonly class ClassFactory implements FactoryInterface
{
    use InteractsWithAttributes, BuildsImplementationFileName;

    public function __construct(
        private MethodValidator $methodValidator,
        private PathParser $pathParser,
    ) {}

    public function generateForInterface(string $interface): string
    {
        if (!interface_exists($interface)) {
            throw new NotAnInterfaceException($interface);
        }

        $reflectionInterface = new ReflectionClass($interface);

        $this->methodValidator->validateAll($reflectionInterface->getMethods());

        $className = $this->buildFileName($interface);

        $phpFile = new PhpFile();
        $namespace = $phpFile->addNamespace("Waffler\\Impl");
        $namespace->addUse(RequestOptions::class);
        $namespace->addUse(Client::class);
        $namespace->addUse($reflectionInterface->getName());
        $namespace->addUse(ImplHash::class);
        $namespace->addUse(WafflerImplConstructor::class);
        $namespace->addUseFunction('Waffler\\Waffler\\arrayGet');
        $namespace->addUse(PromiseInterface::class);

        $class = $namespace->addClass($className);
        $class->addAttribute(ImplHash::class, [md5_file($reflectionInterface->getFileName())]);
        $class->addImplement($interface);
        $class->addTrait(WafflerImplConstructor::class);

        foreach ($reflectionInterface->getMethods() as $reflectionMethod) {
            $this->processMethodImplMethods($class, $reflectionMethod);
        }

        return (string) $phpFile;
    }

    private function processMethodImplMethods(ClassType $class, ReflectionMethod $reflectionMethod): void
    {
        $implMethod = $class->addMethod($reflectionMethod->getName())
            ->setPublic();

        $hiddenMethod = $class->addMethod("wafflerImplFor".ucfirst($reflectionMethod->getName()))
            ->setPrivate();

        $hiddenMethod->addParameter('_additionalOptions')
            ->setType('array');

        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            $parameter = $implMethod->addParameter($reflectionParameter->getName());
            $hiddenParameter = $hiddenMethod->addParameter($reflectionParameter->getName());

            if ($reflectionParameter->isDefaultValueAvailable()) {
                $parameter->setDefaultValue($reflectionParameter->getDefaultValue());
                $hiddenParameter->setDefaultValue($reflectionParameter->getDefaultValue());
            }
            if ($reflectionParameter->isOptional()) {
                $parameter->setNullable();
                $hiddenParameter->setNullable();
            }
            if ($reflectionParameter->hasType()) {
                $parameter->setType($reflectionParameter->getType()
                    ->getName());
                $hiddenParameter->setType($reflectionParameter->getType()
                    ->getName());
            }
        }
        if ($reflectionMethod->hasReturnType()) {
            $implMethod->setReturnType($reflectionMethod->getReturnType()
                ->getName());
            $hiddenMethod->setReturnType(PromiseInterface::class);
        }

        $hiddenMethod->setBody($this->generateMethodBody($reflectionMethod));
        $implMethod->setBody(
            $this->generateMainMethodBody(
                $hiddenMethod->getName(),
                $reflectionMethod),
        );
    }

    private function generateMainMethodBody(string $hiddenMethodName, ReflectionMethod $reflectionMethod): string
    {
        $lines = [];
        $reflectionReturnType = $reflectionMethod->getReturnType();
        if ($reflectionReturnType !== null && is_a($reflectionReturnType->getName(), PromiseInterface::class)) {
            $lines[] = "return \$this->{$hiddenMethodName}(...func_get_args());";
        } else {
            $lines[] = "\$response = \$this->{$hiddenMethodName}([RequestOptions::SYNCHRONOUS => true], ...func_get_args())->wait();";
            $lines[] = $this->respond(
                $reflectionReturnType?->getName(),
                $this->reflectionHasAttribute($reflectionMethod, Unwrap::class)
                    ? $this->getAttributeInstance($reflectionMethod, Unwrap::class)->property
                    : null
            );
            $lines[] = "return \$result;";
        }
        return implode("\n", $lines);
    }

    private function generateMethodBody(ReflectionMethod $reflectionMethod): string
    {
        if ($this->reflectionHasAttribute($reflectionMethod, NestedResource::class)) {
            return $this->generateNestedResourceMethodBody($reflectionMethod);
        }

        return $this->generateMethodBodyForRequest($reflectionMethod);
    }

    /**
     * @param \ReflectionMethod $reflectionMethod
     *
     * @return string
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function generateNestedResourceMethodBody(ReflectionMethod $reflectionMethod): string
    {
        $returnTypeName = $reflectionMethod->getReturnType()
            ->getName();

        $lines = ['$options = [...$this->options];'];

        if ($this->reflectionHasAttribute($reflectionMethod, Path::class)) {
            $path = $this->getAttributeInstance($reflectionMethod, Path::class);
            $lines[] = "\$path = \"{$this->pathParser->parse($path->path, $reflectionMethod->getParameters())}\";";
            $lines[] = "\$options['base_uri'] = (\$options['base_uri'] ?? '').\$path;";
        }

        $lines[] = "\$resource = '{$returnTypeName}';";

        $lines[] = "return \$this->buildNestedResource(\$resource, \$options);";

        return implode('\n', $lines);
    }

    /**
     * @param \ReflectionMethod $reflectionMethod
     *
     * @return string
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function generateMethodBodyForRequest(ReflectionMethod $reflectionMethod): string
    {
        $lines = [];

        if ($this->reflectionHasAttribute($reflectionMethod, Batch::class)) {
            throw new Exception("Batch is not supported yet");
        }

        $verb = $this->getAttributeInstance($reflectionMethod, Verb::class, true);

        $lines[] = "\$verb = '{$verb->getName()}';";
        $lines[] = "\$path = \"{$this->pathParser->parse($verb->getPath(), $reflectionMethod->getParameters())}\";";
        $lines[] = '$options = $_additionalOptions;';

        $methodHeaders = [];

        if ($this->reflectionHasAttribute($reflectionMethod, Headers::class)) {
            $methodHeaders = $this->getAttributeInstance($reflectionMethod, Headers::class)->headers;
        }

        if ($this->reflectionHasAttribute($reflectionMethod, Produces::class)) {
            $methodHeaders = array_merge_recursive(
                $methodHeaders,
                $this->getAttributeInstance($reflectionMethod, Produces::class)->headers,
            );
        }

        if (!empty($methodHeaders)) {
            $lines[] = '$options[RequestOptions::HEADERS] = '.var_export($methodHeaders, true).';';
        }
        if ($this->reflectionHasAttribute($reflectionMethod, Timeout::class)) {
            $lines[] = '$options[RequestOptions::TIMEOUT] = '
                . $this->getAttributeInstance($reflectionMethod, Timeout::class)->timeout
                . ";";
        }
        if ($this->reflectionHasAttribute($reflectionMethod, Suppress::class)) {
            $lines[] = '$options[RequestOptions::HTTP_ERRORS] = false;';
        }

        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            if ($this->reflectionHasAttribute($reflectionParameter, Body::class)) {
                $lines[] = "\$options[RequestOptions::BODY] = \${$reflectionParameter->getName()};";
            } elseif ($this->reflectionHasAttribute($reflectionParameter, Json::class)) {
                $lines[] = "\$options[RequestOptions::JSON] = \${$reflectionParameter->getName()};";
            } elseif ($this->reflectionHasAttribute($reflectionParameter, Query::class)) {
                $lines[] = "\$options[RequestOptions::QUERY] = \${$reflectionParameter->getName()};";
            } elseif ($this->reflectionHasAttribute($reflectionParameter, FormParam::class)) {
                $formParamName = $this->getAttributeInstance($reflectionParameter, FormParam::class)
                    ->getKey();
                $lines[] = "\$options[RequestOptions::FORM_PARAMS]['$formParamName'] = \${$reflectionParameter->getName()};";
            } elseif ($this->reflectionHasAttribute($reflectionParameter, Multipart::class)) {
                $lines[] = "\$options[RequestOptions::MULTIPART] = \${$reflectionParameter->getName()};";
            } elseif ($this->reflectionHasAttribute($reflectionParameter, Basic::class)) {
                $lines[] = "\$options[RequestOptions::AUTH] = \${$reflectionParameter->getName()};";
            } elseif ($this->reflectionHasAttribute($reflectionParameter, Digest::class)) {
                $lines[] = "\$options[RequestOptions::AUTH] = [...\${$reflectionParameter->getName()}, 'digest'];";
            } elseif ($this->reflectionHasAttribute($reflectionParameter, Ntml::class)) {
                $lines[] = "\$options[RequestOptions::AUTH] = [...\${$reflectionParameter->getName()}, 'ntml'];";
            } elseif ($this->reflectionHasAttribute($reflectionParameter, Bearer::class)) {
                $lines[] = "\$options[RequestOptions::HEADERS]['Authorization'] = \"Bearer \${$reflectionParameter->getName()}\";";
            }
        }

        $lines[] = 'return $this->client->requestAsync($verb, $path, $options);';

        return implode("\n", $lines);
    }

    private function respond(?string $returnType, ?string $wrapperProperty): string
    {
        $unwrapper = $wrapperProperty ?
            "arrayGet(json_decode(\$response->getBody()->getContents(), true), '$wrapperProperty');" :
            'json_decode($response->getBody()->getContents(), true)';
        return match ($returnType ?? 'mixed') {
            'array' => "\$result = $unwrapper;",
            'null' => '$result = null;',
            'void' => '',
            'bool' => '$result = $response->getStatusCode() < 400;',
            'string' => '$result = $response->getBody()->getContents();',
            'int', 'float', 'double' => '$result = $response->getStatusCode();',
            'object', ArrayObject::class => "\$result = new ArrayObject($unwrapper, ArrayObject::ARRAY_AS_PROPS);",
            StreamInterface::class => '$result = $response->getBody();',
            ResponseInterface::class, Response::class, MessageInterface::class, 'mixed' => '$result = $response;',
            default => throw new TypeError()
        };
    }
}
