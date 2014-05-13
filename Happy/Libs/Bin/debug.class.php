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

        foreach (self::$debug as $key => $info) {
            echo $info . '<br/>';
        }
        echo '<p>页面运行时间' . run_time('start', 'end') . '</p>';
        echo '<p>页面内存峰值' . run_memory('start', 'end') . '</p>';
        echo '<p>总共加载文件数<strong>' . load_file() . '</strong>个</p>';
    }

}
