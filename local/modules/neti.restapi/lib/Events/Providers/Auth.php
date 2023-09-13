<?php

namespace Neti\RestApi\Events\Provider;

class Auth
{
    public static function OnRestCheckAuth(array $query, $scope, &$res) {
        echo '<pre>';
        print_r($query);
        print_r($scope);
        die;
    }
}