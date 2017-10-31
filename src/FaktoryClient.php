<?php

namespace BaseKit\Faktory;

class FaktoryClient
{
    /**
     * @var string
     */
    private $faktoryHost;

    /**
     * @var int
     */
    private $faktoryPort;

    public function __construct(string $faktoryHost, int $faktoryPort)
    {
        $this->faktoryHost = $faktoryHost;
        $this->faktoryPort = $faktoryPort;
    }

    public function push(FaktoryJob $job) : void
    {
        $socket = $this->connect();
        $this->writeLine($socket, 'PUSH', json_encode($job));
        $this->close($socket);
    }

    public function fetch(array $queues)
    {
        $socket = $this->connect();
        $response = $this->writeLine($socket, 'FETCH', implode(' ', $queues));

        $char = $response[0];
        if ($char === '$') {
            $count = trim(substr($response, 1, strpos($response, "\r\n")));
            $data = null;
            if ($count > 0) {
                $data = substr($response, strlen($count) + 1);
                $this->close($socket);
                return json_decode($data, true);
            }

            return $data;
        }

        $this->close($socket);

        return $response;
    }

    public function ack(string $jobId) : void
    {
        $socket = $this->connect();
        $this->writeLine($socket, 'ACK', json_encode(['jid' => $jobId]));
        $this->close($socket);
    }

    public function fail(string $jobId) : void
    {
        $socket = $this->connect();
        $this->writeLine($socket, 'FAIL', json_encode(['jid' => $jobId]));
        $this->close($socket);
    }

    private function connect()
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

    private function readLine($socket, int $length = 1024) : string
    {
        $bytes = socket_read($socket, $length, PHP_BINARY_READ);
        while (strpos($bytes, "\r\n") === false) {
            $bytes .= socket_read($socket, $length - strlen($bytes), PHP_BINARY_READ);
        }
        return $bytes;
    }

    private function writeLine($socket, string $command, string $json) : string
    {
        $buffer = $command . ' ' . $json . "\r\n";
        socket_write($socket, $buffer, strlen($buffer));
        $read = $this->readLine($socket);
        return $read;
    }

    private function close($socket) : void
    {
        socket_close($socket);
    }
}
