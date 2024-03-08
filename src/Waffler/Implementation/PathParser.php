<?php

namespace Waffler\Waffler\Implementation;

use ReflectionAttribute;
use ReflectionParameter;
use Waffler\Waffler\Attributes\Request\PathParam;
use Waffler\Waffler\Implementation\Exceptions\UnableToParsePathException;

class PathParser
{
    /**
     * @param string                      $path
     * @param array<\ReflectionParameter> $reflectionParameters
     *
     * @return string
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function parse(string $path, array $reflectionParameters): string
    {
        $pathParameters = array_values(
            array_filter(
                $reflectionParameters,
                fn (ReflectionParameter $parameter) => count($parameter->getAttributes(PathParam::class, ReflectionAttribute::IS_INSTANCEOF)) > 0
            )
        );
        foreach ($pathParameters as $pathParameter) {
            $attributeInstance = $pathParameter->getAttributes(PathParam::class, ReflectionAttribute::IS_INSTANCEOF)[0]->newInstance();
            $placeholder = $attributeInstance->name ?? $pathParameter->getName();
            $count = 0;
            $path = str_replace('{'.$placeholder.'}', "\${$pathParameter->getName()}", $path, $count);
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
        return trim($path, '/');
    }
}