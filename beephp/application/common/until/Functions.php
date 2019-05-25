<?php
/* +----------------------------------------------------------------------
 | 框架扩展 助手函数
 +----------------------------------------------------------------------
 | Author: Dayongwang <807763083@qq.com>
 +----------------------------------------------------------------------
 | Date:2018/07/18
 +----------------------------------------------------------------------
*/

use bee\Config;

if (!function_exists('check_str')) {
   /**
    * 安全过滤输入
    * @param string    $string 要验证的字符串
    * @param string    $isurl  是否是url
    * @return boolean
    */
    function check_str($string, $isurl = false)
    {
        $string = preg_replace('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/','',$string); //去掉控制字符
        $string = str_replace(array("\0","%00","\r"),'',$string); //\0表示ASCII 0x00的字符，通常作为字符串结束标志；这三个都是可能有害字符
        empty($isurl) && $string = preg_replace("/&(?!(#[0-9]+|[a-z]+);)/si",'&',$string); //HTML里面可以用&#xxx;来对一些字符进行编码，比如 (空格), ? Unicode字符等，A(?!B) 表示的是A后面不是B,所以作者想保留 ?类似的 HTML编码字符，去掉其他的问题字符
        $string = str_replace(array("%3C",'<'),'<',$string); //ascii的'<'转成'<';
        $string = str_replace(array("%3E",'>'),'>',$string);
        $string = str_replace(array('"',"'","\t",' '),array('“','‘',' ',' '),$string);
        return trim($string);
    }
}

if (!function_exists('input')) {
   /**
    * 获取输入参数 支持过滤和默认值
    * 使用方法:
    * <code>
    * I('id',0); 获取id参数 自动判断get或者post
    * I('post.name','','htmlspecialchars'); 获取$_POST['name']
    * I('get.'); 获取$_GET
    * </code>
    * @param string $name 变量的名称 支持指定类型
    * @param mixed $default 不存在的时候默认值
    * @param mixed $filter 参数过滤方法
    * @param mixed $datas 要获取的额外数据源
    * @return mixed
    */
    function input($name = '', $default = '', $filter = null, $datas = null)
    {
        $varAutoString = Config::get('var_auto_string');
        static $_PUT = null;
        if (strpos($name, '/')) {
            // 指定修饰符
            list($name, $type) = explode('/', $name, 2);
        } elseif ($varAutoString) {
            // 默认强制转换为字符串
            $type = 's';
        }
        if (strpos($name, '.')) {
            // 指定参数来源
            list($method, $name) = explode('.', $name, 2);
        } else {
            // 默认为自动判断
            $method = 'param';
        }
        switch (strtolower($method)) {
            case 'get':
                $input = &$_GET;
                break;
            case 'post':
                $input = &$_POST;
                break;
            case 'put':
                if (is_null($_PUT)) {
                    parse_str(file_get_contents('php://input'), $_PUT);
                }
                $input = $_PUT;
                break;
            case 'param':
                switch ($_SERVER['REQUEST_METHOD']) {
                    case 'POST':
                        $input = $_POST;
                        break;
                    case 'PUT':
                        if (is_null($_PUT)) {
                            parse_str(file_get_contents('php://input'), $_PUT);
                        }
                        $input = $_PUT;
                        break;
                    default:
                        $input = $_GET;
                }
                break;
            case 'path':
                $input = array();
                if (!empty($_SERVER['PATH_INFO'])) {
                    $depr  = Config::get('pathinfo_depr');
                    $input = explode($depr, trim($_SERVER['PATH_INFO'], $depr));
                }
                break;
            case 'request':
                $input = &$_REQUEST;
                break;
            case 'session':
                $input = &$_SESSION;
                break;
            case 'cookie':
                $input = &$_COOKIE;
                break;
            case 'server':
                $input = &$_SERVER;
                break;
            case 'globals':
                $input = &$GLOBALS;
                break;
            case 'data':
                $input = &$datas;
                break;
            default:
                return null;
        }
        
        if ('' == $name) {
            // 获取全部变量
            $data    = $input;
            $filters = isset($filter) ? $filter : 'htmlspecialchars';
            if ($filters) {
                if (is_string($filters)) {
                    $filters = explode(',', $filters);
                }
                foreach ($filters as $filter) {
                    $data = array_map_recursive($filter, $data); // 参数过滤
                }
            }
        } elseif (isset($input[$name])) {
            // 取值操作
            $data    = $input[$name];
            $filters = isset($filter) ? $filter : 'htmlspecialchars';
            if ($filters) {
                if (is_string($filters)) {
                    if (0 === strpos($filters, '/')) {
                        if (1 !== preg_match($filters, (string) $data)) {
                            // 支持正则验证
                            return isset($default) ? $default : null;
                        }
                    } else {
                        $filters = explode(',', $filters);
                    }
                } elseif (is_int($filters)) {
                    $filters = array($filters);
                }

                if (is_array($filters)) {
                    foreach ($filters as $filter) {
                        $filter = trim($filter);
                        if (function_exists($filter)) {
                            $data = is_array($data) ? array_map_recursive($filter, $data) : $filter($data); // 参数过滤
                        } else {
                            $data = filter_var($data, is_int($filter) ? $filter : filter_id($filter));
                            if (false === $data) {
                                return isset($default) ? $default : null;
                            }
                        }
                    }
                }
            }
            if (!empty($type)) {
                switch (strtolower($type)) {
                    case 'a': // 数组
                        $data = (array) $data;
                        break;
                    case 'd': // 数字
                        $data = (int) $data;
                        break;
                    case 'f': // 浮点
                        $data = (float) $data;
                        break;
                    case 'b': // 布尔
                        $data = (boolean) $data;
                        break;
                    case 's': // 字符串
                    default:
                        $data = (string) $data;
                }
            }
        } else {
            // 变量默认值
            $data = isset($default) ? $default : null;
        }
        is_array($data) && array_walk_recursive($data, 'bee_filter');
        return $data;
    }
}

if (!function_exists('array_map_recursive')) {
    function array_map_recursive($filter, $data)
    {
        $result = array();
        foreach ($data as $key => $val) {
            $result[$key] = is_array($val)
                ? array_map_recursive($filter, $val)
                : call_user_func($filter, $val);
        }
        return $result;
    }
}

if (!function_exists('bee_filter')) {
    /**
     * 安全过滤类-过滤javascript,css,iframes,object等不安全参数 过滤级别高
     * @param  string $value 需要过滤的值
     * @return string
     */
    function bee_filter($value) {
        $value = preg_replace("/(javascript:)?on(click|load|key|mouse|error|abort|move|unload|change|dblclick|move|reset|resize|submit)/i","&111n\\2",$value);
        $value = preg_replace("/(.*?)<\/script>/si","",$value);
        $value = preg_replace("/(.*?)<\/iframe>/si","",$value);
        return $value;
    }
}

if (!function_exists('fliter_script')) {
   /**
    * 安全过滤类-过滤javascript,css,iframes,object等不安全参数 过滤级别高
    * @param  string $value 需要过滤的值
    * @return string
    */
    function fliter_script($value) {
        $value = preg_replace("/(javascript:)?on(click|load|key|mouse|error|abort|move|unload|change|dblclick|move|reset|resize|submit)/i","&111n\\2",$value);
        $value = preg_replace("/(.*?)<\/script>/si","",$value);
        $value = preg_replace("/(.*?)<\/iframe>/si","",$value);
        return $value;
    }
}

if (!function_exists('fliter_html')) {
   /**
    * 安全过滤类-过滤HTML标签
    * @param  string $value 需要过滤的值
    * @return string
    */
    function fliter_html($value) {
        if (function_exists('htmlspecialchars')) return htmlspecialchars($value);
        return str_replace(array("&", '"', "'", "<", ">"), array("&", "\"", "'", "<", ">"), $value);
    }
}

if (!function_exists('fliter_sql')) {
   /**
    * 安全过滤类-对进入的数据加下划线 防止SQL注入
    * @param  string $value 需要过滤的值
    * @return string
    */
    function fliter_sql($value) {
        $sql = array("select", 'insert', "update", "delete", "\'", "\/\*","\.\.\/", "\.\/", "union", "into", "load_file", "outfile");
        $sql_re = array("","","","","","","","","","","","");
        return str_replace($sql, $sql_re, $value);
    }
}

if (!function_exists('fliter_escape')) {
   /**
    * 安全过滤类-通用数据过滤
    * @param string $value 需要过滤的变量
    * @return string|array
    */
    function fliter_escape($value) {
        if (is_array($value)) {
           foreach ($value as $k => $v) {
                $value[$k] = fliter_str($v);
           }
        } else {
            $value = fliter_str($value);
        }
        return $value;
    }
}

if (!function_exists('fliter_str')) {
   /**
    * 安全过滤类-字符串过滤 过滤特殊有危害字符
    * @param  string $value 需要过滤的值
    * @return string
    */
    function fliter_str( $string)
    {
        if ( isPhpVersion())
        {
            return addslashes( trim($string));
        }
        return mysql_real_escape_string( trim($string));
    }
}

if (!function_exists('fliter_int')) {
   /**
    * 安全过滤类-整形过滤 过滤特殊有危害字符
    * @param  int $value 需要过滤的值
    * @return int
    */
    function fliter_int( $num)
    {
        return max(0, (int)$num);
    }
}

if (!function_exists('fliter_float')) {
   /**
    * 安全过滤类-浮点型过滤 过滤特殊有危害字符
    * @param  int $value 需要过滤的值
    * @return int
    */
    function fliter_float( $float)
    {
        return max(0, floatval($float));
    }
}

if (!function_exists('isPhpVersion')) {
    /**
     * 判断当前环境php版本是否大于大于等于指定的一个版本
     * @param sreing $version default=5.3.0
     * @return boolean 大于true,小于false
     */
    function isPhpVersion( $version = '5.3.0' )
    {
        $is_pass = version_compare(PHP_VERSION,'5.3.0') ? true : false;
        return $is_pass;
    }
}

if (!function_exists('filter_dir')) {
   /**
    * 私有路劲安全转化
    * @param string $fileName
    * @return string
    */
    function filter_dir($fileName) {
        $tmpname = strtolower($fileName);
        $temp = array(':/',"\0", "..");
        if (str_replace($temp, '', $tmpname) !== $tmpname) {
            return false;
        }
        return $fileName;
    }
}

if (!function_exists('filter_path')) {
   /**
    * 过滤目录
    * @param string $path
    * @return array
    */
    function filter_path($path) {
        $path = str_replace(array("'",'#','=','`','$','%','&',';'), '', $path);
        return rtrim(preg_replace('/(\/){2,}|(\\\){1,}/', '/', $path), '/');
    }
}

if (!function_exists('filter_phptag')) {
   /**
    * 过滤PHP标签
    * @param string $string
    * @return string
    */
    function filter_phptag($string) {
        return str_replace(array(''), array('<?', '?>'), $string);
    }
}

if (!function_exists('str_out')) {
   /**
    * 安全过滤类-返回函数
    * @param  string $value 需要过滤的值
    * @return string
    */
    function str_out($value) {
        $badstr = array("<", ">", "%3C", "%3E");
        $newstr = array("<", ">", "<", ">");
        $value  = str_replace($newstr, $badstr, $value);
        return stripslashes($value); //下划线
    }
}

if (!function_exists('mydebug')) {
    /**
    * 写debug日志
    * @param array $param 要写日志的数据
    * @param string $file 写日志的文件名
    * @return void
    */
    function mydebug($params, $file = 'debug.txt')
    {
        clearstatcache();
        $path = APP_PATH."log/debug/";
        //判断目录存在否，存在给出提示，不存在则创建目录
        if (!is_dir($path)){
          //第三个参数是“true”表示能创建多级目录，iconv防止中文目录乱码
          mkdir(iconv("UTF-8", "GBK", $path),0777,true);
        }
        $file = $path.$file .date("Ymd"). '.php';
        $size = @filesize($file);
        $time = date('Y-m-d H:i:s');
        $contents = ($size ? '' : "<?php die();?>\n") . $time . "\n" . var_export($params, TRUE) . "\n\n";
        $re = @file_put_contents($file, $contents, $size < 64 * 1024 ? FILE_APPEND : NULL);
    }
}

if (!function_exists('paylog')) {
    /**
    * 写支付日志
    * @param array $param 要写日志的数据
    * @param string $file 写日志的文件名
    * @return void
    */
    function paylog($params, $file = 'debug.txt')
    {
        clearstatcache();
        $path = APP_PATH."log/paylog/";
        //判断目录存在否，存在给出提示，不存在则创建目录
        if (!is_dir($path)){
          //第三个参数是“true”表示能创建多级目录，iconv防止中文目录乱码
          mkdir(iconv("UTF-8", "GBK", $path),0777,true);
        }
        $file = $path.$file.date("Ymd") . '.php';
        $size = @filesize($file);
        $time = date('Y-m-d H:i:s');
        $contents = ($size ? '' : "<?php die();?>\n") . $time . "\n" . var_export($params, TRUE) . "\n\n";
        $re = @file_put_contents($file, $contents, $size < 64 * 1024 ? FILE_APPEND : NULL);
    }
}

if (!function_exists('http_curl')) {
    /**
     * 获取网页数据
     * @param string $url
     * @param array $post_data post的数据,为空时表示get请求
     * @param string $json 返回数据格式，0表示json 1原数据返回
     * @return array/int
     */
    function http_curl( $url, $post_data=array(), $json=1, $timeout=3)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

        if( ! empty( $post_data))
        {
            curl_setopt($ch, CURLOPT_POST, true);
            if( is_array( $post_data))
            {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
            }
            else
            {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            }
        }
        $result = curl_exec( $ch);

        curl_close($ch);
        return $data = empty($json) ? $result : json_decode($result, true);
    }
}

if (!function_exists('getClientIp')) {
    /*
     * 获取用户登陆ip
     */
    function getClientIp(){
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")){
            $ip = getenv("HTTP_CLIENT_IP");
        }else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")){
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        }else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")){
            $ip = getenv("REMOTE_ADDR");
        }else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")){
            $ip = $_SERVER['REMOTE_ADDR'];
        }else{
            $ip = "unknown";
        }
        return $ip;
    }
}


