<?php

/**
 * =================================================
 * 框架底层配置文件
 * ================================================
 * @category happy
 * @package Admin/
 * @subpackage Action
 * @author Happy <yangbai6644@163.com>
 * @dateTime 2014-5-3 15:41:38
 * ================================================
 */
return array(
    'APP_DEBUG'          => true, //调试模式
    'DEBUG_TRACE'        => true, //显示调试信息
    'COMMON_MODULE'      => 'Common', //公共模块
    'DEFAULT_MODULE'     => 'Home', //默认模块
    'DEFAULT_CONTROLLER' => 'Index', //默认控制器,
    'DEFAULT_ACTION'     => 'index', //默认方法
    'VAR_MODULE'         => 'm', //默认模块变量
    'VAR_CONTROLLER'     => 'c', //默认控制器变量,
    'VAR_ACTION'         => 'a', //默认方法变量
    'ERROR_TPL'          => './Happy/Tpl/error.php', //默认错误信息模版
);

