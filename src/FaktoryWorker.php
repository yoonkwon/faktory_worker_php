<?php
declare(strict_types=1);

namespace BaseKit\Faktory;

class FaktoryWorker
{
    private $client;
    private $queues = [];
    private $jobTypes = [];
    private $stop = false;

    public function __construct(FaktoryClient $client)
    {
        $this->client = $client;
    }

    public function setQueues(array $queues)
    {
        $this->queues = $queues;
    }

    public function register($jobType, callable $callable)
    {
        $this->jobTypes[$jobType] = $callable;
    }

    public function run(bool $daemonize = false)
    {
        pcntl_async_signals(true);

        pcntl_signal(SIGTERM, function ($signo) {
            exit(0);
        });

        pcntl_signal(SIGINT, function ($signo) {
            exit(0);
        });

        do {
            $job = $this->client->fetch($this->queues);

            if ($job !== null) {
                $callable = $this->jobTypes[$job['jobtype']];

                $pid = pcntl_fork();
                if ($pid === -1) {
                    throw new \Exception('Could not fork');
                }

                if ($pid > 0) {
                    pcntl_wait($status);
                } else {
                    call_user_func($callable, $job);
                    $this->client->ack($job['jid']);
                    exit(0);
                }
            }
            usleep(100);
        } while($daemonize && !$this->stop);
    }
}
