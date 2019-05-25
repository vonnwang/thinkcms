<?php

namespace app\common\model;

use bee\Config;
use bee\Db;

/**
 * 用户操作类
 *
 * @author vonnwang
 * @date 2019/05/08
 */

class Test
{
    // 设置当前模型对应的完整数据表名称
    protected static $_instance = [];
    protected static $obj = [];
    protected static $table = 'visitorid';
    protected static $db = 'database.cgh';

    public static function __callStatic($method, $args)
    {
        $class1 = get_called_class();
        $class2 = get_class();
        print_r(array($class1,$class2));
        print_r(array($method, $args));
        $config = Config::get(self::$db);
        self::$obj = Db::connect($config)->table(self::$table);
    }


}

?>