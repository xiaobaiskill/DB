<?php
/**
 * 在原有的基础数据类上扩展，防止原有的数据库类的不完美
 */
namespace DB\extend;
use DB\driver\CT_mysqli;
class MY_mysqli extends CT_mysqli{

}
