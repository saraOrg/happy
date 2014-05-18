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
        $style      = '<style type="text/css">';
        $style .= '.close{font-size:20px;line-height:30px;padding-right:5px;float:right;cursor:pointer}.close:hover{color:red}';
        $style .= '</style>';
        $javascript = '<script type="text/javascript" language="javascript">';
        $javascript .= 'var trace = document.getElementById("trace"), close = document.getElementsByClassName("close")[0];tips = document.getElementById("tips");';
        $javascript .= 'close.onclick = function() {trace.style.display = "none";tips.style.display = "block";};';
        $javascript .= 'tips.onclick = function() {trace.style.display = "block";this.style.display = "none";};';
        $javascript .= '</script>';
        $tpl        = '<div id="trace" style="width:99%;height:200px;position:absolute;bottom:0;display:none">';
        $tpl .= '<div title="关闭" style="height:30px;background:#ccc;"><div class="close">X</div></div>';
        $tpl .= '<div style="height:150px;padding:10px;overflow:auto">';
        foreach (self::$debug as $info) {
            $tpl .= $info . '<br/>';
        }
        $tpl .= '<p>页面运行时间' . run_time('start', 'end') . '</p>';
        $tpl .= '<p>页面内存峰值' . run_memory('start', 'end') . '</p>';
        $tpl .= '<p>总共加载文件数<strong>' . load_file() . '</strong>个</p>';
        $tpl .= '</div></div>';
        $tips = '<div id="tips" style="width:80px;height:40px;background:#000;color:#fff;line-height:40px;position:absolute;bottom:0;right:0;text-align:center;cursor:pointer">' 
                . run_time('start', 'end') . '</div>';
        echo $style . $tpl . $tips . $javascript;
    }

}
