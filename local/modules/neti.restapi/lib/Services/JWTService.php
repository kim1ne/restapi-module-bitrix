<?php

namespace Neti\RestApi\Services;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Neti\RestApi\Description;

class JWTService
{
    public function __construct(
        private \Neti\RestApi\HighLoadBlock\User $hl
    ){}

    private string $token;
    private string $tokenLifeTime;
    private string $refreshToken;
    private string $refreshTokenLifeTime;
    private int $userId;
    private string $error;
    private bool $isRefresh = false;

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    private function getTimeLife(): void
    {
        $tokenLifeTime = (int) Option::get(Description::MODULE_NAME, Description::PARAMETER_LIVE_TOKEN_IN_HOURS, 1);
        if ($tokenLifeTime === 0) $tokenLifeTime = 1;

        $refreshTokenLifeTime = (int) Option::get(Description::MODULE_NAME, Description::PARAMETER_LIVE_TOKEN_IN_HOURS, 24);
        if ($refreshTokenLifeTime === 0) $refreshTokenLifeTime = 24;

        $this->tokenLifeTime = $tokenLifeTime;
        $this->refreshTokenLifeTime = $refreshTokenLifeTime;

    }

    public function generateTokens(): void
    {
        $this->getTimeLife();
        $this->isRefresh = false;
        $this->token = $this->generate($this->tokenLifeTime * 60 * 60);
        $this->isRefresh = true;
        $this->refreshToken = $this->generate($this->refreshTokenLifeTime * 60 * 60);
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function save(): array
    {
        $this->generateTokens();
        $res = $this->add($this->refreshToken);

        if ($res->isSuccess() === false) {
            throw new \Exception(json_encode($res->getErrors()));
        }

        return [
            'auth' => [
                'access_token' => $this->token,
                'refresh_token' => $this->refreshToken,
            ]
        ];
    }

    private function add(string $refreshToken): bool|\Bitrix\Main\ORM\Data\AddResult
    {
        $currentDate = new DateTime();
        $expireDate = (new DateTime())->add($this->refreshTokenLifeTime . ' hours');

        return $this->hl->add([
            'UF_USER_ID' => $this->userId,
            'UF_REFRESH_TOKEN' => $refreshToken,
            'UF_DATE_START' => $currentDate,
            'UF_DATE_END' => $expireDate,
        ]);
    }

    private function generate(int $expireMinute, bool $refresh = false): string
    {
        $algoritms = $this->getHashAlgos();
        $algIndex = array_rand($algoritms);
        $algoHash = $algoritms[$algIndex];

        $secret = $this->getSecretKey();

        $header = json_encode([
            "alg" => $algoHash,
            "typ" => "JWT",
        ]);

        $payload = json_encode([
            "iss" => json_encode($this->getIssData()),
            "iat" => time(),
            "exp" => time() + $expireMinute,
        ]);

        $data = $this->jwt_clear(base64_encode($header)) . '.' . $this->jwt_clear(base64_encode($payload));
        $key = base64_encode(hash_hmac($algoHash, $data, $secret, true));
        $key = $this->jwt_clear($key);
        return $data . '.' . $key;
    }

    public function getIssData(): array
    {
        return [
            'user_id' => $this->userId
        ];
    }

    public function getHashAlgos(): array
    {
        return hash_hmac_algos();
    }

    private function jwt_clear(string $string): string
    {
        return str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            $string
        );
    }

    private function getSecretKey(): string
    {
        $secretKey = 'ejrewiuhg/ekrt?*plrmgt';
        if ($this->isRefresh) {
            $secretKey = 'erwhgywrhg' . $secretKey;
        }

        return $secretKey;
    }

    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function refresh(): bool|array
    {
        $this->isRefresh = true;
        $decoded = $this->decode($this->refreshToken);

        if ($decoded === false) {
            return false;
        }

        $res = $this->hl->getClassName()::getList([
            'filter' => [
                'UF_USER_ID' => $decoded['user_id'],
                'UF_REFRESH_TOKEN' => $this->refreshToken
            ]
        ])->fetch();

        if ($res === false) {
            $this->error = 'Invalid Token';
            return false;
        }

        $this->hl->delete($res['ID']);

        $this->generateTokens();

        $this->add($this->refreshToken);

        return [
            'access_token' => $this->token,
            'refresh_token' => $this->refreshToken
        ];
    }

    public function checkRefreshToken(string $jwtToken): bool|array
    {
        $this->isRefresh = true;
        $decode = $this->decode($jwtToken);

        if ($decode !== false) {
            $this->refreshToken = $jwtToken;
        }

        return $decode;
    }

    public function checkAccessToken(string $jwtToken): bool|array
    {
        $this->isRefresh = false;
        return $this->decode($jwtToken);
    }

    private function decode(string $jwtToken): array|bool
    {
        $explode = explode('.', $jwtToken);

        if (count($explode) !== 3) {
            $this->error = 'jwt token is not valid';
            return false;
        }

        $header = $explode[0];
        $payload = $explode[1];

        $headerDecoded = json_decode(base64_decode($header), true);
        $algoritm = $headerDecoded['alg'];

        $secret = $this->getSecretKey();
        $secretKeyJwt = $explode[2];

        $secretKey = $this->jwt_clear(base64_encode(hash_hmac($algoritm, $header . '.' . $payload, $secret, true)));

        if ($secretKeyJwt !== $secretKey) {
            $this->error = 'User not auth';
            return false;
        }

        $payloadDecoded = json_decode(base64_decode($payload), true);

        $expireTime = $payloadDecoded['exp'];

        if (time() > $expireTime) {
            $this->error = 'User not auth';
            return false;
        }

        $userId = json_decode($payloadDecoded['iss'], true)['user_id'];

        $this->userId = $userId;

        return $this->getIssData();
    }
}