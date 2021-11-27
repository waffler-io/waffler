<?php

declare(strict_types = 1);

namespace Waffler\Client;

use Exception;
use InvalidArgumentException;
use ReflectionParameter;
use Waffler\Attributes\Auth\Basic;
use Waffler\Attributes\Auth\Bearer;
use Waffler\Attributes\Auth\Digest;
use Waffler\Attributes\Auth\Ntml;
use Waffler\Attributes\Request\Body;
use Waffler\Attributes\Request\FormData;
use Waffler\Attributes\Request\FromParam;
use Waffler\Attributes\Request\HeaderParam;
use Waffler\Attributes\Request\Json;
use Waffler\Attributes\Request\JsonParam;
use Waffler\Attributes\Request\Multipart;
use Waffler\Attributes\Request\PathParam;
use Waffler\Attributes\Request\Query;
use Waffler\Attributes\Request\QueryParam;
use Waffler\Attributes\Utils\RawOptions;
use Waffler\Client\Traits\InteractsWithAttributes;

/**
 * Class Parameters
 *
 * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package  Waffler\Client
 * @internal
 */
class Parameters
{
    use InteractsWithAttributes;

    /**
     * @var array<string,mixed>
     */
    protected array $parameterMap = [];

    /**
     * @param array<\ReflectionParameter> $reflectionParameters
     * @param array<int|string, mixed>    $arguments
     */
    public function __construct(protected array $reflectionParameters, protected array $arguments)
    {
        $this->loadParameterMap();
    }

    /**
     * @return array<int|string, string>
     * @throws \Exception
     */
    public function getQueryParams(): array
    {
        return $this->valuesForPair(
            Query::class,
            QueryParam::class
        );
    }

    /**
     * @return array<int|string,string>
     * @throws \Exception
     */
    public function getHeaderParams(): array
    {
        return $this->valuesFor(HeaderParam::class) + $this->getBearerParam();
    }

    /**
     * @return array<string,string>
     * @throws \Exception
     */
    public function getBearerParam(): array
    {
        $token = $this->valueFor(Bearer::class);
        return $token ? ['Authorization' => "Bearer $token"] : [];
    }

    /**
     * @return array<int|string, mixed>|null
     * @throws \Exception
     */
    public function getFormParams(): ?array
    {
        return $this->valuesForPair(
            FormData::class,
            FromParam::class,
        );
    }

    /**
     * @return array<string,mixed>|null
     * @throws \Exception
     */
    public function getMultipartParams(): ?array
    {
        return $this->valueFor(Multipart::class);
    }

    /**
     * @return array<string>|null
     * @throws \Exception
     * @psalm-suppress PropertyTypeCoercion
     */
    public function getAuthParams(): ?array
    {
        if ($value = $this->valueFor(Basic::class)) {
            $value[] = 'basic';
        } elseif ($value = $this->valueFor(Digest::class)) {
            $value[] = 'digest';
        } elseif ($value = $this->valueFor(Ntml::class)) {
            $value[] = 'ntml';
        }
        return $value;
    }

    /**
     * @throws \Exception
     */
    public function getBodyParam(): ?string
    {
        return $this->valueFor(Body::class);
    }

    /**
     * @return array<int|string,mixed>|null
     * @throws \Exception
     */
    public function getJsonParams(): ?array
    {
        return $this->valuesForPair(
            Json::class,
            JsonParam::class,
        ) ?: null;
    }

    /**
     * @return array<string,mixed>
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function getRawOptions(): array
    {
        return $this->valueFor(RawOptions::class) ?? [];
    }

    /**
     * @param string $path
     *
     * @return string
     * @throws \InvalidArgumentException If the Value of PathParam does not pass the type check.
     * @throws \Exception If the path param is not used, or is repeated, or has no replacement that use it.
     */
    public function parsePath(string $path): string
    {
        $pathParameters = $this->withAttributes(PathParam::class);
        foreach ($pathParameters as $pathParameter) {
            $attributeInstance = $this->getAttributeInstance($pathParameter, PathParam::class);
            $value = $this->get($pathParameter);
            AttributeChecker::check(PathParam::class, $value);
            $placeholder = $attributeInstance->name ?? $pathParameter->name;
            $count = 0;
            $path = str_replace('{' . $placeholder . '}', (string)$value, $path, $count);
            if ($count === 0) {
                throw new Exception("The argument \"{$pathParameter->getName()}\" is not used by any path parameter.");
            } elseif ($count > 1) {
                throw new Exception("The path parameter \"$placeholder\" is repeated.");
            }
        }
        $missing = [];
        if (preg_match('/{.*?}/', $path, $missing)) {
            throw new Exception("The path parameter \"$missing[0]\" has no replacement");
        }
        return $path;
    }

    // protected

    /**
     * @param class-string<TAttributeType> $attribute
     * @param mixed                        $default
     *
     * @return mixed
     * @template TAttributeType
     * @throws \Exception
     */
    protected function valueFor(string $attribute, mixed $default = null): mixed
    {
        $data = $this->valuesFor($attribute);
        if (count($data) > 1) {
            throw new Exception("Only one attribute of type {$attribute} are allowed");
        }
        return $data[0] ?? $default;
    }

    /**
     * @param class-string<T> $attribute
     *
     * @return array<int,mixed>
     * @template T
     */
    protected function valuesFor(string $attribute): array
    {
        $data = [];
        foreach ($this->reflectionParameters as $reflectionParameter) {
            if (!$this->reflectionHasAttribute($reflectionParameter, $attribute)) {
                continue;
            }
            $value = $this->get($reflectionParameter);
            AttributeChecker::check($attribute, $value);
            $data[] = $value;
        }
        return $data;
    }

    /**
     * @param class-string<TFirstAttributeType> $listTypeAttribute
     * @param class-string<TSecondAttributeType> $singleTypeAttribute
     *
     * @return array<int|string, mixed>
     * @throws \Exception
     * @template TFirstAttributeType of object
     * @template TSecondAttributeType of object
     */
    protected function valuesForPair(string $listTypeAttribute, string $singleTypeAttribute): array
    {
        $group = $this->valueFor($listTypeAttribute, []);
        foreach ($this->withAttributes($singleTypeAttribute) as $parameter) {
            $key = $parameter->getAttributes($singleTypeAttribute)[0]->getArguments()[0];
            $group[$key] = $this->get($parameter);
        }
        return $group;
    }

    /**
     * @param class-string<T> $attribute
     *
     * @return array<ReflectionParameter>
     * @template T
     */
    protected function withAttributes(string $attribute): array
    {
        return array_values(
            array_filter(
                $this->reflectionParameters,
                fn(ReflectionParameter $parameter) => $this->reflectionHasAttribute($parameter, $attribute)
            )
        );
    }

    protected function loadParameterMap(): void
    {
        foreach ($this->reflectionParameters as $parameter) {
            $this->parameterMap[$parameter->name] =
                // Load by name or by position
                $this->arguments[$parameter->name] ??
                $this->arguments[$parameter->getPosition()] ??
                // If the parameter is not available by name or by position
                // we will try to get the default value. If the default value is not available,
                // we will throw an exception.
                (
                $parameter->isDefaultValueAvailable()
                    ? $parameter->getDefaultValue()
                    : throw new InvalidArgumentException("Required argument {$parameter->name} is missing.")
                );
        }
    }

    protected function get(ReflectionParameter $parameter): mixed
    {
        return $this->parameterMap[$parameter->name];
    }
}
