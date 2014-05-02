<?php

/**
*	框架核心文件
*/

/**
 * 定义运行时间函数
 */

function run_time($start, $end = '', $decimal = 3) {
    static $_time = array();
    if ('' !== $end) {
        $_time[$end] = microtime();
        return number_format($_time[$end]-$_time[$start], $decimal);
    }
    $_time[$start] = \microtime();
}

//定义项目目录
define('APP_PATH') || define('APP_PATH', filter_input(INPUT_SERVER, ''));