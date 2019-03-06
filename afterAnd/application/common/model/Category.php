<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/21
 * Time: 11:48
 */

namespace app\common\model;


use think\Model;

class Category extends Model
{
    //获取树的根到子节点的路径
    public function getPath($id){
        $path = array();
        $nav = $this->get($id);
        $path[] = $nav;
        if($nav['pid'] >1){
            $path = array_merge($this->getPath($nav['pid']),$path);
        }
        return $path;
    }

    /**
     * 把返回的数据集转换成Tree
     * @access public
     * @param array $list 要转换的数据集
     * @param string $pid parent标记字段
     * @param string $child level标记字段
     * @return array
     */
    public function toTree($list=null, $pk='id',$pid = 'pid',$child = '_child'){
        if(null === $list) {
            // 默认直接取查询返回的结果集合
            $list   =   &$this->dataList;
        }
        // 创建Tree
        $tree = array();
        if(is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();

            foreach ($list as $key => $data) {
                $_key = is_object($data)?$data->$pk:$data[$pk];
                $refer[$_key] =& $list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = is_object($data)?$data->$pid:$data[$pid];
                $is_exist_pid = false;
                foreach($refer as $k=>$v){
                    if($parentId==$k){
                        $is_exist_pid = true;
                        break;
                    }
                }
                if ($is_exist_pid) {
                    if (isset($refer[$parentId])) {
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                } else {
                    $tree[] =& $list[$key];
                }
            }
        }
        return $tree;
    }

    /**
     * 将格式数组转换为树
     * @param array $list
     * @param integer $level 进行递归时传递用的参数
     */
    private $formatTree; //用于树型数组完成递归格式的全局变量
    private function _toFormatTree($list, $level=0, $title = 'title') {
        foreach($list as $key=>$val){
            $tmp_str=str_repeat("&nbsp;&nbsp;",$level*2);
            $tmp_str.="└";
            $val['level'] = $level;
            $val['title_show'] =$level==0?$val[$title]."&nbsp;":$tmp_str.$val[$title]."&nbsp;";
            // $val['title_show'] = $val['id'].'|'.$level.'级|'.$val['title_show'];
            if(!array_key_exists('_child',$val)){
                array_push($this->formatTree,$val);
            }else{
                $tmp_ary = $val['_child'];
                unset($val['_child']);
                array_push($this->formatTree,$val);
                $this->_toFormatTree($tmp_ary,$level+1,$title); //进行下一层递归
            }
        }
        return;
    }

    public function toFormatTree($list, $title='title', $pk='id', $pid='pid', $root = 0){
        $list = list_to_tree($list, $pk, $pid, '_child', $root);
        $this->formatTree = array();
        $this->_toFormatTree($list,0,$title);
        return $this->formatTree;
    }

    /*
     * 删除子菜单
     * */
    public static function delChildMenu($menuId) {
        $list = self::where('pid', $menuId)->select();
        foreach ($list as $menu) {
            self::delChildMenu($menu['id']);
            self::destroy($menu['id']);
        }
    }

    /**
     * 获取图片
     * @param $value
     * @return string
     */
    public function getImgAttr($value)
    {
        if ($value){
            $url = config('web.host');
            return $url.$value;
        }
        return $value;
    }

    public function getContentAttr($value)
    {
        if ($value){
            $content = trim(str_replace("&nbsp;", '', strip_tags($value)));
            return $content;
        }
        return $value;
    }

    /*
     * 获取子分类详情*/
    public static function categoryDetail($id)
    {
        $model = new self();
        $map =[
            'id'=>$id,
            'status'=>1,
        ];
        return $model->where($map)->field('id,title,img,content')->find();
    }


    /*
     * 获取主分类*/
    public static function getCategoryMain()
    {
        $model = new self();
        $map =[
            'status'=>1,
            'pid'=>0,
        ];
        return $model->where($map)->field('id,title,img')->order('sort asc')->select();
    }

    /*
    * 获取子级分类*/
    public static function getCategorySon($id)
    {
        $model = new self();
        $map =[
            'pid'=>$id,
            'status'=>1,
        ];
        return $model->where($map)->field('id,title,img')->order('sort asc')->select();
    }

    /*
   * 获取子级分类*/
    public static function getCategoryDetail($id)
    {
        $model = new self();
        $map =[
            'id'=>$id,
            'status'=>1,
        ];
        return $model->where($map)->field('id,title,content,img')->find();
    }
}