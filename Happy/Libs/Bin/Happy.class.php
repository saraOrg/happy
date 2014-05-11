<?php

/**
 * =================================================
 * 框架核心类
 * ================================================
 * @category happy
 * @package Admin/
 * @subpackage Action
 * @author Happy <yangbai6644@163.com>
 * @dateTime 2014-5-3 12:52:42
 * ================================================
 */
class Happy {

    /**
     * 框架初始化开始
     */
    public static function start() {
        set_error_handler(array(__CLASS__, 'error'));           //注册自动处理错误方法
        spl_autoload_register(array(__CLASS__, 'autoload'));    //注册自动加载类的方法
        function_exists('date_default_timezone_set') && date_default_timezone_set(config('DEFAULT_TIMEZONE'));  //设置默认时区
        App::run();
        if (config('APP_DEBUG') === true) {
            debug::show();
        }
    }

    /**
     * 根据类名自动加载类
     * @param type $class
     */
    public static function autoload($class) {
        load_file(CORE_PATH . $class . '.class.php');
    }

    /**
     *  自定义错误处理
     */
    public static function error($errno, $errstr, $errfile, $errline) {
        switch ($errno) {
            case E_ERROR:
            case E_USER_ERROR:
                error('EOOER: ' . $errstr . ' ' . $errfile . '[' . $errline . ']');
            case E_WARNING:
            case E_USER_WARNING:
                error('WARING: ' . $errstr . ' ' . $errfile . '[' . $errline . ']');
            case E_NOTICE:
            case E_USER_NOTICE:
                notice(func_get_args());
        }
    }

}
