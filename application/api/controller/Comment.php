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
        $options=[
            'page'=>1
        ];
        $db = Db::field('a.*,b.username')
            ->table("t_comment")
            ->alias('a')
            ->join('t_user b','b.id=a.user_id')
            ->where(" a.uid='$uid' and a.type_id='$typeId' and a.pid=0")
            ->order('a.id desc')
            ->paginate(15,false,$options);

        $result = [
            "code"=>10000,
            "desc"=>"",
            "data"=>$this->filterData($db)
        ];

        return json($result);
    }

    protected function filterData(&$db){

        $lists = $db->all();
        foreach ($lists as $key => &$item) {
            $sql = 'select a.*,b.replayname as replayname,c.username from t_comment a '.
                'left join t_user b on b.id=a.reply_id '.
                'left join t_user c on c.id=a.user_id '.
                'where a.pid='.$item['id'].' order by id asc';
            $childrens =Db::query($sql);
            $lists[$key]['childs'] = $childrens;
        }
        return $lists;
    }

}