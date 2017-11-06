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
use app\common\service\DataService;

class Comment extends Rest{

    /**获取评论列表
     * @param int $uid
     * @param int $typeId
     * @param int $page
     * @param int $pageSize
     * @return mixed
     */
    public function getCommentForPage($uid=0,$typeId=0,$page = 1,$pageSize = 15){
        $options=[
            'page'=>$page
        ];
        $db = Db::field('a.*,b.username')
            ->table("t_comment")
            ->alias('a')
            ->join('t_user b','b.id=a.user_id')
            ->where(" a.uid='$uid' and a.type_id='$typeId' and a.pid=0")
            ->order('a.id desc')
            ->paginate($pageSize,false,$options);

        $result = [
            "code"=>10000,
            "desc"=>"",
            "data"=>$this->filterData($db)
        ];

        return json($result);
    }

    public function addComment(){

        $uid = empty(Request::instance()->param('uid'))?0:Request::instance()->param('uid');
        $type_id = empty(Request::instance()->param('type_id'))?0:Request::instance()->param('type_id');
        $pid = empty(Request::instance()->param('pid'))?0:Request::instance()->param('pid');
        $context = empty(Request::instance()->param('context'))?"":Request::instance()->param('context');
        $user_id = empty(Request::instance()->param('user_id'))?0:Request::instance()->param('user_id');
        $reply_id = empty(Request::instance()->param('reply_id'))?0:Request::instance()->param('reply_id');

        if(empty($uid) || empty($context) || empty($type_id)){
            return json(["code"=>20001,"desc"=>"不能为空","data"=>[]]);
        }

        $data = [
            'uid'=>$uid,
            'type_id'=>$type_id,
            'context'=>$context,
            'user_id'=>$user_id,
            'reply_id'=>$reply_id,
            'pid'=>$pid
        ];

        $db = Db::name($this->table);
        $pk = $db->getPk() ? $db->getPk() : 'id';
        $result = DataService::save($db, $data, $pk, []);

        if($result !== false){
            return json(["code"=>10000,"desc"=>"评论成功","data"=>$data]);
        }else{
            return json(["code"=>20001,"desc"=>"评论失败","data"=>$data]);
        }


    }

    protected function filterData(&$db){

        $lists = $db->all();
        foreach ($lists as $key => &$item) {
            $sql = 'select a.*,b.username as replayname,c.username from t_comment a '.
                'left join t_user b on b.id=a.reply_id '.
                'left join t_user c on c.id=a.user_id '.
                'where a.pid='.$item['id'].' order by id asc';
            $childrens =Db::query($sql);
            $lists[$key]['childs'] = $childrens;
        }
        return $lists;
    }

}