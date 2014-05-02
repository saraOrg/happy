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

if (!is_dir(COMMON_PATH)) {
    build_app_dir();
} else {
    check_runtime();
}

// 检查缓存目录(Runtime) 如果不存在则自动创建
function check_runtime() {
    if (!is_dir(RUNTIME_PATH)) {
        mkdir(RUNTIME_PATH, 0777);
    } else if (!is_writable(RUNTIME_PATH)) {
        header('Content-Type:text/html;charset=utf-8');
        exit('目录 [ ' . RUNTIME_PATH . ' ] 不可写！');
    }
    is_dir(CACHE_PATH) || mkdir(CACHE_PATH, 0777);
    is_dir(LOG_PATH) || mkdir(LOG_PATH, 0777);
    is_dir(DATA_PATH) || mkdir(DATA_PATH, 0777);
    is_dir(TEMP_PATH) || mkdir(TEMP_PATH, 0777);
    return true;
}

// 创建应用目录结构
function build_app_dir() {
    mkdir(COMMON_PATH, 0777);
}