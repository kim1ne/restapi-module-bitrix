<?php

namespace Neti\RestApi\Bootstrap\Enums;

enum Status: string
{
    case OK = 'OK';
    case BAD_REQUEST = 'BAD REQUEST';
    case ERROR404 = 'ERROR 404';
    case ERROR_AUTH = 'ERROR 401 NOT AUTH';
}