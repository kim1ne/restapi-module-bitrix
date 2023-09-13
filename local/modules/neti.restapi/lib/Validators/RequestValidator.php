<?php

namespace Neti\RestApi\Validators;

use Bitrix\Main\HttpRequest;
use Neti\RestApi\Tools\UrlWriter;

class RequestValidator
{
    private array $requestSettings;
    private array $requestParams;
    private array $keyValidRequest;

    private array $error;
    private array $requireParams = [];

    public function __construct(
        private HttpRequest $request
    ){}

    private function setRequestParams(): void
    {
        if ($this->request->getServer()['CONTENT_TYPE'] === 'application/json') {
            $this->requestParams = $this->request->getJsonList()->getValues();
        } else {
            $post = $this->request->getValues();
            unset($post[UrlWriter::KEY_RULE_PATH]);
            $this->requestParams = $post;
        }
    }

    private function setKeyValidRequest()
    {
        $this->keyValidRequest = array_keys($this->requestSettings);
    }

    private function setRequireParameters()
    {
        foreach ($this->requestSettings as $key => $param) {
            if (in_array('require', $param)) {
                $this->requireParams[] = $key;
            }
        }
    }

    public function validated(array $settings): array
    {
        $this->requestSettings = $settings;
        $this->setRequireParameters();
        $this->setRequestParams();
        $this->setKeyValidRequest();
        return $this->start();
    }

    private function isExistParam(string $key)
    {
        return in_array($key, $this->keyValidRequest);
    }

    private function start()
    {
        foreach ($this->requestParams as $key => $value) {

            if ($this->requestSettings[$key] === []) continue;

            if (!$this->isExistParam($key)) {
                $this->foundParameterRequestException($key);
            }

            if (isset($this->requestSettings[$key])) {
                $this->validateDataWithSettings($key, $value, $this->requestSettings[$key]);
            }

            if (in_array($key, $this->requireParams)) {
                $keySearch = array_search($key, $this->requireParams);
                unset($this->requireParams[$keySearch]);
            }
        }

        if (!empty($this->requireParams)) {
            $error = 'Require parameters `' . implode('`, `', $this->requireParams) . '`';
            if (!empty($this->error)) {
                array_unshift($this->error, $error);
            } else {
                $this->error[] = $error;
            }

        }

        if (!empty($this->error)) {
            throw new \Exception(json_encode($this->error), 400);
        }

        return $this->requestParams;
    }

    private function validateDataWithSettings(string $key, $value, array $settings): void
    {
        $setting = array_shift($settings);
        $params = explode('|', $setting);
        $setting = array_shift($params);
        [$min, $max] = $this->returnMinMax($params);

        switch ($setting) {
            case 'int':
                $this->checkParamIsInteger($key, $value);
                $this->validateSize($key, $value, $min, $max);
                break;
            case 'string':
                $this->checkParamIsString($key, $value);
                $this->validateSize($key, $value, $min, $max);
                break;
            case 'float':
                $this->checkParamIsFloat($key, $value);
                break;
        }

        if (!empty($settings)) {
            $this->validateDataWithSettings($key, $value, $settings);
        }
    }

    private function checkParamIsFloat(string $key, $value)
    {
        if (!($value == (float)$value)) {
            $this->error[] = "`$key` should be float type";
        }
    }

    private function validateSize(string $key, string $value, ?int $min, ?int $max)
    {
        if ($min === null && $max === null) return;

        $len = mb_strlen($value);

        if ($len < $min && $min !== null) {
            $this->error[] = "`$key` should be long min $min symbol";
        }
        if ($len > $max && $max !== null) {
            $this->error[] = "`$key` should be long max $max symbol";
        }
    }

    private function returnMinMax(array $params)
    {
        if (empty($params)) return [null, null];

        foreach ($params as $param) {
            if (str_contains($param, 'min:')) {
                $min = (int) explode(':', $param)[1];
            } elseif (str_contains($param, 'max:')) {
                $max = (int) explode(':', $param)[1];
            }
        }

        return [$min ?? null, $max ?? null];
    }

    private function checkParamIsString(string $key, $value)
    {
        if (!is_string($value)) {
            $this->error[] = "`$key` is not string";
        }
    }

    private function checkParamIsInteger(string $key, $value)
    {
        if (!($value == (int)$value)) {
            $this->error[] = "`$key`  is not integer";
        }
    }

    private function foundParameterRequestException(string $parameter)
    {
        $this->error[] = 'found parameter `' . $parameter . '` is not rule for request';
    }
}