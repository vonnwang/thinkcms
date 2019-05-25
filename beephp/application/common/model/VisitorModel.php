<?php

namespace app\common\model;

/**
 * 游客
 *
 * @author vonnwang
 * @date 2019/05/08
 */

class VisitorModel extends BaseModel
{
    protected static $table = 'visitor';
    protected static $db = 'database.cgh';

    /**
     * 根据玩家id获取玩家基础信息
     *
     *  @param $uid int 玩家id
     *  @return array
     */
    public static function get( $condition)
    {
        return self::run(self::$db, self::$table)->where($condition)->find();
    }



}

?>