<?php

namespace extend\login;


/**
 * 登陆驱动器
 *
 * @author vonnwang
 * @date 2019/05/15
 */
class Base
{
    protected $config = [];
    public function __construct( $config = [])
    {
        $this->config = $config;
    }

    public function factory()
    {
        return $this;
    }

    /**
     * 获取玩家初始化信息
     *
     * @param
     * @return
     */
    public function getUser( $param = []) {}


}
