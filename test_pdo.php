<?php
$dsn  = 'mysql:host=127.0.0.1;dbname=db;port=3306';
$user = 'vagrant';
$pwd  = 'vagrant';

function dd($data, $isDump = false)
{
    echo '<pre>';
    $isDump ? var_dump($data) : print_r($data);
    echo '</pre>';
}

try {
    $pdo = new \pdo($dsn, $user, $pwd);

    $sql = <<<EOF
		CREATE TABLE IF NOT EXISTS user(
		id INT UNSIGNED AUTO_INCREMENT KEY,
		username VARCHAR(20) NOT NULL UNIQUE,
		password CHAR(32) NOT NULL,
		email VARCHAR(30) NOT NULL
		);
EOF;

    $sqlA1 = "INSERT `user`(`username`,`password`,`email`) VALUES('king3','" . md5('king') . "','112537890@qq.com')";

    $sqlB1 = "UPDATE `user` set `username` = 'jmz',`password`='" . md5('jmz') . "' WHERE `id` =1";

    $sqlC1 = "DELETE FROM `user` WHERE `id` = 1";

    $sqlD1 = "select * from `user` where 1 = 1";

    $sqlD2 = "select * from `user` where `id` > 1";

    // $res = $pdo->exec($sql);
    // var_dump($res);

    //增
    //$res = $pdo->exec($sqlA1);
    // echo $pdo->lastInsertId();

    //改
    // $res = $pdo->exec($sqlB1);

    //删
    //$res = $pdo->exec($sqlC1);

    //查
    /*$stmt = $pdo->query($sqlD1);
    foreach ($stmt as $v) {
    dd($v);
    echo "<hr>";
    }*/

    //prepare 和 execute 预处理的形式。  加上fetch
    // $stmt = $pdo->prepare($sqlD2); //返回pdostatement对象
    // $res = $stmt->execute(); //执行一条预处理语句
    /*while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
    //fetch() 括号内部不填的话返回关联与索引都有的一条结果集。 即默认 PDO::FETCH_BOTH
    //PDO::FETCH_ASSOC  一条关联数组
    //PDO::FETCH_OBJ  一条对象的结果集
    //PDO::FETCH_NUM  一条索引的结果集
    $data[] = $row;
    }*/
    //or
    //$data = $stmt->fetchAll(PDO::FETCH_OBJ); // 与以上内容一样使用
    //or
    // $stmt->setFetchMode(PDO::FETCH_ASSOC); //直接通过setFetchMode 设置fetchmode
    // dd($stmt->fetchAll());

    // 报错
    /*
$sqla = "delete from user12 where id = 1";
$res = $pdo->exec($sqla);
if ($res === false) {
echo $pdo->errorCode();
echo "<br>";
print_r($pdo->errorInfo());
}
 */

} catch (\PDOException $e) {
    print "Errot:" . $e->getMessage() . "<br/>";
    die();
}
