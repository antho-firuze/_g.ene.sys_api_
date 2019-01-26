<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

function logme($channel='app', $level='info', $message, $context=[])
{
    // Create some handlers
    $stream = new StreamHandler(FCPATH.'/service.log', Logger::DEBUG);
    $firephp = new FirePHPHandler();

    // create a log channel
    $log = new Logger($channel);
    $log->pushHandler($stream);
    $log->pushProcessor(function ($record) {
        $BM =& load_class('Benchmark', 'core');
        $elapsed = $BM->elapsed_time('total_execution_time_start', 'total_execution_time_end');
        $record['extra'] = ['exec_time' => $elapsed];
        return $record;
    });

    $log->{$level}($message, $context);
}
