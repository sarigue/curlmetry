<?php

namespace Curlmetry\Utils;

/**
 * Utility class for constructing instances of classes from JSON data.
 *
 * @author  github.com/sarigue
 * @license Apache 2.0
 */
class FromJsonUtils
{
    /**
     * @template T
     *
     * @param string $classname
     * @param array $data
     * @param T $parentClass
     *
     * @return T
     */
    public static function buildInstance($classname, $data, $parentClass)
    {
        if (!$classname) {
            /** @codeCoverageIgnore */
            return null;
        }
        if (!class_exists($classname)) {
            /** @codeCoverageIgnore */
            return null;
        }
        if ($parentClass && !is_a($classname, $parentClass, true)) {
            /** @codeCoverageIgnoreStart */
            return null;
            /** @codeCoverageIgnoreEnd */
        }

        if (method_exists($classname, 'fromJson') && $data) {
            return $classname::fromJson($data);
        } else {
            return new $classname();
        }
    }
}
