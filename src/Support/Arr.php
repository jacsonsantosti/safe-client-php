<?php

namespace DataFrost\Support;

class Arr
{
    /**
     * @param array $arr
     * @param callback $callback
     * @return array
     */
    public static function filter(array $arr, $callback = null): array
    {
        return !is_null($callback) ? array_filter($arr, $callback) : array_filter($arr);
    }

    public static function filterRegex($arr, $regex)
    {
        $matches = preg_grep("/$regex/", array_keys($arr));
        return array_intersect_key($arr, array_flip($matches));
    }
}
