<?php

namespace app\billiard\model;

use app\common\model\BaseUserModel;

/**
 * 用户操作类
 *
 * @author vonnwang
 * @date 2019/05/08
 */

class UserModel extends BaseUserModel
{
    protected static $_instance = [];

    /**
     *  数据库操作单例
     *
     *  @param $table string 要操作的数据表
     *  @param $db string 数据库配置
     *  @return obj
     */
    public static function factory( $table = 'visitorid', $db = 'CGH')
    {
        parent::factory($table, $db);

        if (( ! isset( self::$_instance[__METHOD__])) || ( ! is_object( self::$_instance[__METHOD__]))) {
            self::$_instance[__METHOD__] = new self();
        }

        return self::$_instance[__METHOD__];
    }


}

?>