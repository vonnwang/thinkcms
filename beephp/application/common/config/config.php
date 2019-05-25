<?php
/**
 * 公共应用基础配置
 * 各项目之间相同的部分可以统一写到此处
 * Author: vonnwang <xinglinxueba@163.com>
 * date:2019.04.25
 */

return [
    // 是否支持多模块
    'app_multi_module'       => true,
    // 扩展函数文件
    'extra_file_list'        => [
        BEE_PATH . 'helper' . EXT,
        COMMON_PATH .'until/Functions' . EXT
    ],
    // +----------------------------------------------------------------------
    // | 模块设置
    // +----------------------------------------------------------------------
    // 禁止访问模块
    'deny_module_list'       => ['common'],
    //自定义错误异常处理日志
    'error_log'              => 'app\common\until\Error::errorLog',
];
