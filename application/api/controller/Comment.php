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
            "a.uid"=>$uid,
            "a.type_id"=>$typeId,
            "a.pid"=>0
        );
        $options=[
            'page'=>1
        ];

        $db = Db::field('a.*')
            ->table("t_comment")
            ->alias('a')
            ->join('t_user b','b.id = a.user_id')
            ->join('t_user c','c.id = a.reply_id')
           // ->where(" a.uid='$uid' and a.type_id='$typeId' and a.pid=0")
            ->order('a.id desc')
            ->paginate(15,false,$options);

        //$lists = Db::name("comment")->where($where)->order('id desc')
            //->paginate(15,false,$options);
        return json($this->filterData($db));
    }

    protected function filterData(&$db){

        $lists = $db->all();
        /*
        foreach ($lists as $key => &$item) {
            $childrens = Db::name("comment")->where("pid",$item['id'])->order('id desc');
            $item['childrens'] = $childrens->select();
        }*/
        return $lists;
    }

}