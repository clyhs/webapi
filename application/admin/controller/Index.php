<?php
namespace app\admin\controller;

use app\common\controller\BaseAdmin;

class Index extends BaseAdmin
{
    public function index()
    {
        return $this->fetch();
    }
}
