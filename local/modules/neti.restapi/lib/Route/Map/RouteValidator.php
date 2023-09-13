<?php

namespace Neti\RestApi\Route\Map;

class RouteValidator
{
    public static function url(string $url): array|string
    {
        $symbol = substr($url, -1);

        if ($symbol === '/') {
            $url = substr($url, 0, -1);
        }

        if ($url[0] !== '/') {
            $url = '/' . $url;
        }

        $url = self::replaceRegexp($url);

        $url = str_replace('//', '/', $url);

        return  str_replace('//', '/', $url);
    }

    public static function replaceRegexp(
        string $url,
        string $startSymbol = '{',
        string $endSymbol = '}',
        string $replacement = '(\w+)',
        bool $loop = true
    ): string
    {
        if ($start = strpos($url, $startSymbol)) {
            $end = strpos($url, $endSymbol);
            $length = (int) ++$end - $start;
            $url = substr_replace($url, $replacement, $start, $length);
            if ($loop) $url = self::replaceRegexp($url, $startSymbol, $endSymbol, $replacement);
        }

        return $url;
    }
}