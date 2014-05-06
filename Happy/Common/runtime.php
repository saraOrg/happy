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
//加载核心文件
$files = require HAPPY_PATH . 'Common/files.php';
foreach ($files as $file) {
    require $file;
}
check_runtime();    //检查运行环境
config(require ETC_PATH . 'config.php');                //加载框架底层配置

define('MODULE_NAME', getModuleName());                 //当前模块名称
define('CONTROLLER_NAME', getControllerName());         //当前控制器名称
define('ACTION_NAME', getActionName());                 //当前方法名称
define('COMMON_PATH', APP_PATH . 'Common/');            //公共模块目录
define('MODULE_PATH', APP_PATH . MODULE_NAME . '/');    //当前模块目录
define('CONTROLLER_PATH', MODULE_PATH . 'Controller/'); //当前控制器目录
define('MODEL_PATH', MODULE_PATH . 'Model/');           //当前模型目录
define('CONF_PATH', MODULE_PATH . 'Conf/');             //当前配置文件目录
define('VIEW_PATH', MODULE_PATH . 'View/');             //当前视图目录

/**
 * 检查缓存目录(Runtime) 如果不存在则自动创建
 * @return boolean
 */
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

/**
 * 获取当前模块名称
 */
function getModuleName() {
    if (filter_input(INPUT_GET, config('VAR_MODULE')) && filter_input(INPUT_GET, config('VAR_MODULE')) !== '') {
        return filter_input(INPUT_GET, config('VAR_MODULE'));
    }
    return config('DEFAULT_MODULE');
}

/**
 * 获取当前控制器名称
 */
function getControllerName() {
    if (filter_input(INPUT_GET, config('VAR_CONTROLLER')) && filter_input(INPUT_GET, config('VAR_CONTROLLER')) !== '') {
        return filter_input(INPUT_GET, config('VAR_CONTROLLER'));
    }
    return config('DEFAULT_CONTROLLER');
}

/**
 * 获取当前方法名称
 */
function getActionName() {
    if (filter_input(INPUT_GET, config('VAR_ACTION')) && filter_input(INPUT_GET, config('VAR_ACTION')) !== '') {
        return filter_input(INPUT_GET, config('VAR_ACTION'));
    }
    return config('DEFAULT_ACTION');
}
