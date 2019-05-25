<?php
/**
 * 自定义错误处理
 *
 * @author by vonnwang <xinglingxueba@l63.com>
 * @date 2019/04/26
 */

namespace app\common\until;
use bee\Error as baseError;

class Error extends baseError
{
    protected static $logPath = MODULE_PATH .'/logs/';//日志文件目录

    /**
     * 自定义错误日志
     *
     * @author by vonnwang <xinglingxueba@l63.com>
     * @date 2019/04/26
     */
    public static function errorLog( $exception){
        if ( empty( $exception)) {
            return false;
        }

        $aError['code'] = $exception->getCode();
        $aError['msg'] =  $exception->getMessage();
        $aError['line'] = $exception->getLine();
        $aError['file'] = $exception->getFile();

        $date       = date( 'Ymd');
        $fileName   = $date . '.php';
        $fileDir    = self::$logPath . 'phperror/';
        $file       = $fileDir . $fileName;
        $error      = '';

        if ( ! file_exists( $file)) {
            $error = "<?php\nexit();\n";
        }

        if ( ! is_dir( $fileDir)) {
            mkdir( $fileDir,  0777);
        }

        $error .= date( 'Y-m-d H:i:s') . '---';
        $error .= 'Code:'  . $aError['code'] . '--';
        $error .= 'Error:' . $aError['msg'] . '--';
        $error .= 'Line:'  . $aError['line']. '--';
        $error .= 'File:'  . $aError['file'] . '--';
        $error .= "\n";

        $backtrace = array();
        $straces = debug_backtrace();

        foreach ((array) $straces as $k => $v) {
            if ($k == 0) {
                continue;
            }

            //$json = $v['args'] ? $v['args'] : array();
            if( !empty($v['file']) && !empty($v['line'])){
                $error .= sprintf("#%s %s:%s:%s@%s \n", $k, $v['file'], $v['line'],$v['class'], $v['function']);
            }else{
                $error .= sprintf("#%s:%s@%s \n", $k, $v['class'], $v['function']);
            }
        }

        @file_put_contents( $file, $error . " \n ", FILE_APPEND | LOCK_EX);
    }

}
