<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2017/10/11
 * Time: 14:09
 */

namespace app\admin\controller;

use app\common\controller\BaseAdmin;
use app\common\service\DataService;
use app\common\service\ToolService;
use think\Db;

class Region extends BaseAdmin{

    public $table = 'region';

    public function index()
    {
        $this->title = '地区管理';
        $db = Db::name($this->table)->order('code asc');
        return parent::_list($db, true);

    }

    protected function _index_data_filter(&$data)
    {
        foreach ($data as &$vo) {
            ($vo['parentCode'] !== '100000') && ($vo['parentCode'] !=='0' );
            $vo['ids'] = join(',', ToolService::getArrSubIds($data, $vo['code'],"code","parentCode"));
        }
        $data = ToolService::arr2table($data,"code","parentCode");
    }
}