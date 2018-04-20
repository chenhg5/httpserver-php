<?php

$source = $argv[1];

// 状态集合
const STATE_0 = '';
const STATE_1 = '[0-9]';

// 接受状态
const receive_state = [STATE_1];

// 起始状态
$curren_state = '';

$i = 0;
while (1) {
    switch ($curren_state) {
        case STATE_0:
            switch (preg_match('/[0-9]/', $source[$i])) {
                case true:
                    $i++;
                    $curren_state = STATE_1;
                    break;
                default:
                    die('failed STATE_0');
            }
            break;
        case STATE_1:
            switch (preg_match('/[0-9]/', $source[$i])) {
                case true:
                    $i++;
                    $curren_state = STATE_1;
                    if ($i == strlen($source)) {
                        die('success');
                    }
                    break;
                default:
                    die('failed STATE_0');
            }
            break;
        default:
            die('failed');
    }
}