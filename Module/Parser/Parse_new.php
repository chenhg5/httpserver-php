<?php

$source = $argv[1];

// 状态集合
const STATE_0 = '';
const STATE_1 = 'n';
const STATE_2 = 'ne';
const STATE_3 = 'new';

// 接受状态
const receive_state = [STATE_3];

// 起始状态
$curren_state = '';

$i = 0;
while (1) {
    switch ($curren_state) {
        case STATE_0:
            switch ($source[$i]) {
                case 'n':
                    $i++;
                    $curren_state = STATE_1;
                    break;
                default:
                    die('failed STATE_0');
            }
            break;
        case STATE_1:
            switch ($source[$i]) {
                case 'e':
                    $i++;
                    $curren_state = STATE_2;
                    break;
                default:
                    die('failed STATE_1');
            }
            break;
        case STATE_2:
            switch ($source[$i]) {
                case 'w':
                    $i++;
                    $curren_state = STATE_3;
                    if ($i == strlen($source)) {
                        die('success');
                    } else {
                        die('failed STATE_3');
                    }
                default:
                    die('failed STATE_2');
            }
            break;
        default:
            die('failed');
    }
}