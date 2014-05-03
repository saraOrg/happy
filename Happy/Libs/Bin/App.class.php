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
        self::init();
    }

    /**
     * 项目初始化
     */
    public static function init() {
        self::config(); //初始化配置
    }

    /**
     * 载入项目配置
     */
    public static function config() {
        config(require ETC_PATH . 'config.php');   //加载框架底层配置
        if (file_exists(CONF_PATH . 'config.php')) {    //加载项目配置
            config(require CONF_PATH . 'config.php');
        }
    }

}
