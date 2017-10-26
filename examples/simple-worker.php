<?php
require __DIR__ . '/../vendor/autoload.php';

use BaseKit\Faktory\FaktoryClient;
use BaseKit\Faktory\FaktoryWorker;

$client = new FaktoryClient(getenv('FAKTORY_HOST'), getenv('FAKTORY_PORT'));
$worker = new FaktoryWorker($client);
$worker->register('somejob', function ($job) {
    echo "You got the job buddy!\n";
    var_dump($job);
});

$worker->setQueues(['critical', 'default', 'bulk']);
$worker->run();
