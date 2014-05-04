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
build_app_dir();
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

// 创建应用目录结构
function build_app_dir() {
    is_dir(APP_PATH) || mkdir(APP_PATH, 0777);  //应用目录
    is_dir(APP_PATH . config('COMMON_MODEL')) || mkdir(APP_PATH . config('COMMON_MODEL'), 0777);      //公共模块目录
    is_dir(APP_PATH . config('DEFAULT_MODEL')) || mkdir(APP_PATH . config('DEFAULT_MODEL'), 0777);    //默认模块目录
    is_dir(APP_PATH . config('COMMON_MODEL') . '/Controller') || mkdir(APP_PATH . config('COMMON_MODEL') . '/Controller', 0777);
    is_dir(APP_PATH . config('DEFAULT_MODEL') . '/Controller') || mkdir(APP_PATH . config('DEFAULT_MODEL') . '/Controller', 0777);
    is_file(APP_PATH . config('DEFAULT_MODEL') . '/Controller' . 'IndexController.class.php') || build_first_Controller();
    is_dir(APP_PATH . config('COMMON_MODEL') . '/Model') || mkdir(APP_PATH . config('COMMON_MODEL') . '/Model', 0777);
    is_dir(APP_PATH . config('DEFAULT_MODEL') . '/Model') || mkdir(APP_PATH . config('DEFAULT_MODEL') . '/Model', 0777);
    is_dir(APP_PATH . config('COMMON_MODEL') . '/Common') || mkdir(APP_PATH . config('COMMON_MODEL') . '/Common', 0777);
    is_dir(APP_PATH . config('DEFAULT_MODEL') . '/Common') || mkdir(APP_PATH . config('DEFAULT_MODEL') . '/Common', 0777);
    is_dir(APP_PATH . config('COMMON_MODEL') . '/Conf') || mkdir(APP_PATH . config('COMMON_MODEL') . '/Conf', 0777);
    is_dir(APP_PATH . config('DEFAULT_MODEL') . '/Conf') || mkdir(APP_PATH . config('DEFAULT_MODEL') . '/Conf', 0777);
    is_file(APP_PATH . config('COMMON_MODEL') . '/Conf' . 'config.php') || file_put_contents(APP_PATH . config('COMMON_MODEL') . '/Conf/' . 'config.php', "<?php\nreturn array(\n\t//'配置项'=>'配置值'\n);\n?>");
    is_file(APP_PATH . config('DEFAULT_MODEL') . '/Conf' . 'config.php') || file_put_contents(APP_PATH . config('DEFAULT_MODEL') . '/Conf/' . 'config.php', "<?php\nreturn array(\n\t//'配置项'=>'配置值'\n);\n?>");
    is_dir(APP_PATH . config('COMMON_MODEL') . 'View') || mkdir(APP_PATH . config('COMMON_MODEL') . 'View', 0777);    //项目试图目录
    is_dir(APP_PATH . config('DEFAULT_MODEL') . 'View') || mkdir(APP_PATH . config('DEFAULT_MODEL') . 'View', 0777);    //项目试图目录
}

// 创建测试Controller
function build_first_Controller() {
    $content = file_get_contents(HAPPY_PATH . 'Tpl/default_index.php');
    file_put_contents(APP_PATH . config('DEFAULT_MODEL') . '/Controller/IndexController.class.php', $content);
}
