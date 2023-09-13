<?php

namespace Neti\RestApi\Services;

class Helper
{
    public static function stringToCamelCase(string $str): string
    {
        if (intval($str)) {
            return $str;
        }
        return lcfirst(str_replace('_', '', ucwords(mb_strtolower($str), '_')));
    }

    public static function arrayToCamelCase(array $array): array
    {
        $arResult = [];
        foreach ($array as $key => $value) {
            $key = self::stringToCamelCase($key);
            if (is_array($value)) {
                $value = self::arrayToCamelCase($value);
            }
            $arResult[$key] = $value;
        }
        return $arResult;
    }

    public static function arrayToUnderScore(array $array): array
    {
        $arResult = [];
        foreach ($array as $key => $value) {
            $key = self::stringToUnderscore($key);
            if (is_array($value)) {
                $value = self::arrayToUnderScore($value);
            }
            $arResult[$key] = $value;
        }
        return $arResult;
    }

    public static function stringToUnderscore(string $source): string
    {
        return strtoupper(preg_replace('/(?<!^)[A-Z]/', '_$0', $source));
    }
}