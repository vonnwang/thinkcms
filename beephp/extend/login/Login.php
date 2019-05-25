<?php

namespace extend\login;


/**
 * 登陆驱动器
 *
 * @author vonnwang
 * @date 2019/05/15
 */
class Login
{
    /**
     * @var Connection[] 连接实例
     */
    private static $instance = [];

    /**
     * 登陆驱动实例
     * @access public
     * @param  $class string 登陆方式
     * @param  $param array  登陆参数
     * @return Connection
     * @throws Exception
     */
    public static function driver( $class = '', $param = [])
    {
        if ( empty($class)) {
            throw new \InvalidArgumentException('Undefined login type');
        }

        $name = md5(serialize($class));

        if ( !isset(self::$instance[$name])) {
            // 解析实例化参数 支持数组和字符串
            $class = false !== strpos( $class, '\\') ? $class : '\\extend\\login\\'.strtolower($class).'\\' . ucwords($class);

            self::$instance[$name] = new $class($param);
        }

        return self::$instance[$name];
    }

    /**
     * 清除连接实例
     * @access public
     * @return void
     */
    public static function clear()
    {
        self::$instance = [];
    }

    /**
     * 调用驱动类的方法
     * @access public
     * @param  string $method 方法名
     * @param  array  $params 参数
     * @return mixed
     */
    public static function __callStatic( $method, $params)
    {
        return call_user_func_array([self::driver($method, $params), "factory"], $params);
    }
}
