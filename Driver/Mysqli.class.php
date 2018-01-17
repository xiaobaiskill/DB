<?php
/**
 * 基础mysqli类
 */
namespace Driver;
use Base\DbSql;
class Mysqli extends DbSql 
{
	public $db;
	public $table_name = '';
	public $sql;
	public $fetch_sql  = false;
	public $resulttype = 'array'; //表示默认返回数组的数据
	public $comparison = [
		'eq'  => '=',
		'neq' => '!=',
		'gt'  => '>',
		'egt' => '>=',
		'lt'  => '<',
		'elt' => '<='
	];
	public function __construct($config)
	{
		$this->connect($config);
		unset($config);
	}
	/**
	 * 连接数据库
	 * @param  [type] $config [数据库配置]
	 * @return [type]         [description]
	 */
	public function connect($config)
	{
		$this->db = new \mysqli($config['host'], $config['user'], $config['pwd'], $config['name'], $config['port']);
		$this->selectDb($config['charset']);

		if ($this->db->connect_error) {
			throw new \Exception("连接失败：mysqli", 1);
		}
	}

	/**
	 * 选择数据库
	 * @param  [type] $table [数据库]
	 * @return [type]        [description]
	 */
	public function selectDb($table)
	{
		$this->db->select_db($table);
		return $this;
	}

	/**
	 * 设置编码
	 * @param [type] $charset [编码]
	 */
	public function setCharset($charset)
	{
		$this->db->set_charset($charset);
		return $this;
	}

	public function table($table)
	{
		$this->table_name = $table;
		return $this;
	}

	/**
	 * value 处理
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function perseValue($value = null)
	{
		$v = '\'\'';
		if (is_numeric($value)) {
			$v = $value;
		} elseif (isset($value)) {
			$v = '\'' . $this->db->real_escape_string($value) . '\'';
		}
		return $v;
	}

	/**
	 * 二维数组的一一对应字段
	 * @param  [type] $fields [字段数组]
	 * @param  [type] $data   [随便按字段排序的数组]
	 * @return [type]         [整理好的与字段一一对应的数组]
	 */
	public function dataMappingField($fields, $data)
	{
		$new_data = [];
		foreach ($data as $k => $v) {
			foreach ($fields as $fk => $fv) {
				$new_data[$k][$fv] = !empty($v[$fv]) ? $v[$fv] : '';
			}
		}
		return $new_data;
	}

	/**
	 * 返回 键值数组 数据
	 * @return [type] [description]
	 */
	public function as_array()
	{
		$this->resulttype = 'array';
		return $this;
	}

	/**
	 * 返回 对象 数据
	 * @return [type] [description]
	 */
	public function as_object()
	{
		$this->resulttype = 'object';
		return $this;
	}
	/**
	 * 释放结果集
	 * @return [type] [description]
	 */
	public function free()
	{
		$this->affected_rows = 0;
		$this->insert_id     = 0;
	}

	/**
	 * 开启 获取sql语句;
	 * @return [type] [description]
	 */
	public function fetchSql()
	{
		$this->fetch_sql = true;
		return $this;
	}

	/**
	 * 获取sql语句
	 * @return [type] [description]
	 */
	public function getSql()
	{
		$this->as_array();
		$this->fetch_sql = false;
		return $this->sql;
	}

	/**
	 * 获取最后执行的 sql 语句
	 * @return [type] [description]
	 */
	public function lastSql()
	{
		return $this->sql;
	}

	/**
	 * 开启事务
	 * @return [type] [description]
	 */
	public function startTrans()
	{
		$this->db->autocommit(false);
	}

	/**
	 * 提交事务  只有提交才会关闭事务
	 * @return [type] [description]
	 */
	public function commit()
	{
		$this->db->commit();
		$this->db->autocommit(true);
	}

	/**
	 * 回滚事务  不会关闭书屋
	 * @return [type] [description]
	 */
	public function rollback()
	{
		$this->db->rollback();
	}

	/**
	 * mysqli 查操作
	 * @param  [type] $sql [description]
	 * @return [type]      [description]
	 */
	public function query($sql)
	{
		if ('' == $sql) {return false;}
		$this->free();
		$this->sql   = $sql;
		$this->query = $this->db->query($sql);

		if ('array' == $this->resulttype) {
			while ($info = $this->query->fetch_array(MYSQLI_ASSOC)) {
				$data[] = $info;
			}

		} else {
			while ($info = $this->query->fetch_object()) {
				$data[] = $info;
			}
		}
		$this->num_rows = $this->db->affected_rows;
		$this->query->close();
		$this->as_array();
		return $data;
	}
	/**
	 * mysqli 增删改操作
	 * @param  [type] $sql [description]
	 * @return [type]      [description]
	 */
	public function execute($sql)
	{
		if ('' == $sql) {return false;}
		$this->free();
		$this->sql       = $sql;
		$this->query     = $this->db->query($sql);
		$this->num_rows  = $this->db->affected_rows;
		$this->insert_id = $this->db->insert_id;
		return $this->num_rows;
	}

	/**
	 * 添加单条数据
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function insert($data = [])
	{
		$this->free();
		$data = array_merge((array) $this->options['data'], (array) $data);
		if (empty($data)) {return false;}
		$fields = $values = '';
		foreach ($data as $k => $v) {
			$fields[] = $k;
			$values[] = $this->perseValue($v);
		}
		$this->sql = 'INSERT INTO ' . $this->table_name . '(' . implode(',', $fields) . ') values(' . implode(',', $values) . ')';
		unset($this->options);
		if ($this->fetch_sql) {return $this->getSql();}
		$result = $this->db->query($this->sql);
		$this->num_rows  = $this->db->affected_rows;
		$this->insert_id = $this->db->insert_id;
		return $result;
	}

	/**
	 * 添加多条数据
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function insertAll($data = [])
	{
		$this->free();
		if (is_array($data)) {
			$data = array_merge((array) $this->options['data'], (array) $data);
			if (empty($data)) {return false;}
			$fields      = array_keys($data[0]);
			$datas       = $this->dataMappingField($fields, $data);
			$datas_count = count($datas);
			$values      = '';
			$i           = 0;

			foreach ($datas as $k => $data) {
				$values .= '(';
				foreach ($data as $v) {
					$value[] = $this->perseValue($v);
				}
				$values .= implode(',', $value) . ')';
				unset($value);
				$i++;
				if ($i !== $datas_count) {
					$values .= ',';
				}
			}

			$this->sql = 'INSERT INTO ' . $this->table_name . '(' . implode(',', $fields) . ') values' . $values;
			unset($this->options);
			if ($this->fetch_sql) {return $this->getSql();}
			$result = $this->db->query($this->sql);
			$this->num_rows  = $this->db->affected_rows;
			$this->insert_id = $this->db->insert_id;
			return $result;
		} else {
			throw new \Exception("insertAll数据有误:mysqli", 1);
		}
	}

	public function save($field, $data = null)
	{
		$this->free();
		$update_str = '';
		if (is_array($field) || is_object($field)) {
			is_object($field) && $field = get_object_vars($field);
			$field                      = array_merge((array) $field, (array) $this->options['data']);
			if (empty($field)) {return false;}
			$count = count($field);
			$i     = 0;
			foreach ($field as $key => $value) {
				$update_str .= $key . ' = ' . $this->perseValue($value);
				$i++;
				if ($i !== $count) {
					$update_str .= ',';
				}
			}
		} elseif (is_string($field)) {
			$update_str = $field . '=' . $this->perseValue($data);
		} else {
			throw new \Exception("save数据有误:mysqli", 1);
		}
		$this->sql = 'UPDATE ' . $this->table_name . ' SET ' . $update_str . $this->perseWhere();
		unset($this->options);
		if ($this->fetch_sql) {return $this->getSql();}
		$this->query    = $this->db->query($this->sql);
		$this->num_rows = $this->db->affected_rows;
		return $this->num_rows;
	}

	public function delete()
	{
		$this->free();
		$this->sql = 'DELETE FROM ' . $this->table_name . ' ' . $this->perseWhere();
		unset($this->options);
		if ($this->fetch_sql) {return $this->getSql();}
		$this->query    = $this->db->query($this->sql);
		$this->num_rows = $this->db->affected_rows;
		return $this->num_rows;
	}

	public function find()
	{
		$this->free();
		$this->sql = preg_replace("/[\s]+/is", " ", 'SELECT ' . $this->perseField() . ' FROM ' . $this->table_name . ' ' . $this->perseJoin() . ' ' . $this->perseWhere() . ' ' . $this->perseGroup() . ' ' . $this->perseHaving() . ' ' . $this->perseOrder() . ' ' . $this->perseLimit());
		unset($this->options);
		if ($this->fetch_sql) {return $this->getSql();}
		$this->query = $this->db->query($this->sql);
		if ('array' == $this->resulttype) {
			$data = $this->query->fetch_array(MYSQLI_ASSOC);
		} else {
			$data = $this->query->fetch_object();
		}
		$this->query->close();
		$this->as_array();
		return $data;
	}

	public function findAll()
	{
		$this->free();
		$this->sql = preg_replace("/[\s]+/is", " ", 'SELECT ' . $this->perseField() . ' FROM ' . $this->table_name . ' ' . $this->perseJoin() . ' ' . $this->perseWhere() . ' ' . $this->perseGroup() . ' ' . $this->perseHaving() . ' ' . $this->perseOrder() . ' ' . $this->perseLimit());
		unset($this->options);
		if ($this->fetch_sql) {return $this->getSql();}
		$this->query = $this->db->query($this->sql);
		if ('array' == $this->resulttype) {
			while ($info = $this->query->fetch_array(MYSQLI_ASSOC)) {
				$data[] = $info;
			}
		} else {
			while ($info = $this->query->fetch_object()) {
				$data[] = $info;
			}
		}
		$this->query->close();
		$this->as_array();
		return $data;
	}

	public function count()
	{
		$this->free();
		$this->sql = preg_replace("/[\s]+/is", " ", 'SELECT ' . $this->perseField() . ' FROM ' . $this->table_name . ' ' . $this->perseJoin() . ' ' . $this->perseWhere() . ' ' . $this->perseGroup() . ' ' . $this->perseHaving() . ' ' . $this->perseOrder() . ' ' . $this->perseLimit());
		unset($this->options);
		if ($this->fetch_sql) {return $this->getSql();}
		return  $this->db->query($this->sql)->num_rows;
	}

	public function __destruct()
	{
		$this->db->close();
	}

}
