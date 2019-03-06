<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/3
 * Time: 10:32
 */

namespace app\admin\controller;

use app\common\model\Category as CategoryModel;
use app\common\server\Cache as cacheServer;

class Section extends Admin
{
    public function index()
    {
        /**
         * 项目首页
         */
        $pid  = input('pid', 0);
        if($pid) {
            $data = CategoryModel::get($pid);
            $this->assign('data',$data);
        }
        $title = trim(input('title'));

        $map['pid'] =   $pid;
        if($title) $map['title'] = array('like',"%{$title}%");
        $list = CategoryModel::all(function($query) use($map) {
            $query->where($map)->order('sort asc, id asc');
        });
        $this->assign('list',$list);

        // 组合上级菜单
        $all_menu = CategoryModel::column('id,title');
        foreach ($list as &$key) {
            if($key['pid']){
                $key['up_title'] = $all_menu[$key['pid']];
            }
        }

        // 所有菜单的下拉树
        $MenuModel = new CategoryModel();
        $menus = $MenuModel->all();
        foreach ($menus as $key=>$value) {
            $menus[$key] = $value->toArray();
        }
        $menus = $MenuModel->toFormatTree($menus);
        $menus = array_merge(array(0=>array('id'=>0,'title_show'=>'顶级菜单')), $menus);
        $this->assign('Menus', $menus);
        $this->assign("pid", $pid);

        return $this->fetch();
    }

    /**
     * 新增项目
     */
    public function add(){
        $MenuModel = new CategoryModel();
        unset($_POST['id']);    //过滤ID空值
        $result = $MenuModel->validate('Category')->save($_POST);
        if($result){
            //重置缓存
            cacheServer::resetCache('categoryMain');
            $this->success('新增成功');
        } else {
            $this->error($MenuModel->getError());
        }
    }

    /**
     * 编辑项目
     */
    public function edit(){
        $MenuModel = new CategoryModel();
        $id = input('id',0);
        $map['id'] = $id;

        $result = $MenuModel->validate('Category')->save($_POST, $map);

        if($result){
            cacheServer::resetCache($id.'categoryMain');
            cacheServer::resetCache($id.'categorySon');
            cacheServer::resetCache($id.'categoryDetail');
            $this->success("更新成功");
        }else{
            $this->error($MenuModel->getError());
        }
    }

    /**
     * 删除项目
     */
    public function del(){
        $id = input('id');
        if(CategoryModel::destroy($id)){

            CategoryModel::delChildMenu($id);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
    /**
     * 获取菜单详情
     */
    public function getInfo() {
        $menu = CategoryModel::get(input('id', 0));
        $data = $menu->getData();
        return json_encode($data);
    }
}