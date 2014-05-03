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
        spl_autoload_register(array(__CLASS__, 'autoload'));
        App::run();
    }

    public static function autoload($class) {
        if (file_exists(CORE_PATH . $class . '.class.php')) {
            require CORE_PATH . $class . '.class.php';
        }
    }

}
