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

class User extends Rest{

    public function getAllUsers(){
        $model = model("User");
        $data = $model->all();
        return json($data);
    }

}