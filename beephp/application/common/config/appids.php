<?php
defined( 'IN_APP' ) or die( 'Include Error!' );
/**
 * 所有平台appid配置表
 *
 * @author vonnwang
 * @time   2019-04-22
 */

$configAppids = array();

$configAppids['appids'] = array(1);
$configAppids['allAppids'] = array(
		//appid
		1 		=> 'billiard',
);

$configAppids['allAppidscn'] = array(
		//appid
		1 		=> '台球',
);
//登录方式
$configAppids['lmodes'] = array(
		1       => '游客登陆',
        2       => '微信登陆',
        3       => 'facebook登陆'
);

return $configAppids;