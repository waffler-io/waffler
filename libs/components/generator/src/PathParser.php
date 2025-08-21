<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Generator;

use ReflectionAttribute;
use ReflectionParameter;
use Waffler\Component\Attributes\Request\PathParam;
use Waffler\Component\Generator\Exceptions\UnableToParsePathException;

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
        if (!$this->hasPlaceholders($path)) {
            return $path;
        }
        $pathParameters = array_values(
            array_filter(
                $reflectionParameters,
                fn(ReflectionParameter $parameter) => count($parameter->getAttributes(PathParam::class, ReflectionAttribute::IS_INSTANCEOF)) > 0,
            ),
        );
        foreach ($pathParameters as $pathParameter) {
            $attributeInstance = $pathParameter->getAttributes(PathParam::class, ReflectionAttribute::IS_INSTANCEOF)[0]->newInstance();
            $placeholder = $attributeInstance->name ?? $pathParameter->getName();
            $count = 0;
            $path = str_replace('{' . $placeholder . '}', "\${$pathParameter->getName()}", $path, $count);
            if ($count === 0) {
                throw new UnableToParsePathException("The argument \"$placeholder\" is not used by any path parameter.", 1);
            } elseif ($count > 1) {
                throw new UnableToParsePathException("The path parameter \"$placeholder\" is repeated.", 2);
            }
        }
        $missing = [];
        if (preg_match('/{.*?}/', $path, $missing) === 1) {
            throw new UnableToParsePathException("The path parameter \"$missing[0]\" has no replacement.", 3);
        }
        return trim($path, '/');
    }

    private function hasPlaceholders(string $path): bool
    {
        return preg_match('/\{.*}/', $path) === 1;
    }
}
