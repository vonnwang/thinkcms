<?php

namespace app\common\logic;
use app\common\service\AuthService as Auth;
use app\common\service\UserService as User;
use app\common\service\LoginService as Login;

/**
 * 登陆基类
 *
 * @author by vonnwang
 * @date 2019/05/09
 */

class LoginLogic
{
    /**
     * 登陆
     *
     */
    public function login()
    {
        $aRet = array();
        $isCreate = 0;
        $stime = microtime( true);

        $siteuid = $_REQUEST['siteuid'];
        $sig = '';
        $sig_siteuid = '';

        //接收客户端传过来的基础参数
        $param = str_replace( '\\', '', $_REQUEST['gameParam']);
        $aRequest = json_decode ( $param, true);

        if( ! Auth::checkSig( $aRequest)) {
            $aRet['code'] = -2;
            $aRet['codemsg'] = '验证错误';

            return $aRet;
        }

        $best64Siteuid 	= fliter_escape( $aRequest['sig_siteuid']);
        $version 		= isset( $_REQUEST['version'] ) ? fliter_escape( $_REQUEST['version'] ) : ""; // lua戏版本号
        $appVersion 	= isset( $_REQUEST['appVersion'] ) ? fliter_escape( $_REQUEST['appVersion'] ) : ""; // 安装包版本 APK,IPA
        $siteUid        = Auth::checkSiteuid( $best64Siteuid);
        //登陆校验
        $aParam['siteUid']      = $siteUid;
        $aParam['version']      = $version;
        $aParam['appVersion']   = $appVersion;
        $currentUser = User::getUser( $aParam);

        if ( $currentUser['code'] < 0 )
        {
            return $currentUser;
        }

        //根据uid获取用户基础信息
        $aUser = User::getUserBySiteUid( $siteUid);

        if ( ! $aUser)
        {
            $aInfo = array();
            $aInfo['sitemid'] 	= $sitemid;
            $aInfo['name'] 		= $currentUser['name'];
            $aInfo['sex'] 		= $currentUser['sex']; 		// 0为知1男2女
            $aInfo['icon'] 		= $currentUser['icon']; 	// 头像
            $aInfo['bicon'] 	= $currentUser['bicon']; 	// 大头像
            $aInfo['invite'] 	= $currentUser['invite']; 	// 邀请人ID
            $aInfo['email'] 	= $currentUser['email']; 	// 用户邮箱
            $aInfo['unionid'] 	= $currentUser['unionid']; 	// 微信登录unionid
            //如果还没注册的开始注册
            $aUser = User::register( $aInfo);
            $isCreate = 1;
        }

        if (( ! is_array( $aUser )) ||( ! $uid = (int)$aUser['uid']))
        {
            $aRet['code'] 		= -6;
            $aRet['codemsg'] 	= '不存在该用户';
            $aRet['data'] 		= array();
            return $aRet;
        }

        //用户状态是否正常
        if ( $aUser['status'] != 0)
        {
            $aRet['code'] 		= -7;
            $aRet['codemsg'] 	= '此账户被冻结';
            $aRet['data'] 		= array();
            return $aRet;

        }

        $aLoad = Login::returnLoad( $aUser, $isCreate);
        $update = 0;

        $aRet['code'] 		= 1;
        $aRet['codemsg'] 	= 'success';
        $aRet['data'] 		= $aLoad;
        $aRet['time'] 		= $stime;
        $aRet['exetime'] 	= microtime( true) - $stime; //脚本执行时间

        return $aRet;
    }



}
