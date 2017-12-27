<?php

function dd($data, $isDump = false)
{
	echo '<pre>';
	$isDump ? var_dump($data) : print_r($data);
	die;
}


$host = '127.0.0.1';
$user = 'vagrant';
$pwd  = 'vagrant';
$name = 'db';
$port = 3306;

$db = new \mysqli($host, $user, $pwd, $name, $port);

if ($db->connect_error) {
	die('连接失败');
}

$sql  = "insert into `student`(`name`,`class`) values('轩墨宝宝',3)";
$sql1 = "insert into `subject`(`name`) values('英语')";
$sql2 = "insert into `score`(`sid`,`subject_id`,`score`) values(7,1,66),(7,3,87),(8,1,79),(8,2,89),(8,3,55)";

$sql3 = "update `student` set name='王全蛋2',class=4 where id = 9";

$sql4 = "delete from `student` where  id = 10";


$sql5 = "select * from `student`";
$sql6 ="select * from `student` where class = 1";

$result = $db->query($sql6);

if ($result) {
	
	//dd($result->fetch_all(MYSQLI_BOTH));          //MYSQLI_ASSOC  field键     MYSQLI_NUM  数字数组   MYSQLI_BOTH  前两者都显示在一起
	dd($result->fetch_array(MYSQLI_ASSOC));       //只显示一个数组

} else {
	echo 'no';
}
echo '<br>';
//echo $db->insert_id;      //插入多条数据时会返回首先插入的id；

echo '<br>';
echo $db->close();
