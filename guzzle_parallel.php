<?php
require_once 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7\Response;

define('BASE_URI', 'http://guzzle-demo/');


// Dump it pretty!
function dump_pretty($data)
{
    echo json_encode($data, JSON_PRETTY_PRINT);
}


// Print the elapsed time.
// Returns a decorated function.
function time_it(callable $fn)
{
    return function () use ($fn) {
        $start = microtime(true);
        $result = $fn();
        $end = microtime(true);
        echo ($end - $start) . PHP_EOL; // elapsed seconds
        return $result;
    };
}


// Traverse the depths of the response object.
function unwrap(Response $r)
{
    return json_decode($r->getBody()->getContents())->accessed_on;
}


// Do it async.
function parallel_ping()
{
    $http = new Client(['base_uri' => BASE_URI]);
    
    $promises = [];
    for ($i = 0; $i < 10; $i++) {
        $promises[] = $http->getAsync('');
    }
    $responses = Promise\Utils::unwrap($promises);
    
    return array_map(unwrap(...), $responses);
}


// Do it the old fashioned way.
function sequential_ping()
{
    $http = new Client(['base_uri' => BASE_URI]);
    
    $responses = [];
    for ($i = 0; $i < 10; $i++) {
        $responses[] = $http->get('');
    }
    
    return array_map(unwrap(...), $responses);
}


//
// FIGHT!
//
echo "## Parallel Ping ##" . PHP_EOL;
dump_pretty(time_it(parallel_ping(...))());
echo PHP_EOL;
echo PHP_EOL;

echo "## Sequential Ping ##" . PHP_EOL;
dump_pretty(time_it(sequential_ping(...))());
echo PHP_EOL;
