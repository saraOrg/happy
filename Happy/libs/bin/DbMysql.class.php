<?php

/**
 * ================================================
 * mysql 数据库处理类
 * ================================================
 * @category SoFreight
 * @package SoFreight/Web
 * @subpackage Action
 * @access Public
 * @author YangBai <yangbai@echounion.com>
 * @dateTime 2014-4-28 11:21:14
 * ================================================
 */

namespace Common\Util;

class DbMysql {

    const DB_HOST = 'localhost';
    const DB_USER = 'root';
    const DB_PWD  = '358975';
    const DB_NAME = 'happy';

//数据库连接句柄
    private static $link  = null;
//模型句柄
    private static $model = null;
//连接参数
    private $params       = array();
//配置选项
    private $opt          = array();
//数据集合
    private $data         = array();

    /**
     * 构造函数 初始化资源连接
     * @param type $model
     */
    private function __construct($model) {
        self::$link = mysql_connect(self::DB_HOST, self::DB_USER, self::DB_PWD);
        if (self::$link === false) {
            exit(mysql_error());
        }
        mysql_select_db(self::DB_NAME, self::$link);
        mysql_set_charset('utf8');
        $this->opt['model'] = $model;
    }

    /**
     * 初始化类的数据
     */
    private function _init() {
        $this->opt['where'] = $this->opt['limit'] = $this->opt['order'] = $this->opt['field'] = '';
    }

    /**
     * 实例化模型
     * @param type $model
     * @return type
     */
    public static function getModel($model) {
        if (is_null(self::$link)) {
            self::$model = new DbMysql($model);
        }
        return self::$model;
    }

    /**
     * 将mysql结果集转换成数组
     * @return type
     */
    private function _getRows() {
        $data = array();
        while ($row  = mysql_fetch_assoc($this->opt['res'])) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * 解析运算符
     * @param type $oper
     * @return string
     */
    private function _parseOper($oper = '') {
        $dbOper = '=';
        switch (strtolower($oper)) {
            case 'eq':
                $dbOper = ' = ';
                break;
            case 'lt':
                $dbOper = ' < ';
                break;
            case 'gt':
                $dbOper = ' > ';
                break;
            case 'lte':
                $dbOper = ' <= ';
                break;
            case 'gte':
                $dbOper = ' >= ';
                break;
            case 'neq':
                $dbOper = ' <> ';
                break;
        }
        return $dbOper;
    }

    /**
     * 解析数据
     * @param type $data
     * @return type解析
     */
    private function _parseData($data = array()) {
        $keys   = '(';
        $values = '(';
        foreach ($data as $key => $value) {
            $keys .= $key . ',';
            $values .= "'" . $value . "'" . ',';
        }
        return rtrim($keys, ',') . ') ' . 'VALUES ' . rtrim($values, ',') . ')' . ';';
    }

    /**
     * 准备sql语句[只针对查询]
     */
    private function _prepareSql() {
        $sql = 'SELECT ' . $this->opt['fields'] . ' FROM ' . $this->opt['model'] . ' ' . $this->opt['group'] . 
                ' ' . $this->opt['where'] . ' ' . $this->opt['order'] . ' ' . $this->opt['limit'];
        return $this->opt['sql'] = $sql;
    }

    /**
     * 获取表的字段信息
     * @return type
     */
    public function getColumns() {
        $fields           = array();
        $this->opt['sql'] = "show full columns from " . $this->opt['model'];
        $this->opt['res'] = mysql_query($this->opt['sql'], self::$link);
        if (false === $this->opt['res']) {
            $this->opt['error'] = mysql_error();
            return false;
        }
        foreach ($this->_getRows() as $value) {
            if (is_array($value)) {
                $fields[$value['Field']] = array(
                    'name' => $value['Field'],
                    'type' => $value['Type']
                );
            }
        }
        return $fields;
    }

    /**
     * 获取数据库的所有表名称
     * @return type
     */
    public function getTables() {
        $tables           = array();
        $this->opt['sql'] = 'show tables';
        $this->opt['res'] = \mysql_query($this->opt['sql'], self::$link);
        if (false === $this->opt['res']) {
            $this->opt['error'] = mysql_error();
            return false;
        }
        foreach ($this->_getRows() as $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    $tables[] = $v;
                }
            }
        }
        return $tables;
    }

    /**
     * 组装sql语句的数据
     * @param type $data
     * @return \DbMysql|string
     */
    public function data($data = array()) {
        if (!is_array($data)) {
            return '';
        }
        $this->opt['data'] = $data;
        return $this;
    }

    /**
     * 组装sql语句的字段
     * @param type $fields
     * @param type $ext
     * @return \DbMysql
     */
    public function Fields($fields = '', $ext = false) {
        if (is_string($fields)) {
            $this->opt['fields'] = explode(',', $fields);
        } else {
            $this->opt['fields'] = $fields;
        }
        if (true === $ext) {
            $this->opt['fields'] = array_diff($this->getFields(), $this->opt['fields']);
        }
        $this->opt['fields'] = join(',', $this->opt['fields']) ? join(',', $this->opt['fields']) : '*';
        return $this;
    }

    /**
     * 组装sql语句的where条件
     * @param type $where
     * @return \DbMysql
     */
    public function where($where = '') {
        if ($where === '') {
            $this->opt['where'] = ' ';
            return $this;
        }
        if (is_array($where)) {
            foreach ($where as $key => $value) {
                if (is_array($value)) {
                    $this->opt['where'] .= $key . ' ' . $this->_parseOper($value[0]) . ' ' . $value[1];
                } else {
                    $this->opt['where'] .= $key . ' = ' . $value . ' ';
                }
            }
        } else {
            $this->opt['where'] .= $where;
        }
        return $this;
    }
    
    /**
     * 组装limit条件
     */
    public function limit($limit = '') {
        $this->opt['limit'] = $limit;
        return $this;
    }
    /**
     * 组装order条件
     */
    public function order($order = '') {
        $this->opt['order'] = $order . ' ' . $type;
    }
    
    /**
     * 组装group条件
     */
    public function group($group = '') {
        $this->opt['group'] = $group;
    }

    /**
     * 获取所有数据
     * @return type
     */
    public function select() {
        $this->opt['res'] = mysql_query($this->_prepareSql(), self::$link);
        if ($this->opt['res']) {
            return $this->_getRows();
        } else {
            $this->opt['error'] = mysql_error();
            return false;
        }
    }

    /**
     * 获取单挑数据
     * @return type
     */
    public function find($id = '') {
        if ('' !== $id) {
            $this->opt['where'] = ' WHERE id = ' . intval($id);
        }
        $this->opt['limit'] = ' LIMIT 1';
        $dataOne          = array();
        $this->opt['res'] = mysql_query($$this->_prepareSql(), self::$link);
        if (false === $this->opt['res']) {
            $this->opt['error'] = mysql_error();
            return false;
        }
        foreach ($this->_getRows() as $value) {
            foreach ($value as $k => $v) {
                $dataOne[$k] = $v;
            }
        }
        return $dataOne;
    }

    /**
     * 根据字段名称获取字段值[目前只只支持单个字段]
     * @param type $field
     * @param type $muti
     * @return type
     */
    public function getField($field, $muti = false) {
        $data  = array();
        $this->opt['limit'] = ' LIMIT 1';
        if (true === $muti) {
            $this->opt['limit'] = '';
        }
        $this->opt['res'] = mysql_query($this->_prepareSql(), self::$link);
        if (false === $this->opt['res']) {
            $this->opt['error'] = mysql_error();
            return false;
        }
        foreach ($this->_getRows() as $value) {
            foreach ($value as $v) {
                $data[] = $v;
            }
        }
        return $data;
    }
    
    /**
     * 统计总的记录数
     */
    public function count() {
        $this->opt['sql'] = 'SELECT COUNT(*) FROM ' . $this->opt['model']; 
        $this->opt['res'] = mysql_query($this->opt['sql'], self::$link);
        if (false === $this->opt['res']) {
            $this->opt['error'] = mysql_error();
            return false;
        }
        foreach ($this->_getRows($this->opt['res']) as $value) {
            foreach ($value as $v) {
                return $v;
            }
        }
    }

    /**
     * 添加一条记录
     * @param type $data
     * @return boolean
     */
    public function add($data = array()) {
        if (empty($data)) {
            $values = $this->_parseData($this->opt['data']);
        } else {
            $values = $this->_parseData($data);
        }
        $sql                         = 'INSERT INTO ' . $this->opt['model'] . $values;
        mysql_query($sql, self::$link);
        $this->opt['last_insert_id'] = mysql_insert_id(self::$link);
        $this->opt['sql']            = $sql;
        if (!$this->opt['last_insert_id']) {
            $this->opt['error'] = mysql_error();
            return false;
        }
        return true;
    }

    /**
     * 添加多条记录
     * @param type $datas
     * @return boolean
     */
    public function addAll($datas = array()) {
        $sql    = 'INSERT INTO ' . $this->opt['model'] . ' ';
        $keys   = '(';
        $values = '';
        if (empty($datas)) {
            $this->opt['error'] = '数据为空';
            return false;
        }
        foreach ($datas[0] as $key => $value) {
            $keys .= $key . ',';
        }
        $keys = rtrim($keys, ',') . ') ';
        foreach ($datas as $data) {
            $values .= '(';
            $value = '';
            foreach ($data as $v) {
                $value .= $v . ',';
            }
            $values .= rtrim($value, ',') . '),';
        }
        $sql .= $keys . 'VALUES ' . rtrim($values, ',');
        $this->opt['sql'] = $sql;
        if (false === mysql_query($sql, self::$link)) {
            $this->opt['error'] = mysql_error();
            return false;
        } else {
            $this->opt['last_insert_id'] = mysql_insert_id();
            $this->opt['affected_rows']  = mysql_affected_rows();
            return true;
        }
    }

    /**
     * 更新记录
     * @param type $data
     * @return booleang
     */
    public function save($data = array()) {
        $where = isset($this->opt['where']) ? $this->opt['where'] : '';
        if ($where === '') {
            $this->opt['error'] = '更新语句条件为空';
            return false;
        }
        if (empty($data)) {
            $data = $this->opt['data'];
        }
        $sql = 'UPDATE ' . $this->opt['model'] . ' SET ';
        foreach ($data as $key => $value) {
            $sql .= $key . " = '" . $value . "',";
        }
        $sql              = rtrim($sql, ',') . $where;
        $this->opt['sql'] = $sql;
        if (false === mysql_query($sql)) {
            $this->opt['error'] = mysql_error();
            return false;
        } else {
            $this->opt['affected_rows'] = mysql_affected_rows();
            return true;
        }
    }

    /**
     * 根据字段名称和值重新设置
     * @param type $key
     * @param type $value
     * @return boolean
     */
    public function setField($key, $value) {
        $where            = isset($this->opt['where']) ? $this->opt['where'] : '';
        $sql              = 'UPDATE ' . $this->opt['model'] . ' SET ' . $key . " = '" . $value . "' " . $where;
        $this->opt['sql'] = $sql;
        if (false === mysql_query($sql, self::$link)) {
            $this->opt['error'] = mysql_error();
            return false;
        } else {
            $this->opt['affected_rows'] = mysql_affected_rows();
            return true;
        }
    }

    /**
     * 字段+n
     * @param type $field
     * @param type $step
     * @return boolean
     */
    public function setInc($field, $step = 1) {
        $columns = $this->getColumns();
        if (strpos($columns[$field]['type'], 'int') === false && strpos($columns[$field]['type'], 'float') === false) {
            $this->opt['error'] = '字段非整型或浮点型';
            return false;
        }
        $where            = isset($this->opt['where']) ? $this->opt['where'] : '';
        $sql              = 'UPDATE ' . $this->opt['model'] . ' SET ' . $field . ' = ' . $field . ' + ' . $step . $where;
        $this->opt['sql'] = $sql;
        if (false === mysql_query($sql, self::$link)) {
            $this->opt['error'] = mysql_error();
            return false;
        } else {
            $this->opt['affected_rows'] = mysql_affected_rows();
            return true;
        }
    }

    /**
     * 字段-n
     * @param type $field
     * @param type $step
     * @return boolean
     */
    public function setDec($field, $step = 1) {
        $columns = $this->getColumns();
        if (strpos($columns[$field]['type'], 'int') === false && strpos($columns[$field]['type'], 'float') === false) {
            $this->opt['error'] = '字段非整型或浮点型';
            return false;
        }
        $where = isset($this->opt['where']) ? $this->opt['where'] : '';
        $data  = $this->where($where)->getField($field, true);
        if (in_array(0, $data) && strpos($columns[$field]['type'], 'unsigned') !== false) {
            $this->opt['error'] = '非负值不能小于0';
            return false;
        }
        $sql              = 'UPDATE ' . $this->opt['model'] . ' SET ' . $field . ' = ' . $field . ' - ' . $step . $where;
        $this->opt['sql'] = $sql;
        if (false === mysql_query($sql, self::$link)) {
            $this->opt['error'] = mysql_error();
            return false;
        } else {
            $this->opt['affected_rows'] = mysql_affected_rows();
            return true;
        }
    }

    /**
     * 删除记录
     * @param type $id
     * @return boolean
     */
    public function delete($id = '') {
        if ('' === $id && $this->opt['where'] === '') {
            $this->opt['error'] = '没有指定删除条件';
            return false;
        }
        if ($id === '') {
            $where = $this->opt['where'];
        } else {
            $where = ' WHERE id = ' . intval($id);
        }
        $this->opt['sql'] = 'DELETE FROM ' . $this->opt['model'] . $where;
        if (false === mysql_query($this->opt['sql'])) {
            $this->opt['error'] = mysql_error();
            return false;
        } else {
            $this->opt['affected_rows'] = mysql_affected_rows();
            return true;
        }
    }

    /**
     * 执行原生的sql语句
     */
    public function query($sql = '') {
        if ('' === $sql) {
            $this->opt['error'] = 'sql语句为空';
            return false;
        }
        $this->opt['sql'] = $sql;
        if ($res              = mysql_query($this->opt['sql'])) {
            $this->opt['error'] = mysql_error();
            return false;
        } else {
            return $res;
        }
    }

    /**
     * 返回执行的sql语句
     * @return type
     */
    public function sql() {
        return isset($this->opt['sql']) ? $this->opt['sql'] : '';
    }

    /**
     * 返回执行中的错误信息
     * @return type
     */
    public function error() {
        return isset($this->opt['error']) ? $this->opt['error'] : '';
    }

    /**
     * 返回添加记录后的id
     * @return type
     */
    public function getLastInsertId() {
        return isset($this->opt['last_insert_id']) ? $this->opt['last_insert_id'] : '';
    }

    /**
     * 返回sql语句执行后的影响记录数
     * @return type
     */
    public function getAffectedRows() {
        return isset($this->opt['affected_rows']) ? $this->opt['affected_rows'] : '';
    }

    /**
     * 管局数据库资源连接
     */
    public function close() {
        if (self::$link) {
            mysql_close(self::$link);
        }
    }

    /**
     * 析构函数释放资源
     */
    public function __destruct() {
        $this->close();
    }

}

//$model = DbMysql::getModel('test');


//$data = array('content' => 'happy', 'click' => 45);
//p($model->getColumns());
//var_dump($model->setDec('click', 3));
//p($model->error());

function p($arr) {
    echo "<pre>", print_r($arr, true), "</pre>";
}
