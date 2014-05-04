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
        $str = '<div class="debug-info" style="border:1px solid #ccc;margin:20px;padding:10px">';
        $str .= '<ul style="list-style:none;margin:0;padding:0"><li style="display:inline-block;width:80px;line-height:25px;text-align:center;background:#f2f2f2">基本信息</li><li style="display:inline-block;">文件信息</li></ul>';
        self::$debug[] = '页面运行时间 ' . run_time('start', 'end') . ' 秒';
        $str .= '<ul style="list-style:none;margin:0;padding:0">';
        foreach (array_reverse(self::$debug) as $info) {
            $str .= $info . '<br/>';
        }
        $str .= '</ul></div>';
        echo $str;
    }
}

