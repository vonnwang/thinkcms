<?php

namespace app\billiard\logic;

use app\common\logic\LoginLogic as baseLogin;

/**
 * 登陆逻辑层
 *
 * @author by vonnwang
 * @date 2019/05/09
 */

class LoginLogic extends baseLogin
{
    protected static $_instance = [];

    public static function factory()
    {
        if (( ! isset( self::$_instance[__METHOD__])) || ( ! is_object( self::$_instance[__METHOD__]))) {
            self::$_instance[__METHOD__] = new self();
        }

        return self::$_instance[__METHOD__];
    }


}
