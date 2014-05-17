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
        $tree = array();
        $dirname   = self::formatPath($dirname);
        $id = 0;    //目录树标识ID
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
                    $tree[$id]['son'] = self::getTree($value, $recursion, $exts);
                }
                ++$id;
            }
        }
        return $tree;
    }
    
    /**
     * 获取目录的数据结构，非递归获取
     * @param type $dirname
     * @return type
     */
    public static function getDirTree($dirname) {
        $tree = array();
        $dirname   = self::formatPath($dirname);
        $id = 0;    //目录树标识ID
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

}
