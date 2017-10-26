<?php
declare(strict_types=1);

namespace BaseKit\Faktory;

class FaktoryWorker
{
    private $client;
    private $queues = [];
    private $jobTypes = [];

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

    public function run()
    {
        $job = $this->client->fetch($this->queues);

        if ($job !== null) {
            $callable = $this->jobTypes[$job['jobtype']];
            call_user_func($callable, $job);
        }
    }
}
