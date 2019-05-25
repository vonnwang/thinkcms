<?php
/**
 * 框架错误处理
 *
 * author vonnwang <xinglinxueba@163.com>
 * date:2019.04.30
 */

namespace bee;

use bee\console\Output as ConsoleOutput;
use bee\exception\ErrorException;
use bee\exception\Handle;
use bee\exception\ThrowableError;

class Error
{
    /**
     * 注册异常处理
     * @access public
     * @return void
     */
    public static function register()
    {
        //根据配置设置报错级别
        $errorLevel = Config::readByName("config",'error_level');

        switch ( $errorLevel){
            case 1:
                error_reporting(E_ALL);
                break;
            case 2:
                error_reporting(E_ALL & ~E_NOTICE);
                break;
            case 3:
                error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
                break;
            default:
                error_reporting(E_ALL);
        }

        set_error_handler([__CLASS__, 'appError']);
        set_exception_handler([__CLASS__, 'appException']);
        register_shutdown_function([__CLASS__, 'appShutdown']);
    }

    /**
     * 异常处理
     * @access public
     * @param  \Exception|\Throwable $e 异常
     * @return void
     */
    public static function appException($e)
    {
        if (!$e instanceof \Exception) {
            $e = new ThrowableError($e);
        }

        $handler = self::getExceptionHandler();
        $handler->report($e);
        //根据配置参数，如果自定义，采用自定义方法记录错误日志信息
        $errorHandleFunc = Config::get('error_log');

        if ( !empty($errorHandleFunc)) {
            $errorHandleFunc($e);
        }

        if (IS_CLI) {
            $handler->renderForConsole(new ConsoleOutput, $e);
        } else {
            $handler->render($e)->send();
        }
    }

    /**
     * 错误处理
     * @access public
     * @param  integer $errno      错误编号
     * @param  integer $errstr     详细错误信息
     * @param  string  $errfile    出错的文件
     * @param  integer $errline    出错行号
     * @return void
     * @throws ErrorException
     */
    public static function appError($errno, $errstr, $errfile = '', $errline = 0)
    {
        $exception = new ErrorException($errno, $errstr, $errfile, $errline);
        //根据配置参数，如果自定义，采用自定义方法，没有则采用系统默认
        $errorHandleFunc = Config::get('error_log');

        if ( !empty($errorHandleFunc)) {
            $errorHandleFunc($exception);
        }

        // 符合异常处理的则将错误信息托管至 bee\exception\ErrorException
        if (error_reporting() & $errno) {
            throw $exception;
        }

        self::getExceptionHandler()->report($exception);
    }

    /**
     * 异常中止处理
     * @access public
     * @return void
     */
    public static function appShutdown()
    {
        $error = error_get_last();

        if( !is_null($error) && self::isFatal($error['type'])){
            // 将错误信息托管至 bee\ErrorException
            $exception = new ErrorException($error['type'], $error['message'], $error['file'], $error['line']);
            self::appException($exception);
            //根据配置参数，如果自定义，采用自定义方法，没有则采用系统默认
            $errorHandleFunc = Config::get('error_log');

            if ( !empty($errorHandleFunc)) {
                $errorHandleFunc($exception);
            }
        }

        // 写入日志
        Log::save();
    }

    /**
     * 确定错误类型是否致命
     * @access protected
     * @param  int $type 错误类型
     * @return bool
     */
    protected static function isFatal($type)
    {
        return in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]);
    }

    /**
     * 获取异常处理的实例
     * @access public
     * @return Handle
     */
    public static function getExceptionHandler()
    {
        static $handle;

        if (!$handle) {
            // 异常处理 handle
            $class = Config::get('exception_handle');

            if ($class && is_string($class) && class_exists($class) &&
                is_subclass_of($class, "\\bee\\exception\\Handle")
            ) {
                $handle = new $class;
            } else {
                $handle = new Handle;

                if ($class instanceof \Closure) {
                    $handle->setRender($class);
                }

            }
        }

        return $handle;
    }
}
