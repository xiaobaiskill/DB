<?php
/**
 * 基础mysqli类
 */
namespace Driver;

class Mysqli
{
	public $db;
	public $sql;
	public $options;
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
		$this->options['table'] = $table;
		return $this;
	}
	/**
	 * 处理数据
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function data($data)
	{
		$this->options['data'] = $data;
		return $this;
	}

	/**
	 * value 处理
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function perseValue($value)
	{
		$v = '';
		if (is_numeric($value)) {
			$v = $value;
		} elseif (is_null($value)) {
			$v = 'null';
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
		!empty($this->options['data']) && $data[] = $this->options['data'];
		$new_data                                 = [];
		foreach ($data as $k => $v) {
			foreach ($fields as $fk => $fv) {
				$new_data[$k][$fv] = $v[$fv];
			}
		}
		return $new_data;
	}

	/**
	 * in 查询
	 * @param  [type] $field [字段]
	 * @param  [type] $data  [查询数组]
	 * @return [type]        [where 字符串]
	 */
	public function whereIn($field, $data)
	{
		$this->options['where'][] = ' ( ' . $field . ' IN (' . ((is_array($data) && !empty($data)) ? implode(',', $data) : $data) . ')';
		return $this;
	}

	/**
	 *  or in 查询
	 * @param  [type] $field [字段]
	 * @param  [type] $data  [查询数组]
	 * @return [type]        [where 字符串]
	 */
	public function orWhereIn($field, $data)
	{
		$this->options['where']['or'][] = ' ( ' . $field . ' IN (' . ((is_array($data) && !empty($data)) ? implode(',', $data) : $data) . ')';
		return $this;
	}

	/**
	 * where 简单查询   仅支持比较符查询
	 * @param  [type] $field [description]
	 * @param  [type] $data  [description]
	 * @return [type]        [description]
	 */
	public function where($field, $data)
	{
		if(is_array($data) && !empty($data)){
			if(array_key_exists(strtolower($data[0]), $this->comparison)){
				$value  = ' ' . $this->comparison[strtolower($data[0])] . ' ' . $this->perseValue($data[1]) . ' ';
			} else {

			}
		} else {
			$value = ' = ' . $this->perseValue($data);
		}
		$this->options['where'][] = ' ( ' .$field . $value . ' ) ';
		return $this;
	}

	/**
	 * or_where 简单查询   仅支持比较符查询
	 * @param  [type] $field [description]
	 * @param  [type] $data  [description]
	 * @return [type]        [description]
	 */
	public function orWhere($field, $data)
	{
		if(is_array($data) && !empty($data)){
			if(array_key_exists(strtolower($data[0]), $this->comparison)){
				$value  = ' ' . $this->comparison[strtolower($data[0])] . ' ' . $this->perseValue($data[1]) . ' ';
			} else {

			}
		} else {
			$value = ' = ' . $this->perseValue($data);
		}
		$this->options['where']['or'][] = ' ( ' .$field . $value . ' ) ';
		return $this;
	}

	/**
	 * like 查询
	 * @param  [type] $field [description]
	 * @param  [type] $data  [description]
	 * @return [type]        [description]
	 */
	public function like($field, $data)
	{
		$this->options['where'][] = ' ( ' . $field . ' LIKE ' . $data . ' ) ';
		return $this;
	}

	/**
	 * or_like 查询
	 * @param  [type] $field [description]
	 * @param  [type] $data  [description]
	 * @return [type]        [description]
	 */
	public function orLike($field,$data)
	{
		$this->options['where']['or'][] = ' ( ' . $field . ' LIKE ' . $data . ' ) ';
		return $this;
	}

	public function allWhere($field, $data)
	{

	}

	public function perseWhere()
	{
		
	}
	
	/**
	 * 添加单条数据
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function insert($data = [])
	{
		$data = array_merge($this->options['data'], $data);
		if (empty($data)) {
			throw new \Exception("无数据，无法向数据库增加数据:mysqli", 1);
		}
		$fields = $values = '';
		foreach ($data as $k => $v) {
			$fields[] = $k;
			$values[] = $this->perseValue($v);
		}
		$this->sql = 'INSERT INTO ' . $this->options['table'] . '(' . implode(',', $fields) . ') values(' . implode(',', $values) . ')';
		unset($this->options['data']);
		return $this->db->query($this->sql);
	}

	/**
	 * 添加多维数组
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function insertAll($data = [])
	{
		if (is_array($data) && !empty($data)) {
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
			$this->sql = 'INSERT INTO ' . $this->options['table'] . '(' . implode(',', $fields) . ') values' . $values;
			return $this->db->query($this->sql);
		} else {
			throw new \Exception("数据有误:mysqli", 1);
		}
	}

	public function save($data)
	{
	}

	public function delete()
	{
	}

	public function find()
	{
		return $this->options['where'];
	}
}
