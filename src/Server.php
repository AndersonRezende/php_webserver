<?php

namespace Anderson\PhpWebserver;

use Closure;
use Exception;
use Socket;

class Server
{
    protected Socket $socket;

    /**
     * @throws Exception
     */
    public function __construct(protected string $host, protected int $port)
    {
        $this->createSocket();
        $this->bind();
    }

    protected function createSocket(): void {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, 0);
    }

    /**
     * @throws Exception
     */
    protected function bind(): void {
        if (!socket_bind($this->socket, $this->host, $this->port)) {
            throw new Exception("Socket could not be bind. Host: {$this->host}:{$this->port}");
        }
    }

    /**
     * @throws Exception
     */
    public function listen(Closure $callback): void {
        if (!is_callable($callback)) {
            throw new Exception("Closure must be a callable");
        }

        while (true) {
            socket_listen($this->socket);
            $client = socket_accept($this->socket);
            if (!$client) {
                socket_close($client);
                throw new Exception("Could not connect to {$this->host}:{$this->port}");
            }

            $request = Request::withHeaderString(socket_read($client, 1024));
            $response = call_user_func($callback, $request);
            if (!$response) {
                $response = Response::error(404);
            }

            $response = (string) $response;
            socket_write($client, $response, strlen($response));
            socket_close($client);
        }

    }
}