<?php

namespace Neti\RestApi\Bootstrap;

use Bitrix\Main\Engine\Response\Json;
use Neti\RestApi\Services\Helper;

class ExceptionResponse
{
    public function __construct(
        private $message,
        private readonly int $statusCode = 0
    ){}

    public function getResponse()
    {
        $parsedJson = json_decode($this->message, true);
        if (!empty($parsedJson)) {
            $this->message = Helper::arrayToCamelCase($parsedJson);
        }

        $status = $this->statusCode;

        if ($status === 0) {
            $status = 200;
        }

        $data = [
            'status' => $status,
            'message' => $this->message
        ];

        $response = new Json($data);
        $response->setStatus($status);
        return $response;
    }
}