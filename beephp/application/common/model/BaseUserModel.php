<?php

namespace app\common\model;

use bee\Db;
use bee\Config;

/**
 * 用户基础公共类
 *
 *  @author vonnwang
 *  @date 2019/05/08
 */

class BaseUserModel
{
    protected static $obj = [];

    /**
     *  数据库操作单例
     *
     *  @param $table string 要操作的数据表
     *  @param $db string 数据库配置
     *  @return viod
     */
    public static function factory( $table = '', $db = '')
    {
        if (( ! isset( self::$obj)) || ( ! is_object( self::$obj))) {
            $db = $db ? "database.".$db : "database.CGH";
            $config = Config::get($db);
            self::$obj = Db::connect($config)->table($table);
        }
    }

    /**
     * 根据玩家id获取玩家基础信息
     *
     *  @param $uid int 玩家id
     *  @return array
     */
    public function getUserByUid( $uid)
    {
        if ( empty($uid)) {
            return false;
        }

        return self::$obj->where(array("mid"=>$uid))->find();
    }

    /**
     *  玩家注册
     *
     */
    public function register( $param){}

    /**
     *  更新玩家基础信息
     *
     */
    public function updateAttr( $param){}

}

?>