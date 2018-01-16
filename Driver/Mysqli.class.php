<?php
/**
 * 基础mysqli类
 */
namespace Driver;

class Mysqli
{
	public $db;
	public $table_name = '';
	public $sql;
	public $options;
	public $concat     = false;
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
		$where                  = $field . ' IN (' . ((is_array($data) && !empty($data)) ? implode(',', $data) : $data) . ')';
		$this->options['where'] = !empty($this->options['where']) ? $this->options['where'] . ($this->concat ? $where : ' AND ' . $where) : $where;
		$this->concat           = false;
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
		$where                  = $field . ' IN (' . ((is_array($data) && !empty($data)) ? implode(',', $data) : $data) . ')';
		$this->options['where'] = !empty($this->options['where']) ? $this->options['where'] . ($this->concat ? $where : ' OR ' . $where) : $where;
		$this->concat           = false;
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
		if (is_array($data) && !empty($data)) {
			if (array_key_exists(strtolower($data[0]), $this->comparison)) {
				$value = ' ' . $this->comparison[strtolower($data[0])] . ' ' . $this->perseValue($data[1]) . ' ';
			} else {
				throw new \Exception("where条件语句有误", 1);
			}
		} else {
			$value = ' = ' . $this->perseValue($data);
		}
		$where                  = $field . $value;
		$this->options['where'] = !empty($this->options['where']) ? $this->options['where'] . ($this->concat ? $where : ' AND ' . $where) : $where;
		$this->concat           = false;
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
		if (is_array($data) && !empty($data)) {
			if (array_key_exists(strtolower($data[0]), $this->comparison)) {
				$where = ' ' . $this->comparison[strtolower($data[0])] . ' ' . $this->perseValue($data[1]) . ' ';
			} else {
				throw new \Exception("where条件语句有误", 1);
			}
		} else {
			$value = ' = ' . $this->perseValue($data);
		}
		$where                  = $field . $value;
		$this->options['where'] = !empty($this->options['where']) ? $this->options['where'] . ($this->concat ? $where : ' OR ' . $where) : $where;
		$this->concat           = false;
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
		$where                  = $field . ' LIKE ' . '\'%' . addslashes($data) . '%\'';
		$this->options['where'] = !empty($this->options['where']) ? $this->options['where'] . ($this->concat ? $where : ' AND ' . $where) : $where;
		$this->concat           = false;
		return $this;
	}

	/**
	 * or_like 查询
	 * @param  [type] $field [description]
	 * @param  [type] $data  [description]
	 * @return [type]        [description]
	 */
	public function orLike($field, $data)
	{
		$where                  = $field . ' LIKE ' . '\'%' . addslashes($data) . '%\'';
		$this->options['where'] = !empty($this->options['where']) ? $this->options['where'] . ($this->concat ? $where : ' OR ' . $where) : $where;
		$this->concat           = false;
		return $this;
	}

	/**
	 * allWhere thinkPHP where 执行相近
	 * @param  [type] $field [description]
	 * @param  [type] $data  [description]
	 * @return [type]        [description]
	 */
	public function allWhere($field, $data = null)
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
	private function analyseWhere($where, $data = null)
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
								$where_str .= ' ' . $k . ' ' . strtoupper($v[0]) . ' \'%' . trim($this->perseValue($v[1]), '\'') . '%\' ';
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
		if ($this->options['where']) {
			return ' WHERE ' . $this->options['where'];
		}
		return $where;
	}

	public function group($group)
	{
		$this->options['group'] = empty($group) ? '' : $group;
		return $this;
	}

	public function perseGroup()
	{
		$group_str = '';
		if (!empty($this->options['group'])) {
			$group_str .= 'GROUP BY ' . $this->options['group'];
		}
		return $group_str;
	}

	/**
	 * having 条件语句
	 * @param  [type] $field [description]
	 * @param  [type] $data  [description]
	 * @return [type]        [description]
	 */
	public function having($field, $data = null)
	{
		$this->options['having'] = $this->analyseWhere($field, $data);
		return $this;
	}

	/**
	 * 解析 having 语句
	 * @return [type] [description]
	 */
	public function perseHaving()
	{
		$having = '';
		if ($this->options['having']) {
			return ' HAVING ' . $this->options['having'];
		}
		return $having;
	}

	/**
	 * 查询字段
	 * @param  [type] $field [description]
	 * @return [type]        [description]
	 */
	public function field($field)
	{
		$this->options['field'] = $field;
		return $this;
	}

	/**
	 * 解析字段
	 * @return [type] [description]
	 */
	public function perseField()
	{
		$field = ' * ';
		if (!empty($this->options['field'])) {
			$field = ' ' . $this->options['field'] . ' ';
		}
		return $field;
	}

	/**
	 * 添加 jion 语句
	 * @param  [type] $join  [thinkphp join]
	 * @param  string $about [left right inner]
	 * @return [type]        [description]
	 */
	public function join($join, $about = '')
	{
		if (in_array(strtolower($about), ['left', 'right', 'inner'])) {
			$join_str = strtoupper($about) . ' JOIN ' . $join . ' ';
		} else {
			$join_str = ' JOIN ' . $join;
		}
		$this->options['join'] = !empty($this->options['join']) ? $this->options['join'] . ' ' . $join_str : $join_str;
		return $this;
	}

	/**
	 * 解析join
	 * @return [type] [description]
	 */
	public function perseJoin()
	{
		$join = '';
		if (!empty($this->options['join'])) {
			$field = $this->options['join'];
		}
		return $field;
	}

	/**
	 * 添加order 语句
	 * @param  [type] $order [description]
	 * @return [type]        [description]
	 */
	public function order($order)
	{
		$order_str = '';
		if (is_string($order)) {
			$order_str .= $order . ' ';

		} elseif (is_array($order)) {
			$count = $order;
			$i     = 0;
			foreach ($order as $k => $v) {
				if (is_string($k)) {
					$ororder_strder .= $k . ' ' . strtoupper($v);
				} else {
					$order_str .= $v . ' ASC';
				}
				$i++;
				if ($i !== $count) {
					$order_str .= ',';
				}
			}
		}
		$this->options['order'] = $order_str;
		return $this;
	}

	/**
	 * 解析order语句
	 * @return [type] [description]
	 */
	public function perseOrder()
	{
		$join = '';
		if (!empty($this->options['order'])) {
			$join .= ' ORDER BY ' . $this->options['order'];
		}
		return $join;
	}

	/**
	 * 添加limie 语句
	 * @param  [type] $start [description]
	 * @param  [type] $end   [description]
	 * @return [type]        [description]
	 */
	public function limit($start, $end = null)
	{
		$limit_str = '';
		if (is_null($end)) {
			$limit_str .= $start;
		} else {
			$limit_str .= $start . ',' . $end;
		}
		$this->options['limit'] = $limit_str;
		return $this;
	}

	/**
	 * 解析limit
	 * @return [type] [description]
	 */
	public function perseLimit()
	{
		$limit = '';
		if (!empty($this->options['limit'])) {
			$limit .= ' LIMIT ' . $this->options['limit'];
		}
		return $limit;
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
		if (!empty($this->query)) {$this->query->close();}
		$this->affected_rows = 0;
		$this->insert_id     = 0;
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
			while ($obj = $this->query->fetch_array(MYSQLI_ASSOC)) {
				$data[] = $obj;
			}

		} else {
			while ($obj = $this->query->fetch_object()) {
				$data[] = $obj;
			}
		}
		$this->as_array();
		$this->affected_rows = $this->db->affected_rows;
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
		$this->sql       = 'INSERT INTO ' . $this->table_name . '(' . implode(',', $fields) . ') values(' . implode(',', $values) . ')';
		$this->num_rows  = $this->db->affected_rows;
		$this->insert_id = $this->db->insert_id;
		unset($this->options);
		return $this->db->query($this->sql);
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
			$this->sql       = 'INSERT INTO ' . $this->table_name . '(' . implode(',', $fields) . ') values' . $values;
			$this->num_rows  = $this->db->affected_rows;
			$this->insert_id = $this->db->insert_id;
			unset($this->options);
			return $this->db->query($this->sql);
		} else {
			throw new \Exception("insertAll数据有误:mysqli", 1);
		}
	}

	public function save($field, $data = null)
	{
		//update `table` set 字段1 = 字段数据1,字段2 = 字段数据2 where 条件
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
		$this->sql      = 'UPDATE ' . $this->table_name . ' SET ' . $update_str . $this->perseWhere();
		$this->query    = $this->db->query($this->sql);
		$this->num_rows = $this->db->affected_rows;
		unset($this->options);
		return $this->num_rows;
	}

	public function delete()
	{
		//delete from `table` where 条件
		$this->free();
		$this->sql      = 'DELETE FROM ' . $this->table_name . ' ' . $this->perseWhere();
		$this->query    = $this->db->query($this->sql);
		$this->num_rows = $this->db->affected_rows;
		unset($this->options);
		return $this->num_rows;
	}

	public function find()
	{
		// select field from `tableA` A join `tableB` b on a.fieldA = b.fieldB and a.fieldAA = b.fieldBB left join `tableC` c on c.fieldC = a.fieldA where a.fieldA >100 group by a.fieldA having avg(a.fieldAAA) >= XXX order by a.fieldAA desc limit 3
		$this->free();
		$this->sql   = 'SELECT ' . $this->perseField() . ' FROM ' . $this->table_name . ' ' . $this->perseJoin() . ' ' . $this->perseWhere() . ' ' . $this->perseGroup() . ' ' . $this->perseHaving() . ' ' . $this->perseOrder() . ' ' . $this->perseLimit();
		$this->query = $this->db->query($this->sql);
		if ('array' == $this->resulttype) {
			$data = $this->query->fetch_array(MYSQLI_ASSOC);
		} else {
			$data = $this->query->fetch_object();
		}
		$this->as_array();
		return $data;
	}

	public function findAll()
	{
		$this->free();
		$this->sql   = 'SELECT ' . $this->perseField() . ' FROM ' . $this->table_name . ' ' . $this->perseJoin() . ' ' . $this->perseWhere() . ' ' . $this->perseGroup() . ' ' . $this->perseHaving() . ' ' . $this->perseOrder() . ' ' . $this->perseLimit();
		$this->query = $this->db->query($this->sql);
		if ('array' == $this->resulttype) {
			while ($obj = $this->query->fetch_array(MYSQLI_ASSOC)) {
				$data[] = $obj;
			}
		} else {
			while ($obj = $this->query->fetch_object()) {
				$data[] = $obj;
			}
		}
		$this->as_array();
		return $data;
	}

}
