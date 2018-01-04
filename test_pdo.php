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

	$sql = <<<EOF
		CREATE TABLE IF NOT EXISTS user(
		id INT UNSIGNED AUTO_INCREMENT KEY,
		username VARCHAR(20) NOT NULL UNIQUE,
		password CHAR(32) NOT NULL,
		email VARCHAR(30) NOT NULL
		);
EOF;

	$sqlA1 = "INSERT `user`(`username`,`password`,`email`) VALUES('king','".md5('king')."','112537890@qq.com')";
	
	$sqlB1 ="UPDATE `user` set `username` = 'jmz',`password`='".md5('jmz')."' WHERE `id` =1";

	$sqlC1 ="DELETE FROM `user` WHERE `id` = 1";



	$res = $pdo->exec($sql);
	var_dump($res);
	
	//增
	// $res = $pdo->exec($sqlA1);
	
	//改
	// $res = $pdo->exec($sqlB1);
	
	//删
	//$res = $pdo->exec($sqlC1);




}catch(\PDOException $e){
	print "Errot:" . $e->getMessage() . "<br/>";
	die();
}

