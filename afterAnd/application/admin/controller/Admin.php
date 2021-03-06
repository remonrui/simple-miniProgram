<?php
/**
 * 后台的基本父类
 * Created by PhpStorm.
 * User: 虚空之翼 <183700295@qq.com>
 * Date: 16/3/17
 * Time: 14:50
 */

namespace app\admin\controller;

use app\common\model\Config;
use app\common\model\Menu;
use org\Auth;
use think\Controller;
use think\Request;

class Admin extends Controller
{
    protected $moduleName;
    protected $controllerName;
    protected $actionName;

    /* 后台控制器初始化 */
    protected function _initialize()
    {
        $request = Request::instance();
        $this->moduleName = $request->module();
        $this->controllerName = $request->controller();
        $this->actionName = $request->action();

        // 获取当前用户ID
        if (defined('UID')) return;
        define('UID', is_login());
        if (!UID) {// 还没登录 跳转到登录页面
            $url = config('web.host').'/admin-login.html';
            $this->redirect($url);
        }
        /* 读取数据库中的配置 */
        $config = cache('db_config_data');
        if (!$config) {
            $configModel = new Config();
            $config = $configModel->lists();
            cache('db_config_data', $config);
        }
        config($config); //添加配置

        /* 是否是超级管理员 */
        define('IS_ROOT', is_administrator());
        if (!IS_ROOT && config('admin_allow_ip')) {
            // 检查IP地址访问
            if (!in_array(get_client_ip(), explode(',', config('admin_allow_ip')))) {
                $this->error('403:禁止访问');
            }
        }
        // 检测系统权限
        if (!IS_ROOT) {
            $access = $this->accessControl();
            if (false === $access) {
                $this->error('403:禁止访问');
            } elseif (null === $access) {
                //检测访问权限
                $rule = strtolower($this->moduleName . '/' . $this->controllerName . '/' . $this->actionName);
                if (!$this->checkRule($rule, [1, 2])) {
                    $this->error('未授权访问!');
                } else {
                    // 检测分类及内容有关的各项动态权限
                    $dynamic = $this->checkDynamic();
                    if (false === $dynamic) {
                        $this->error('未授权访问!');
                    }
                }
            }
        }

        $this->assign('__MENU__', $this->getMenus());
        // 判断二级菜单位置
        $menu = Menu::where('url', 'like', '%' . $this->controllerName . '/' . $this->actionName . '')->order('id desc')->find();
        if ($menu['level'] == 3) {
            $menu = Menu::find($menu['pid']);
        }
        $this->assign('subMenu', $menu);
        //后台用户名头像用户组显示
        $this->assign('user_auth', session('user_auth'));

        // 未读的消息数据
//        $subQuery = MessageStatus::field('message_id')->where(['is_view'=>1, 'member_id'=>UID, 'type'=>1])->buildSql();
//        $unreadMessage = Message::where(['status'=>1, 'type'=>1])->where('receive_id', 'exp', 'in (0, '.UID.') ')
//            ->where('id', 'exp', "not in $subQuery")->order('id desc')->select();
//        $this->assign('unreadMessage', $unreadMessage);
        // 总数
//        $messageTotal = Message::where(['status'=>1])->count();
        $this->assign('messageTotal', 0);
    }

    /**
     * 权限检测
     * @param string $rule 检测的规则
     * @param string $mode check模式
     * @return boolean
     */
    final protected function checkRule($rule, $type = \app\common\model\AuthRule::RULE_URL, $mode = 'url')
    {
        if (IS_ROOT) {
            return true;//管理员允许访问任何页面
        }
        static $Auth = null;
        if (!$Auth) {
            $Auth = new Auth();
        }
        if (!$Auth->check($rule, UID, $type, $mode)) {
            return false;
        }
        return true;
    }

    /**
     * 检测是否是需要动态判断的权限
     * @return boolean|null
     *      返回true则表示当前访问有权限
     *      返回false则表示当前访问无权限
     *      返回null，则会进入checkRule根据节点授权判断权限
     */
    protected function checkDynamic()
    {
        if (IS_ROOT) {
            return true;//管理员允许访问任何页面
        }
        return null;//不明,需checkRule
    }


    /**
     * action访问控制,在 **登陆成功** 后执行的第一项权限检测任务
     * @return boolean|null  返回值必须使用 `===` 进行判断
     *   返回 **false**, 不允许任何人访问(超管除外)
     *   返回 **true**, 允许任何管理员访问,无需执行节点权限检测
     *   返回 **null**, 需要继续执行节点权限检测决定是否允许访问
     */
    final protected function accessControl()
    {
        $allow = Config::get('allow_visit');
        $deny = Config::get('deny_visit');
        $check = strtolower($this->controllerName . '/' . $this->actionName);
        if (!empty($deny) && in_array_case($check, $deny)) {
            return false;//非超管禁止访问deny中的方法
        }
        if (!empty($allow) && in_array_case($check, $allow)) {
            return true;
        }
        return null;//需要检测节点权限
    }

    /**
     * 获取控制器菜单数组,二级菜单元素位于一级菜单的'_child'元素中
     */
    final public function getMenus()
    {
        $menus = session('admin_menu_list');
        if (empty($menus)) {
            // 获取主菜单
            $menus = Menu::all(function ($query) {
                $query->where('pid', 0)->where('hide', 0)->order('sort', 'asc');
                if (Config::get('develop_mode')) { // 是否开发者模式
                    $query->where('is_dev', 0);
                }
            });
            foreach ($menus as $key => &$item) {
                // 错误配置
                if (empty($item['title']) || empty($item['url'])) {
                    $this->error('控制器基类$menus属性元素配置有误');
                }
                $item = $item->toArray();

                // 判断主菜单权限
                if (!IS_ROOT && !$this->checkRule(strtolower($this->moduleName . '/' . $item['url']), [1, 2], null)) {
                    unset($menus[$key]);
                    continue;   //继续循环
                }
                // 当前菜单位置
//                if (strtolower(CONTROLLER_NAME.'/'.ACTION_NAME)  == strtolower($item['url'])) {
//                    $menus[$key]['class']='current';
//                }
                // 自动添加module
                if (stripos($item['url'], $this->moduleName) !== 0) {
                    $item['url'] = $this->moduleName . '/' . $item['url'];
                }

                // 查找二级菜单
                $where['pid'] = $item['id'];  // 原先pid字段时无法显示菜单。
                $where['hide'] = 0;
                if (!Config::get('DEVELOP_MODE')) { // 是否开发者模式
                    $where['is_dev'] = 0;
                }
                $secondMenus = Menu::where($where)->order('sort', 'asc')->select();
                foreach ($secondMenus as $k => &$value) {
                    if (!IS_ROOT && !$this->checkRule(strtolower($this->moduleName . '/' . $value['url']), [1, 2], null)) {
                        unset($secondMenus[$k]);
                        continue;   //继续循环
                    }
                    $value = $value->toArray();
                }
                $menus[$key]['child'] = list_to_tree($secondMenus, 'id', 'pid', 'operater', $item['id']);
            }
            session('admin_menu_list', $menus);
        }

        return $menus;
    }

    /**
     * 返回后台节点数据
     * 注意,返回的主菜单节点数组中有'controller'元素,以供区分子节点和主节点
     * @param bool $tree 是否返回多维数组结构(生成菜单时用到),为false返回一维数组(生成权限节点时用到)
     * @return array|false|mixed|static[]
     */
    final protected function returnNodes($tree = true)
    {
        static $tree_nodes = array();
        if ($tree && !empty($tree_nodes[(int)$tree])) {
            return $tree_nodes[$tree];
        }
        if ((int)$tree) {
            $list = Menu::all(function ($query) {
                $query->field('id,pid,title,url,tip,hide')->order('sort asc');
            });
            foreach ($list as $key => $value) {
                if (stripos($value['url'], $this->moduleName) !== 0) {
                    $list[$key]['url'] = $this->moduleName . '/' . $value['url'];
                }
            }
            foreach ($list as $key => $value) {
                $list[$key] = $value->toArray();
            }
            $nodes = list_to_tree($list, $pk = 'id', $pid = 'pid', $child = 'operator', $root = 0);
            foreach ($nodes as $key => $value) {
                if (!empty($value['operator'])) {
                    $nodes[$key]['child'] = $value['operator'];
                    unset($nodes[$key]['operator']);
                }
            }
        } else {
            $nodes = Menu::all(function ($query) {
                $query->field('title,url,tip,pid')->order('sort asc');
            });
            foreach ($nodes as $key => $value) {
                if (stripos($value['url'], $this->moduleName) !== 0) {
                    $nodes[$key]['url'] = $this->moduleName . '/' . $value['url'];
                }
            }
        }
        $tree_nodes[(int)$tree] = $nodes;

        return $nodes;
    }

    /**
     * 判断无修改时错误返回提示
     */
    public function get_update_error_msg($msg)
    {
        if (empty($msg)) {
            return $this->error("未修改内容,更新失败");
        } else {
            return $this->error($msg);
        }
    }

    /**
     * 获取当月时间
     */
    public function getMonthTime()
    {
        $date = getdate();
        $time = mktime(0, 0, 0, $date['mon'], 1, $date['year']);
        $res = array('egt', $time);
        return $res;
    }

}