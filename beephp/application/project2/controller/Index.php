<?php

namespace app\billiard\controller;

//use app\common\controller\Login as baseLogin;
use bee\Config;
use app\billiard\service\IndexService;
//use app\common\until\Logs;
//use app\billiard\model\User;

/**
 * 项目基础入口
 *
 * @author vonnwang
 * @date 2019/05/08
 */
class Index
{
    public function index()
    {
        $rData = array("name"=>'张三',"age"=>27);
        $aRet['code'] = ( $rData == false)? 0 : 1;
        $aRet['codemsg'] = '';
        $aRet['data'] = $rData;

        return json($aRet);
    }


}
