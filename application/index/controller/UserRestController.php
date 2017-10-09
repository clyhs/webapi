<?php
/**
 * Created by PhpStorm.
 * User: chenliyu
 * Date: 17/10/9
 * Time: 下午11:12
 */

namespace app\index\controller;
use think\Request;
use think\controller\Rest;

class UserRestController extends Rest{

    public function index(){
        return "hello world!";
    }
}