<?php

namespace app\billiard\controller;

use app\billiard\logic\LoginLogic;

/**
 * 台球登陆业务层
 *
 * @author by vonnwang
 * @date 2019/04/29
 */

class Login
{
    /**
	* 游戏登陆入口
	*
	* @author by vonnwang
	* @date 2019/05/14
	*/
    public function index()
    {
        $return = LoginLogic::factory()->login();

        return json($return);
    }



}
