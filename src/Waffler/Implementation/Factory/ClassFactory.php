<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Implementation\Factory;

use ArrayObject;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
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
use Waffler\Waffler\Attributes\Request\FormData;
use Waffler\Waffler\Attributes\Request\FormParam;
use Waffler\Waffler\Attributes\Request\Headers;
use Waffler\Waffler\Attributes\Request\Json;
use Waffler\Waffler\Attributes\Request\JsonParam;
use Waffler\Waffler\Attributes\Request\Multipart;
use Waffler\Waffler\Attributes\Request\Path;
use Waffler\Waffler\Attributes\Request\Produces;
use Waffler\Waffler\Attributes\Request\Query;
use Waffler\Waffler\Attributes\Request\QueryParam;
use Waffler\Waffler\Attributes\Request\Timeout;
use Waffler\Waffler\Attributes\Utils\Batch;
use Waffler\Waffler\Attributes\Utils\NestedResource;
use Waffler\Waffler\Attributes\Utils\RawOptions;
use Waffler\Waffler\Attributes\Utils\Suppress;
use Waffler\Waffler\Attributes\Utils\Unwrap;
use Waffler\Waffler\Implementation\Attributes\ImplHash;
use Waffler\Waffler\Implementation\Exceptions\NotAnInterfaceException;
use Waffler\Waffler\Implementation\Exceptions\ParameterWithoutAttributesException;
use Waffler\Waffler\Implementation\MethodValidator;
use Waffler\Waffler\Implementation\PathParser;
use Waffler\Waffler\Implementation\Traits\BuildsImplementationFileName;
use Waffler\Waffler\Implementation\Traits\InteractsWithAttributes;
use Waffler\Waffler\Implementation\Traits\WafflerImplConstructor;

readonly class ClassFactory implements FactoryInterface
{
    use InteractsWithAttributes;
    use BuildsImplementationFileName;

    public function __construct(
        private MethodValidator $methodValidator,
        private PathParser $pathParser,
        private string $classNamespace,
    ) {
    }

    public function generateForInterface(string $interface): string
    {
        if (!interface_exists($interface)) {
            throw new NotAnInterfaceException($interface);
        }

        $reflectionInterface = new ReflectionClass($interface);

        $this->methodValidator->validateAll($reflectionInterface->getMethods());

        $className = $this->buildFileName($interface);

        $phpFile = new PhpFile();
        $namespace = $phpFile->addNamespace($this->classNamespace);
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
            $this->processMethodImplMethods($namespace, $class, $reflectionMethod);
        }

        return (string) $phpFile;
    }

    private function processMethodImplMethods(PhpNamespace $namespace, ClassType $class, ReflectionMethod $reflectionMethod): void
    {
        $implMethod = $class->addMethod($reflectionMethod->getName())
            ->setPublic();
        $hiddenMethod = $class->addMethod("wafflerImplFor".ucfirst($reflectionMethod->getName()))
            ->setPrivate();
        $isBatched = $this->reflectionHasAttribute($reflectionMethod, Batch::class);
        if (!$isBatched) {
            $hiddenMethod->addParameter('_additionalOptions')
                ->setType('array');
        }

        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            $parameter = $implMethod->addParameter($reflectionParameter->getName());
            $hiddenParameter = $hiddenMethod->addParameter($reflectionParameter->getName());

            if ($reflectionParameter->isDefaultValueAvailable()) {
                $parameter->setDefaultValue($reflectionParameter->getDefaultValue());
                $hiddenParameter->setDefaultValue($reflectionParameter->getDefaultValue());
            }
            if ($reflectionParameter->allowsNull()) {
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
        if ($isBatched) {
            $implMethod->setReturnType('array');
            $namespace->addUse(ResponseInterface::class);
        } elseif ($reflectionMethod->hasReturnType()) {
            $hasNested = $this->reflectionHasAttribute($reflectionMethod, NestedResource::class);
            $returnType = $reflectionMethod
                ->getReturnType()
                ->getName();
            $implMethod->setReturnType($returnType);
            if ($hasNested) {
                $namespace->addUse($returnType);
                $hiddenMethod->setReturnType($returnType);
            } else {
                $hiddenMethod->setReturnType(PromiseInterface::class);
            }
        }

        $hiddenMethod->setBody($this->generateMethodBody($reflectionMethod));
        $implMethod->setBody(
            $this->generateMainMethodBody(
                $hiddenMethod->getName(),
                $reflectionMethod
            ),
        );
    }

    private function generateMainMethodBody(string $hiddenMethodName, ReflectionMethod $reflectionMethod): string
    {
        $lines = [];
        $reflectionReturnType = $reflectionMethod->getReturnType();
        $hasNested = $this->reflectionHasAttribute($reflectionMethod, NestedResource::class);
        if ($reflectionReturnType !== null && is_a($reflectionReturnType->getName(), PromiseInterface::class)) {
            $lines[] = "return \$this->{$hiddenMethodName}(...func_get_args());";
        } elseif ($hasNested) {
            $lines[] = "return \$this->{$hiddenMethodName}([RequestOptions::SYNCHRONOUS => true], ...func_get_args());";
        } else {
            $processingResult = $this->respond(
                $reflectionReturnType?->getName(),
                $this->reflectionHasAttribute($reflectionMethod, Unwrap::class)
                    ? $this->getAttributeInstance($reflectionMethod, Unwrap::class)->property
                    : null
            );
            if ($this->reflectionHasAttribute($reflectionMethod, Batch::class)) {
                $lines[] = "\$responses = \$this->{$hiddenMethodName}(...func_get_args());";
                $lines[] = "return array_map(function (ResponseInterface \$response) {
                        {$processingResult}
                        return \$result;
                    }, \$responses);";
            } else {
                $lines[] = "\$response = \$this->{$hiddenMethodName}([RequestOptions::SYNCHRONOUS => true], ...func_get_args())->wait();";
                $lines[] = $processingResult;
                if ($reflectionReturnType?->getName() !== 'void') {
                    $lines[] = "return \$result;";
                }
            }
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
        $lines = ['$options = $_additionalOptions;'];
        $fullPath = $this->getFullMethodPath($reflectionMethod);
        $lines[] = "\$path = \"{$this->pathParser->parse($fullPath, $reflectionMethod->getParameters())}\";";
        $lines[] = "\$options['base_uri'] = (\$options['base_uri'] ?? '').\$path;";
        $lines[] = "\$resource = '{$returnTypeName}';";
        $lines[] = "return \$this->buildNestedResource(\$resource, \$options);";

        return implode(PHP_EOL, $lines);
    }

    private function getFullMethodPath(ReflectionMethod $reflectionMethod): string
    {
        $reflectionClass = $reflectionMethod->getDeclaringClass();
        $fullPath = [];
        if ($this->reflectionHasAttribute($reflectionClass, Path::class)) {
            $fullPath[] = $this->getAttributeInstance($reflectionClass, Path::class, true)
                ->getPath();
        }
        if ($this->reflectionHasAttribute($reflectionMethod, Path::class)) {
            $fullPath[] = $this->getAttributeInstance($reflectionMethod, Path::class, true)
                ->getPath();
        }
        if ($this->reflectionHasAttribute($reflectionMethod, Verb::class, true)) {
            $verb = $this->getAttributeInstance($reflectionMethod, Verb::class, true);
            $fullPath[] = $verb->getPath();
        }
        return implode(
            '/',
            array_filter(
                array_map(fn ($path) => trim($path, '/'), $fullPath),
                fn ($path) => !empty($path),
            ),
        );
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
        if ($this->reflectionHasAttribute($reflectionMethod, Batch::class)) {
            $batchMethodName = $this->getAttributeInstance($reflectionMethod, Batch::class, true);
            $args = implode(', ', array_map(fn ($param) => "\${$param->getName()}", $reflectionMethod->getParameters()));
            return "return \$this->performBatchMethod('{$batchMethodName->methodName}', $args);";
        }
        $lines = [];

        $verb = $this->getAttributeInstance($reflectionMethod, Verb::class, true);
        $lines[] = "\$verb = '{$verb->getName()}';";
        $lines[] = "\$path = \"{$this->pathParser->parse($this->getFullMethodPath($reflectionMethod), $reflectionMethod->getParameters())}\";";
        $lines[] = '$_options = $_additionalOptions;';

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
            $lines[] = '$_options[RequestOptions::HEADERS] = '.var_export($methodHeaders, true).';';
        }
        if ($this->reflectionHasAttribute($reflectionMethod, Timeout::class)) {
            $lines[] = '$_options[RequestOptions::TIMEOUT] = '
                . $this->getAttributeInstance($reflectionMethod, Timeout::class)->timeout
                . ";";
        }
        if ($this->reflectionHasAttribute($reflectionMethod, Suppress::class)) {
            $lines[] = '$_options[RequestOptions::HTTP_ERRORS] = false;';
        }

        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            if ($this->reflectionHasAttribute($reflectionParameter, Body::class)) {
                $lines[] = "\$_options[RequestOptions::BODY] = \${$reflectionParameter->getName()};";
                $bodyAttr = $this->getAttributeInstance($reflectionParameter, Body::class, true);
                foreach ($bodyAttr->getMimeTypes() as $contentType) {
                    $lines[] = "\$_options[RequestOptions::HEADERS]['Content-Type'][] = \"$contentType\";";
                }
            } elseif ($this->reflectionHasAttribute($reflectionParameter, Json::class)) {
                $lines[] = "\$_options[RequestOptions::JSON] = array_merge((\$_options[RequestOptions::JSON] ?? []), \${$reflectionParameter->getName()});";
            } elseif ($this->reflectionHasAttribute($reflectionParameter, JsonParam::class)) {
                $jsonParam = $this->getAttributeInstance($reflectionParameter, JsonParam::class, true);
                $jsonParamLine = "\$_options[RequestOptions::JSON]";
                foreach (explode($jsonParam->getPathSeparator(), $jsonParam->getKey()) as $key) {
                    $jsonParamLine .= "['$key']";
                }
                $jsonParamLine .= " = \${$reflectionParameter->getName()};";
                $lines[] = $jsonParamLine;
            } elseif ($this->reflectionHasAttribute($reflectionParameter, Query::class)) {
                $lines[] = "\$_options[RequestOptions::QUERY] = \${$reflectionParameter->getName()};";
            } elseif ($this->reflectionHasAttribute($reflectionParameter, QueryParam::class)) {
                $queryParamName = $this->getAttributeInstance($reflectionParameter, QueryParam::class, true)
                    ->getKey();
                $lines[] = "\$_options[RequestOptions::QUERY]['$queryParamName'] = \${$reflectionParameter->getName()};";
            } elseif ($this->reflectionHasAttribute($reflectionParameter, FormData::class)) {
                $lines[] = "\$_options[RequestOptions::FORM_PARAMS] = array_merge((\$_options[RequestOptions::FORM_PARAMS] ?? []), \${$reflectionParameter->getName()});";
            } elseif ($this->reflectionHasAttribute($reflectionParameter, FormParam::class)) {
                $formParamName = $this->getAttributeInstance($reflectionParameter, FormParam::class, true)
                    ->getKey();
                $lines[] = "\$_options[RequestOptions::FORM_PARAMS]['$formParamName'] = \${$reflectionParameter->getName()};";
            } elseif ($this->reflectionHasAttribute($reflectionParameter, Multipart::class)) {
                $lines[] = "\$_options[RequestOptions::MULTIPART] = \${$reflectionParameter->getName()};";
            } elseif ($this->reflectionHasAttribute($reflectionParameter, Basic::class)) {
                $lines[] = "\$_options[RequestOptions::AUTH] = [...\${$reflectionParameter->getName()}, 'basic'];";
            } elseif ($this->reflectionHasAttribute($reflectionParameter, Digest::class)) {
                $lines[] = "\$_options[RequestOptions::AUTH] = [...\${$reflectionParameter->getName()}, 'digest'];";
            } elseif ($this->reflectionHasAttribute($reflectionParameter, Ntml::class)) {
                $lines[] = "\$_options[RequestOptions::AUTH] = [...\${$reflectionParameter->getName()}, 'ntml'];";
            } elseif ($this->reflectionHasAttribute($reflectionParameter, Bearer::class)) {
                $lines[] = "\$_options[RequestOptions::HEADERS]['Authorization'] = \"Bearer \${$reflectionParameter->getName()}\";";
            } elseif ($this->reflectionHasAttribute($reflectionParameter, RawOptions::class)) {
                $lines[] = "\$_options = [...\$_options, ...\${$reflectionParameter->getName()}];";
            } elseif (count($reflectionParameter->getAttributes()) === 0) {
                throw new ParameterWithoutAttributesException("The parameter '{$reflectionParameter->getName()}' does not have any attributes defined.");
            }
        }

        $lines[] = 'return $this->client->requestAsync($verb, $path, $_options);';

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
