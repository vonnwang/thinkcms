<?php
namespace app\common\until;
use bee\Log as baseLog;

class Logs extends baseLog
{
    protected static $_instance = array();
    protected $logPath = MODULE_PATH .'/logs/';//日志文件目录

    /**
     * 工厂方法
     * 只实例化一次该类以节省大量new实例产生性能开销
     */
    public static function factory()
    {
        if (( ! isset( self::$_instance[__METHOD__])) || ( ! is_object( self::$_instance[__METHOD__])))
        {
            self::$_instance[__METHOD__] = new self();
        }
        return self::$_instance[__METHOD__];
    }

    public function __construct()
    {
        parent::init([
            'type'  =>  'File',
            'path'  =>  $this->logPath
        ]);
    }

    /**
     * UDP SWOOLE上报日志
     *
     * @param string $type
     * @param unknown $params
     * @param string $filename
     * @return
     */
    public function logsUdp ( $type='debug', $params, $filename='debug.txt')
    {
        $getSwooleLogConfig = Config::get("swoole");
        $host = $getSwooleLogConfig['ip'];
        $port = $getSwooleLogConfig['port'];
        $error_code = 0;
        $error_message = '';
        $socket = stream_socket_client("udp://{$host}:{$port}", $error_code, $error_message, 300);
        $log_buff_str = array('filename'=>$filename,'type'=>$type,'content'=>is_array($params) ? var_export($params, TRUE) : $params);
        $log_buff_str = json_encode( $log_buff_str);
        @fwrite($socket, $log_buff_str);
    }

    /**
     * 写日志,带时间，文件过大会删除
     * @param unknown_type $params 信息
     * @param unknown_type $file   地址
     */
    public function debug ($params, $file = 'debug.txt')
    {
        clearstatcache();
        $file = $this->logPath . $file . '.php';
        $size = 0;

        if( file_exists($file)){
            $content = file_get_contents($file);
            $size = mb_strlen($content );  //获取文件大小
        }

        $time = date('Y-m-d H:i:s');
        $contents = ($size ? '' : "<?php die();?>\n") . $time . "\n" . var_export($params, TRUE) . "\n\n";
        @file_put_contents($file, $contents, $size < 64 * 1024 ? FILE_APPEND : NULL);
    }

    /**
     * 每日日志，每天记录一个文件
     * @param mixed  $params 要记录的内容
     * @param string $file   文件名
     * @param string $folder 文件夹的名字
     * @param bool   $daylog 是否每天一个文件，如果为true，则每天会新建一个文件
     */
    public function dayLog ($params, $file = 'daylog.txt', $folder = 'log', $daylog = TRUE)
    {
        clearstatcache();
        $day = date('Ymd');
        $fileDir = $this->logPath . $folder;
        $file = $fileDir . '/' . $file;

        if ( ! is_dir( $fileDir)) {
            mkdir( $fileDir,  0777);
        }

        if ($daylog) {
            $file .= $day;
        }

        $file .= '.php';
        $size = 0;

        if( file_exists($file)){
            $content = file_get_contents($file);
            $size = mb_strlen($content );  //获取文件大小
        }

        $time = date('Y-m-d H:i:s');
        $contents = ($size ? '' : "<?php die();?>\n") . $time . "\n" . var_export($params, TRUE) . "\n\n";
        @file_put_contents($file, $contents, FILE_APPEND);
    }

    /**
     * 写日志,支付日志，每日一个文件
     * @param unknown_type $params 信息
     * @param unknown_type $file   地址
     */
    public function payLog ($params, $file = 'pay.txt')
    {
        clearstatcache();
        $day = date('Ymd');
        $fileDir = $this->logPath . 'paylog/';
        $file = $fileDir . $file . $day . '.php';
        $size = 0;

        if ( ! is_dir( $fileDir)) {
            mkdir( $fileDir,  0777);
        }

        if( file_exists($file)){
            $content = file_get_contents($file);
            $size = mb_strlen($content );  //获取文件大小
        }

        $time = date('Y-m-d H:i:s');
        $contents = ($size ? '' : "<?php die();?>\n") . $time . "\n" . var_export($params, TRUE) . "\n\n";
        @file_put_contents($file, $contents, FILE_APPEND);
    }

    /**
     * 不追加写日志，用于登陆，查看客户端请求及返回写日志
     * @param unknown $params
     * @param string  $file
     */
    public function debugNoAppend ($params, $file = 'log.txt')
    {
        $file = $this->logPath . 'log/' . $file;
        $size = 0;

        if (!is_dir($this->logPath . 'log')) {
            mkdir($this->logPath . 'log', 0777);
        }

        if( file_exists($file)){
            $content = file_get_contents($file);
            $size = mb_strlen($content );  //获取文件大小
        }

        $contents = var_export($params, TRUE);
        @file_put_contents($file, $contents, $size < 100 * 1024 ? FILE_APPEND : NULL);
    }

}
