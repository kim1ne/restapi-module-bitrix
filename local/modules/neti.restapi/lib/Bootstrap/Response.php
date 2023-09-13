<?php

namespace Neti\RestApi\Bootstrap;

use Neti\RestApi\Bootstrap\Enums\Status;

class Response
{
    public function __construct(
        public readonly Status $status
    ){}
}