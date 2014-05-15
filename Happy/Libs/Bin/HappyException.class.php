<?php

/**
 * =================================================
 * 框架异常处理类
 * ================================================
 * @category happy
 * @package Admin/
 * @subpackage Action
 * @author Happy <yangbai6644@163.com>
 * @dateTime 2014-5-12 22:42:09
 * ================================================
 */
class HappyException extends Exception {
    
    /**
     * 架构函数，必须执行一次父类的架构函数
     */
    function __construct($message, $code = null, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * 返回异常信息
     */
    public function show() {
        $e = array();
        $e['message'] = "Exception: " . $this->getMessage() . " <br/>" . $this->getFile() . '[' . $this->getLine() . ']';
        $e['info']    = '<ol class="linenums">';
        foreach ($this->getTrace() as $value) {
            $e['info'] .= '<li><span>';
            isset($value['file']) && $e['info'] .= $value['file'] . ' ';
            $e['info'] .= '<strong>';
            isset($value['class']) && $e['info'] .= $value['class'];
            isset($value['type']) && $e['info'] .= $value['type'];
            isset($value['function']) && $e['info'] .= $value['function'] . '()</strong>';
            $e['info'] .= '</span></li>';
        }
        $e['info'] .= '</ol>';
        $exception = strip_tags($e['message']); 
        Log::write($exception, 'EXCEPTION');
        return $e;
    }
}
