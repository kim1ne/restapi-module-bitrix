<?php

namespace Neti\RestApi\Controllers;

class IndexController
{
    public function __invoke()
    {
        return ['Index_Controller' => 'GET'];
    }
}