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

class Television extends BaseAdmin{

    public $table = 'television';

    public function index(){

        $this->title = '系统操作日志';
        $db = Db::name($this->table)->order('id desc');
        return parent::_list($db);
    }

    public function add()
    {
        $get = $this->request->get();


        $country = "100000";
        $countrys = Db::name("region")->where("code",$country)->order('code asc')->select();
        $this->assign('countrys', $countrys);
        $this->assign('country', $country);

        if(isset($get['province']) && $get['province'] !== ''){
            $province = $get['province'];
            $provinces = Db::name("region")->where("code",$province)->order('code asc')->select();
            $this->assign('provinces', $provinces);
            $this->assign('province', $province);
        }else{
            $this->assign('provinces', "");
        }
        if(isset($get['city']) && $get['city'] !== ''){
            $city = $get['city'];
            $citys = Db::name("region")->where("code",$city)->order('code asc')->select();
            $this->assign('citys', $citys);
            $this->assign('city', $city);
        }else{
            $this->assign('citys', "");
        }

        return $this->_form($this->table, 'form');
    }

    public function getchildregion($parentCode){
        $db = Db::name("region")->where("parentCode",$parentCode)->order('code asc');
        $data = $db->select();
        return json($data);
    }

}