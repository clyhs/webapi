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

class Television extends BaseAdmin{

    public $table = 'television';

    public function index(){

        $this->title = '电视台管理';
        //$db = Db::name($this->table)->order('id desc');

        $get = $this->request->get();

        $db = Db::field('a.*,b.name as countryName,c.name as provinceName,GROUP_CONCAT(d.name) AS typeNames')
            ->table("t_television")
            ->alias('a')
            ->join(' t_region b ',' a.country = b.code ','left')
            ->join(' t_region c ',' a.province = c.code ','left')
            ->join(' t_dict d','FIND_IN_SET(d.id , a.type_ids) ','left')
            ->group('a.id');
        /*
        foreach ([ 'name'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                $db->where('a.'.$key, 'like', "%{$get[$key]}%");
            }
        }*/
        /*
        foreach ([ 'type_id'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {

                if($get[$key]>0){
                    $db->where('a.'.$key, '=', "{$get[$key]}");
                }

            }
        }*/
        $db->order('a.id asc');

        $where = [
            "char"=>"CHANNEL",
            "pid"=>1
        ];

        $channels = Db::name("dict")->where($where)->order('id asc')->select();
        $this->assign('channels', $channels);

        return parent::_list($db);
    }

    protected function _index_data_filter(&$data)
    {
    }

    public function add()
    {


        return $this->_form($this->table, 'form');
    }

    public function getchildregion($parentCode){
        $db = Db::name("region")->where("parentCode",$parentCode)->order('code asc');
        $data = $db->select();
        return json($data);
    }

    public function edit()
    {
        return $this->_form($this->table, 'form');
    }

    protected function _form_filter(&$vo)
    {
        if ($this->request->isPost()) {

            if (isset($vo['type_ids']) && is_array($vo['type_ids'])) {
                $vo['type_ids'] = join(',', $vo['type_ids']);
            }
        }

        if ($this->request->isGet()) {
            //$get = $this->request->get();
            if(isset($vo['country']) && $vo['country'] !== ''){
                $country = $vo['country'];
            }else{
                $country = 100000;
            }
            $countrys = Db::name("region")->where("code",$country)->order('code asc')->select();
            $this->assign('countrys', $countrys);
            $this->assign('country', $country);

            $provinces = Db::name("region")->where("parentCode",$country)->order('code asc')->select();
            $this->assign('provinces', $provinces);

            if(isset($vo['province']) && $vo['province'] !== ''){
                $province = $vo['province'];
                $this->assign('province', $province);
            }else{
                $province = 0;
                $this->assign('province', $province);
            }
            if(isset($vo['city']) && $vo['city'] !== ''){
                $city = $vo['city'];
                $citys = Db::name("region")->where("parentCode",$province)->order('code asc')->select();
                $this->assign('citys', $citys);
                $this->assign('city', $city);
            }else{
                $this->assign('citys', "");
            }

            $where = [
                "char"=>"CHANNEL",
                "pid"=>1
            ];

            $channels = Db::name("dict")->where($where)->order('id asc')->select();
            $this->assign('channels', $channels);

            $typeIds = explode($vo['type_ids']);
            $this->assign('typeIds', $typeIds);
        }
    }

    public function del()
    {
        if (DataService::update($this->table)) {
            $this->success("删除成功!", '');
        }
        $this->error("删除失败, 请稍候再试!");
    }

}