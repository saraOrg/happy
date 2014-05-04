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

//初始化运行时间
run_time('start');

//版本信息
const HAPPY_VERSION = '0.01';

/**
 * 系统常量定义
 */
define('ROOT_PATH', dirname(filter_input(INPUT_SERVER, 'SCRIPT_FILENAME')) . '/');   //根目录
define('HAPPY_PATH', __DIR__ . '/');   //框架目录
defined('APP_PATH') || define('APP_PATH', dirname(filter_input(INPUT_SERVER, 'SCRIPT_FILENAME')) . '/Application/');  //应用目录
define('CORE_PATH', HAPPY_PATH . 'Libs/Bin/');  //框架核心目录
define('ETC_PATH', HAPPY_PATH . 'Libs/Etc/');   //框架配置文件目录
define('RUNTIME_PATH', ROOT_PATH . 'Runtime/');  //运行时目录
define('TEMP_PATH', RUNTIME_PATH . 'Temp/');    //应用缓存目录
define('LOG_PATH', RUNTIME_PATH . 'Log/');      //应用日志目录
define('HTML_PATH', RUNTIME_PATH . 'Html/');    //应用静态目录
define('DATA_PATH', RUNTIME_PATH . 'Data/');    //应用数据目录
define('CACHE_PATH', RUNTIME_PATH . 'Cache/');  //应用模版编译目录
//加载运行时文件
if (file_exists(TEMP_PATH . '~rumetime.php')) {
    require TEMP_PATH . '~rumetime.php';
} else {
    require HAPPY_PATH . 'Common/runtime.php';
}

Happy::start(); //框架初始化开始
