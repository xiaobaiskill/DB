<?php
global $comparison;

$comparison = [
	'eq'  => '=',
	'neq' => '!=',
	'gt'  => '>',
	'egt' => '>=',
	'lt'  => '<',
	'elt' => '<='
];

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
function perse_where($where, $data = null)
{
	$comparison = $GLOBALS['comparison'];
	$where_str  = '';
	if (null === $data) {
		if (is_array($where) && !empty($where)) {
			if (array_key_exists('_logic', $where)) {
				$operate = ' ' . strtoupper($where['_logic']) . ' ';
				unset($where['_logic']);
			} else {
				$operate = ' AND ';
			}
			$i     = 0;
			$count = count($where);
			foreach ($where as $k => $v) {
				$where_str .= ' ( ';
				if (strpos($k, '_') !== false) {
					$where_str .= special_where($k, $v);
				} else {
					if (is_string($v)) {
						$where_str .= ' `' . $k . '`=\'' . $v . '\' ';
					} elseif (is_numeric($v)) {
						$where_str .= ' `' . $k . '`=' . $v . ' ';
					} elseif (is_array($v)) {
						if (array_key_exists(strtolower($v[0]), $comparison)) {
							$where_str .= ' `' . $k . '` ' . $comparison[strtolower($v[0])] . ' ' . (int) $v[1] . ' ';
						} elseif (in_array(strtolower($v[0]), ['like', 'not like'])) {
							$where_str .= ' `' . $k . '` ' . strtoupper($v[0]) . ' \'' . $v[1] . '\' ';
						} elseif ('in' == strtolower($v[0])) {
							$array_to_str = $v[1];
							if (is_array($array_to_str)) {
								$array_to_str = implode(',', $array_to_str);
							}
							$where_str .= ' `' . $k . '` IN(' . $array_to_str . ') ';
						} elseif ('between' == strtolower($v[0])) {
							if (is_array($v[1])) {
								$where_str .= ' `' . $k . '` BETWEEN ' . $v[1][0] . ' AND ' . $v[1][1] . ' ';
							} else {
								$where_str .= ' `' . $k . '` BETWEEN ' . str_replace(',', ' AND ', $v[1]) . ' ';
							}
						} else {
							throw new \Exception("perse_where():" . $v . "暂未处理", 1);
						}
					} else {
						throw new \Exception("perse_where():" . $v . "非法条件", 1);
					}
				}
				$where_str .= ' ) ';
				$i++;
				if ($i !== $count) {
					$where_str .= $operate;
				}
			}
		} else {
			throw new \Exception("parse_where():第二参数不存在，则第一参数只能是array类型", 1);
		}
	} else {
		if (is_string($where)) {
			if (is_numeric($data)) {
				$where_str = '`' . $where . '` = ' . $data . ' ';
			} elseif (is_string($data)) {
				$where_str = '`' . $where . '` = \'' . $data . '\' ';
			} else {
				throw new \Exception("parse_where():第二参数类型有误", 1);
			}
		} else {
			throw new \Exception("parse_where():第二参数存在，则第一参数只能是string类型", 1);
		}
	}
	return $where_str;
}

function special_where($key, $val)
{
	switch ($key) {
		case '_string':
			$where_str = $val;
			break;
		case '_complex':
			$where_str = perse_where($val);
			break;
	}
	return $where_str;
}

function perse_join()
{
	return;
}

function perse_order($order)
{
	if (!empty($order)) {
		$order_str = '';
		if (is_string($order)) {
			$order_str .= ' ORDER BY ' . $order . ' ';
		} elseif (is_array($order)) {
			$order_str .= ' ORDER BY ';
			$i     = 0;
			$count = count($order);
			foreach ($order as $k => $v) {
				$order_str .= '`' . $k . '` ' . $v;
				$i++;
				if ($i !== $count) {
					$order_str .= ', ';
				}
			}
		}
		return $order_str;
	} else {
		return;
	}
}

function perse_group($group)
{
	if (!empty($group)) {
		$group_str = '';
		if (is_string($group)) {
			$group_str .= 'GROUP BY ' . $group . ' ';
		}
		return $group_str;
	} else {
		return;
	}
}
function perse_limit()
{
	return;
}

// 初始化操作

$host    = '127.0.0.1';
$user    = 'vagrant';
$pwd     = 'vagrant';
$name    = 'db';
$port    = 3306;
$charset = 'utf8';

$db = new \mysqli($host, $user, $pwd, $name, $port);

$db->select_db($name);      //切换数据库
$db->set_charset($charset); //编码

$where1 = [
	'class'  => ['gt', 2],
	'status' => 1
];

$where = [
	'id'       => ['in', [1, 2, 10, 40, 27, 37, 12, 8]],
	'_complex' => $where1,
	'_logic'   => 'or'
];

$sql  = 'select name,sum(class),status from `student` where ' . perse_where($where) . ' group by class ' . perse_order(['class' => 'asc', 'id' => 'desc']);
echo $sql;
echo '<hr>';
$sqlD6 = "select a.`sid`,b.`name`,sum(a.`score`) as `ascore`,count(a.`id`) as `cid`  from `score` a left join `student` b on a.`sid` =  b.`id` left join `subject` c on c.`id` = a.`subject_id` where a.`id` < 20 group by a.`sid` having avg(a.`score`) > 70 order by a.`sid` desc limit 0,2";
echo $sqlD6;
echo '<hr>';
$sqlD5 = "select `sid`,avg(`score`) as `score` from `score` where id >3 group by `sid`";
echo $sqlD5;
echo '<hr>';
$stmt = $db->query($sql);
dd($stmt->fetch_all(MYSQLI_ASSOC));
$db->close();
