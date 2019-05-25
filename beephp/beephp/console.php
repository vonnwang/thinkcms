<?php

namespace bee;

//框架引导文件
// 加载基础文件
require __DIR__ . '/base.php';

// 执行应用
App::initCommon();
Console::init();
