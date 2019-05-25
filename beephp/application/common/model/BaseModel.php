<?php

namespace app\common\model;

use bee\Db;
use bee\Config;

/**
 * 微信
 *
 * @author vonnwang
 * @date 2019/05/08
 */

class BaseModel
{
    protected static $_instance = [];

    /**
     *  model操作初始化
     *
     *  @param $table string 要操作的数据表
     *  @param $db string 数据库配置
     *  @return viod
     */
    protected static function run( $db, $table)
    {
        if (( ! isset( self::$_instance[$db])) || ( ! is_object( self::$_instance[$db]))) {
            $config = Config::get($db);
            self::$_instance[$db] = Db::connect($config)->table($table);
        }

        return self::$_instance[$db];
    }

    /**
     * 根据玩家id获取玩家基础信息
     *
     *  @param $condition array 搜索条件
     *  @return array
     */
    public static function get( $condition) {}

    /**
     * 根据玩家id获取玩家基础信息
     *
     *  @param $data array 玩家注册信息
     *  @return array
     */
    public static function add( $data) {}

    /**
     * 根据玩家id获取玩家基础信息
     *
     *  @param $condition array 搜索条件
     *  @param $data array 更新信息
     *  @return array
     */
    public static function update( $condition, $data) {}


}

?>