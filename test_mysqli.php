<?php

/**
 *增
 *insert into table(fieldA,fieldB,fieldC) values(X,X,X),(X,X,X)....
 *
 * 改
 * update table set fieldA = XX, fieldB = XX where fieldA = XX
 *
 * 删
 * delete from table where fieldA = XXX
 *
 *
 *查询
 *select field from table
 *select field from tableA A join tableB b on a.fieldA = b.fieldB and a.fieldAA = b.fieldBB left join tableC c on c.fieldC = a.fieldA where a.fieldA >100 group by a.fieldA having avg(a.fieldAAA) >= XXX order by a.fieldAA desc limit 3
 *
 *
 */

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
$charset = 'utf8';

$db = new \mysqli($host, $user, $pwd, $name, $port);

$db->select_db($name);       //切换数据库
$db->set_charset($charset);		//编码

if ($db->connect_error) {
	die('连接失败');
}

$sqlA1 = "insert into `student`(`name`,`class`) values('轩墨宝宝',3)";
$sqlA2 = "insert into `subject`(`name`) values('英语')";
$sqlA3 = "insert into `score`(`sid`,`subject_id`,`score`) values(7,1,66),(7,3,87),(8,1,79),(8,2,89),(8,3,55)";

$name = "jmz,2)#lalal";
$name = $db->real_escape_string($name);

$sqlA4 = "insert into `student`(`name`,`class`) values('{$name}',3)";

$sqlB1 = "update `student` set name='王全蛋5',class=4 where id = 9";

$sqlC1 = "delete from `student` where  id = 10";

$sqlD1 = "select * from `student`";
$sqlD2 = "select * from `student` where class = 1";
$sqlD3 = "select a.id,b.name,c.name subject,score from `score` a left join `student` b on b.`id` =a.`sid` left join `subject` c on c.`id` = a.subject_id";
$sqlD4 = "select a.id,b.name,c.name subject,score from `score` a left join `student` b on b.`id` =a.`sid` left join `subject` c on c.`id` = a.subject_id order by a.sid asc";

$sqlD5 = "select `sid`,avg(`score`) as `score` from `score` group by `sid`"; //group 分组的话，关于field 的部分要有关于分组的field

$sqlD6 = "select a.`sid`,b.`name`,sum(a.`score`) as `ascore`,count(a.`id`) as `cid`  from `score` a left join `student` b on a.`sid` =  b.`id` left join `subject` c on c.`id` = a.`subject_id` where a.`id` < 20 group by a.`sid` having avg(a.`score`) > 70 order by a.`sid` desc limit 0,2";

$sqlD7 = "select a.`sid`,b.`name`,sum(a.`score`) as `ascore`,count(a.`id`) as `cid`  from `score` a left join `student` b on a.`sid` =  b.`id` left join `subject` c on c.`id` = a.`subject_id` where a.`id` < 20 group by a.`sid` having avg(a.`score`) > 70 order by a.`sid` asc limit 3";

$result = $db->query($sqlD1);

if ($result) {
	//array
	//dd($result->fetch_all(MYSQLI_ASSOC));  //MYSQLI_ASSOC  field键   MYSQLI_NUM  数字数组   MYSQLI_BOTH  前两者都显示在一起 仅可用于 mysqlnd。
	//dd($result->fetch_array(MYSQLI_ASSOC)); //只显示一个数组

	//array
	/*while($arr = $result->fetch_array(MYSQLI_ASSOC)){
	$data[] =$arr;
	}
	dd($data);*/

	//object
	/*while ($obj =$result->fetch_object() ) {
	$data[] = $obj;
	}
	dd($data);*/

	var_dump($result->fetch_array(MYSQLI_ASSOC));

	echo $db->affected_rows; //受影响属性
	$result->close();        //查询语句   需要释放结果集
} else {
	echo 'no';
}

echo '<hr>';

$sqlE1 = "insert into `student`(`name`,`class`) values ( ?, ?)";

/*
//预处理语句 insert
$mysqli_stmt = $db->prepare($sqlE1);
$mysqli_stmt->bind_param('si', $name, $class);   //不能引用传递，需要通过变量传值
$name = "jmz,2)#lalal";
$class =3;

if($mysqli_stmt->execute()){
echo $mysqli_stmt->insert_id;
echo '<br>';
$mysqli_stmt->close();			//释放结果集 
}else{
$mysqli_stmt->error;
}
*/
//echo $db->insert_id;      //插入多条数据时会返回首先插入的id；

$db->close();
