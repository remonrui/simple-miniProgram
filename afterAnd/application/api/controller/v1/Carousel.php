<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/22
 * Time: 14:47
 */

namespace app\api\controller\v1;


use app\api\controller\Base;
use app\common\model\Carousel as carouselModel;

class Carousel extends Base
{
    public function getCarousel()
    {
        $list = carouselModel::getImg();
        return json($list,200);
    }
}