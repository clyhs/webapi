<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2017/10/12
 * Time: 8:53
 */

namespace app\admin\controller;

use app\common\controller\BaseAdmin;
use think\Db;
use app\common\service\DataService;

class Video extends BaseAdmin{

    public $table = 'video';

    public function index(){

        $this->title = '短视频管理';

        $db = Db::field('a.*,b.username')
            ->table("t_video")
            ->alias('a')
            ->join(' t_user b ',' a.user_id = b.id ','left');

        $db->group('a.id');
        $db->order('a.id desc');

        return parent::_list($db);
    }

    protected function _index_data_filter(&$data)
    {
    }



    public function del()
    {
        if (DataService::update($this->table)) {
            $this->success("删除成功!", '');
        }
        $this->error("删除失败, 请稍候再试!");
    }

}