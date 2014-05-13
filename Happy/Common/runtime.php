<?php

/**
 * =================================================
 * 框架运行时文件
 * ================================================
 * @category happy
 * @package Admin/
 * @subpackage Action
 * @author Happy <yangbai6644@163.com>
 * @dateTime 2014-5-2 22:59:52
 * ================================================
 */
/**
 * 项目目录创建和初始化
 */
//加载核心文件
$files = require HAPPY_PATH . 'Common/files.php';
foreach ($files as $file) {
    require $file;
}
check_runtime();    //检查运行环境
config(require ETC_PATH . 'config.php');                //加载框架底层配置
complie_file($files);                                   //生成编译文件

/**
 * 检查缓存目录(Runtime) 如果不存在则自动创建
 * @return boolean
 */
function check_runtime() {
    if (!is_dir(RUNTIME_PATH)) {
        mkdir(RUNTIME_PATH, 0777);
    } else if (!is_writable(RUNTIME_PATH)) {
        header('Content-Type:text/html;charset=utf-8');
        exit('目录 [ ' . RUNTIME_PATH . ' ] 不可写！');
    }
    is_dir(CACHE_PATH) || mkdir(CACHE_PATH, 0777);  //缓存目录
    is_dir(LOG_PATH) || mkdir(LOG_PATH, 0777);      //日志目录
    is_dir(DATA_PATH) || mkdir(DATA_PATH, 0777);    //数据目录
    is_dir(TEMP_PATH) || mkdir(TEMP_PATH, 0777);    //临时目录
    return true;
}

/**
 * 生成编译文件
 */
function complie_file($files) {
    $data = '<?php config(require ETC_PATH . "config.php");';
    foreach ($files as $file) {
        $content = file_get_contents($file);
        //过滤PHP语法的开始和结合苏标签
        $content = str_replace(array('<?php', '?>'), array('', ''), $content);
        //过滤多行注释，单行注释，
        $pattern = array('/\/\*.*?\*\//is', '/\/\/.*?[\r\n]/is');
        $content = preg_replace($pattern, '', $content);
        $data .= $content;
    }
    $data = $data . ' ?>';
    file_put_contents(TEMP_PATH . '~runtime.php', $data);
}
