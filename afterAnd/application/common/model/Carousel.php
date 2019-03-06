<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/22
 * Time: 14:26
 */

namespace app\common\model;


use think\Model;

class Carousel extends Model
{
    public static function getImg()
    {
        $model = new self();
        $map = [
            'status'=>1,
        ];
        $field = ['id,img'];
        return $model->where($map)->field($field)->order('sort asc')->select();
    }

    public static function getList()
    {
        $model = new self();
        $map = [
            'status'=>1,
        ];
        $field = ['id,img,sort'];
        return $model->where($map)->field($field)->order('sort asc')->paginate(12);
    }

    public function getImgAttr($value)
    {
        if ($value){
            $url = config('web.host');
            return $url.$value;
        }
        return $value;
    }
}