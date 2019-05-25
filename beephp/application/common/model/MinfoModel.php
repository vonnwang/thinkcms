<?php

namespace app\common\model;

/**
 * 用户基础信息
 *
 * @author vonnwang
 * @date 2019/05/08
 */

class MinfoModel extends BaseModel
{
    protected static $table = 'minfo';
    protected static $db = 'database.cghinfo';

    /**
     * 根据玩家id获取玩家基础信息
     *
     *  @param $uid int 玩家id
     *  @return array
     */
    public static function getMinfo( $condition)
    {
        $uid = fliter_int( $condition['uid']);

        if ( !$uid)
        {
            return [];
        }

        $table = self::$table. $uid%100;

        return self::run(self::$db, $table)->where($condition)->find();
    }



}

?>