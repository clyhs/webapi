<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2018/2/1
 * Time: 10:02
 */
namespace app\admin\controller;

use app\common\controller\BaseAdmin;
use think\Db;
use app\common\service\DataService;

class Tvprogram extends BaseAdmin{
    public $table = 'television_program';

    public function index(){

        $this->title = '电视台节目管理';

        $db = Db::field('a.*,b.name ')
            ->table("t_television_program")
            ->alias('a')
            ->join(' t_television b ',' a.tv_id = b.id ','left');
        $db->order('a.id desc');


        return parent::_list($db);

    }

    protected function _index_data_filter(&$data)
    {
    }

}