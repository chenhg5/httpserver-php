<?php

use HttpServer\Module\Coroutine\SystemCall;
use HttpServer\Module\Coroutine\Task;
use HttpServer\Module\Coroutine\Scheduler;

if (!function_exists('newTask')) {
    function newTask(Generator $coroutine)
    {
        return new SystemCall(
            function (Task $task, Scheduler $scheduler) use ($coroutine) {
                $task->setSendValue($scheduler->newTask($coroutine));
                $scheduler->schedule($task);
            }
        );
    }
}

if (!function_exists('killTask')) {
    function killTask($tid)
    {
        return new SystemCall(
            function (Task $task, Scheduler $scheduler) use ($tid) {
                $task->setSendValue($scheduler->killTask($tid));
                $scheduler->schedule($task);
            }
        );
    }
}

if (!function_exists('getTaskId')) {
    function getTaskId()
    {
        return new SystemCall(function (Task $task, Scheduler $scheduler) {
            $task->setSendValue($task->getTaskId());
            $scheduler->schedule($task);
        });
    }
}