<?php
header('content-type:text/html;char_set=utf-8');
$dsn  = 'mysql:host=127.0.0.1;dbname=db;port=3306';
$user = 'vagrant';
$pwd  = 'vagrant';

function dd($data, $isDump = false)
{
	echo '<pre>';
	$isDump ? var_dump($data) : print_r($data);
	echo '</pre>';
}

$pdo = new \pdo($dsn, $user, $pwd);

$sql = <<<EOF
		CREATE TABLE IF NOT EXISTS user(
		id INT UNSIGNED AUTO_INCREMENT KEY,
		username VARCHAR(20) NOT NULL UNIQUE,
		password CHAR(32) NOT NULL,
		email VARCHAR(30) NOT NULL
		);
EOF;

$sqlA1 = "INSERT `user`(`username`,`password`,`email`) VALUES('king9','" . md5('king') . "','112537890@qq.com')";

$sqlB1 = "UPDATE `user` set `username` = 'jmz',`password`='" . md5('jmz') . "' WHERE `id` =1";

$sqlC1 = "DELETE FROM `user` WHERE `id` = 1";

$sqlD1    = "select * from `user` where 1 = 1";
$sqlD2    = "select * from `user` where `id` > 1";
$username = '1\' or 1=1 #';
$username = $pdo->quote($username); //防注入攻击
$password = md5('king');
$sqlD3    = "select * from `user` where `username`={$username} `password`='{$password}'";
$sqlD5    = "select `username`,`password`,`email` from `user`";
//建表
// $res = $pdo->exec($sql);                     //exec 成功返回影响行数
// var_dump($res);

//增
/*
$res = $pdo->exec($sqlA1);
echo $pdo->lastInsertId();     //返回id
 */

//改
// $res = $pdo->exec($sqlB1);

//删
//$res = $pdo->exec($sqlC1);

//查  和 个数
/*
$stmt = $pdo->query($sqlD1);
echo $stmt->rowCount();
foreach ($stmt as $v) {
dd($v);
echo "<hr>";
}
 */

//预处理语句1 占位符prepare  execute  查询预处理 sprintf() 这个函数有点
/*
$sqlD4 = "select * from `user` where `username` = :username and `password`= :password";
$stmt = $pdo->prepare($sqlD4);
$stmt->execute(array(':username'=>'king1',':password'=>md5('king')));
if($stmt->rowCount()){
dd($stmt->fetchAll(PDO::FETCH_ASSOC));
}
 */

//预处理语句2 占位符
/*
$sqlD4 = "select * from `user` where `username` = ? and `password`= ?";
$stmt = $pdo->prepare($sqlD4);
$stmt->execute(array('king2',md5('king')));
if($stmt->rowCount()){
dd($stmt->fetchAll(PDO::FETCH_ASSOC));
}else{
echo 'no';
}
 */

//添加,更改,删除  预处理语句1
/*
$sqlA2 = "INSERT INTO `user`(`username`,`password`,`email`) VALUES(:username,:password,:email)";
$sqlC2 = "DELETE from `user` where id = :id";
//删 $sqlC2
$stmt = $pdo->prepare($sqlC2);
$stmt->bindParam(':id',$id);
$id=7;
$stmt->execute();
echo $stmt->rowCount().'<br>';exit;

//增 $sqlA2
$stmt = $pdo->prepare($sqlA2);
$stmt->bindParam(':username',$username);
$stmt->bindParam(':password',$password,PDO::PARAM_STR);
$stmt->bindParam(':email',$email);
$username = '1\' or 1=1 #';
$password = md5('kiaang');
$email = '1123142321@qq.com';
$stmt->execute();
echo '<br>';
// echo $pdo->lastInsertId();
$username = 'king1\' or 1=1 #';
$password = md5('kinaag1');
$email = '112eq31@qq.com';
$stmt->execute();
echo '<br>';
echo $pdo->lastInsertId();
 */

//添加,更改,删除 预处理语句2
/*
$sqlA2 = "INSERT INTO `user`(`username`,`password`,`email`) VALUES(?,?,?)";
$stmt = $pdo->prepare($sqlA2);
$stmt->bindParam(1,$username,PDO::PARAM_STR);
$stmt->bindParam(2,$password,PDO::PARAM_STR);
$stmt->bindParam(3,$email);
$username = 'jmz';
$password = md5('jmz');
$email = 'jmz@imooc.com';
$stmt->execute();
$stmt->debugDumpParams();      // 打印一条 SQL 预处理命令
//echo $pdo->lastInsertId();
 */

//查 bindParam  预处理语句
/*
$sqlD6 = "select * from `user` where `id` > :id";
$stmt = $pdo->prepare($sqlD6);
$stmt->bindParam(':id',$id,PDO::PARAM_INT);
$id =8;
$stmt->execute();
$stmt->debugDumpParams();
dd($stmt->fetchAll(PDO::FETCH_ASSOC));
 */

//添加  bindValue    可以用? 或者使用:username 一样都可以
/*
$sqlA2 = "INSERT INTO `user`(`username`,`password`,`email`) VALUES(?,?,?)";
$stmt = $pdo->prepare($sqlA2);
$username = 'kinaaa';
$password = md5('kingaa');
$email = 'da3s3f@imooc.com';
$stmt->bindValue(1,$username);
$stmt->bindValue(2,$password);
$stmt->bindValue(3,$email);
$stmt->execute();
echo $pdo->lastInsertId();
$username = 'kinaaabb';
$password = md5('kingaabb');
$stmt->bindValue(1,$username);
$stmt->bindValue(2,$password);
$stmt->execute();
echo $pdo->lastInsertId();
 */

//查  bindColumn
/*
$stmt = $pdo->prepare($sqlD5);
$stmt->execute();
$stmt->bindColumn(1,$username);
$stmt->bindColumn(2,$password);
$stmt->bindColumn(3,$email);
echo'结果集中的列数'.$stmt->columnCount().'<br>';       //有多少列
echo '<hr/>';
dd($stmt->getColumnMeta(0));                            //获取第一列的信息，实验性函数，后期可能会改变
echo '<hr>';
while($stmt->fetch(PDO::FETCH_BOUND)){  //PDO::FETCH_BOUND
echo '用户名：'.$username.'<br>';
echo '密码：'.$password.'<br>';
echo '邮箱：'.$email.'<br>';
echo '<hr/>';
}
 */

//查 fetchColumn
/*
$stmt = $pdo->query($sqlD5);
dd($stmt->fetchColumn(0));    //只能获取一行数据的第一列，一个进程中每使用一次指针就会向下移动一次
dd($stmt->fetchColumn(2));
 */

//prepare 和 execute 预处理的形式。  加上fetch
// $stmt = $pdo->prepare($sqlD2); //返回pdostatement对象
// $res = $stmt->execute(); //执行一条预处理语句
/*
while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
//fetch() 括号内部不填的话返回关联与索引都有的一条结果集。 即默认 PDO::FETCH_BOTH
//PDO::FETCH_ASSOC  一条关联数组
//PDO::FETCH_OBJ  一条对象的结果集
//PDO::FETCH_NUM  一条索引的结果集
$data[] = $row;
}
 */
//or
/*$data = $stmt->fetchAll(PDO::FETCH_OBJ);*/// 与以上内容一样使用
//or
/*$stmt->setFetchMode(PDO::FETCH_ASSOC);*///直接通过setFetchMode 设置fetchmode
// dd($stmt->fetchAll());

// 报错
/**
 * PDO::ERRMODE_SLIENT 默认模式，静默模式
 * PDO::ERRMOE_WARNING 警告模式
 * PDO::ERRMODE_EXCEPTION 异常模式   建议使用
 */
/*
$sqla = "delete from `user12` where id = 1";
$res = $pdo->exec($sqla);
if ($res === false) {
echo $pdo->errorCode();
echo "<br>";
print_r($pdo->errorInfo());
}
 */
/*
$sqlb = "select * from `user12`";
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); //设置错误级别
$pdo->query($sqlb);
echo $pdo->errorCode();
echo '<br>';
dd($pdo->errorInfo());
*/



/**
 * 事务
 * beginTransaction     启动事务
 * commit               提交事务
 * rollBack             回滚事务
 * inTransaction        检测是否在一个事务内 返回如果当前事务处于激活，则返回 TRUE ，否则返回 FALSE 。
 */

/*$sqlB4 = "update `user` set `username`='lalal111',`email`='lalal@imooc.com' where id = 4";
$pdo->beginTransaction();
$res = $pdo->exec($sqlB4);
if($res){
    echo $res;
    $pdo->commit();
} else {
    $pdo->rollBack();
}*/

unset($pdo);