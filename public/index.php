<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
// 加载框架引导文件



define("VIP_CSS","/public/static/vip/css/");
define("VIP_JS","/public/static/vip/js/");
define("VIP_IMG","/public/static/vip/img/");
define("TEMPLATE","/public/static/Template/");
define('VIP_AWE',"/public/static/vip/font-awesome/css/");
define('DP_CSS',"/public/static/Template/");
define("DL","/");
define("PO",".");

define('CACHE_FILE',__DIR__."/../extend/caches/cache.php");


define("WEB_URL","10.166.146.6:8080");

define("WEB_DIR",dirname(__DIR__));
session_start();
require __DIR__ . '/../thinkphp/start.php';


