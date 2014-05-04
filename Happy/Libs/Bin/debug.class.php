<?php

/**
 * =================================================
 * 框架调试信息类
 * ================================================
 * @category happy
 * @package Admin/
 * @subpackage Action
 * @author Happy <yangbai6644@163.com>
 * @dateTime 2014-5-3 15:13:43
 * ================================================
 */

class Debug {
    
    static $debug = array();    //调试信息
    
    /**
     * 记录调试信息
     */
    public static function msg($info) {
        self::$debug[] = $info;
    }
    
    /**
     * 输出调试信息
     */
    public static function show() {
        self::$debug[] = '页面运行时间 ' . run_time('start', 'end') . ' 秒';
        foreach (self::$debug as $info) {
            echo $info . '<br/>';
        }
    }
}

