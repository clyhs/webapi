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

        $get = $this->request->get();
        $db = Db::field('a.*,b.name ')
            ->table("t_television_program")
            ->alias('a')
            ->join(' t_television b ',' a.tv_id = b.id ','left');


        foreach ([ 'name'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                $db->where('b.'.$key, 'like', "%{$get[$key]}%");
            }
        }

        if (isset($get['date']) && $get['date'] !== '') {
            $play_date=   $get['date'];
            $db->where('play_date', $play_date);
        }

        $db->order('a.id desc');

        return parent::_list($db);

    }

    protected function _index_data_filter(&$data)
    {
    }

    public function importpg()
    {
        return $this->_form($this->table, 'importpg');
    }

    public function importsubmit(){
        $post = $this->request->post();
        $date = $post['date2'];
        $tvtype = $post['tvtype'];
        $this->success('恭喜, 数据保存成功!'.$date.$tvtype, '');
    }

}