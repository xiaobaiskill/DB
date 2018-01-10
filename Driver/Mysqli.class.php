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
		} elseif(isset($value)) {
			$v = '\'' . $this->db->real_escape_string($value) . '\'';
		}
		return $v;
	}

	public function insert($data = array())
	{
		$data  = array_merge($this->options['data'], $data);
		if(empty($data)) {
			throw new \Exception("无数据，无法向数据库增加数据:mysqli", 1);
		}
		$fields = $values = ''; 
		foreach ($data as $k => $v) {
			$fields[] = $k;
			$values[] = $this->perseValue($v);
		}
		$this->sql = 'INSERT INTO ' . $this->options['table'] . '('.implode(',',$fields).') values('.implode(',',$values).')';
		return $this->db->query($this->sql);
		
	}

	public function insertAll($data = array())
	{
		
	}
}