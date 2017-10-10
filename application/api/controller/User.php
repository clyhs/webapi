<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2017/10/10
 * Time: 11:00
 */

namespace app\api\controller;

use think\Request;
use think\controller\Rest;
use app\admin\model\User as UserModel;

class User extends Rest{

    public function getAllUsers(){
        $model = new UserModel();
        $data = $model->all();
        return json($data);
    }

    public function getPageForUser(){
        $model = new UserModel();
        $count = $model->where()->count();
        $list  = $model->where()->paginate(10);
        return json($list);
    }

}