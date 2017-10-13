<?php
namespace app\api\controller;

use think\Request;
use think\controller\Rest;

class Index extends Rest
{
    public function index()
    {
        return "hello webapi";
    }
}
