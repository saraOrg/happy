<?php

/**
 * =================================================
 * 框架缓存类
 * ================================================
 * @category happy
 * @package Admin/
 * @subpackage Action
 * @author Happy <yangbai6644@163.com>
 * @dateTime 2014-5-7 20:50:34
 * ================================================
 */
class Cache {

    private static $handle = null;
    private $path          = '';
    private static $obj = null;

    private function __construct($type, $path) {
        if ($type === '') {
            self::$handle = 'file';
            $this->setPath($path);
            return;
        }
        if (strtolower($type) === 'memcache' && extension_loaded('memcache')) {
            self::$handle = new Memcache;
            self::$handle->connect(C('MEM_HOST'), C('MEM_PORT'));
        }
    }

    public static function getInstace($type = '', $path = '') {
        if (is_null(self::$obj)) {
            self::$obj = new Cache($type, $path);
        }
        return self::$obj;
    }

    private function setPath($path = '') {
        if ($path === '') {
            $this->path = RUNTIME_PATH . 'Temp/';
        } else {
            $this->path = $path;
        }
    }

    private function getFilename($key) {
        return $this->path . md5($key) . '.php';
    }

    public function set($key, $data, $expire = 60) {
        if (self::$handle === 'file') {
            $filename = $this->getFilename($key);
        }
        $data = serialize($data);   //序列化
        $data = gzcompress($data);  //压缩
        if (is_writable($this->path)) {
            file_put_contents($filename, $data);
            touch($filename, time() + $expire);
        }
    }

    public function get($key = null) {
        if (self::$handle === 'file') {
            $filename = $this->getFilename($key);
            if (file_exists($filename) && filemtime($filename) > time()) {
                $data = file_get_contents($filename);
                $data = gzuncompress($data);
                $data = unserialize($data);
                return $data;
            } else {
                file_exists($filename) && unlink($filename);
                return null;
            }
        }
    }

    public function delete($key) {
        
    }

    public function flush() {
        
    }

}
