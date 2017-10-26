<?php
namespace BaseKit\Faktory;

class FaktoryClient
{
    private $faktoryHost;
    private $faktoryPort;

    public function __construct(string $faktoryHost, string $faktoryPort)
    {
        $this->faktoryHost = $faktoryHost;
        $this->faktoryPort = $faktoryPort;
    }

    public function push(FaktoryJob $job)
    {
        $socket = $this->connect();
        $response = $this->writeLine($socket, 'PUSH', json_encode($job));
        $this->close($socket);
    }

    public function connect()
    {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_connect($socket, $this->faktoryHost, $this->faktoryPort);

        $response = $this->readLine($socket);
        if ($response !== "+HI {\"v\":\"1\"}\r\n") {
            throw new \Exception('Hi not received :(');
        }

        $this->writeLine($socket, 'HELLO', '{"wid":"foo"}');
        return $socket;
    }

    public function readLine($socket)
    {
        return socket_read($socket, 1024, PHP_BINARY_READ);
    }

    public function writeLine($socket, $command, $json)
    {
        $buffer = $command . ' ' . $json . "\r\n";
        socket_write($socket, $buffer, strlen($buffer));
        $read = $this->readLine($socket);
        return $read;
    }

    public function close($socket)
    {
        socket_close($socket);
    }
}
