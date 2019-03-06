<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/22
 * Time: 15:26
 */

namespace app\api\controller\v1;


use app\api\controller\Base;
use app\common\model\Cases as casesModel;
class Cases extends Base
{
    public function getCases()
    {
        $list = casesModel::getImg();
        return json($list,200);
    }
}