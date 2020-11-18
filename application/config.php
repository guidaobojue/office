<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------

    // 应用调试模式
    'app_debug'              => true,
    // 应用Trace
    'app_trace'              => false,
    // 应用模式状态
    'app_status'             => '',
    // 是否支持多模块
    'app_multi_module'       => true,
    // 入口自动绑定模块
    'auto_bind_module'       => false,
    // 注册的根命名空间
    'root_namespace'         => [],
    // 扩展函数文件
    'extra_file_list'        => [THINK_PATH . 'helper' . EXT],
    // 默认输出类型
    'default_return_type'    => 'html',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return'    => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler'  => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler'      => 'callback',
    // 默认时区
    'default_timezone'       => 'PRC',
    // 是否开启多语言
    'lang_switch_on'         => false,
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'         => '',
    // 默认语言
    'default_lang'           => 'zh-cn',
    // 应用类库后缀
    'class_suffix'           => false,
    // 控制器类后缀
    'controller_suffix'      => false,

    // +----------------------------------------------------------------------
    // | 模块设置
    // +----------------------------------------------------------------------

    // 默认模块名
    'default_module'         => 'index',
    // 禁止访问模块
    'deny_module_list'       => ['common'],
    // 默认控制器名
    'default_controller'     => 'Index',
    // 默认操作名
    'default_action'         => 'index',
    // 默认验证器
    'default_validate'       => '',
    // 默认的空控制器名
    'empty_controller'       => 'Error',
    // 操作方法后缀
    'action_suffix'          => '',
    // 自动搜索控制器
    'controller_auto_search' => false,

    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------

    // PATHINFO变量名 用于兼容模式
    'var_pathinfo'           => 's',
    // 兼容PATH_INFO获取
    'pathinfo_fetch'         => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
    // pathinfo分隔符
    'pathinfo_depr'          => '/',
    // URL伪静态后缀
    'url_html_suffix'        => 'html',
    // URL普通方式参数 用于自动生成
    'url_common_param'       => false,
    // URL参数方式 0 按名称成对解析 1 按顺序解析
    'url_param_type'         => 0,
    // 是否开启路由
    'url_route_on'           => true,
    // 路由使用完整匹配
    'route_complete_match'   => false,
    // 路由配置文件（支持配置多个）
    'route_config_file'      => ['route'],
    // 是否强制使用路由
    'url_route_must'         => false,
    // 域名部署
    'url_domain_deploy'      => false,
    // 域名根，如thinkphp.cn
    'url_domain_root'        => '',
    // 是否自动转换URL中的控制器和操作名
    'url_convert'            => true,
    // 默认的访问控制器层
    'url_controller_layer'   => 'controller',
    // 表单请求类型伪装变量
    'var_method'             => '_method',
    // 表单ajax伪装变量
    'var_ajax'               => '_ajax',
    // 表单pjax伪装变量
    'var_pjax'               => '_pjax',
    // 是否开启请求缓存 true自动缓存 支持设置请求缓存规则
    'request_cache'          => false,
    // 请求缓存有效期
    'request_cache_expire'   => null,
    // 全局请求缓存排除规则
    'request_cache_except'   => [],

    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------

    'template'               => [
        // 模板引擎类型 支持 php think 支持扩展
        'type'         => 'Think',
        // 默认模板渲染规则 1 解析为小写+下划线 2 全部转换小写
        'auto_rule'    => 1,
        // 模板路径
        'view_path'    => '',
        // 模板后缀
        'view_suffix'  => 'html',
        // 模板文件名分隔符
        'view_depr'    => DS,
        // 模板引擎普通标签开始标记
        'tpl_begin'    => '{',
        // 模板引擎普通标签结束标记
        'tpl_end'      => '}',
        // 标签库标签开始标记
        'taglib_begin' => '{',
        // 标签库标签结束标记
        'taglib_end'   => '}',
    ],

    // 视图输出字符串内容替换
    'view_replace_str'       => [],
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
    'dispatch_error_tmpl'    => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',

    // +----------------------------------------------------------------------
    // | 异常及错误设置
    // +----------------------------------------------------------------------

    // 异常页面的模板文件
    'exception_tmpl'         => THINK_PATH . 'tpl' . DS . 'think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'          => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'         => false,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle'       => '',

    // +----------------------------------------------------------------------
    // | 日志设置
    // +----------------------------------------------------------------------

    'log'                    => [
        // 日志记录方式，内置 file socket 支持扩展
        'type'  => 'File',
        // 日志保存目录
        'path'  => LOG_PATH,
        // 日志记录级别
        'level' => [],
    ],

    // +----------------------------------------------------------------------
    // | Trace设置 开启 app_trace 后 有效
    // +----------------------------------------------------------------------
    'trace'                  => [
        // 内置Html Console 支持扩展
        'type' => 'Html',
    ],

    // +----------------------------------------------------------------------
    // | 缓存设置
    // +----------------------------------------------------------------------

    'cache'                  => [
        // 驱动方式
        'type'   => 'File',
        // 缓存保存目录
        'path'   => CACHE_PATH,
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ],

    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------

    'session'                => [
        'id'             => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix'         => 'think',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',
        // 是否自动开启 SESSION
        'auto_start'     => true,
    ],

    // +----------------------------------------------------------------------
    // | Cookie设置
    // +----------------------------------------------------------------------
    'cookie'                 => [
        // cookie 名称前缀
        'prefix'    => '',
        // cookie 保存时间
        'expire'    => 0,
        // cookie 保存路径
        'path'      => '/',
        // cookie 有效域名
        'domain'    => '',
        //  cookie 启用安全传输
        'secure'    => false,
        // httponly设置
        'httponly'  => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],

    //分页配置
    'paginate'               => [
        'type'      => 'bootstrap',
        'var_page'  => 'page',
        'list_rows' => 15,
    ],

    'view_replace_str'  =>  [
	    '__PUBLIC__'=>'/public/',
	    '__ROOT__' => '/',
	    '__TITLE__' => '南院科技-公服中心工具平台',
    ],

    //root组
    'root'=>'1',

    'icons' => [
	    'fa-files-o' =>'fa-files-o',
	    'fa-user'=> 'fa-user',
    ],



    'pri' => [
	    	[
			'm' => 'index',
			'c' => 'manager',
			'a'=> [
				['method'=>'user', 'pri_name'=>'用户列表'],
				['method'=>'editUser', 'pri_name'=>'修改用户'],
				['method'=>'addUser', 'pri_name'=>'增加用户'],
				['method'=>'delUser', 'pri_name'=>'删除用户'],
				['method'=>'level', 'pri_name'=>'级别列表'],
				['method'=>'chgPosition', 'pri_name'=>'级别修改'],
				['method'=>'search', 'pri_name'=>'搜索用户'],
				['method'=>'register', 'pri_name'=>'增加群组'],
				['method'=>'delGroup', 'pri_name'=>'删除群组'],
				['method'=>'editGroup', 'pri_name'=>'编缉群组'],
				['method'=>'group', 'pri_name'=>'群组列表'],
				['method'=>'update', 'pri_name'=>'更新群组缓存'],
				['method'=>'pri', 'pri_name'=>'查看群组菜单'],
				['method'=>'changePri', 'pri_name'=>'查看群组权限'],
				['method'=>'updatePri', 'pri_name'=>'更新群组权限缓存'],
				['method'=>'unstall', 'pri_name'=>'卸载权限模块'],
				['method'=>'active', 'pri_name'=>'安装权限模块'],
				['method'=>'installs', 'pri_name'=>'模块列表'],

			],
		],
		[
			'm' => 'index',
			'c' => 'Assets',
			'a'=> [
				['method'=>"addItem",'pri_name'=>'增加物品'],
				['method'=>"myassets",'pri_name'=>'个人资产'],
				['method'=>"hasFinish",'pri_name'=>'己完结流转'],
				['method'=>"checkList",'pri_name'=>'正在进行中流转'],
				['method'=>"verify",'pri_name'=>'审核列表'],
				['method'=>"asset",'pri_name'=>'当事人资产'],
				['method'=>"apply_confirm",'pri_name'=>'流转确认'],
				['method'=>"applys",'pri_name'=>'申请流转'],
				['method'=>"allow",'pri_name'=>'审核通过'],
				['method'=>"deny",'pri_name'=>'审核不通过'],
				['method'=>"roam",'pri_name'=>'人员列表明'],
				['method'=>"detail",'pri_name'=>'资产详情'],
				['method'=>"print",'pri_name'=>'打印'],
			]
		],
		[
			'm' => 'index',
			'c' => 'Common',
			'a'=> [
				['method'=>'thanks','pri_name'=>'感谢名单'],
				['method'=>'qrcode','pri_name'=>'生成二维码'],
				['method'=>'qrupload','pri_name'=>'二维码传图页面'],
				['method'=>'comment','pri_name'=>'问题反馈页面'],
				['method'=>'details','pri_name'=>'查看评论'],
				['method'=>'question','pri_name'=>'评论页面'],
			]
		],
		[
			'm' => 'index',
			'c' => 'User',
			'a'=> [
				['method'=>'index','pri_name'=>'个人主页'],
				['method'=>'changePwd','pri_name'=>'修改密码'],
			]
		],
		[
			'm' => 'index',
			'c' => 'Category',
			'a'=> [
			['method'=>'index','pri_name'=>'菜单列表'],
			['method'=>'update','pri_name'=>'更新菜单缓存'],
			['method'=>'addCategory','pri_name'=>'增加菜单'],
			['method'=>'editCategory','pri_name'=>'修改菜单'],
			['method'=>'delCategory','pri_name'=>'删除菜单'],
			]
		],
		[
			'm' => 'index',
			'c' => 'Ftzj',
			'a'=> [
			['method'=>'barrage','pri_name'=>'大屏显示'],
			['method'=>'admin','pri_name'=>'后台控制'],
			['method'=>'exportJob','pri_name'=>'导出xls'],
			['method'=>'editCom','pri_name'=>'修改公司'],
			['method'=>'job','pri_name'=>'显示职位'],
			['method'=>'delCom','pri_name'=>'删除公司'],
			['method'=>'addCom','pri_name'=>'增加公司'],
			['method'=>'addJob','pri_name'=>'增加职位'],
			['method'=>'addTime','pri_name'=>'延长半年'],
			['method'=>'editJob','pri_name'=>'修改职位'],
			['method'=>'delJob','pri_name'=>'删除职位'],
			['method'=>'changeShow','pri_name'=>'显示控制'],
			]
		],
		[
			'm' => 'index',
			'c' => 'Record',
			'a'=> [
			['method'=>"supplement",'pri_name'=>'街道匹配提交(第一步)'],
			['method'=>"exportExcel",'pri_name'=>'街道匹配街道(第二步)'],
			['method'=>"status",'pri_name'=>'文档状态'],
			['method'=>"street",'pri_name'=>'街道分配'],
			['method'=>"exportStreet",'pri_name'=>'街道导出'],
			['method'=>"export",'pri_name'=>'导入街道库'],
			['method'=>"comstreet",'pri_name'=>'查示公司所在街道'],
			]
		],
		[
			'm' => 'index',
			'c' => 'Comment',
			'a'=> [
			['method'=>'commentList','pri_name'=>'评论列表'],
			['method'=>'details','pri_name'=>'评论详情'],
			['method'=>'auditing','pri_name'=>'审核评论 '],
			]
		],
		[
			'm' => 'index',
			'c' => 'Message',
			'a'=> [
				['method'=>'my','pri_name'=>'我的消息'],
				['method'=>'read','pri_name'=>'设为己读'],
			]
		],
		[
			'm' => 'index',
			'c' => 'Supplies',
			'a'=> [
				['method'=>"supplement",'pri_name'=>'街道匹配提交(第一步)'],
				['method'=>"exportExcel",'pri_name'=>'街道匹配街道(第二步)'],
				['method'=>"status",'pri_name'=>'文档状态'],
				['method'=>"street",'pri_name'=>'街道分配'],
				['method'=>"exportStreet",'pri_name'=>'街道导出'],
				['method'=>"export",'pri_name'=>'导入街道库'],
				['method'=>"comstreet",'pri_name'=>'查示公司所在街道'],
			]
		],

	],














'data' => '[{"RecID":10001,"EPName":"北京市银帆涂料有限责任公司（测试）","Contact":"张笑男","ContactTel":"","USCCode":"91110106700061576M","RecName":"办公室文员","Nature":"全职","Category":"行政专员/助理","WDName":"南四环西路188号","AreaName":"丰台区","RecMoney":"","EduName":"本科及以上","Age":"25-40","WorkYears":"3年以上","RecDesc":"从事办公室行政工作","PublishTime":"2019-03-21T10:39:34","ExpTime":"2019-05-20T10:39:34","LastTime":"2019-03-21T10:39:00"},{"RecID":10002,"EPName":"北京锦鲤云帆国际文化交流有限公司","Contact":"张瞳","ContactTel":"","USCCode":"91110106MA007A7822","RecName":"文案专员","Nature":"全职","Category":"助理/秘书/文员","WDName":"赛欧科技园","AreaName":"丰台区","RecMoney":"","EduName":"专科及以上","Age":"18-40","WorkYears":"不限","RecDesc":"主要进行学生材料的审核整理，需要一定的文字、办公软件基础，需要与学生进行沟通处理相关问题。","PublishTime":"2019-03-27T10:12:38.59","ExpTime":"2019-04-26T10:12:38.59","LastTime":"2019-03-27T10:12:26"},{"RecID":10003,"EPName":"鼎佑教育科技（北京）有限公司","Contact":"张笑男","ContactTel":"","USCCode":"91110105MA00FK2E06","RecName":"销售","Nature":"全职","Category":"销售|客服|市场","WDName":"朝阳区霄云路36号","AreaName":"不限","RecMoney":"","EduName":"高中及以上","Age":"20-40","WorkYears":"不限","RecDesc":"销售培训","PublishTime":"2019-03-21T14:18:02","ExpTime":"2019-05-20T14:18:02","LastTime":"2019-03-21T14:16:44"},{"RecID":10004,"EPName":"北京吾为友人力资源顾问有限责任公司","Contact":"王雨桐","ContactTel":"15101052470","USCCode":"9111010667571698X6","RecName":"招标助理","Nature":"全职","Category":"项目招投标","WDName":"东城区国华投资大厦","AreaName":"东城区","RecMoney":"","EduName":"专科及以上","Age":"25-40","WorkYears":"2年以上","RecDesc":"辅助制作招投标文件，熟练Word，Excel技能，具有良好的沟通技巧，能配合上级工作，","PublishTime":"2019-03-25T10:56:31.373","ExpTime":"2019-04-24T10:56:31.373","LastTime":"2019-03-22T12:12:05"},{"RecID":10005,"EPName":"北京燕德宝嘉泰汽车销售有限公司","Contact":"孙春艳","ContactTel":"","USCCode":"91110106MA0020GF2D","RecName":"客服专员","Nature":"不限","Category":"客户服务专员/助理","WDName":"丰台区南四环中路18号（燕德宝豪华车销售服务中心）","AreaName":"不限","RecMoney":"","EduName":"不限","Age":"18-40","WorkYears":"不限","RecDesc":"岗位职责：\r\n1、根据公司提供的客户电话，向客户推广公司的产品服务；\r\n2、负责接听客户热线，为客户讲解、；\r\n3、通过电话负责客户的约访工作；\r\n4、协助配合销售团队，创造销售业绩。\r\n任职资格：\r\n1、声音甜美，普通话标准，沟通表达能力佳；\r\n2、熟练操作办公自动化设备及OFFICE软件；\r\n3、良好的执行力和团队合作精神；\r\n4、熟悉电话销售或客户服务的业务模式，有电话销售经验者优先。\r\n公司福利：\r\n福利待遇：绩效奖金、带薪年假、带薪病假、节日福利、免费住宿（女生）、免费午餐晚餐","PublishTime":"2019-03-22T15:40:17.277","ExpTime":"2019-04-21T15:40:17.277","LastTime":"2019-03-22T14:23:49"}]',




];
