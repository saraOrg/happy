<?php

/**
 * =================================================
 * 框架文件上传处理类
 * ================================================
 * @category happy
 * @package Libs/
 * @subpackage Bin
 * @author Happy <yangbai6644@163.com>
 * @dateTime 2014-5-27 23:04:38
 * ================================================
 */
class UploadFile {

    private $path  = './Uploads/';   //上传根目录
    private $ext   = array('jpg', 'gif', 'jpeg', 'gif', 'doc');   //允许上传文件的类型
    private $size  = 19880430;   //上传文件大小上限
    private $error = '';    //上传错误信息

    /**
     * 构造函数，初始化配置数据
     * @param type $path
     * @param type $ext
     */

    public function __construct($config = array()) {
        config('UPLOAD_PATH') && $this->path = config('UPLOAD_PATH');
        config('UPLOAD_FILE_EXT') && $this->path = config('UPLOAD_FILE_EXT');
        foreach (array_keys(get_class_vars(__CLASS__)) as $attr) {
            isset($config[$attr]) && $this->$attr = $config[$attr];
        }
    }

    private function _checkUploadDir() {
        !is_dir($this->path) && Dir::mkdir($this->path);
        if (is_dir($this->path) && !is_writeable($this->path)) {
            $this->error = '上传根目录不存在';
            return false;
        }
    }

    public function upload() {
        $this->_checkUploadDir();
    }
    
    /**
     * 返回上传错误信息
     * @return type
     */
    public function getError() {
        return $this->error;
    }
}
