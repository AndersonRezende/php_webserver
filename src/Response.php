<?php

namespace Anderson\PhpWebserver;

class Response
{
    protected static array $statusCodes = [
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',

        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',

        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found', // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        // 306 is deprecated but reserved
        307 => 'Temporary Redirect',

        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',

        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded'
    ];

    protected array $headers = [];
    private int $status;

    public function __construct(protected string $body, int $status = 200)
    {
        if ($status) {
            $this->status = $status;
        }
        $this->header('Date', gmdate('D, d M Y H:i:s'));
        $this->header('Content-Type', 'text/html; charset=utf-8');
        $this->header('Server', 'PHPServer/1.0.0');
    }

    public function header($key, $value): void {
        $this->headers[ucfirst($key)] = $value;
    }

    public static function error(int $status): string {
        return new self("<h1>PHP Server error: ".self::$statusCodes[$status]."</h1>", $status);
    }

    public function buildHeaderString(): string {
        $lines = [];
        $lines[] = "HTTP/1.1 ".$this->status." ".static::$statusCodes[$this->status];
        foreach( $this->headers as $key => $value ) {
            $lines[] = $key.": ".$value;
        }

        return implode( " \r\n", $lines )."\r\n\r\n";
    }

    public function __toString(){
        return $this->buildHeaderString().$this->body;
    }
}