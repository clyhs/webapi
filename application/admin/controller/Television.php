<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2017/10/12
 * Time: 8:53
 */

namespace app\admin\controller;

use app\common\controller\BaseAdmin;

class Television extends BaseAdmin{

    public $table = 'television';

    public function index(){
        return view('', ['title' => '电台管理']);
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
        $db = Db::name($this->table)->where("parentCode",$parentCode)->order('code asc');
        $data = $db->select();
        return json($data);
    }

}