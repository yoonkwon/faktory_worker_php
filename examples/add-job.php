<?php
require __DIR__ . '/../vendor/autoload.php';

/*
 * Use nc to listen for connections
 *
 * nc -l 32769
 */

$client = new BaseKit\Faktory\FaktoryClient;
$job = new BaseKit\Faktory\FaktoryJob("somejob", [1, 2, 3]);
$client->push($job);
