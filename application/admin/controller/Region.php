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

        $get = $this->request->get();
        $country = $get['country']!=null?$get['country']:"";
        $province= $get['province']!=null?$get['province']:"";
        $city = $get['city']!=null?$get['city']:"";

        if(!$country==""){
            $countrys = Db::name($this->table)->where("code",$country)->order('code asc')->select();
            $this->assign('countrys', $countrys);
        }
        if(!$province==""){
            $provinces = Db::name($this->table)->where("code",$province)->order('code asc')->select();
            $this->assign('provinces', $provinces);
        }
        if(!$city==""){
            $citys = Db::name($this->table)->where("code",$city)->order('code asc')->select();
            $this->assign('citys', $citys);
        }
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

    public function getchildregion($parentCode){
        $db = Db::name($this->table)->where("parentCode",$parentCode)->order('code asc');
        $data = $db->select();
        return json($data);
    }
}