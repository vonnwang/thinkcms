<?php

namespace app\billiard\service;

//use app\common\service\UserService as baseUser;

/**
 * 用户中心服务层
 *
 * @author by vonnwang
 * @date 2019/05/09
 */

class UserService
{
    /**
     * 通过设备id获取玩家基础信息
     *
     * @param $siteUid string 玩家设备id
     * @return $aUser array 玩家信息
     */
    public static function getUserBySiteUid( $siteUid)
    {
        if ( empty($siteUid))
        {
            return [];
        }

        $aUser = [];
        //通过siteUid查找玩家对应的uid

        //读取model或者redis获取玩家信息
        return $aUser;
    }


}
