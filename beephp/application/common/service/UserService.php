<?

namespace app\common\service;

use bee\App;
use extend\login\Login;
use app\common\model\VisitorModel as Visitor;
use app\common\model\MinfoModel as Minfo;

/**
 * 用户公共基础Service层
 *
 * @author vonnwang
 * @date 2019/05/15
 */

class UserService
{
    /**
     * 验证并获取玩家信息
     *
     * @param $param array 登陆参数
     * @DateTime  2019-05-14
     * @return   $aUser array
     */
    public static function getUser( $param)
    {
        $siteUid = fliter_escape( $param['siteUid']);
        $loginStatus = true;
        $aRet['appid']      = APPID;
        $aRet['lmode']      = LMODE;

        if ( LMODE == 1 && $siteUid === false )
        {
            $loginStatus = false;
            $aRet['code'] = - 1;
            $aRet['codemsg'] = '游客设备id错误';
        }
        elseif ( LMODE == 2 && $siteUid === false)
        {
            $loginStatus = false;
            $aRet['code'] = - 1;
            $aRet['codemsg'] = '微信登陆设备id错误';
        }
        elseif (( LMODE == 3) && ( $siteUid == ''))
        {
            $loginStatus = false;
            $aRet['code'] = - 1;
            $aRet['codemsg'] = 'facebook登陆设备id错误';
        }

        if( $loginStatus == false )
        {
            return $aRet;
        }

        if ( LMODE == 1)
        {
            //游客，调对应的第三方模块
            $retUser = Login::Visitor()->getUser();
        }
        else if ( LMODE == 2)
        {
            //根据$siteUid获取微信头像昵称相关信息
            $retUser = Login::Wechat()->getUser();
        }
        else if( LMODE == 3)
        {
            //根据$siteUid获取facebook头像昵称相关信息
            $retUser = Login::Facebook()->getUser();
        }
        else
        {
            $aRet['code']       = -3;
            $aRet['codemsg']    = 'lmode错误';
            $aRet['data']       = array();
        }

        if ( $retUser['code'] != 0)
        {
            return $retUser;
        }

        $aRet['code']       = 0;
        $aRet['codemsg']    = 'success';
        $aRet['data']       = $retUser['data'];

        return $aRet;
    }

    /**
     * 通过设备id获取玩家基础信息
     *
     * @param $siteUid string 玩家设备id
     * @return $aUser array 玩家信息
     */
    public static function getUserBySiteUid( $siteUid, $lmode = LMODE)
    {
        if ( !fliter_escape($siteUid))
        {
            return [];
        }

        $con['siteuid'] = $siteUid;

        switch ( $lmode)
        {
            case 1:
                $result = Visitor::get( $con);
                break;
            case 2:
                $result = Wechat::get( $con);
                break;
            case 3:
                $result = Facebook::get( $con);
                break;
            default:
        }

        return empty($result) ? [] : self::getUserByUid( $result["uid"]);
    }

    /**
     * 通过uid获取玩家基础信息
     *
     * @param $uid string 玩家id
     * @return $aUser array 玩家信息
     */
    public static function getUserByUid( $uid, $lmode = LMODE)
    {
        if ( !fliter_escape($uid))
        {
            return [];
        }

        $con["uid"] = $uid;
        $userInfo = self::uerInfo( $uid);
        $gameInfo = self::gameInfo( $uid);

        return array_merge((array)$userInfo, (array)$gameInfo);
    }

    /**
     * 玩家游戏信息
     *
     * @param $uid string 玩家id
     * @return $aUser array 玩家信息
     */
    private static function gameInfo( $uid, $lmode = LMODE)
    {
        if ( !fliter_escape($uid))
        {
            return [];
        }

        $aMgame = array(
            'exp'	   		=> 1,
            'level'	 		=> 1,
            'money'	 		=> 0,
            'safebox'   	=> 0,
            'wintimes'  	=> 0,
            'losetimes' 	=> 0,
            'dogfalltimes' 	=> 0,
            'diamond'		=> 0,
            'card'			=> 0,
        );
        //$aMgame = Model_Mserver::factory()->getGameInfo( $uid);
        //$aMgame = '';//请求server封装成一个第三方扩展类
        return $aMgame;
    }

    /**
     * 玩家基础信息
     *
     * @param $uid string 玩家id
     * @return $aUser array 玩家信息
     */
    private static function uerInfo( $uid, $lmode = LMODE)
    {
        if ( !fliter_escape($uid))
        {
            return [];
        }

        return Minfo::getMinfo( array("uid"=>$uid));
    }

    /**
     * 用户注册
     *
     * @param
     * @return
     */
    public static function register( $param)
    {

    }

    /**
     * 更新用户基础信息
     *
     * @param
     * @return
     */
    public static function updateMinfo( $condition = [], $param = [])
    {

    }


}



?>