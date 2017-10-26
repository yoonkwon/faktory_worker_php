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
        $this->writeLine('PUSH', json_encode($job));
    }

    public function writeLine($command, $json)
    {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_connect($socket, $this->faktoryHost, $this->faktoryPort);
        socket_write($socket, $command . ' ' . $json);
        socket_close($socket);
    }
}
