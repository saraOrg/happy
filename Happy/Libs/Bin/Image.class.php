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
    private $water_switch = true;               //图像水印开关
    private $image        = null;               //原始图像资源
    private $water        = null;               //水印图像资源
    private $thumb        = null;               //缩略图像资源
    private $waterImage   = 'logo.png';         //水印图片
    private $type         = array(//图片类型
        1 => 'gif',
        2 => 'jpeg',
        3 => 'png'
    );
    private $margin       = 10;                         //水印图片的margin值
    private $pos          = self::RIGHT_BOTTOM;         //水印图片位置
    private $text         = 'http://weibo.com/yangbai1988';
    private $fontfile     = 'arial.ttf';                    //水印文字字体
    private $size         = 14;                         //水印文字字体大小
    private $color        = '#000000'; //水印文字颜色
    private $opacity      = 80;     //水印透明度
    private $quality      = 75;     //jpeg图片压缩比
    private $thumb_switch = true;   //图像缩略图开关
    private $thumb_mode   = 5;      //缩略图模式
    private $thumb_type   = 'gif';  //缩略图类型
    private $thumb_size   = array(//缩略图尺寸
        'width'  => 80,
        'height' => 80
    );
    private $thumb_pre    = 'thumb_';  //缩略图名称前缀

    /**
     * 架构函数
     */

    public function __construct() {
        config('WATER_SWITCH') && $this->water_switch = config('WATER_SWITCH');
        config('THUMB_SWITCH') && $this->thumb_switch = config('THUMB_SWITCH');
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
        //p(mb_detect_encoding($this->text));die;
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
        if (!file_exists($image)) {
            return array('width' => '', 'height' => '', 'type' => '');
        }
        $info = getimagesize($image);
        return array('width' => $info[0], 'height' => $info[1], 'type' => $this->type[$info[2]]);
    }

    /**
     * 快速获取图像类型
     * @param type $image   [图像路径]
     * @return type
     */
    private function _getImageType($image) {
        return image_type_to_extension(exif_imagetype($image));
    }

    /**
     * 检测图像类型
     * @param type $image
     * @return boolean
     */
    private function _checkImageType($image = null) {
        if (!is_null($image)) {
            $info = $this->_getImageInfo($image);
            if (in_array($info['type'], $this->type)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 环境检测
     */
    private function _checkEnv($srcImage, $waterImage = '') {
        extension_loaded('gd') || error('GD库没有开启');
        file_exists($srcImage) || error('原始图像不存在');
        $this->_checkImageType($srcImage) || error('原始图像格式不正确');
        if ($waterImage !== '' && file_exists($waterImage)) {
            $this->_checkImageType($waterImage) || error('水印图像格式不正确');
        }
    }

    /**
     * 获取水印位置
     * @param type $pos
     * @return type
     */
    private function _getWaterPos($srcImage, $waterImage, $pos = self::RIGHT_BOTTOM) {
        $location = array();
        ($pos < 1 || $pos > 9) && $pos      = mt_rand(1, 9);
        switch ($pos) {
            case self::LEFT_TOP:
                $location['x'] = $location['y'] = $this->margin;
                return $location;
            case self::MIDDLE_TOP:
                $location['x'] = floor(($srcImage['width'] - $waterImage['width']) / 2);
                $location['y'] = $this->margin;
                return $location;
            case self::RIGHT_TOP:
                $location['x'] = $srcImage['width'] - $waterImage['width'] - $this->margin;
                $location['y'] = $this->margin;
                return $location;
            case self::LEFT_MIDDLE:
                $location['x'] = $this->margin;
                $location['y'] = floor(($srcImage['height'] - $waterImage['height']) / 2);
                return $location;
            case self::CENTER:
                $location['x'] = floor(($srcImage['width'] - $waterImage['width']) / 2);
                $location['y'] = floor(($srcImage['height'] - $waterImage['height']) / 2);
                return $location;
            case self::RIGHT_MIDDLE:
                $location['x'] = $srcImage['width'] - $waterImage['width'] - $this->margin;
                $location['y'] = floor(($srcImage['height'] - $waterImage['height']) / 2);
                return $location;
            case self::LEFT_BOTTOM:
                $location['x'] = $this->margin;
                $location['y'] = $srcImage['height'] - $waterImage['height'] - $this->margin;
                return $location;
            case self::MIDDLE_BOTTOM:
                $location['x'] = floor(($srcImage['width'] - $waterImage['width']) / 2);
                $location['y'] = $srcImage['height'] - $waterImage['height'] - $this->margin;
                return $location;
            case self::RIGHT_BOTTOM:
                $location['x'] = $srcImage['width'] - $waterImage['width'] - $this->margin;
                $location['y'] = $srcImage['height'] - $waterImage['height'] - $this->margin;
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
     * 获取等比缩放的尺寸
     * @param type $srcImage
     * @return type
     */
    private function _geometricScaling($srcImage) {
        $thumb_size            = $this->thumb_size;
        $thumb_size['swidth']  = $srcImage['width'];
        $thumb_size['sheight'] = $srcImage['height'];
        if ($srcImage['width'] / $this->thumb_size['width'] < $srcImage['height'] / $this->thumb_size['height']) {
            $thumb_size['width'] = $srcImage['width'] * $this->thumb_size['height'] / $srcImage['height'];
        } else {
            $thumb_size['height'] = $srcImage['height'] * $this->thumb_size['width'] / $srcImage['width'];
        }
        return $thumb_size;
    }

    /**
     * 获取缩略图的尺寸(默认是执行等比缩放)
     * @param type $srcImage
     * @return type
     */
    private function _getThumbSize($srcImage) {
        $size            = $this->thumb_size;
        $size['swidth']  = $srcImage['width'];
        $size['sheight'] = $srcImage['height'];
        list($sW, $sH) = array($srcImage['width'], $srcImage['height'], $srcImage['type']);
        if ($sW > $size['width'] || $sH > $size['height']) {
            switch ($this->thumb_mode) {
                case 1: //宽度不变，高度自适应缩放
                    $size['height']  = $size['width'] / $sW * $sH;
                    break;
                case 2: //高度不变，宽度自适应缩放
                    $size['width']   = $size['height'] / $sH * $sW;
                    break;
                case 3: //宽度不变，自适应高度裁剪
                    $size['sheight'] = min($size['height'] * $sW / $size['width'], $sH);
                    break;
                case 4: //高度不变，自适应宽度裁剪
                    $size['swidth']  = min($size['width'] * $sH / $size['height'], $sW);
                    break;
                case 5:
                default:
                    $size            = $this->_geometricScaling($srcImage);
                    break;
            }
        } else {
            $size['width']  = $sW;
            $size['height'] = $sH;
        }
        return $size;
    }

    /**
     * 针对gif图像的颜色透明做特殊处理
     */
    private function _gifColorTransparent() {
        $otsc = imagecolortransparent($this->image);
        if ($otsc >= 0 && $otsc <= imagecolorstotal($this->image)) {
            $tran  = imagecolorsforindex($this->image, $otsc);
            $color = imagecolorallocate($this->thumb, $tran["red"], $tran["green"], $tran["blue"]);
            imagefill($this->image, 0, 0, $color);
            imagecolortransparent($this->thumb, $color);
        }
    }

    /**
     * 图像缩放处理
     * @param type $srcImage    [原图像]
     * @param type $ext         [扩展配置]
     * @return boolean          
     */
    public function thumb($srcImage, $ext = array()) {
        if ($this->thumb_switch !== true) {
            return false;
        }
        $this->_checkEnv($srcImage);
        $this->setConfig($ext);
        $src              = $this->_getImageInfo($srcImage);
        $size             = $this->_getThumbSize($src);
        $function         = 'imagecreatefrom' . $src['type'];
        $this->thumb_type = $src['type'];
        $this->image      = $function($srcImage);
        if ($this->thumb_type === 'gif') {
            $this->thumb = imagecreate($size['width'], $size['height']);
            $this->_gifColorTransparent();
        } else {
            $this->thumb = imagecreatetruecolor($size['width'], $size['height']);
        }
        imagecopyresampled($this->thumb, $this->image, 0, 0, 0, 0, $size['width'], $size['height'], $size['swidth'], $size['sheight']);
        //imagecopyresized($this->thumb, $this->image, 0, 0, 0, 0, $size['width'], $size['height'], $size['swidth'], $size['sheight']);
        $info  = pathinfo($srcImage);
        $alias = $info['dirname'] . '/' . $this->thumb_pre . $info['basename'];
        $this->_generatedImage($this->thumb, $this->thumb_type, $srcImage, $alias);
        is_null($this->thumb) || imagedestroy($this->thumb);
        is_null($this->image) || imagedestroy($this->image);
    }

    /**
     * 图片加水印
     * @param type $srcImage    [原始图片]
     * @param type $waterImage  [水印图片]
     * @param type $ext         [额外参数]
     */
    public function waterMark($srcImage, $waterImage, $ext = array()) {
        if ($this->water_switch !== true) {
            return false;
        }
        $this->_checkEnv($srcImage, $waterImage);
        $this->setConfig($ext);
        $filename    = $srcImage;
        $this->image = $this->_getImageResourceFromType($srcImage);
        $src         = $this->_getImageInfo($srcImage);
        $alias       = isset($ext['alias']) ? $ext['alias'] : '';
        if (file_exists($waterImage)) {
            $water       = $this->_getImageInfo($waterImage);
            $this->water = $this->_getImageResourceFromType($waterImage);
            $pos         = $this->_getWaterPos($src, $water, $this->pos);
            imagecopymerge($this->image, $this->water, $pos['x'], $pos['y'], 0, 0, $water['width'], $water['height'], $this->opacity);
        } else {
            $arr             = imagettfbbox($this->size, 0, $this->fontfile, $this->text);
            $water           = array();
            $water['width']  = $arr[2] - $arr[0];
            $water['height'] = $arr[3] - $arr[7];
            $pos             = $this->_getWaterPos($src, $water, $this->pos);
            $this->color     = imagecolorallocate($this->image, $this->color['red'], $this->color['green'], $this->color['blue']);
            imagettftext($this->image, $this->size, 0, $pos['x'], $pos['y'] + $water['height'], $this->color, $this->fontfile, $this->text);
        }
        $this->_generatedImage($this->image, $src['type'], $filename, $alias);
        is_null($this->water) || imagedestroy($this->water);
        is_null($this->image) || imagedestroy($this->image);
    }

    /**
     * 输出图像
     * @param type $imageType   [图像类型]
     * @param type $filename    [图像保存路径]
     * @param type $alias       [图像保存别名]
     * @return boolean
     */
    private function _generatedImage($image, $imageType, $filename = '', $alias = '') {
        if (empty($imageType) || empty($image)) {
            return false;
        }
        if (!empty($alias)) {
            $filename = $alias;
        }
        if (empty($filename)) {
            return false;
        }
        switch ($imageType) {
            case 'gif':
                imagegif($image, $filename);
                break;
            case 'jpeg':
                imagejpeg($image, $filename, $this->quality);
                break;
            case 'png':
                imagepng($image, $filename);
                break;
        }
        return $filename;
    }

}
