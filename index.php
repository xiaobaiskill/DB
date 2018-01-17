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
$config['charset'] = 'UTF-8';
$db = new \Driver\Mysqli($config);


/*
echo '1<br>';
$data = array(
	'name'=>'class111',
	'class'=>3
);
dd($db->table('student')->data($data)->insert(array('class'=>4)));
 dd($db->insert_id);
dd($db->lastSql());






echo '<br>';
echo '2<br>';
$data1 =array(
	array(
		'name'=>'jmz1',
		'class'=>6,
	),
	array(
		'name'=>'jmz2',
		'class'=>7,
	),
	array(
		'name'=>'jmz3',
		'class'=>8,
	),
	array(
		'name'=>'',
		'class'=>8,
	),
	array(
		'name'=>'jmz10',
	)
);
dd($db->data($data1)->table('student')->insertAll());
dd($db->lastSql());





echo '<br>';
echo '3<br>'; 
$data = array(
	'name' => null
);

$where = array(
	'id' => array('gt',168),
	'status'=>1
);
dd($db->allWhere($where)->table('student')->data($data)->save(array('class'=>7)));
dd($db->lastSql());*/

$start = microtime(true);
echo '<br>';
echo '4<br>';
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
->count();
dd($result);
dd($db->lastSql());
$end = microtime(true);
echo '<br>';
echo $end-$start;

/*echo '<br>';
echo '5<br>';
$result1 = $db->table('score a')
	->field('a.id,b.name,c.name subject,score')
	->join('student b on b.id =a.sid','left')
	->join(' subject c on c.id = a.subject_id')
	->order(array('a.sid'=>'asc'))
	->fetchSql()
	->find();
dd($result1);




echo '<br>';
echo '6<br>';
$result2 = $db->table('score a')
	->field('a.id,b.name,c.name subject,score')
	->join('student b on b.id =a.sid','left')
	->join(' subject c on c.id = a.subject_id','left')
	->order(array('a.sid'=>'asc'))
	->find();
dd($result2);
dd($db->lastSql());




$db->startTrans();
echo '7<br>';
dd($db->table('student')->whereIn('id',array(29,30))->orWhere('id',array('gt',170))->delete());
dd($db->lastSql());
// $db->rollback();
$db->commit();
*/