<?php

/**
 * =================================================
 * Description of Log
 * ================================================
 * @category happy
 * @package Admin/
 * @subpackage Action
 * @author Happy <yangbai6644@163.com>
 * @dateTime 2014-5-14 20:34:32
 * ================================================
 */
class Log {

    // 日志级别 从上到下，由低到高
    const EMERG        = 'EMERG';  // 严重错误: 导致系统崩溃无法使用
    const ALERT        = 'ALERT';  // 警戒性错误: 必须被立即修改的错误
    const CRIT         = 'CRIT';  // 临界值错误: 超过临界值的错误，例如一天24小时，而输入的是25小时这样
    const ERROR        = 'ERROR';  // 一般错误: 一般性错误
    const WARNING      = 'WARNING';  // 警告性错误: 需要发出警告的错误
    const NOTICE       = 'NOTIC';  // 通知: 程序可以运行但是还不够完美的错误
    const EXCEPTION    = 'EXCEPTION';  //异常信息
    const INFO         = 'INFO';  // 信息: 程序输出信息
    const DEBUG        = 'DEBUG';  // 调试: 调试信息
    const SQL          = 'SQL';  // SQL：SQL语句 注意只在调试模式开启时有效
    // 日志记录方式
    const SYSTEM       = 0;
    const MAIL         = 1;
    const FILE         = 3;
    const SAPI         = 4;
    //记录日志的方式
    const LOG_TYPE     = 'file'; //mongo/db...
    //日志文件大小
    const LOG_MAX_SIZE = 19880430;

    // 日志信息
    static $log    = array();
    // 日期格式
    static $format = '[ Y-m-d H:i:s ]';

    /**
     * 记录日志，并会过滤未经设置的级别
     * @param type $message 日起信息
     * @param type $level   日志级别
     * @param type $record  是否强制记录，不过滤日志级别
     */
    public static function record($message, $level = self::ERROR, $record = false) {
        if ($record || in_array($level, config('LOG_LEVEL'))) {
            self::$log[] = $level . ': ' . $message;
        }
    }

    /**
     * 保存日志
     * @param type $type        日志记录方式
     * @param type $destination 日志保存位置 
     * @param type $extra       保存额外参数
     * @return type
     */
    public static function save($type = '', $destination = '', $extra = '') {
        if (empty(self::$log)) {
            return;
        }
        $type = config('LOG_TYPE') ? : 3;
        if ($type === self::FILE) {
            //没有指定日志文件名则按照当前年月日组装
            empty($destination) && $destination = LOG_PATH . date('y_m_d') . '.log';
            //检测日志文件大小，超过配置大小则备份日志文件重新生成
            if (is_file($destination) && floor(self::LOG_MAX_SIZE) <= filesize($destination)) {
                \rename($destination, dirname($destination) . '/' . time() . '-' . basename($destination));
            }
        } else {
            $destination = $destination ? : config('LOG_DEST');
            $extra       = $extra ? : config('LOG_EXTRA');
        }
        $content = date(self::$format) . ' [' . get_client_ip() . '] [' . filter_input(INPUT_SERVER, 'REQUEST_URI') . "] " . implode('', self::$log) . "\r\n";
        error_log($content, $type, $destination, $extra);
        //保存后清空日志缓存
        self::$log = array();
    }

    /**
     * 直接写日志操作
     * @param type $message
     * @param type $level
     * @param type $destination
     * @param type $extra
     */
    public static function write($message = '', $level = self::INFO, $destination = '', $extra = '') {
        switch (self::LOG_TYPE) {
            case 'file':
                self::_write($message, $level, $destination, $extra);
                break;
            case 'db':
                $data = array(
                    'message'     => $message,
                    'level'       => $level,
                    'create_date' => time(),
                    'create_by'   => $_SESSION['user']['login_name'],
                );
                M('SysLog')->data($data)->add();
                break;
            case 'mongo':
                //待续...
                break;
            default:
                break;
        }
    }

    /**
     * 以文件方式写日志
     * @param type $message
     * @param type $level
     * @param type $destination
     * @param type $extra
     */
    private static function _write($message = '', $level = self::ERR, $destination = '', $extra = '') {
        //日志每一行的前缀
        $pre = date(self::$format);
        //没有指定日志文件名则按照当前年月日组装
        if ($destination === '') {
            $destination = LOG_PATH . date('Ymd') . '.log';
        }
        $logMaxSize = config('LOG_MAX_SIZE') ?: self::LOG_MAX_SIZE;
        //日志文件存在且大小超过了系统配置的大小，则备份重新生成
        if (is_file($destination) && floor($logMaxSize) <= filesize($destination)) {
            rename($destination, dirname($destination) . '/' . time() . '-' . basename($destination));
        }
        //使用PHP系统函数记录
        \error_log($pre . ' ' . $level . ': ' . $message . "\r\n", self::FILE, $destination, $extra);
    }

}
