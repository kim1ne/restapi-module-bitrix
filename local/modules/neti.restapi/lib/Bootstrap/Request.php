<?php

namespace Neti\RestApi\Bootstrap;

class Request
{
    public readonly array $params;
    public readonly array $post;
    public readonly array $body;
    public readonly array $cookie;
    private ?array $match;

    public readonly string $method;
    public readonly string $userAgent;
    public readonly string $accept;
    public readonly string $domain;

    public function __construct(
        public readonly string $requestId,
        array $params = null,
        array $post = null,
        string $parseBody = 'php://input'
    )
    {
        $this->params = $params ?? $_GET;
        $this->post = $post ?? $_POST;
        $this->body = $this->withBody($parseBody);
        $this->cookie = $_COOKIE;
    }

    public function withBody(string $parseBody): array
    {
        return json_decode(file_get_contents($parseBody), true) ?? [];
    }

    public function all(): array
    {
        return array_merge($this->params, $this->post, $this->body);
    }

    public function server(): array
    {
        return $_SERVER;
    }

    public function setMatch(array|string|int $match): void
    {
        $this->match = $match;
    }

    public function getMatch(): ?array
    {
        return $this->match;
    }
}