<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/21
 * Time: 11:30
 */
namespace app\api\controller\v1;

use app\api\controller\Base;
use app\common\model\Category as CategoryModel;
use app\common\Server\Cache as cacheServer;
use app\common\model\Like;
use app\common\model\Message;
use app\common\model\MessageUserCategory;
class Category extends  Base
{

    /*
     * 主分类接口*/
    public function getCategoryMain()
    {
        $category = cacheServer::getCache('categoryMain');
        if (!$category){
            $category = CategoryModel::getCategoryMain();
            cacheServer::setCache('categoryMain',$category);
        }
        return json($category,200);
    }

    /*
     * 子分类接口*/
    public function getCategorySon($id = 0)
    {
        $category = cacheServer::getCache($id.'categorySon');
        if (!$category){
            $category = CategoryModel::getCategorySon($id);
            cacheServer::setCache($id.'categorySon',$category);
        }
        return json($category,200);
    }

    /*分类详情接口
    */
    public function getCategoryDetail($id = 0)
    {
        $detail = cacheServer::getCache($id.'categoryDetail');
        if (!$detail){
            $detail = CategoryModel::getCategoryDetail($id);
            cacheServer::setCache($id.'categoryDetail',$detail);
        }
        return json($detail,200);
    }

    /*分类是否被收藏
    */
    public function getCategoryLike($id = 0)
    {
        $like = Like::getDetailLike($id);
        $return = 0;
        if ($like){
            $return = $like['status'] == 1 ? 1 : 0;
        }
        return json($return,200);
    }

    /*
     * 改变收藏*/
    public function categoryChangeLike($id = 0)
    {
        $like = Like::getDetailLike($id);
        if ($like){
           $status = $like['status'] == 1 ? 2 :1;
           Like::changeDetailLike($id,$status);
        }else{
            Like::createDetailLike($id);
        }
        $return = $like['status'] == 1 ? 0 : 1;
        return json($return,200);
    }

    //留言
    public function categoryMessage($id,$msg)
    {
        $m_id = Message::createMessage($msg);
        $msg = false;
        if ($m_id){
            $msg = MessageUserCategory::createMessageConnection($id,$m_id);
        }
        $code = $msg ? 1 : 0;
        return json($code,200);
    }
}