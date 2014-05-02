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
