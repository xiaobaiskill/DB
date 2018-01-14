<?php
/**
 * 在原有的基础数据类上扩展，防止原有的数据库类的不完美
 */
namespace Extend;

use Driver\Mysqli;

class Mysqli extends mysqli
{

}
