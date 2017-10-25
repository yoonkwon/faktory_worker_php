<?php
require __DIR__ . '/vendor/autoload.php';

$client = new FaktoryClient;
$job = new FaktoryJob("somejob", [1, 2, 3]);
$client->push($job);
