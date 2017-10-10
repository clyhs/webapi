<?php
namespace app\admin\controller;

use app\common\controller\BaseAdmin;
use think\Db;
use think\View;

class Index extends BaseAdmin
{
    public function index()
    {
        $menus = array();
        return view('', ['title' => '系统管理', 'menus' => $menus]);
    }
}
