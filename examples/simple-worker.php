<?php
require __DIR__ . '/vendor/autoload.php';

$worker = new FaktoryWorker();
$worker->register('ExampleJob', function ($job) {
    // do something
});

$worker->setQueues(['critical', 'default', 'bulk']);
$worker->run();
