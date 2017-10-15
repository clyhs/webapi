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

        $db = Db::field('a.*,b.username,c.username as replyname,d.name as typename')
            ->table("t_comment")
            ->alias('a')
            ->join(' t_user b ',' a.user_id = b.id ','left')
            ->join(' t_user c ',' a.reply_id = c.id ','left')
            ->join(' t_dict d ','a.type_id=d.id ','left')
            ->order(' a.id desc');

        return parent::_list($db, true);
    }

    protected function _index_data_filter(&$data)
    {
        foreach ($data as &$vo) {
            $vo['ids'] = join(',', ToolService::getArrSubIds($data, $vo['id']));

            if($vo['type_id'] == 11){
                
            }

        }
        $data = ToolService::arr2table($data);
    }

}