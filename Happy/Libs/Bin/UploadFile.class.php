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
    private $uploadFiles = array(); //保存上传文件返回的信息
    private $errorInfo = array(
        1   =>  '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值',
        2   =>  '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值',
        3   =>  '文件只有部分被上传',
        4   =>  '没有文件被上传',
        5   =>  '上传文件大小为0'
    );

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
    
    /**
     * 检查上传目录的完整性
     * @return boolean
     */
    private function _checkUploadDir() {
        !is_dir($this->path) && Dir::mkdir($this->path);
        if (is_dir($this->path) && !is_writeable($this->path)) {
            $this->error = '上传根目录不存在';
            return false;
        }
        return true;
    }

    public function upload() {
        if ($this->_checkUploadDir() !== true) {
            return false;
        }
        $files = $this->_formatUploadFiles();
        if ($this->_checkFile($files) !== true) {
            return false;
        }
        $this->uploadFiles = $this->_save($files);
    }
    
    private function _save($files = array()) {
        
    }
    
    /**
     * 检测文件的正确性
     * @param type $files
     * @return boolean
     */
    private function _checkFile($files = array()) {
        foreach ($files as $value) {
            if ($value['error'] !== 0) {
                $this->error = $this->errorInfo[$value['error']];
                return false;
            }
            $filename = pathinfo($value['name']);
            $extension = $filename['extension'];
            if (!in_array($extension, $this->ext)) {
                $this->error = '非法上传文件';
                return false;
            }
            if (intval($value['size']) > $this->size) {
                $this->error = '上传文件大小超过限制';
                return false;
            }
        }
        return true;
    }
    
    /**
     * 格式化上传文件数组
     * @return array
     */
    private function _formatUploadFiles() {
        $files =  $_FILES;
        if (empty($files)) {
            $this->error = '没有上传文件';
            return false;
        }
        $upload_files = array();
        $num = 0;
        foreach ($files as $file) {
            if (is_array($file['name'])) {
                foreach ($file['name'] as $key => $value) {
                    foreach ($file as $k => $v) {
                        $upload_files[$num][$k] = $v[$key];
                    }
                    $num++;
                }        
            } else {
                $upload_files[$num] = $file;
                $num++;
            }
        }
        return $upload_files;
    }
    
    /**
     * 返回上传错误信息
     * @return type
     */
    public function getError() {
        return $this->error;
    }
}
