<?php

/**
 * 框架核心文件
 */
function run_time($start, $end = '', $decimal = 3) {
    static $_time = array();
    if ('' !== $end) {
        $_time[$end] = microtime(true);
        return number_format($_time[$end] - $_time[$start], $decimal);
    }
    isset($_time[$start]) || $_time[$start] = \microtime(true);
}


run_time('start');  //初始化运行时间

/**
 * 系统常量定义
 */

define('HAPPY_PATH', __DIR__ . '/');   //框架目录
defined('APP_PATH') || define('APP_PATH', dirname(filter_input(INPUT_SERVER, 'SCRIPT_FILENAME')) . '/');  //项目目录
define('RUNTIME_PATH', APP_PATH . 'Runtime/');  //运行时目录
define('TEMP_PATH', RUNTIME_PATH . 'Temp/');    //应用缓存目录
define('LOG_PATH', RUNTIME_PATH . 'Log/');      //应用日志目录
define('HTML_PATH', RUNTIME_PATH . 'Html/');    //应用静态目录
define('DATA_PATH', RUNTIME_PATH . 'Data/');    //应用数据目录
define('CACHE_PATH', RUNTIME_PATH . 'Cache/');  //应用模版编译目录
define('COMMON_PATH', HAPPY_PATH . 'Common/');   //框架公共目录

//加载运行时文件
if (file_exists(TEMP_PATH . '~rumetime.php')) {
    include TEMP_PATH . '~rumetime.php';
} else {
    include COMMON_PATH . 'runtime.php';
}

