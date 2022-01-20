<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Client\Readers;

use Exception;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use ReflectionParameter;
use Waffler\Attributes\Auth\Basic;
use Waffler\Attributes\Auth\Bearer;
use Waffler\Attributes\Auth\Digest;
use Waffler\Attributes\Auth\Ntml;
use Waffler\Attributes\Contracts\ArraySettable;
use Waffler\Attributes\Request\Body;
use Waffler\Attributes\Request\FormData;
use Waffler\Attributes\Request\FormParam;
use Waffler\Attributes\Request\HeaderParam;
use Waffler\Attributes\Request\Json;
use Waffler\Attributes\Request\JsonParam;
use Waffler\Attributes\Request\Multipart;
use Waffler\Attributes\Request\PathParam;
use Waffler\Attributes\Request\Query;
use Waffler\Attributes\Request\QueryParam;
use Waffler\Attributes\Utils\RawOptions;
use Waffler\Client\AttributeChecker;
use Waffler\Client\Readers\Exceptions\UnableToParsePathException;
use Waffler\Client\Traits\InteractsWithAttributes;

use function Waffler\arraySet;

/**
 * Class ParameterReader.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
class ParameterReader
{
    use InteractsWithAttributes;

    /**
     * @var array<string,mixed>
     */
    private array $parameterMap = [];

    /**
     * @param array<\ReflectionParameter> $reflectionParameters
     * @param array<int|string, mixed>    $arguments
     */
    public function __construct(
        private array $reflectionParameters,
        private array $arguments
    ) {
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
     * @return array<int|string, array<array-key, string>|mixed|string>
     * @throws \Exception
     */
    public function getHeaderParams(): array
    {
        return array_merge_recursive(
            $this->valuesForKeyedAttribute(HeaderParam::class),
            $this->getBearerParam(),
            $this->getBodyMimes(),
        );
    }

    /**
     * @return array<string,string>
     * @throws \Exception
     */
    private function getBearerParam(): array
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
            FormParam::class,
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
     * @throws UnableToParsePathException If the path param is not used, or is repeated, or has no replacement that use it.
     */
    public function parsePath(string $path): string
    {
        $pathParameters = $this->withAttributes(PathParam::class);
        foreach ($pathParameters as $pathParameter) {
            $attributeInstance = $this->getAttributeInstance($pathParameter, PathParam::class);
            $value = $this->get($pathParameter);
            AttributeChecker::check(PathParam::class, $value);
            $placeholder = $attributeInstance->name ?? $pathParameter->getName();
            $count = 0;
            $path = str_replace('{'.$placeholder.'}', (string) $value, $path, $count);
            if ($count === 0) {
                throw new UnableToParsePathException("The argument \"$placeholder\" is not used by any path parameter.", 1);
            } elseif ($count > 1) {
                throw new UnableToParsePathException("The path parameter \"$placeholder\" is repeated.", 2);
            }
        }
        $missing = [];
        if (preg_match('/{.*?}/', $path, $missing)) {
            throw new UnableToParsePathException("The path parameter \"$missing[0]\" has no replacement.", 3);
        }
        return $path;
    }

    // private

    /**
     * @psalm-param class-string<TAttributeType> $attribute
     * @param mixed                              $default
     *
     * @return mixed
     * @template TAttributeType
     * @throws \Exception
     */
    private function valueFor(string $attribute, mixed $default = null): mixed
    {
        $data = $this->valuesFor($attribute);
        if (count($data) > 1) {
            throw new Exception("Only one attribute of type {$attribute} are allowed");
        }
        return $data[0] ?? $default;
    }

    /**
     * @psalm-param class-string<T> $attribute
     *
     * @return array<int,mixed>
     * @template T
     */
    private function valuesFor(string $attribute): array
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
     * @psalm-param class-string                                               $listTypeAttribute
     * @psalm-param class-string<\Waffler\Attributes\Contracts\KeyedAttribute> $singleTypeAttribute
     *
     * @return array<int|string, mixed>
     * @throws \Exception
     */
    private function valuesForPair(string $listTypeAttribute, string $singleTypeAttribute): array
    {
        $group = $this->valueFor($listTypeAttribute, []);

        foreach ($this->valuesForKeyedAttribute($singleTypeAttribute) as $k => $v) {
            $group[$k] = $v;
        }

        return $group;
    }

    /**
     * @psalm-param class-string<T> $attribute
     *
     * @return array<ReflectionParameter>
     * @template T
     */
    private function withAttributes(string $attribute): array
    {
        return array_values(
            array_filter(
                $this->reflectionParameters,
                fn (ReflectionParameter $parameter) => $this->reflectionHasAttribute($parameter, $attribute)
            )
        );
    }

    private function loadParameterMap(): void
    {
        foreach ($this->reflectionParameters as $parameter) {
            $parameterName = $parameter->getName();
            $parameterPosition = $parameter->getPosition();

            if (array_key_exists($parameterName, $this->arguments)) {
                $this->parameterMap[$parameterName] = $this->arguments[$parameterName];
            } elseif (array_key_exists($parameterPosition, $this->arguments)) {
                $this->parameterMap[$parameterName] = $this->arguments[$parameterPosition];
            } elseif ($parameter->isDefaultValueAvailable()) {
                $this->parameterMap[$parameterName] = $parameter->getDefaultValue();
            } else {
                throw new InvalidArgumentException("Required argument {$parameter->getName()} is missing.");
            }
        }
    }

    #[Pure]
    private function get(ReflectionParameter $parameter): mixed
    {
        return $this->parameterMap[$parameter->getName()];
    }

    /**
     * @return array<string, array<string>>
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function getBodyMimes(): array
    {
        $result = [];
        if ($param = $this->withAttributes(Body::class)[0] ?? false) {
            $bodyAttribute = $param->getAttributes(Body::class)[0]->newInstance();
            $result['Content-Type'] = $bodyAttribute->getMimeTypes();
        }
        return $result;
    }

    /**
     * @psalm-param class-string<\Waffler\Attributes\Contracts\KeyedAttribute> $singleTypeAttribute
     *
     * @return array<int|string, mixed>
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function valuesForKeyedAttribute(string $singleTypeAttribute): array
    {
        $group = [];
        foreach ($this->withAttributes($singleTypeAttribute) as $parameter) {
            $attributeInstance = $parameter->getAttributes($singleTypeAttribute)[0]->newInstance();
            $key = $attributeInstance->getKey();
            $value = $this->get($parameter);
            if ($attributeInstance instanceof ArraySettable) {
                arraySet(
                    $group,
                    $key,
                    $value,
                    $attributeInstance->getPathSeparator()
                );
            } else {
                $group[$key] = $value;
            }
        }
        return $group;
    }
}
