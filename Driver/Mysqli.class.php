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
	public $concat=false;
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
		$where = $field . ' IN (' . ((is_array($data) && !empty($data)) ? implode(',', $data) : $data) . ')';
		$this->options['where'] = $this->options['where'] ? $this->options['where'] . ( $this->concat ? $where : ' AND ' . $where ) : $where;
		$this->concat = false;
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
		$where = $field . ' IN (' . ((is_array($data) && !empty($data)) ? implode(',', $data) : $data) . ')';
		$this->options['where'] = $this->options['where'] ? $this->options['where'] . ( $this->concat ? $where : ' OR ' . $where ) : $where;
		$this->concat = false;
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
				throw new \Exception("where条件语句有误", 1);
			}
		} else {
			$value = ' = ' . $this->perseValue($data);
		}
		$where =  $field . $value;
		$this->options['where'] = $this->options['where'] ? $this->options['where'] . ( $this->concat ? $where : ' AND ' . $where) : $where;
		$this->concat = false;
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
				$where  = ' ' . $this->comparison[strtolower($data[0])] . ' ' . $this->perseValue($data[1]) . ' ';
			} else {
				throw new \Exception("where条件语句有误", 1);
			}
		} else {
			$value = ' = ' . $this->perseValue($data);
		}
		$where =  $field . $value;
		$this->options['where'] = $this->options['where'] ? $this->options['where'] . ( $this->concat ? $where : ' OR ' . $where) : $where;
		$this->concat = false;
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
		$where =  $field . ' LIKE ' . '\'%' . addslashes($data) . '%\'';
		$this->options['where'] =  $this->options['where'] ? $this->options['where'] . ( $this->concat ? $where : ' AND '. $where ) : $where;
		$this->concat = false;
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
		$where =  $field . ' LIKE ' . '\'%' . addslashes($data) . '%\'';
		$this->options['where'] =  $this->options['where'] ? $this->options['where'] . ( $this->concat ? $where : ' OR '. $where ) : $where;
		$this->concat = false;
		return $this;
	}

	/**
	 * allWhere thinkPHP where 执行相近 
	 * @param  [type] $field [description]
	 * @param  [type] $data  [description]
	 * @return [type]        [description]
	 */
	public function allWhere($field, $data = NULL)
	{
		$this->options['where'] = $this->analyseWhere($field, $data);
		return $this;
	}

	/**
	 * analyseWhere 解析allWhere 条件语句
	 * @param  [type] $where [description]
	 * @param  [type] $data  [description]
	 * @return [type]        [description]
	 */
	private function analyseWhere($where, $data = NULL)
	{
		$where_str = '';
		if (null === $data) {
			if (is_array($where) && !empty($where)) {
				if (array_key_exists('_logic', $where)) {
					$operate = ' ' . strtoupper($where['_logic']) . ' ';
					unset($where['_logic']);
				} else {
					$operate = ' AND ';
				}
				$i     = 0;
				$count = count($where);
				foreach ($where as $k => $v) {
					$where_str .= ' ( ';
					if (strpos($k, '_') !== false) {
						$where_str .= $this->specialWhere($k, $v);
					} else {
						if (is_string($v) || is_numeric($v)) {
							$where_str .= ' ' . $k . '=' . $this->perseValue($v);
						} elseif (is_array($v) || is_object($v)) {
							is_object($v) && $v = get_object_vars($v);
							if (array_key_exists(strtolower($v[0]), $this->comparison)) {
								$where_str .= ' ' . $k . ' ' . $this->comparison[strtolower($v[0])] . ' ' . $this->perseValue($v[1]) . ' ';
							} elseif (in_array(strtolower($v[0]), ['like', 'not like'])) {
								$where_str .= ' ' . $k . ' ' . strtoupper($v[0]) . ' \'%' . trim($this->perseValue($v[1]),'\'') . '%\' ';
							} elseif ('in' == strtolower($v[0])) {
								$array_to_str = $v[1];
								if (is_array($array_to_str)) {
									$array_to_str = implode(',', $array_to_str);
								}
								$where_str .= ' ' . $k . ' IN(' . $array_to_str . ') ';
							} elseif ('between' == strtolower($v[0])) {
								if (is_array($v[1])) {
									$where_str .= ' ' . $k . ' BETWEEN ' . $this->perseValue($v[1][0]) . ' AND ' . $this->perseValue($v[1][1]) . ' ';
								} else {
									$where_str .= ' ' . $k . ' BETWEEN ' . str_replace(',', ' AND ', $v[1]) . ' ';
								}
							} else {
								throw new \Exception("perse_where():" . $v . "暂未处理", 1);
							}
						} else {
							throw new \Exception("perse_where():" . $v . "非法条件", 1);
						}
					}
					$where_str .= ' ) ';
					$i++;
					if ($i !== $count) {
						$where_str .= $operate;
					}
				}
			} else {
				throw new \Exception("parse_where():第二参数不存在，则第一参数只能是array类型", 1);
			}
		} else {
			if (is_string($where)) {
				if (is_numeric($data) || is_string($data)) {
					$where_str = ' ' . $where . ' = ' . $this->perseValue($data) . ' ';
				} else {
					throw new \Exception("parse_where():第二参数类型有误", 1);
				}
			} else {
				throw new \Exception("parse_where():第二参数存在，则第一参数只能是string类型", 1);
			}
		}
		return $where_str;
	}

	/**
	 * allwhere 的条件语句
	 * @param  [type] $allWhere [description]
	 * @return [type]           [description]
	 */
	private function specialWhere($key, $val)
	{
		switch ($key) {
			case '_string':
				$where_str = $val;
				break;
			case '_complex':
			$where_str = $this->analyseWhere($val);
			break;
		}
		return $where_str;
	}

	public function groupStart()
	{
		$this->options['where'] .= '(';
		$this->concat = true;
		return $this;
	}

	public function groupEnd()
	{
		$this->options['where'] .= ')';
		$this->concat = false;
		return $this;
	}

	public function orGroupStart()
	{
		$this->options['where'] .= 'OR (';
		$this->concat = true;
		return $this;
	}

	public function orGroupEnd()
	{
		$this->options['where'] .= ')';
		$this->concat = false;
		return $this;
	}
	/**
	 * 解析 where 条件语句
	 * @return [type] [description]
	 */
	public function perseWhere()
	{	
		$where = '';
		if($this->options['where']){
			return ' WHERE ' . $this->options['where'];
			unset($this->options['where']);
		}
		return $where;
	}
	
	public function query($sql){
		$stmt = $this->db->query($sql);
		dd($stmt->fetch_all(MYSQLI_ASSOC));
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
	 * 添加多条数据
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
