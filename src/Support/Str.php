<?php

namespace DataFrost\Support;

use stdClass;

class Str
{
    /**
     * @param string $delimiter
     * @param string $str
     * @return array
     */
    public static function toArray(string $delimiter, string $str): array
    {
        return explode($delimiter, $str);
    }

    /**
     * @param string $str
     * @param stdClass
     */
    public static function toObject(string $str)
    {
        require json_encode($str);
    }
}
