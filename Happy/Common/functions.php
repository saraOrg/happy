<?php

/**
 * =================================================
 * 框架底层函数库文件
 * ================================================
 * @category happy
 * @package Admin/
 * @subpackage Action
 * @author Happy <yangbai6644@163.com>
 * @dateTime 2014-5-2 23:00:24
 * ================================================
 */

/**
 * 快速格式化打印数组
 */
function p($arr) {
    echo '<pre>', \print_r($arr, true), '</pre>';
}

/**
 * 错误信息输出
 */
function error($msg) {
    if (config('APP_DEBUG') !== true) {
        $msg = '<div style="width:500px;height:100px;border:solid 1px #333;background:#f2f2f2;padding:10px;position:absolute;top:20%;"><h1>出错啦:(</h1></div>';
        exit($msg);
    }
    $e = array('title' => '', 'info' => '');
    if (is_string($msg)) {
        $trace        = debug_backtrace();
        $e['file']    = $trace[0]['file'];
        $e['line']    = $trace[0]['line'];
        $e['message']   = $msg;
        ob_start();
        debug_print_backtrace();
        $e['info']    = nl2br(ob_get_clean());
    } else {
        $e = $msg;
    }
    include config('ERROR_TPL');
    exit;
}

/**
 * 载入文件
 */
function load_file($file = '') {
    if ($file !== '') {
        static $_files = array();
        if (file_exists($file)) {
            if (!isset($_files[$file])) {
                require $file;
                $_files[$file] = $file;
            }
            debug::msg('加载文件 ' . realpath($_files[$file]) . ' 成功');
        } else {
            error('文件 ' . $file . ' 不存在');
        }
    }
}

/**
 * 获取/设置配置项
 */
function config($key = null, $value = null) {
    static $_config = array();
    if (is_null($key)) {
        return $_config;
    }
    //是数组表示合并配置项
    if (is_array($key)) {
        $_config = array_merge($_config, array_change_key_case($key));
        return true;
    }
    $key = strtolower($key);    //不区分大小写
    //带'.'表示二维形式
    if (strstr($key, '.') !== false) {
        $key = explode('.', $key);
        //key值不存在
        if (!isset($_config[$key[0]][$key[1]])) {
            return false;
        }
        if (is_null($value)) {  //取值
            return $_config[$key[0]][$key[1]];
        } else {    //重新设置
            $_config[$key[0]][$key[1]] = $value;
            return true;
        }
    }
    //key值不存在
    if (!isset($_config[$key])) {
        return false;
    }
    //字符串表示设置/获取 值
    if (is_string($key)) {
        if (is_null($value)) {  //取值
            return $_config[$key];
        } else {    //重新设置
            $_config[$key] = $value;
            return true;
        }
    }
    return false;
}

/**
 * 获得对象实例
 * @staticvar array $_cacheObj
 * @param type $class
 * @param type $method
 * @param type $args
 * @return null|array
 */
function get_instance($class = '', $method = '', $args = array()) {
    static $_cacheObj = array();
    if ($class === '') {
        return null;
    }
    if (isset($_cacheObj[$class])) {
        return $_cacheObj[$class];
    }
    if ($method === '') {
        return $_cacheObj[$class] = new $class;
    }
    if (!method_exists($class, $method)) {
        error('Class ' . $class . ' 没有 ' . $method . ' 这个方法');
    }
    if (empty($args)) {
        return call_user_func(array(new $class, $method));
    }
    return call_user_func_array(array(new $class, $method), array($args));
}

/**
 * 实例化控制器
 * @staticvar array $_controller
 * @param string $name
 * @param type $method
 * @return \name
 */
function controller($name, $method = null) {
    if (!is_dir(MODULE_PATH)) {
        error('模块' . MODULE_NAME . '不存在');
    }
    if (!file_exists(CONTROLLER_PATH . $name . 'Controller.class.php')) {
        error('控制器' . $name . '不存在');
    }
    static $_controller = array();
    $path               = CONTROLLER_PATH . $name . 'Controller.class.php';
    load_file($path);
    $name .= 'Controller';
    if (!isset($_controller[$name])) {
        $_controller[$name] = new $name;
    }
    if (is_null($method) || $method === '') {
        return $_controller[$name];
    }
    if (!method_exists($_controller[$name], $method)) {
        error('方法' . $method . '不存在');
    }
    return $_controller[$name]->$method();
}
