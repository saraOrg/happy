<?php

/**
 * =================================================
 * 框架路由器处理类
 * ================================================
 * @category happy
 * @package Libs/
 * @subpackage Bin
 * @author Happy <yangbai6644@163.com>
 * @dateTime 2014-5-18 15:49:46
 * ================================================
 */
final class Router {

    static $pathinfo;   //PATHINFO变量
    
    /**
     * URL解析
     */
    public static function parseUrl() {
        $info = array();
        $get  = array();
        if (self::parsePathinfo() === true) {
            $info = explode(config('PATHINFO_DLI'), trim(self::$pathinfo, '/'));
            if ($info[0] !== config('VAR_MODULE')) {
                $get['m'] = isset($info[0]) ? $info[0] : config('DEFAULT_MODULE');
                array_shift($info);
                $get['c'] = isset($info[0]) ? $info[0] : config('DEFAULT_CONTROLLER');
                array_shift($info);
                $get['a'] = isset($info[0]) ? $info[0] : config('DEFAULT_ACTION');
                array_shift($info);
            }
            $i = '';
            for ($i = 0; $i < count($info); $i+=2) {
                $get[$info[$i]] = isset($info[$i + 1]) ? $info[$i + 1] : '';
            }
            $_GET = $get;
        }
        define('MODULE_NAME', getModuleName());                 //当前模块名称
        define('CONTROLLER_NAME', getControllerName());         //当前控制器名称
        define('ACTION_NAME', getActionName());                 //当前方法名称
        define('COMMON_PATH', APP_PATH . 'Common/');            //公共模块目录
        define('MODULE_PATH', APP_PATH . MODULE_NAME . '/');    //当前模块目录
        define('CONTROLLER_PATH', MODULE_PATH . 'Controller/'); //当前控制器目录
        define('MODEL_PATH', MODULE_PATH . 'Model/');           //当前模型目录
        define('CONF_PATH', MODULE_PATH . 'Conf/');             //当前配置文件目录
        define('VIEW_PATH', MODULE_PATH . 'View/');             //当前视图目录
    }
    
    /**
     * pathinfo解析
     * @return boolean
     */
    public static function parsePathinfo() {
        if (isset($_GET[config('VAR_PATHINFO')])) {
            self::$pathinfo = $_GET[config('VAR_PATHINFO')];
        } else if (isset($_SERVER['PATH_INFO'])) {
            self::$pathinfo = $_SERVER['PATH_INFO'];
        } else {
            return false;
        }
        if (strpos(ltrim(self::$pathinfo, '/'), config('PATHINFO_DLI')) === false) {
            return false;
        }
        $suffix = '.' . trim(config('URL_HTML_SUFFIX'), '.');
        self::$pathinfo = str_ireplace($suffix, '', self::$pathinfo);
        return true;
    }

}
