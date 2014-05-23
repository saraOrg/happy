<?php

/**
 * =================================================
 * 框架图像处理类
 * ================================================
 * @category happy
 * @package Libs/
 * @subpackage Bin
 * @author Happy <yangbai6644@163.com>
 * @dateTime 2014-5-23 21:43:19
 * ================================================
 */
class Image {

    /**
     * 类常量定义区
     */
    const LEFT_TOP      = 1;
    const MIDDLE_TOP    = 2;
    const RIGHT_TOP     = 3;
    const LEFT_MIDDLE   = 4;
    const CENTER        = 5;
    const RIGHT_MIDDLE  = 6;
    const LEFT_BOTTOM   = 7;
    const MIDDLE_BOTTOM = 8;
    const RIGHT_BOTTOM  = 9;

    /**
     * 普通属性定义区
     */
    private $image    = null;  //图像资源句柄
    private $water    = 'logo.png';    //水印图片
    private $type     = array(1 => 'gif', 2 => 'jpeg', 3 => 'png');    //图片类型
    private $margin   = '10'; //水印图片的margin值
    private $pos      = self::RIGHT_BOTTOM;   //水印图片位置
    private $overflow = true;   //是否使用原图
    private $text     = '楊佰Happy';
    private $fontfile = '1.ttf';    //水印文字字体
    private $size     = '16';   //水印文字字体大小
    private $color    = '#000000'; //水印文字颜色

    /**
     * 架构函数
     */

    public function __construct() {
        
    }

    /**
     * 设置配置项
     * @param type $config
     */
    public function setConfig($config = array()) {
        foreach (array_keys(get_class_vars(__CLASS__)) as $attr) {
            isset($config[$attr]) && $this->$attr = $config[$attr];
        }
        $this->fontfile = CORE_PATH . 'Verify/ttf/' . $this->fontfile;
        $this->color    = $this->_getRgb($this->color);
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
     * 获取图像的信息
     * @param type $image
     * @return type
     */
    private function _getImageInfo($image) {
        $info = getimagesize($image);
        return array('width' => $info[0], 'height' => $info[1], 'type' => $this->type[$info[2]]);
    }

    /**
     * 检测图像类型
     * @param type $image
     * @return boolean
     */
    private function _checkImageType($image = null) {
        if (!is_null($image)) {
            $info = _getImageSize($image);
            if (in_array($info['type'], $this->type)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 环境检测
     */
    private function _checkEnv($srcImage, $waterImage) {
        extension_loaded('gd') || error('GD库没有开启');
        file_exists($srcImage) || error('原始图像不存在');
        $this->_checkImageType($srcImage) || error('原始图像格式不正确');
        $this->_checkImageType($waterImage) || error('水印图像格式不正确');
    }

    /**
     * 获取水印位置
     * @param type $pos
     * @return type
     */
    private function _getWaterPos($pos = self::RIGHT_BOTTOM, $srcImage, $waterImage) {
        $location = array();
        $srcImage = $this->_getImageInfo($srcImage);
        $water    = $this->_getImageInfo($waterImage);
        ($pos < 1 || $pos > 9) && $pos      = mt_rand(1, 9);
        switch ($pos) {
            case self::LEFT_TOP:
                $location['x'] = $location['y'] = $this->margin;
                return $location;
            case self::MIDDLE_TOP:
                $location['x'] = floor(($srcImage['width'] - $water['width']) / 2);
                $location['y'] = $this->margin;
                return $location;
            case self::RIGHT_TOP:
                $location['x'] = $srcImage['width'] - $this->margin;
                $location['y'] = $this->margin;
                return $location;
            case self::LEFT_MIDDLE:
                $location['x'] = $this->margin;
                $location['y'] = floor(($srcImage['height'] - $water['height']) / 2);
                return $location;
            case self::CENTER:
                $location['x'] = floor(($srcImage['width'] - $water['width']) / 2);
                $location['y'] = floor(($srcImage['height'] - $water['height']) / 2);
                return $location;
            case self::RIGHT_MIDDLE:
                $location['x'] = $srcImage['width'] - $this->margin;
                $location['y'] = floor(($srcImage['height'] - $water['height']) / 2);
                return $location;
            case self::LEFT_BOTTOM:
                $location['x'] = $this->margin;
                $location['y'] = $srcImage['height'] - $this->margin;
                return $location;
            case self::MIDDLE_BOTTOM:
                $location['x'] = floor(($srcImage['width'] - $water['width']) / 2);
                $location['y'] = $srcImage['height'] - $this->margin;
                return $location;
            case self::RIGHT_BOTTOM:
                $location['x'] = $srcImage['width'] - $this->margin;
                $location['y'] = $srcImage['height'] - $this->margin;
                return $location;
        }
    }

    /**
     * 根据图像格式获取图形资源
     * @param type $srcImage
     * @return null
     */
    private function _getImageResourceFromType($srcImage) {
        $info = $this->_getImageInfo($srcImage);
        switch ($info['type']) {
            case 'gif':
                return imagecreatefromgif($srcImage);
            case 'jpeg':
                return imagecreatefromjpeg($srcImage);
            case 'png':
                return imagecreatefrompng($srcImage);
        }
        return null;
    }

    /**
     * 图片加水印
     * @param type $srcImage    [原始图片]
     * @param type $waterImage  [水印图片]
     * @param type $ext         [额外参数]
     */
    public function waterImage($srcImage, $waterImage, $ext = array()) {
        p($this->_getWaterPos($this->pos, $srcImage, $waterImage));
        die;
        $this->_checkEnv($srcImage, $waterImage);
        $this->setConfig($ext);
        $pos         = $this->_getWaterPos($this->pos);
        $this->image = $this->_getImageResourceFromType($srcImage);
        if (file_exists($waterImage)) {
            
        } else {
            $this->color = imagecolorallocate($this->image, $this->color['red'], $this->color['green'], $this->color['blue']);
            imagettftext($this->image, $this->size, 0, $pos['x'], $pos['y'], $this->color, $this->fontfile, $this->text);
        }
    }

}
