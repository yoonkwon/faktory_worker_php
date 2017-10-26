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

    public function fetch(array $queues)
    {
        $socket = $this->connect();
        $response = $this->writeLine($socket, 'FETCH', implode(' ', $queues));

        $char = $response[0];
        if ($char == '$') {
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

    private function readLine($socket, $length = 1024)
    {
        $bytes = socket_read($socket, $length, PHP_BINARY_READ);
        while (strpos($bytes, "\r\n") === false) {
            $bytes .= socket_read($socket, $length - strlen($bytes), PHP_BINARY_READ);
        }
        return $bytes;
    }

    private function writeLine($socket, $command, $json)
    {
        $buffer = $command . ' ' . $json . "\r\n";
        socket_write($socket, $buffer, strlen($buffer));
        $read = $this->readLine($socket);
        return $read;
    }

    private function close($socket)
    {
        socket_close($socket);
    }
}
