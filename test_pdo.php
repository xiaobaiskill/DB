<?php
$dsn = 'mysql:host=127.0.0.1;dbname=db;port=3306';
$user = 'vagrant';
$pwd = 'vagrant';

function dd($data, $isDump = false)
{
	echo '<pre>';
	$isDump ? var_dump($data) : print_r($data);
	die;
}

try{
	$pdo  = new \pdo($dsn,$user,$pwd);

	foreach ($pdo->query('select * from `student`') as $v) {
		$data[] = $v;
	}
	dd($data);
	
}catch(\PDOException $e){
	print "Errot:" . $e->getMessage() . "<br/>";
	die();
}

