<?php

namespace Anderson\PhpWebserver;

class Request
{
    protected array $parameters = [];

    /**
     * @param string $method
     * @param string $uri
     * @param array $headers
     */
    public function __construct(protected string $method, protected string $uri, protected array $headers)
    {
        @list($this->uri, $parameters) = explode('?', $this->uri);
        parse_str($parameters, $this->parameters);
    }


    public static function withHeaderString($header): Request {
        /* 1 - Converte a string em um array separando por quebra de linha.
         * 2 - Pega os dois primeiros elementos da primeira linha separando por espaço.
         * 3 - Itera sobre os elementos do array e armazena em um array associativo de chave valor.
         * */
        $lines = explode("\n", $header);
        list($method, $uri) = explode(' ', array_shift($lines));

        $headers = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            list($key, $value) = explode(': ', $line, 2);
            $headers[$key] = trim($value);
        }
        return new self($method, $uri, $headers);
    }

    public function method(): string {
        return $this->method;
    }

    public function uri(): string {
        return $this->uri;
    }

    public function header(string $key, string $default = null): string
    {
        if (!isset($this->headers[$key])) {
            return $default;
        }
        return $this->headers[$key];
    }

    public function parameter(string $key, string $default = null): string {
        if (!isset($this->parameters[$key])) {
            return $default;
        }
        return $this->parameters[$key];
    }
}