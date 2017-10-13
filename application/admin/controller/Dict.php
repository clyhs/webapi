<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2017/10/13
 * Time: 9:22
 */

namespace app\admin\controller;

use app\common\controller\BaseAdmin;
use app\common\service\ToolService;
use app\common\service\DataService;

class Dict extends BaseAdmin{

    public $table = 'dict';

    public function index()
    {
        $this->title = '系统字典管理';
        $db = Db::name($this->table)->order('sort asc,id asc');
        return parent::_list($db, false);
    }

    protected function _index_data_filter(&$data)
    {
        foreach ($data as &$vo) {
            $vo['ids'] = join(',', ToolService::getArrSubIds($data, $vo['id']));
        }
        $data = ToolService::arr2table($data);
    }

    public function del()
    {
        if (DataService::update($this->table)) {
            $this->success("删除成功!", '');
        }
        $this->error("删除失败, 请稍候再试!");
    }

}