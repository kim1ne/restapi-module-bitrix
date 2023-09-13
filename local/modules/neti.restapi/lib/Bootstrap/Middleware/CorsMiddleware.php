<?php

namespace Neti\RestApi\Bootstrap\Middleware;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Engine\Response\Json;
use Bitrix\Main\HttpRequest;
use Neti\RestApi\Description;

class CorsMiddleware implements Middleware
{
    public function handle(HttpRequest $request, callable $next): Json
    {
        $domains = Option::get(Description::MODULE_NAME, Description::PARAMETER_NAME_CORS_DOMAINS);

        if (empty($domains)) {
            $this->getHeader('*');
        } else {
            $domains = explode("\n", $domains);

            foreach ($domains as $domain) $this->getHeader($domain);
        }

        return $next($request);
    }

    private function getHeader(string $domain)
    {
        header('Access-Control-Allow-Origin: ' . $domain);
        header('Access-Control-Allow-Methods: *');
        header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
        header("Access-Control-Allow-Credentials", "true");
    }
}