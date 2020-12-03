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


	'modules' =>[
		[
			'f' => 'Api',
			'c' => '对外接口',
		],
		[
			'f' => 'Assets',
			'c' => '固定资产',
		],
		[
			'f' => 'Common',
			'c' => '对外界面',
		],
		[
			'f' => 'Ftzj',
			'c' => '职介',
		],
		[
			'f' => 'Index',
			'c' => '首页',
		],
		[
			'f' => 'Manager',
			'c' => '管理员',
		],
		[
			'f' => 'Materiel',
			'c' => '耗材',
		],
		[
			'f' => 'Question',
			'c' => '调查',
		],
		[
			'f' => 'Record',
			'c' => '文件匹配',
		],
		[
			'f' => 'Rencai',
			'c' => '人才',
		],
		[
			'f' => 'User',
			'c' => '用户',
		],
	],
	'initConfig' => [
		[
			'id' => 1,
			'name' => '文档工具',
			'url' => '',
			'icon' => 'fa fa-files-o',
			'parent' => 1,
			'childs' => [2,3],
		],
		[
			'id' => 2,
			'name' => '文档匹配',
			'url' => '/index/record/supplement',
			'icon' => 'fa fa-files-o',
			'parent' => 1,
			'childs' => [],
		],
		[
			'id' => 3,
			'name' => '文档状态',
			'url' => '/index/record/status',
			'icon' => 'fa fa-files-o',
			'parent' => 1,
			'childs' => [],
		],
		[
			'id' => 4,
			'name' => '职介',
			'url' => '',
			'icon' => 'fa fa-user',
			'parent' => 0,
			'childs' => [5,6],
		],

		[
			'id' => 5,
			'name' => '屏幕',
			'url' => '/index/ftzj/barrage',
			'icon' => 'fa fa-th-large',
			'parent' => 4,
			'childs' => [],
		],
		[
			'id' => 6,
			'name' => '后台',
			'url' => '/index/ftzj/admin',
			'icon' => 'fa fa-th-large',
			'parent' => 4,
			'childs' => [],
		],
		[
			'id' => 7,
			'name' => '维护',
			'url' => '',
			'icon' => 'fa fa-user',
			'parent' => 0,
			'childs' => [9],
		],

		[
			'id' => 8,
			'name' => '维护',
			'url' => '/index/comment/comment',
			'icon' => 'fa fa-th-large',
			'parent' => 7,
			'childs' => [],
		],
		[
			'id' => 9,
			'name' => '后台',
			'url' => '/index/comment/commentList',
			'icon' => 'fa fa-th-large',
			'parent' => 7,
			'childs' => [],
		],


		[
			'id' => 10,
			'name' => '用户',
			'url' => '',
			'icon' => 'fa fa-user',
			'parent' => 0,
			'childs' => [11,12],
		],

		[
			'id' => 11,
			'name' => '个人主页',
			'url' => '/index/User/index',
			'icon' => 'fa fa-th-large',
			'parent' => 10,
			'childs' => [],
		],
		[
			'id' => 12,
			'name' => '修改密码',
			'url' => '/index/User/changePwd',
			'icon' => 'fa fa-th-large',
			'parent' => 10,
			'childs' => [],
		],


		[
			'id' => 13,
			'name' => '管理员',
			'url' => '',
			'icon' => 'fa fa-user',
			'parent' => 0,
			'childs' => [14,15,16,17],
		],

		[
			'id' => 14,
			'name' => '用户管理',
			'url' => '/index/manager/user',
			'icon' => 'fa fa-th-large',
			'parent' => 13,
			'childs' => [],
		],
		[
			'id' => 15,
			'name' => '群组设置',
			'url' => '/index/manager/group',
			'icon' => 'fa fa-th-large',
			'parent' => 13,
			'childs' => [],
		],
		[
			'id' => 16,
			'name' => '菜单设置',
			'url' => '/index/category/index',
			'icon' => 'fa fa-th-large',
			'parent' => 13,
			'childs' => [],
		],
		[
			'id' => 17,
			'name' => '权限设置',
			'url' => '/index/pr/index',
			'icon' => 'fa fa-th-large',
			'parent' => 13,
			'childs' => [],
		],
		[
			'id' => 18,
			'name' => '设置',
			'url' => '/index/category/index',
			'icon' => 'fa fa-th-large',
			'parent' => 0,
			'childs' => [19],
		],
		[
			'id' => 19,
			'name' => '感谢名单',
			'url' => '/index/common/thanks',
			'icon' => 'fa fa-th-large',
			'parent' => 18,
			'childs' => [],
		],


	],

	'prics' => [
		'manager' => [
			['name' => 'changePri', 'desc' => '更改组权限'],
			['name' => 'level', 'desc' => '等级列表'],
			['name' => 'chgPosition', 'desc' => '更改等级'],

			['name' => 'commentList', 'desc' => '评价列表'],
			['name' => 'detail', 'desc' => '评价内容'],
			['name' => 'auditing', 'desc' => '评价审核'],

			['name' => 'user', 'desc' => '用户列表'],
			['name' => 'search', 'desc' => '用户列表搜索'],
			['name' => 'addUser', 'desc' => '增加用户'],
			['name' => 'editUser', 'desc' => '编缉用户'],
			['name' => 'delUser', 'desc' => '删除用户'],


			['name' => 'group', 'desc' => '群组列表'],
			['name' => 'addGroup', 'desc' => '增加群组'],
			['name' => 'editGroup', 'desc' => '修改群组'],
			['name' => 'delGroup', 'desc' => '删除群组'],
			['name' => 'updatePriCache', 'desc' => '更改权限缓存'],
			['name' => 'menu', 'desc' => '群组菜单列表'],

			['name' => 'updatePri', 'desc' => '群组权限更新'],

			['name' => 'modules', 'desc' => '模块列表'],
			['name' => 'install', 'desc' => '模块安装'],
			['name' => 'unstall', 'desc' => '模块卸载'],

			['name' => 'categorys', 'desc' => '菜单列表'],
			['name' => 'addCategory', 'desc' => '增加菜单'],
			['name' => 'init', 'desc' => '菜单初始化'],
			['name' => 'editCategory', 'desc' => '编缉菜单'],
			['name' => 'delCategory', 'desc' => '删除菜单'],
			['name' => 'updateCategoryCache', 'desc' => '更新菜单缓存'],

			['name' => 'pris', 'desc' => '权限列表'],
			['name' => 'editGroupPri', 'desc' => '编缉群组权限'],
			['name' => 'editPri', 'desc' => '修改权限'],
			['name' => 'delPri', 'desc' => '删除权限'],
			['name' => 'addPri', 'desc' => '增加权限'],




		],
		'user' => [
			['name' => 'changePwd', 'desc' => '修改密码'],
			['name' => 'index', 'desc' => '个人资料'],
			['name' => 'my', 'desc' => '我的消息'],
		],
		'rencai' => [
			['name' => 'addCert', 'desc' => '新增单位'],
			['name' => 'chgCert', 'desc' => '修改单位'],
			['name' => 'certlist', 'desc' => '积分落户单位列表'],
			['name' => 'addNewNum', 'desc' => '增加新办数量'],
			['name' => 'subNewNum', 'desc' => '减少新办数量'],
			['name' => 'addHasNum', 'desc' => '增加己办数量'],
			['name' => 'subHasNum', 'desc' => '减少己办数量'],
		],

		"record" => [
			['name' => 'street', 'desc' => '按街道分配'],
			['name' => 'supplement', 'desc' => 'Excel文件匹配'],
			['name' => 'status', 'desc' => '文件合并第一步'],
			['name' => 'statusCheck', 'desc' => '文件合并第二步'],
			['name' => 'exportExcel', 'desc' => '导出Excel'],
			['name' => 'compare', 'desc' => 'Excel文件对比'],
			['name' => 'export', 'desc' => '文件上传'],
			['name' => 'lookfor', 'desc' => '通过公司名查街道'],
		],

		"question" => [
			['name' => 'index', 'desc' => '问卷列表'],
			['name' => 'add', 'desc' => '增加问卷'],
			['name' => 'addFrag', 'desc' => '增加选题'],
			['name' => 'editFrag', 'desc' => '修改先是'],
			['name' => 'table', 'desc' => '外链'],
			['name' => 'case', 'desc' => '用户问题调查'],
			['name' => 'report', 'desc' => '未定义'],
			['name' => 'export', 'desc' => '导出excel'],
			['name' => 'suc', 'desc' => '提交成功页面'],
			['name' => 'office', 'desc' => '调查问卷'],
		],

		"materiel" => [
			['name' => 'index', 'desc' => '打印机列表'],
			['name' => 'purchase', 'desc' => '进货'],
			['name' => 'addItem', 'desc' => '增加墨盒各类'],
			['name' => 'materiel_list', 'desc' => '墨盒列表'],
			['name' => 'consume', 'desc' => '消耗'],
			['name' => 'record', 'desc' => '墨盒使用记录'],
			['name' => 'initStat', 'desc' => '墨盒使用按月统计'],
			['name' => 'consumeStat', 'desc' => '墨盒消耗统计图'],
			['name' => 'consumeExport', 'desc' => '墨盒消耗记录导出excel'],
			['name' => 'prints', 'desc' => '打印机种类列表'],
			['name' => 'addPrint', 'desc' => '增加打印机种类'],
			['name' => 'del', 'desc' => '删除打印机种类'],
			['name' => 'stats', 'desc' => '墨盒使用记录'],
		],

		"index" => [
			['name' => 'index', 'desc' => '首页'],
			['name' => 'noPri', 'desc' => '无权限页面'],
			['name' => 'login', 'desc' => '登陆'],
			['name' => 'logout', 'desc' => '退出'],

		],
		"ftzj" => [
			['name' => 'barrage', 'desc' => '大黑屏'],
			['name' => 'barrage2', 'desc' => '大黑屏2'],
			['name' => 'timing', 'desc' => '定时抓取数据'],
			['name' => 'jobs', 'desc' => '职位列表'],
			['name' => 'admin', 'desc' => '公司列表'],
			['name' => 'addTime', 'desc' => '延长半年有效期'],
			['name' => 'addCom', 'desc' => '增加公司'],
			['name' => 'editCom', 'desc' => '修改公司'],
			['name' => 'job', 'desc' => '某公司职位列表'],
			['name' => 'changeShow', 'desc' => '改变显示状态'],
			['name' => 'addJob', 'desc' => '增加职位'],
			['name' => 'editJob', 'desc' => '修改职位'],
			['name' => 'exportJob', 'desc' => '导出职位'],
			['name' => 'delJob', 'desc' => '删除职位'],
			['name' => 'delCompany', 'desc' => '删除公司'],
			['name' => 'exportComment', 'desc' => '导出评论'],
			['name' => 'screen', 'desc' => '触摸屏'],
			['name' => 'importComs', 'desc' => '导入公司'],
			['name' => 'importJobs', 'desc' => '导入职位'],
		],


		"common" => [
			['name' => 'com', 'desc' => '经办人导出'],
			['name' => 'add', 'desc' => '增加备案说明'],
			['name' => 'thanks', 'desc' => '感谢名单'],
			['name' => 'qrcode', 'desc' => '生成二维码'],
			['name' => 'qrupload', 'desc' => '有二维码用户评论'],
			['name' => 'comment', 'desc' => '问题反馈'],
			['name' => 'details', 'desc' => '问题详情'],
			['name' => 'quesiton', 'desc' => '用户评论'],

		],


		"assets" => [
			['name' => 'addItem', 'desc' => '入库'],
			['name' => 'myassets', 'desc' => '查看个人资产'],
			['name' => 'hasFinish', 'desc' => '查看己完成审批列表'],
			['name' => 'checkList', 'desc' => '查看审批中列表'],
			['name' => 'verify', 'desc' => '查看审批列表'],
			['name' => 'asset', 'desc' => '查看某人资产'],
			['name' => 'apply_confirm', 'desc' => '提交申请'],
			['name' => 'applys', 'desc' => '填写申请原因'],
			['name' => 'allow', 'desc' => '同意'],
			['name' => 'deny', 'desc' => '拒绝'],
			['name' => 'roam', 'desc' => '流转主页'],
			['name' => 'detail', 'desc' => '查看资产详情'],
			['name' => 'print', 'desc' => '打印'],

		],

		"api" => [
			['name' => 'rencai', 'desc' => '用户接口'],
			['name' => 'ftzj', 'desc' => '人才接口'],
			['name' => 'user', 'desc' => '用户接口'],
			['name' => 'record', 'desc' => '记录接口'],
			['name' => 'machine', 'desc' => '耗材接口'],
		],




	],




];
