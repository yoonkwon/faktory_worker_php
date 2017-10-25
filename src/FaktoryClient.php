<?php
namespace BaseKit\Faktory;

class FaktoryClient
{
    public function push(FaktoryJob $job)
    {
        $this->writeLine('PUSH', json_encode($job));
    }

    public function writeLine($command, $json)
    {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_connect($socket, '172.17.0.1', '32769');
        socket_write($socket, $command . ' ' . $json);
        socket_close($socket);
    }
}
