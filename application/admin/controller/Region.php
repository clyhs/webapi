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


        $country = "100000";
        $countrys = Db::name($this->table)->where("code",$country)->order('code asc')->select();
        $this->assign('countrys', $countrys);
        $this->assign('country', $country);

        if(isset($get['province']) && $get['province'] !== ''){
            $province = $get['province'];
            $provinces = Db::name($this->table)->where("code",$province)->order('code asc')->select();
            $this->assign('provinces', $provinces);
            $this->assign('province', $province);
        }else{
            $this->assign('provinces', "");
        }
        if(isset($get['city']) && $get['city'] !== ''){
            $city = $get['city'];
            $citys = Db::name($this->table)->where("code",$city)->order('code asc')->select();
            $this->assign('citys', $citys);
            $this->assign('city', $city);
        }else{
            $this->assign('citys', "");
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

    public function add()
    {
        $_menus = Db::name($this->table)->order('code asc')->select();
        $_menus[] = ['name' => '顶级地区', 'code' => '0', 'parentCode' => '0'];
        $menus = ToolService::arr2table($_menus,'code','parentCode');
        $this->assign('menus', $menus);
        return $this->_form($this->table, 'form','code');
    }

    /*
    protected function _form_filter(&$vo)
    {
        if ($this->request->isGet()) {
            // 上级菜单处理
            $_menus = Db::name($this->table)->order('code asc')->select();
            $_menus[] = ['name' => '顶级地区', 'code' => '0', 'parentCode' => '0'];
            $menus = ToolService::arr2table($_menus,'code','parentCode');
            
            foreach ($menus as $key => &$menu) {
                if (substr_count($menu['path'], '-') > 3) {
                    unset($menus[$key]);
                    continue;
                }
                if (isset($vo['parentCode'])) {
                    $current_path = "-{$vo['parentCode']}-{$vo['code']}";
                    if ($vo['parentCode'] !== '' && (stripos("{$menu['path']}-", "{$current_path}-") !== false || $menu['path'] === $current_path)) {
                        unset($menus[$key]);
                    }
                }
            }
            // 读取系统功能节点

            $this->assign('menus', $menus);

        }
    }*/
}