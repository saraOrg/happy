<?php

/**
 * =================================================
 * 分页处理类
 * ================================================
 * @category happy
 * @package Admin/
 * @subpackage Action
 * @author Happy <yangbai6644@163.com>
 * @dateTime 2014-5-1 22:08:54
 * ================================================
 */

namespace Common\Util;

class Page {

    /**
     * 定义基本属性
     */
    private $total_rows;        //总记录数
    private $total_page;        //总页数
    private $page_rows;         //每页显示记录数
    private $page_count;        //每页显示行数
    private $page_now;          //当前页
    private $start_id;          //当前页起始页ID
    private $end_id;            //当前页结束ID
    private $url;               //当前也url地址
    private $desc   = array();    //描述信息
    private $config = array(//基础配置
        'tital' => '统计信息',
        'first' => '首页',
        'pres'  => '上几页',
        'pre'   => '上一页',
        'list'  => '列表页',
        'next'  => '下一页',
        'nexts' => '下几页',
        'end'   => '末页'
    );

    public function __construct($total_rows, $page_rows = 10, $page_count = 8, $desc = array()) {
        $this->total_rows = $total_rows;
        $this->page_rows  = $page_rows;
        $this->total_page = ceil($this->total_rows / $this->page_rows);
        $this->page_count = max(2, min($page_count, $this->total_page));    //小于总页数，不能小于2
        $this->page_now   = $this->_getPageNow();
        $this->start_id   = ($this->page_now - 1) * $this->page_rows + 1;
        $this->end_id     = min($this->total_rows, $this->page_now * $this->page_rows);
        $this->url        = $this->_getCurrentUrl();
        $this->desc       = $this->_getDesc($desc);
    }

    /**
     * 获取当前页
     */
    private function _getPageNow() {
        $pageNow = '';
        $p       = filter_input(INPUT_GET, 'p');
        if ($p && intval($p) > 0 && intval($p) <= $this->total_page) {
            $pageNow = $p;
        } else {
            $pageNow = 1;
            intval($p) > $this->total_page && $pageNow = $this->total_page;
        }
        return intval($pageNow);
    }

    /**
     * 获取当前页URL
     */
    public function _getCurrentUrl() {
        $request_uri  = filter_input(INPUT_SERVER, 'REQUEST_URI');
        $php_self     = filter_input(INPUT_SERVER, 'PHP_SELF');
        $query_string = filter_input(INPUT_SERVER, 'QUERY_STRING');
        $url          = $request_uri ? $request_uri : $php_self . $query_string;
        $request_url  = parse_url($url);
        $query_str    = '';
        if (isset($request_url['query'])) {
            parse_str($request_url['query'], $query_str);
            unset($query_str['p']);
            $url = $request_url['path'] . '?' . (http_build_query($query_str) ? http_build_query($query_str) . '&' : '') . 'p=';
        } else {
            $url = strpos($url, '?') ? $url . 'p=' . $this->page_now : $url . '?p=';
        }
        return $url;
    }

    /**
     * 获取分页描述
     */
    private function _getDesc($desc = array()) {
        $default = array(
            'pre'   => '上一页',
            'next'  => '下一页',
            'total' => '共',
            'unit'  => '条记录',
            'first' => '首页',
            'end'   => '末页'
        );
        if (empty($desc) || !is_array($desc)) {
            return $default;
        }
        foreach ($desc as $key => $value) {
            if ($value === '') {
                unset($desc[$key]);
            }
        }
        $desc = array_merge($default, $desc);
        return $desc;
    }

    /**
     * 产生LIMIT语句
     */
    public function limit() {
        $limit = ' LIMIT ' . ($this->page_now - 1) * $this->page_rows . ', ' . $this->page_rows;
        return $limit;
    }

    /**
     * 上一页
     */
    private function pre() {
        if ($this->page_now > 1) {
            return '<a href="' . $this->url . ($this->page_now - 1) . '">' . $this->desc['pre'] . '</a>&nbsp;';
        } else {
            return '';
        }
    }

    /**
     * 上几页
     */
    private function pres() {
        $step = $this->page_now - $this->page_count;
        return ($this->page_now > $this->page_count) ? '<a href="' . $this->url . $step . '">' . '上' . $this->page_count . '页' . '</a>' : '';
    }

    /**
     * 下一页
     */
    private function next() {
        if ($this->page_now < $this->total_page) {
            return '<a href="' . $this->url . ($this->page_now + 1) . '">' . $this->desc['next'] . '</a>&nbsp;';
        } else {
            return '';
        }
    }

    /**
     * 下几页
     */
    private function nexts() {
        $step = $this->page_now + $this->page_count;
        return ($this->page_now < $this->total_page - $this->page_count) ? '<a href="' . $this->url . $step . '">' . '下' . $this->page_count . '页' . '</a>' : '';
    }

    /**
     * 首页
     */
    private function first() {
        if ($this->page_now > 1) {
            return '<a href="' . $this->url . '1">' . $this->desc['first'] . '</a>&nbsp;';
        } else {
            return '';
        }
    }

    /**
     * 末页
     */
    private function end() {
        if ($this->page_now < $this->total_page) {
            return '<a href="' . $this->url . $this->total_page . '">' . $this->desc['end'] . '</a>&nbsp;';
        } else {
            return '';
        }
    }

    /**
     * 当前页的记录信息
     */
    private function currentPageInfo() {
        return '<span>' . $this->start_id . '-' . $this->end_id . '</span>&nbsp;';
    }

    /**
     * 当前页码
     */
    private function CurrentPage() {
        return '<span>第 ' . $this->page_now . ' 页</span>';
    }

    /**
     * 统计信息
     */
    private function totalInfo() {
        return'<span>总共有 ' . $this->total_page . ' 页，' . $this->total_rows . ' ' . $this->desc['unit'] . '</span>&nbsp;';
    }

    /**
     * 计算分页列表页数组
     */
    private function _getPageList() {
        $page  = array();
        //获取起始页ID  [当前页和页码数的一半做比较]
        $start = \max(1, \min($this->page_now - \ceil($this->page_count / 2), $this->total_page - $this->page_count + 1));
        $end   = $start + $this->page_count;
        $i     = '';
        for ($i = $start; $i < $end; $i++) {
            if ($i == $this->page_now) {
                $page[$i]['str'] = $i;
                $page[$i]['url'] = '';
                continue;
            }
            $page[$i]['str'] = $i;
            $page[$i]['url'] = $this->url . $i;
        }
        return $page;
    }

    /**
     * 输出分页字符串
     */
    public function show() {
        $page = $this->_getPageList();
        $str  = '<div class="page" id="page">';
        $str .= $this->totalInfo();
        $str .= $this->first();
        $str .= $this->pres();
        $str .= $this->pre();
        foreach ($page as $value) {
            if ('' === $value['url']) {
                $str .= '<a class="current">' . $value['str'] . '</a>&nbsp;';
                continue;
            }
            $str .= '<a href="' . $value['url'] . '">' . $value['str'] . '</a>&nbsp;';
        }
        $str .= $this->next();
        $str .= $this->nexts();
        $str .= $this->end();
        $str .= '</div>';
        $str .= $this->_style();
        return $str;
    }

    /**
     * select下拉框页码
     */
    public function _select() {
        
    }

    /**
     * 指定跳转到页
     */
    public function goPage() {
        
    }

    /**
     * 自定义分页输出样式
     */
    public function setConfig($config = array()) {
        
    }

    /**
     * 分页基础样式
     */
    private function _style() {
        $str = '<style tyle="text/css">';
        $str.= '.page a, .page span {float: left;height: 20px;padding: 3px 10px;border: 1px solid #ccc;
            margin-left: 2px;font-family: arial;line-height: 20px;font-size: 14px;overflow: hidden;
            -moz-border-radius: 5px;-webkit-border-radius: 5px;text-decoration: none;}';
        $str .= '.page span {font-size:12px; color:#000;border-radius: 4px;}';
        $str .= '.page .current,.page .current:hover {color: #f60;font-weight: 700;border:0}';
        $str .= '.page .current:hover {background: #fff}';
        $str .= '.page a:hover {background: #005aa0;color: #fff;text-decoration: none;}';
        $str .= '.page a{color: #005aa0;}';
        $str .= '.page a {border-redius: 3px;}';
        $str .= '.page a, .page span {height: 19px;}';
        $str .= '.page a:hover {color: #E4393C}';
        $str .= '</style>';
        return $str;
    }

}
