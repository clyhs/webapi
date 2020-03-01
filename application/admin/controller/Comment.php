<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2017/10/13
 * Time: 13:55
 */

namespace app\admin\controller;

use app\common\controller\BaseAdmin;
use app\common\service\ToolService;
use app\common\service\DataService;
use think\Db;

class Comment extends BaseAdmin{

    public $table = 'comment';

    public function index(){
        $this->title = '评论管理';
        //$db = Db::name($this->table)->order('id asc');

        $get = $this->request->get();
        /*
        $db = Db::field('a.*,b.username,c.username as replyname,d.name as typename')
            ->table("t_comment")
            ->alias('a')
            ->join(' t_user b ',' a.user_id = b.id ','left')
            ->join(' t_user c ',' a.reply_id = c.id ','left')
            ->join(' t_dict d ','a.type_id = d.id ','left');*/
        /*
        $db = Db::field('*')
            ->table("t_comment")
            ->order(' a.id desc');*/
        //$db = Db::name($this->table)->order('id asc');
        $db = Db::table('t_comment')
            ->alias('a')
            ->field("a.*,b.username,c.username as replyname,d.name as typename")
            ->join(' t_user b ',' a.user_id = b.id ','left')
            ->join(' t_user c ',' a.reply_id = c.id ','left')
            ->join(' t_dict d ','a.type_id = d.id ','left');
        //$list = $db->select();
        //var_dump($list);
        /*
        foreach (['type_id'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {

                if($get[$key]>0){
                    //$db->where('a.'.$key, '=', "{$get[$key]}");
                    $db->where('a.type_id','=',$get[$key]);
                }

            }
        }*/
        $db->order(' a.id desc');

        $where = [
            "char"=>"SUBJECT",
            "pid"=>10
        ];

        $subjects = Db::name("dict")->where($where)->order('id asc')->select();
        $this->assign('subjects', $subjects);

        return parent::_list($db,false);
    }

    protected function _index_data_filter(&$data)
    {

        foreach ($data as &$vo) {
            var_dump($vo);
            /*
            $vo['ids'] = join(',', ToolService::getArrSubIds($data, $vo['id']));
            if($vo['type_id'] == 11){
                $vo['title'] = Db::name('television')->where(['id' => $vo['uid']])->value('name');
            }else{
                $vo['title'] = Db::name('chat')->where(['id' => $vo['uid']])->value('context');
            }
            if($vo['replyname'] == null ){
                $vo['replyname'] = '--';
            }
            if($vo['username'] == null ){
                $vo['username'] = '--';
            }*/
        }
        $data = ToolService::arr2table($data);
        var_dump($data);
    }

}