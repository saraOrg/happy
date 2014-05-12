<?php

/**
 * =================================================
 * 框架初始化类
 * ================================================
 * @category happy
 * @package Admin/
 * @subpackage Action
 * @author Happy <yangbai6644@163.com>
 * @dateTime 2014-5-3 12:49:51
 * ================================================
 */
class App {

    /**
     * 初始化运行函数
     */
    public static function run() {
        self::init();   //初始化
        self::exec();   //执行程序
    }

    /**
     * 项目初始化
     */
    public static function init() {
        self::config();                 //初始化配置
        if (!is_dir(APP_PATH)) {
            self::build_app_dir();  //初始化应用目录
        }
    }

    /**
     * 执行程序
     */
    public static function exec() {
        load_file(CORE_PATH . 'Controller.class.php'); //加载底层控制器
        $controller = controller(CONTROLLER_NAME, ACTION_NAME);  //执行默认方法
    }

    /**
     * 载入项目配置
     */
    public static function config() {
        if (file_exists(APP_PATH . config('COMMON_MODEL') . '/Conf/config.php')) {    //加载公共模块配置
            config(require APP_PATH . config('COMMON_MODEL') . '/Conf/config.php');
        }
        if (file_exists(MODULE_PATH . '/Conf/config.php')) {    //加载当前模块配置
            config(require MODULE_PATH . '/Conf/config.php');
        }
    }

    /**
     * 创建基本的应用目录结构
     */
    public static function build_app_dir() {
        is_dir(APP_PATH) || mkdir(APP_PATH, 0777);  //应用目录
        is_dir(COMMON_PATH) || mkdir(COMMON_PATH, 0777);      //公共模块目录
        is_dir(MODULE_PATH) || mkdir(MODULE_PATH, 0777);    //默认模块目录
        is_dir(COMMON_PATH . 'Controller/') || mkdir(COMMON_PATH . 'Controller/', 0777);
        is_dir(CONTROLLER_PATH) || mkdir(CONTROLLER_PATH, 0777);
        is_file(CONTROLLER_PATH . 'IndexController.class.php') || self::createDefaultController();
        is_dir(COMMON_PATH . 'Model/') || mkdir(COMMON_PATH . 'Model/', 0777);
        is_dir(MODEL_PATH) || mkdir(MODEL_PATH, 0777);
        is_dir(COMMON_PATH . 'Common/') || mkdir(COMMON_PATH . 'Common/', 0777);
        is_dir(MODULE_PATH . 'Common/') || mkdir(MODULE_PATH . 'Common/', 0777);
        is_dir(COMMON_PATH . 'Conf/') || mkdir(COMMON_PATH . 'Conf/', 0777);
        is_dir(CONF_PATH) || mkdir(CONF_PATH, 0777);
        is_file(COMMON_PATH . 'Conf/' . 'config.php') || self::createDefaultConf(COMMON_PATH . 'Conf/');
        is_file(CONF_PATH . 'config.php') || self::createDefaultConf(CONF_PATH);
        is_dir(VIEW_PATH) || mkdir(VIEW_PATH, 0777);    //模块试图目录
    }

    /**
     * 创建默认的Controller
     */
    public static function createDefaultController() {
        $content = file_get_contents(HAPPY_PATH . 'Tpl/default_index.php');
        file_put_contents(CONTROLLER_PATH . 'IndexController.class.php', $content);
    }

    /**
     * 创建默认的配置文件
     */
    public static function createDefaultConf($path = '') {
        if ('' === $path) {
            return;
        }
        $content = "<?php\nreturn array(\n\t//'配置项'=>'配置值'\n);\n?>";
        file_put_contents($path . 'config.php', $content);
    }

}
