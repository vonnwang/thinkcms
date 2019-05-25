<?

namespace app\common\service;

use bee\Config;

/**
 * 公共基础登陆Service层
 *
 * @author vonnwang
 * @date 2019/05/16
 */

class LoginService
{

    /**
     * 登陆游戏初始化返回
     *
     * @param array 登陆参数
     * @return
     */
    public static function returnLoad( $aUser, $isCreate=0)
    {
        $isFirst = 0;// 今天第一次登陆

        if ( $aUser['uid'] == 0)
        {
            return false;
        }
        if ( $aUser['logintime'] < strtotime('today'))
        {
            $isFirst = 1;
        }
        else if ( $isCreate == 1)
        {
            $isFirst = 1;
        }

        $getConfigServer = Config::get('server');
        $aLoad = array();
        $aLoad['uid'] 			= $aUser['uid'];
        $aLoad['sesskey'] 		= '';
        $aLoad['aUser'] 		= $aUser;
        //server配置
        $aLoad['hallIpPort']	= $getConfigServer['HallServer'];
        $aLoad['isFirst'] 		= $isFirst;

        return $aLoad;
    }



}



?>