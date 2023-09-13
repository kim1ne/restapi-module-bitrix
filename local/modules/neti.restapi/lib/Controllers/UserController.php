<?php

namespace Neti\RestApi\Controllers;

use Bitrix\Main\HttpRequest;
use Neti\RestApi\Services\Helper;
use Neti\RestApi\Services\JWTService;
use Neti\RestApi\Validators\RequestValidator;

class UserController
{
    public function __invoke()
    {
        return ['Get' => 'token'];
    }

    public function registration(RequestValidator $validator)
    {
        $data = $validator->validated([
            'login' => ['require', 'string|min:6'],
            'email' => ['require', 'string', 'string|min:6'],
            'password' => ['require', 'string|min:6'],
            'confirmPassword' => ['require', 'string|min:6'],
        ]);

        $user = new \CUser();
        if (!$userId = $user->Add(Helper::arrayToUnderScore($data))) {
            throw new \Exception($user->LAST_ERROR);
        }

        $userData = \CUser::GetByID($userId)->Fetch();
        unset($userData['PASSWORD']);
        unset($userData['CHECKWORD']);
        return $userData;
    }

    public function auth(RequestValidator $validator, JWTService $jwtService)
    {
        $data = $validator->validated([
            'login' => ['require'],
            'password' => ['require']
        ]);

        $res = \CUser::GetList('', '', ['LOGIN' => $data['login']])->Fetch();

        if ($res === false) {
            throw new \Exception('user with login `' . $data['login'] . '` does not exist');
        }

        $auth = \Bitrix\Main\Security\Password::equals($res['PASSWORD'], $data['password']);

        if (!$auth) {
            throw new \Exception('Incorrect login or password');
        }

        $jwtService->setUserId($res['ID']);
        $tokenData = $jwtService->save();

        return array_merge($res, $tokenData);
    }

    public function refresh(JWTService $jwtService)
    {
        $decoded = $jwtService->refresh();

        if ($decoded === false) throw new \Exception('refresh-token is not valid', 401);

        return $decoded;
    }
}