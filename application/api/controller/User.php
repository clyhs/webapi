<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2017/10/10
 * Time: 11:00
 */

namespace app\api\controller;

use think\Request;
use think\Db;
use think\db\Query;
use app\common\service\DataService;
use app\admin\model\User as UserModel;
use app\common\controller\BaseApiRest;

class User extends BaseApiRest{

    public $table = 'user';

    public function register(){

        $db = Db::name($this->table);
        $pk = $db->getPk() ? $db->getPk() : 'id';
        $username = $this->request->post('username', '', 'trim');
        $user = $db->where('username', $username)->find();
        if(empty($user)){
            if($this->request->isPost()){
                $data = array_merge($this->request->post(), []);
                $result = DataService::save($db, $data, $pk, []);
                if ($result !== false) {
                    return json(["code"=>10000,"desc"=>"success"]);
                }else{
                    return json(["code"=>20000,"desc"=>"添加失败"]);
                }
            }
        }else{
            return json(["code"=>20000,"desc"=>"用户已经存在"]);
        }

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