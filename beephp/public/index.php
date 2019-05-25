<?php
/**
 * 应用入口文件
 *
 * author vonnwang <xinglinxueba@163.com>
 * date:2019.04.22
 */

$appid = (int)$_REQUEST['appid'];
$lmode = (int)$_REQUEST['lmode'];

// 定义应用目录
define('IN_APP', 1);
define('APP_PATH', __DIR__ . '/../application/');
$arrConfigAppids = include_once APP_PATH.'common/config/appids.php';

if (( (int)$appid == 0) || ( ! array_key_exists( $appid, $arrConfigAppids['allAppids']))) {
    die('no appid.');
}

if ( ( (int)$lmode == 0) || ( ! array_key_exists( $lmode, $arrConfigAppids['lmodes']))) {
    die('no lmode.');
}

defined('APPID') or define('APPID', $appid);
defined('LMODE') or define('LMODE', $lmode);
//根据appid读取对应的配置目录
defined('MODULE_PATH') or define('MODULE_PATH', APP_PATH.$arrConfigAppids['allAppids'][APPID]);

if ( isset( $_REQUEST['demo']) && ( $_REQUEST['demo'] == 1)) {
    define('PRODUCE', 'local');
    define('CONF_PATH', MODULE_PATH.'/config/local/');
    define("PRODUCTION_SERVER", false); 	//内网测试环境
} else {
    define("PRODUCTION_SERVER", true);
    define('PRODUCE', 'online');
    define('CONF_PATH', MODULE_PATH.'/config/online/');
}

// 加载框架引导文件
require __DIR__ . '/../beephp/start.php';

