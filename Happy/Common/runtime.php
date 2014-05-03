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
// 路径设置 可在入口文件中重新定义 所有路径常量都必须以/ 结尾
defined('CONTROLLER_PATH') or define('CONTROLLER_PATH', APP_PATH . 'Controller/'); // 项目控制器目录
defined('MODEL_PATH') or define('MODEL_PATH', APP_PATH . 'Model/'); // 项目模型目录
defined('COMMON_PATH') or define('COMMON_PATH', APP_PATH . 'Common/'); // 项目公共目录
defined('CONF_PATH') or define('CONF_PATH', APP_PATH . 'Conf/'); // 项目配置目录
defined('LANG_PATH') or define('LANG_PATH', APP_PATH . 'Lang/'); // 项目语言包目录
defined('VIEW_PATH') or define('VIEW_PATH', APP_PATH . 'View/'); // 项目模板目录

/**
 * 项目目录创建和初始化
 */
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
    is_dir(CACHE_PATH) || mkdir(CACHE_PATH, 0777);
    is_dir(LOG_PATH) || mkdir(LOG_PATH, 0777);
    is_dir(DATA_PATH) || mkdir(DATA_PATH, 0777);
    is_dir(TEMP_PATH) || mkdir(TEMP_PATH, 0777);
    //载入核心文件
    $files = require HAPPY_PATH . 'Common/files.php';
    foreach ($files as $file) {
        require $file;
    }
    return true;
}

// 创建应用目录结构
function build_app_dir() {
    is_dir(APP_PATH) || mkdir(APP_PATH, 0777);  //项目目录
    is_dir(CONTROLLER_PATH) || mkdir(CONTROLLER_PATH, 0777);  //项目控制器目录
    is_file(CONTROLLER_PATH . 'IndexController.class.php') || build_first_Controller();
    is_dir(MODEL_PATH) || mkdir(MODEL_PATH, 0777);      //项目模型目录
    is_dir(COMMON_PATH) || mkdir(COMMON_PATH, 0777);    //项目公共函数目录
    is_dir(CONF_PATH) || mkdir(CONF_PATH, 0777);        //项目配置文件目录
    file_put_contents(CONF_PATH . 'config.php', "<?php\nreturn array(\n\t//'配置项'=>'配置值'\n);\n?>");
    is_dir(VIEW_PATH) || mkdir(VIEW_PATH, 0777);    //项目试图目录
}

// 创建测试Controller
function build_first_Controller() {
    $content = file_get_contents(HAPPY_PATH . 'Tpl/default_index.php');
    file_put_contents(CONTROLLER_PATH . 'IndexController.class.php', $content);
}
