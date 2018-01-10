<?php
/**
 * 基础mysqli类
 */
namespace Driver;

class Mysqli
{
	public $db;
	public $sql;
	public $data = array();
	public function __construct($config)
	{
		$this->connect($config);
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

	/**
	 * 处理数据
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function data($data)
	{
		$this->data = $data;
		return $this;
	}

	/**
	 * value 处理
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function perseValue($value)
	{	
		$value = '';
		if (is_numeric($value)) {
			$value = $value;
		} elseif (is_null($value)) {
			$value = 'null';
		} elseif(isset($value)) {
			$value = $ $this->db->real_escape_string($value);
		}
		return $value;
	}

	public function add($data = array())
	{
		//'insert into table () values ()'
		$data  = array_merge($this->data, $data);
		if(empty($data)) {
			throw new \Exception("无数据，无法向数据库增加数据:mysqli", 1);
		}
		foreach ($data as $k => $v) {
			
		}
		return $data;
	}

	public function addAll()
	{
		//'insert into table () values (),(),'
	}
}