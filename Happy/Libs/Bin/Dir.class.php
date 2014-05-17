<?php

/**
 * =================================================
 * 框架目录操作类
 * ================================================
 * @category Happy
 * @package Libs/
 * @subpackage Bin
 * @author Happy <yangbai6644@163.com>
 * @dateTime 2014-5-17 19:55:15
 * ================================================
 */
final class Dir {

    /**
     * 格式化目录路径
     * @param type $dirname 目录名称
     * @return type
     */
    public static function formatPath($dirname) {
        $dirname = str_ireplace(array('\\', '//'), '/', $dirname);
        return rtrim($dirname, '/') . '/';
    }

    /**
     * 获得目录/文件名
     * @param type $dirname
     * @return type
     */
    public static function getName($dirname) {
        return basename(self::formatPath($dirname));
    }

    /**
     * 获得文件的扩展名
     * @param type $dirname
     * @return type
     */
    public static function getExtName($dirname) {
        $dirname = self::formatPath($dirname);
        if (is_file($dirname)) {
            $info = pathinfo($dirname);
            return $info['extension'];
        }
    }

    /**
     * 获得目录全部树结构
     * @param type $dirname
     * @param type $recursion
     * @param type $exts
     * @return type
     */
    public static function getTree($dirname, $recursion = false, $exts = array()) {
        $tree    = array();
        $dirname = self::formatPath($dirname);
        $id      = 0;    //目录树标识ID
        if (!empty($exts) && is_array($exts)) {
            $exts = join('|', $exts);
        }
        foreach (glob($dirname . '*') as $value) {
            if (!$exts || preg_match('/(\.' . $exts . ')/i', $value)) {
                $tree[$id]['name']  = basename($value);
                $tree[$id]['path']  = realpath($value);
                $tree[$id]['type']  = filetype($value);
                $tree[$id]['mtime'] = toDate(filemtime($value));
                $tree[$id]['ctime'] = toDate(filectime($value));
                $tree[$id]['read']  = is_readable($value);
                $tree[$id]['write'] = is_writable($value);
                if ($recursion === true && is_dir($value)) {
                    $tree[$id]['child'] = self::getTree($value, $recursion, $exts);
                }
                ++$id;
            }
        }
        return $tree;
    }

    /**
     * 获取目录的数据结构
     * @param type $dirname
     * @return type
     */
    public static function getDirTree($dirname, $recursion = false) {
        $tree    = array();
        $dirname = self::formatPath($dirname);
        $id      = 0;    //目录树标识ID
        foreach (glob($dirname . '*') as $value) {
            if (!is_dir($value)) {
                continue;
            }
            $tree[$id]['name']  = basename($value);
            $tree[$id]['path']  = realpath($value);
            $tree[$id]['type']  = filetype($value);
            $tree[$id]['mtime'] = toDate(filemtime($value));
            $tree[$id]['ctime'] = toDate(filectime($value));
            $tree[$id]['read']  = is_readable($value);
            $tree[$id]['write'] = is_writable($value);
            if ($recursion === true) {
                $tree[$id]['child'] = self::getDirTree($value, true);
            }
            ++$id;
        }
        return $tree;
    }

    /**
     * 递归删除目录
     * @param type $dirname
     * @return boolean
     */
    public static function rmdir($dirname) {
        $dirname = self::formatPath($dirname);
        if (is_dir($dirname)) {
            foreach (glob($dirname . '*') as $value) {
                is_dir($value) ? self::rmdir($value) : unlink($value);
            }
            return rmdir($dirname);
        }
        return false;
    }

    /**
     * 创建目录，支持递归创建
     * @param type $dirname 目录名称
     * @param type $auth    目录权限
     * @return type
     */
    public static function mkdir($dirname, $auth = 0777) {
        $dirname = self::formatPath($dirname);
        if (is_dir($dirname)) {
            return true;
        }
        /**
            //原生递归写法
            $path = explode('/', $dirname);
            $mdir = '';
            foreach ($path as $value) {
                if ($value) {
                    $mdir .= '/' . $value;
                    is_dir($mdir) || mkdir($mdir, $auth);
                }
            }
            return is_dir($dirname);
         */
        //PHP mkdir函数设置递归创建参数
        return mkdir($dirname, $auth, true);
    }
    
    /**
     * 拷贝目录 [注意不要把源目录拷贝到其子目录中，会无限递归导致程序崩溃]
     * @param type $srcDir  源目录
     * @param type $dstDir  目的目录
     */
    public static function copy($srcDir, $dstDir) {
        $srcDir = self::formatPath($srcDir);
        if (!is_dir($srcDir)) {
            error('拷贝目录出错，源目录不存在！');
        }
        $dstDir = self::formatPath($dstDir);
        if (stripos($dstDir, $srcDir) !== false) {
            error('拷贝目录出错，目的目录['.$dstDir.']是源目录['.$srcDir.']的子目录！');
        }
        is_dir($dstDir) || self::mkdir($dstDir);
        foreach (glob($srcDir . '*') as $value) {
            $dst = $dstDir . basename($value);
            if (is_file($dst)) {
                continue;
            }
            if (is_dir($value)) {
                self::copy($value, $dst);
            } else {
                copy($value, $dst);
                chmod($dst, 0777);
            }
        }
    }
}
