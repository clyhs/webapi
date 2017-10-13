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
        $db = Db::name($this->table)->order('sort asc,id asc');
        return parent::_list($db, true);
    }

    protected function _index_data_filter(&$data)
    {
        foreach ($data as &$vo) {
            $vo['ids'] = join(',', ToolService::getArrSubIds($data, $vo['id']));
        }
        $data = ToolService::arr2table($data);
    }

}