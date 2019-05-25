<?php
namespace extend\login\wechat;

use extend\login\Base;

/**
 * 微信登陆
 *
 * @author vonnwang
 * @date 2019/05/15
 */
class Wechat extends Base
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
        $info['data']["name"] 	= '微信用户'.rand(10000,999999);
        $info['data']["sex"]  	= 1;
        $info['data']["icon"] 	= '微信icon';
        $info['data']["bicon"] 	= '微信bicon';

        return $info;
    }

}


?>