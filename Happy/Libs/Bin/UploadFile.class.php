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
    private $ext   = array('jpg', 'gif', 'jpeg', 'png', 'gif', 'doc');   //允许上传文件的类型
    private $size  = 19880430;   //上传文件大小上限
    private $error = '';    //上传错误信息
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
    
    /**
     * 上传文件操作
     * @return boolean
     */
    public function upload() {
        if ($this->_checkUploadDir() !== true) {
            return false;
        }
        $files = $this->_formatUploadFiles();
        if ($this->_checkFile($files) !== true) {
            return false;
        }
        return $this->_save($files);
    }
    
    /**
     * 上传文件到上传目录
     * @param type $files   上传文件队列
     * @return boolean|string
     */
    private function _save($files = array()) {
        $filesInfo = array();
        foreach ($files as $file) {
            $savePath = $this->path . $this->_getFileType($file['name']);
            is_dir($savePath) || mkdir($savePath, 0777);
            $savePath .= '/' . date('Y-m-d') . '/';
            is_dir($savePath) || mkdir($savePath, 0777);
            $type = $this->_getFileExtensionName($file['name']);
            $saveName = md5(uniqid()) . '.' . $type;
            if (!move_uploaded_file($file['tmp_name'], $savePath . $saveName)) {
                $this->error = '上传文件' .$file['name'].'失败';
                return false;
            } else {
                $filesInfo[] = array('savePath' => $savePath, 'type' => $type, 'saveName' => $saveName);
            }
        }
        return $filesInfo;
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
            if (!in_array($this->_getFileExtensionName($value['name']), $this->ext)) {
                $this->error = '文件类型不允许';
                return false;
            }
            if (intval($value['size']) > $this->size) {
                $this->error = '上传文件大小超过限制';
                return false;
            }
            if (!is_uploaded_file($value['tmp_name'])) {
                $this->error = '非法文件';
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
     * 根据文件名获取文件后缀名
     * @param type $filename
     * @return type
     */
    private function _getFileExtensionName($filename = '') {
        $filename = pathinfo($filename);
        return isset($filename['extension']) ? $filename['extension'] : '';
    }
    
    /**
     * 根据文件名获取文件类型
     * @param type $filename
     * @return string
     */
    private function _getFileType($filename = '') {
        $extenion = $this->_getFileExtensionName($filename);
        if (in_array($extenion, array('jpg', 'png', 'gif'))) {
            return 'image';
        } else {
            return $extenion;
        }
    }
    
    /**
     * 返回上传错误信息
     * @return type
     */
    public function getError() {
        return $this->error;
    }
}
