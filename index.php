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
);

dd($db->data($data)->add(array()));