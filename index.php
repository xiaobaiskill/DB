<?php
function dd($data, $isDump = false)
{
	echo '<pre>';
	$isDump ? var_dump($data) : print_r($data);
}
spl_autoload_register('autoload');
function autoload($class){
	require __DIR__.'/'.ltrim(str_replace('\\', '/', $class),'/').'.class.php';
}

$config['host']    = '127.0.0.1';
$config['user']   = 'vagrant';
$config['pwd']     = 'vagrant';
$config['name']    = 'db';
$config['port']   = 3306;
$config['charset'] = 'utf8';
$db = new \Driver\Mysqli($config);

$data = array(
	'name'=>'\'jmz\',1,1) #',
	'class'=>2
);

//dd($db->table('student')->data($data)->insert(array('status'=>0)));

$data_all = array(
	array('name'=>'jmz4','class'=>2,'status'=>1),
	array('class'=>22,'status'=>0,'name'=>'jmz19'),
);
$data1  = 	array('name'=>'jmz3','status'=>1,'class'=>4);
// dd($db->table('student')->data($data1)->insertAll($data_all));


// dd( $db->whereIn('class','1,2,3,4')->orWhereIn('id',array(1,2,34))->where('name','jmz')->orLike('name','j')->perseWhere());

/*$where1 = [
	'class'  => ['gt', 2],
	'status' => 1,
	'name'	 => array('like', 'jmz')
];

$where = [
	'id'       => ['in', [1, 2, 10, 40, 27, 37, 12, 8]],
	'_complex' => $where1,
	'_logic'   => 'or'
];

dd($db->allWhere($where)->perseWhere());*/

/*$db->groupStart()
	->groupStart()
	->where('name','jmz')
	->whereIn('id',array(1,2,35,76))
	->groupEnd()
	->orGroupStart()
	->like('name','jmz')
	->where('id',array('gt',100))
	->orGroupEnd()
	->groupEnd()
	->where('id',array('eq',100));

dd($db->perseWhere());*/

/*$sql = "select a.`sid`,b.`name`,sum(a.`score`) as `ascore`,count(a.`id`) as `cid`  from `score` a left join `student` b on a.`sid` =  b.`id` left join `subject` c on c.`id` = a.`subject_id` where a.`id` < 20 group by a.`sid` having avg(a.`score`) > 70 order by a.`sid` asc limit 3";
$sqlA1 = "insert into `student`(`name`,`class`) values('轩墨宝宝22',2)";
dd($db->query($sqlA1));
dd($db->affected_rows);*/


// dd($db->table('student')->where('id',9)->delete());

/*$data = array(
	'name'=>'class111',
	'class'=>3
);
dd($db->table('student')->data($data)->insert(array('class'=>4)));
dd($db->insert_id);*/

// dd($db->table('student')->whereIn('id',array(24,25,26,27,28))->delete());
 
/*$data = array(
	'name' => null
);
$where = array(
	'id' => array('gt',21),
	'status'=>1
);
dd($db->allWhere($where)->table('student')->data($data)->save(array('class'=>66)));*/

$result = $db->table('score a')
->where('a.id',array('lt',20))
->field(' a.sid,b.name,sum(a.score) as ascore,count(a.id) as cid')
->join('student b on a.sid =  b.id','left')
->join('subject c on c.id = a.subject_id','left')
->limit(3)
->group('a.sid')
->having(array('avg(a.score)'=>array('gt',70)))
->order('a.sid asc')
->as_object()
->findAll();
dd($result);
dd($db->lastSql());