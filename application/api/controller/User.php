<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2017/10/10
 * Time: 11:00
 */

namespace app\api\controller;


use app\admin\model\User as UserModel;
use app\common\controller\BaseApiRest;

class User extends BaseApiRest{

    public $table = 'user';

    public function register(){
        return $this->_form($this->table);
    }

    protected function _form_filter(&$vo)
    {

    }

    protected function _form_result(&$data){
        return json(["code"=>10000,"desc"=>"success"]);
    }

    public function getAllUsers(){
        $model = new UserModel();
        $data = $model->all();
        return json($data);
    }

    public function getPageForUser($page = 1,$pageSize = 10){
        $model = new UserModel();
        //$count = $model->count();
        $options=[
            'page'=>$page
        ];

        $list  = $model->paginate($pageSize,false,$options);
        return json($list,10000);
    }

    public function getUserById($id){
        $model = db('user');
        $data  = $model->where("id",$id)->find();
        return $this->response($data, 'json', 10000);
    }

}