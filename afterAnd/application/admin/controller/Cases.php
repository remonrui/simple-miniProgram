<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/22
 * Time: 15:28
 */

namespace app\admin\controller;

use app\common\model\Cases as casesModel;
/**
 * 案例后台控制器
 * Class Cases
 * @package app\admin\controller
 */
class Cases extends Admin
{
    public function index()
    {
        $list = casesModel::getList();
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 新增
     */
    public function add(){
        $MenuModel = new casesModel();
        unset($_POST['id']);    //过滤ID空值
        $result = $MenuModel->validate('Cases')->save($_POST);
        if($result){
            $this->success('新增成功');
        } else {
            $this->error($MenuModel->getError());
        }
    }

    /**
     * 编辑
     */
    public function edit(){
        $MenuModel = new casesModel();
        $map['id'] = input('id', 0);

        $result = $MenuModel->validate('Cases')->save($_POST, $map);

        if($result){
            $this->success("更新成功");
        }else{
            $this->error($MenuModel->getError());
        }
    }

    /**
     * 删除
     */
    public function del(){
        $id = input('id');
        if(casesModel::destroy($id)){
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
    /**
     * 获取详情
     *
     */
    public function getInfo() {
        $menu = casesModel::get(input('id', 0));
        $data = $menu->getData();
        return json_encode($data);
    }
}