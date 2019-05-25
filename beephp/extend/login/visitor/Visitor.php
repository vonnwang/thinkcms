<?php

namespace extend\login\visitor;

use extend\login\Base;

/**
 * 游客相关
 *
 * @author vonnwang
 * @date 2019/05/15
 */
class Visitor extends Base
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
		$info['data']["name"] 	= '游客'.rand(10000,999999);
		$info['data']["sex"]  	= 0;
		$info['data']["icon"] 	= '';
		$info['data']["bicon"] 	= '';

		return $info;
	}

}


?>