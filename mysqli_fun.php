<?php

$comparison = array(
		'eq' => '=',
		'neq'=>'!=',
		'gt'=>'>',
		'egt'=>'>=',
		'lt'=>'<',
		'elt'=>'<=',
);


function dd($data, $isDump = false)
{
    echo '<pre>';
    $isDump ? var_dump($data) : print_r($data);

}


/**
 * where 条件拼装
 * @param  [type] $data [description]
 * @return [type]       [description]
 */
 $where1=array(
    'b'=>'ll',
    'c'=>array('in',[2,34,5]),
    '_logic'=>'and'
 );

$where=array(
    'a'=>'sa',
    '_complex'=>$where1,
    '_logic'=>'or'
 );



function perse_where($where,$data = null){
	$where_str = '';
	if($data === null)
	{
		if(is_array($where) && !empty($where)) {
			if(array_key_exists('_logic', $where)){
				$operate = ' ' . strtoupper($where['_logic']) . ' ';
				unset($where['_logic']);
			}else{
				$operate = ' AND ';
			}
			foreach ($where as $k => $v) {
				$where_str .= ' ( ';
				if(strpos($k, '_')){
					$where_str = special_where($v);
				}else{
					if(is_string($v)){
						$where_str .= ' `'.$k.'`=\''.$v.'\' ';
					}elseif(is_numeric($v)){
						$where_str .= ' `'.$k.'`='.$v .' ';
					}elseif(is_array($v)){
						if(array_key_exists(strtolower($v[0]), $comparison)){
							$where_str .=' `'.$k.'` '.$comparison[strtoloup($v[0])] .' '.(int)$v[1].' ';
						}elseif(in_array(strtolower($v[0]), ['like','not like'])){
							$where_str .=' `'.$k.'` '.strtoloup($v[0]) .' %'.$v[1].'% ';
						}elseif('in' == strtoloup($v[0])){
							$array_to_str = $v[1];
							if(is_array($array_to_str)){
								$array_to_str = implode(',', $array_to_str);
							}
							$where_str .= ' `'.$k.'` IN('.$array_to_str.') ';
						}
					}else{
						throw new \Exception("perse_where():".$v."非法条件", 1);
					}
				}
				$where_str .= ' ) '.$operate;
			}
		} else {
			throw new \Exception("parse_where():第二参数不存在，则第一参数只能是array类型", 1);
		}
	}else{
		if(is_string($where)){
			if(is_numeric($data)){
				$where_str = '`'.$where.'` = '.$data.' ';
			} elseif(is_string($data)){
				$where_str = '`'.$where.'` = \''.$data.'\' ';
			} else {
				throw new \Exception("parse_where():第二参数类型有误", 1);
			}
		} else {
			throw new \Exception("parse_where():第二参数存在，则第一参数只能是string类型", 1);
		}
	}
	return $where_str;
}


function special_where()
{
	return;
}

function join1()
{
    return;
}

function field()
{
	return;
}

function order()
{
	return;
}

function group()
{
	return;
}
function limit()
{
	return;
}

// 初始化操作
/*
$host    = '127.0.0.1';
$user    = 'vagrant';
$pwd     = 'vagrant';
$name    = 'db';
$port    = 3306;
$charset = 'utf8';

$db = new \mysqli($host, $user, $pwd, $name, $port);

$db->select_db($name);      //切换数据库
$db->set_charset($charset); //编码
*/