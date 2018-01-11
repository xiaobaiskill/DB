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
// 
dd($db->where_in('class',array(1,2,3))->where('name',array('GT',4)));