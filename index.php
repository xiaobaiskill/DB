<?php
spl_autoload_register('autoload');
function autoload($class){
	require __DIR__.'/'.ltrim(str_replace('\\', '/', $class),'/').'.class.php';
}
function dd($data, $isDump = false)
{
	echo '<pre>';
	$isDump ? var_dump($data) : print_r($data);

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

/*$db->options['where']=array(
	'or'=>array(
		'class in (1,2,3)',
		'status = 1',
		'or'=>array(
			'name like %jj%',
			array(
				'sid = 3',
				'delete = 0'
				)
			),
	),
);*/

// $db->options['where']=array('calss = 2','name IN (12,34,5)');

/*$db->options['where']=array(
		'id IN (1,2,3)',
		array(
				'or'=>array(
						'class = 1',
						'status = 1'
					),
				'name like %k%'
			)
	);*/

dd( $db->whereIn('class','1,2,3,4')->orWhereIn('id',array(1,2,34))->where('name','jmz')->orLike('name','%j$')->perseWhere());

//or查询
/*$db->options['where']=array(
	'or'=>array(
		'id in (1,2,3)',
		'status = 0',
	),
);
$sql = 'select * from student where ' . $db->perseWhere();
$db->query($sql);*/