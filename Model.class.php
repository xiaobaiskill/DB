<?php
class Model{
	public $db;
	protected $table_name = '';
	public function __construct($config)
	{
		$mysql_type = ucfirst($config['type']);
		unset($config['type']);
		$mysql_path = "\\Driver\\$mysql_type";
		$this->db = new $mysql_path();
		$this->db->connect($config);
		if(!empty($this->table_name)){
			$this->table($this->table_name);
		}
	}
	public function data($data)
	{
		$this->db->data($data);
		return $this;
	}
	public function whereIn($field, $data)
	{
		$this->db->whereIn($field, $data);
		return $this;
	}
	public function orWhereIn($field, $data)
	{
		$this->db->orWhereIn($field, $data);
		return $this;
	}
	public function where($field, $data)
	{
		$this->db->where($field, $data);
		return $this;
	}
	public function orWhere($field, $data)
	{
		$this->db->orWhere($field, $data);
		return $this;
	}
	public function like($field, $data)
	{
		$this->db->like($field, $data);
		return $this;
	}
	public function orLike($field, $data)
	{
		$this->db->orLike($field, $data);
		return $this;
	}
	public function allWhere($field, $data = NULL)
	{
		$this->db->allWhere($field, $data);
		return $this;
	}
	public function groupStart()
	{
		$this->db->groupStart();
		return $this;
	}
	public function groupEnd()
	{
		$this->db->groupEnd();
		return $this;
	}
	public function orGroupStart()
	{
		$this->db->orGroupStart();
		return $this;
	}
	public function orGroupEnd()
	{
		$this->db->orGroupEnd();
		return $this;
	}
	public function group($group)
	{
		$this->db->group($group);
		return $this;
	}
	public function having($field, $data = null)
	{
		$this->db->having($field, $data);
		return $this;
	}
	public function field($field)
	{
		$this->db->field($field);
		return $this;
	}
	public function join($join, $about = '')
	{
		$this->db->join($join, $about);
		return $this;
	}
	public function order($order)
	{
		$this->db->order($order);
		return $this;
	}
	public function limit($start, $end = null)
	{
		$this->db->limit($start, $end);
		return $this;
	}
	public function selectDb($table)
	{
		$this->db->selectDb($table);
		return $this;
	}
	public function setCharset($charset)
	{
		$this->db->setCharset($charset);
		return $this;
	}
	public function table($table)
	{
		$this->db->table($table);
		return $this;
	}
	public function as_array()
	{
		$this->db->as_array();
		return $this;
	}
	public function as_object()
	{
		$this->db->as_object();
		return $this;
	}
	public function fetchSql()
	{
		$this->db->fetchSql();
		return $this;
	}
	public function getSql()
	{
		return $this->db->getSql();
	}
	public function lastSql()
	{
		return $this->db->lastSql();
	}
	public function startTrans()
	{
		$this->db->startTrans();
	}
	public function commit()
	{
		$this->db->commit();
	}
	public function rollback()
	{
		$this->db->rollback();
	}
	public function query($sql)
	{
		return $this->db->query($sql);
	}
	public function execute($sql)
	{
		return $this->db->execute($sql);
	}
	public function insert($data = [])
	{
		return $this->db->insert($data);
	}
	public function insertAll($data = [])
	{
		return $this->db->insertAll($data);
	}
	public function save($field, $data = null)
	{
		return $this->db->save($field, $data);
	}
	public function delete()
	{
		return $this->db->delete();
	}
	public function find()
	{
		return $this->db->find();
	}
	public function findAll()
	{
		return $this->db->findAll();
	}
	public function count()
	{
		return $this->db->count();
	}
	public function lastInsertId()
	{
		return $this->db->insert_id;
	}
}