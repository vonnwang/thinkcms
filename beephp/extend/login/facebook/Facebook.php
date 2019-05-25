<?php
namespace extend\login\facebook;

use extend\login\Base;

/**
 * 游客登陆
 *
 * @author vonnwang
 * @date 2019/05/15
 */
class Facebook extends Base
{
    /**
     * 获取玩家初始化信息
     *
     * @param
     * @return
     */
    public function getUser( $param = [])
    {
        $info["code"] 	        = 0;
        $info["msg"] 	        = 'success';
        $info['data']["name"] 	= 'facebook用户'.rand(10000,999999);
        $info['data']["sex"]  	= 1;
        $info['data']["icon"] 	= 'facebook icon';
        $info['data']["bicon"] 	= 'facebook bicon';

        return $info;
    }
}


?>