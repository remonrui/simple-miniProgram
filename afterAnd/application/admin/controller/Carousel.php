<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/22
 * Time: 14:11
 */

namespace app\admin\controller;
use app\common\model\Carousel as carouselModel;
/**
 * 后台轮播控制器
 * Class Carousel
 * @package app\admin\controller
 */
class Carousel extends Admin
{
    public function index()
    {
        $list = carouselModel::getList();
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 新增
     */
    public function add(){
        $MenuModel = new carouselModel();
        unset($_POST['id']);    //过滤ID空值
        $result = $MenuModel->validate('Carousel')->save($_POST);
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
        $MenuModel = new carouselModel();
        $map['id'] = input('id', 0);

        $result = $MenuModel->validate('Carousel')->save($_POST, $map);

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
        if(carouselModel::destroy($id)){
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
        $menu = carouselModel::get(input('id', 0));
        $data = $menu->getData();
        return json_encode($data);
    }
}