<?php

/**
 * =================================================
 * 框架验证码类
 * ================================================
 * @category happy
 * @package Libs/
 * @subpackage Bin
 * @author Happy <yangbai6644@163.com>
 * @dateTime 2014-5-20 20:42:26
 * ================================================
 */
class Verify {

    //画布资源句柄
    private $image   = null;
    //画布宽度
    private $width   = 80;
    //画布高度
    private $height  = 25;
    //画布背景色
    private $bgcolor = '#DCDCDC';
    //随机种子
    private $key     = '012345678ABCDEFGHIGHLMNOPQRSTUVWXYZ';
    //长度(位数)
    private $length  = 4;
    //字体文件
    private $font;
    //字体大小
    private $size    = 16;
    //验证码值
    private $verify  = '1988';
    //字体颜色
    private $color   = '#000000';

    /**
     * 构造函数
     */
    public function __construct($config = array()) {
        $this->font = CORE_PATH .  'Verify/ttf' . DIRECTORY_SEPARATOR . '1.ttf';
        if (!empty($config)) {
            foreach (array_keys(get_class_vars(__CLASS__)) as $attr) {
                isset($config[$attr]) && $this->$attr = $config[$attr];
            }
        }
        $this->width = $this->length * $this->size;
        $this->height = $this->width/$this->length * 1.5;
    }

    /**
     * 获取RGB颜色值
     * @param type $color
     * @return type
     */
    private function _getRgb($color = '#000000') {
        $rgb          = array('red' => 0, 'green' => 0, 'blue' => 0);
        $rgb['red']   = hexdec(substr($color, 1, 2));
        $rgb['green'] = hexdec(substr($color, 3, 2));
        $rgb['blue']  = hexdec(substr($color, 5, 2));
        return $rgb;
    }

    /**
     * 创建验证码
     */
    private function _createVerify() {
        $i   = 0;
        $chr = '';
        if (strlen($this->key) >= $this->length) {
            $this->verify = '';
        }
        for ($i = 0; $i < $this->length; $i++) {
            $chr = $this->key[mt_rand(0, strlen($this->key) - 1)];
            if (in_array($chr, array('O', '0', 'o'))) {
                $chr = 'S';
            }
            $this->verify .= $chr;
        }
    }

    /**
     * 环境监测
     * @return type
     */
    private function _checkEnv() {
        if (!extension_loaded('gd')) {
            return false;
        }
        return true;
    }

    /**
     * 创建画布
     */
    private function _createCanvas() {
        $this->_checkEnv() || trigger_error('请确保GD库是否正常', E_USER_WARNING);
        $width       = $this->width;
        $height      = $this->height;
        $rgb         = $this->_getRgb($this->bgcolor);
        $this->image = imagecreatetruecolor($width, $height);
        $color       = imagecolorallocate($this->image, $rgb['red'], $rgb['green'], $rgb['blue']);
        imagefill($this->image, 0, 0, $color);
    }
    
    private function _createDisturb() {
        $color = $this->_getRgb($this->color);
        $color = imagecolorallocate($this->image, $color['red'], $color['green'], $color['blue']);
        for ($i=0; $i<$this->width; $i++) {
            imagesetpixel($this->image, mt_rand(0, $this->width), mt_rand(0, $this->height), $color);
//            $arc = pi() * ($i * $this->width/180/180);
//            imagesetpixel($this->image, $i, sin($arc)*50, $color);
//            imagesetpixel($this->image, $i, cos($arc)*50, $color);
//            imagesetpixel($this->image, $i, cosh($arc)*50, $color);
        }
        //imagearc($this->image, $this->width / 2, $this->height, $this->width, $this->height, 0, 720, $color);
        for ($i = 0; $i < 15; $i++) {
            imageline($this->image, 0, $i * 5, $this->width, $i * 5, $color);
        }
        for ($i = 0; $i < 80; $i++) {
            imageline($this->image, $i * 5, 0, $i * 6, $this->height, $color);
        }
    }
    
    /**
     * 绘制验证码
     */
    private function _drawText() {
        $i = '';
        $rgb   = $this->_getRgb($this->color);
        $color = imagecolorallocate($this->image, $rgb['red'], $rgb['green'], $rgb['blue']);
        $this->_createVerify();
        for ($i = 0; $i < $this->length; $i++) {
            $x = floor($this->width / $this->length * $i)+2;
            imagettftext($this->image, $this->size, mt_rand(-20, 20), $x, $this->height/1.3, $color, $this->font, $this->verify[$i]);
        }
    }

    /**
     * 显示验证码
     * @return boolean
     */
    public function display() {
        $this->_createCanvas();
        $this->_createDisturb();
        $this->_drawText();
        ob_clean(); //清空缓冲区的输出，防止输出图片的时候前面有输出，导致图片无法正常显示
        if (function_exists('imagepng')) {
            header('Content-Type:image/png');
            imagepng($this->image);
            return true;
        }
        if (function_exists('imagegif')) {
            header('Content-Type:image/gif');
            imagegif($this->image);
            return true;
        }
        if (function_exists('imagejpeg')) {
            header('Content-Type:image/jpeg');
            imagejpeg($this->image);
            return true;
        }
        imagedestroy($this->image);
        trigger_error('请确保GD库函数是否正常', E_USER_WARNING);
    }

}
