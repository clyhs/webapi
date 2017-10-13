<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2017/10/13
 * Time: 15:06
 */

namespace app\api\controller;

use think\Request;
use think\controller\Rest;
use think\Db;

class Comment extends Rest{

    public function getCommentForPage($uid=0,$typeId=0){

        $where=array(
            "uid"=>$uid,
            "type_id"=>$typeId,
            "pid"=>0
        );
        $options=[
            'page'=>1
        ];

        $lists = Db::name("comment")->where($where)->order('id desc')
            ->paginate(15,false,$options);
        return json($this->filterData($lists->all()));
    }

    protected function filterData(&$lists){
        foreach ($lists as $key => &$item) {
            $childrens = Db::name("comment")->where("pid",$item['id'])->order('id desc');
            $item['childrens'] = $childrens;
        }
        return $lists;
    }

}