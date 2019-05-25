<?php

namespace app\common\model;

//use bee\Model;
use bee\Db;
use bee\Config;

class BaseDb
{
    protected static $_instance = [];
    protected static $query = [];

    public static function factory( $table = 'visitorid', $db = '')
    {
        if (( ! isset( self::$_instance[__METHOD__])) || ( ! is_object( self::$_instance[__METHOD__]))) {
            self::$_instance[__METHOD__] = new self();
        }

        if (( ! isset( self::$query)) || ( ! is_object( self::$query))) {
            $db = $db ? "database.".$db : "database.CGH";
            $config = Config::get($db);
            self::$query = Db::connect($config)->table($table);
        }

        return self::$_instance[__METHOD__];
    }

    /**
    * 调用驱动类的方法
    * @access public
    * @param  string $method 方法名
    * @param  array  $params 参数
    * @return mixed
    */
    public static function __callStatic($method, $params)
    {
        //return call_user_func_array([self::factory(), $method], $params);
    }

}

?>