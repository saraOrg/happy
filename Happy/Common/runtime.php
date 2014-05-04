<?php

/**
 * =================================================
 * 框架运行时文件
 * ================================================
 * @category happy
 * @package Admin/
 * @subpackage Action
 * @author Happy <yangbai6644@163.com>
 * @dateTime 2014-5-2 22:59:52
 * ================================================
 */
/**
 * 项目目录创建和初始化
 */
//载入核心文件
$files = require HAPPY_PATH . 'Common/files.php';
foreach ($files as $file) {
    require $file;
}
check_runtime();

// 检查缓存目录(Runtime) 如果不存在则自动创建
function check_runtime() {
    if (!is_dir(RUNTIME_PATH)) {
        mkdir(RUNTIME_PATH, 0777);
    } else if (!is_writable(RUNTIME_PATH)) {
        header('Content-Type:text/html;charset=utf-8');
        exit('目录 [ ' . RUNTIME_PATH . ' ] 不可写！');
    }
    is_dir(CACHE_PATH) || mkdir(CACHE_PATH, 0777);  //缓存目录
    is_dir(LOG_PATH) || mkdir(LOG_PATH, 0777);      //日志目录
    is_dir(DATA_PATH) || mkdir(DATA_PATH, 0777);    //数据目录
    is_dir(TEMP_PATH) || mkdir(TEMP_PATH, 0777);    //临时目录
    return true;
}
