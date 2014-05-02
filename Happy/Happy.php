<?php

/**
 * 	框架核心文件
 */

/**
 * 定义运行时间函数
 */

function run_time($start, $end = '', $decimal = 3) {
    static $_time = array();
    if ('' !== $end) {
        $_time[$end] = microtime(true);
        return number_format($_time[$end] - $_time[$start], $decimal);
    }
    isset($_time[$start]) || $_time[$start] = \microtime(true);
}

//定义项目目录
defined('APP_PATH') || define('APP_PATH', dirname(filter_input(INPUT_SERVER, 'SCRIPT_NAME')));
