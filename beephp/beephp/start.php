<?php
/**
 * 框架引导文件
 * 
 * author vonnwang <xinglinxueba@163.com>
 * date:2019.04.22
 */

namespace bee;

// 1. 加载基础文件
require __DIR__ . '/base.php';

// 2. 执行应用
App::run()->send();
