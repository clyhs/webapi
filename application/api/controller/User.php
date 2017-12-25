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
use think\Image;
use think\Config;
use app\common\service\DataService;
use app\common\service\FileService;
use app\admin\model\User as UserModel;
use app\common\controller\BaseApiRest;

class User extends BaseApiRest{

    public $table = 'user';

    /**
     * @return mixed
     */
    public function register(){

        $db = Db::name($this->table);
        $pk = $db->getPk() ? $db->getPk() : 'id';
        $username = empty(Request::instance()->param('username'))?"":Request::instance()->param('username');
        $mail = empty(Request::instance()->param('mail'))?"":Request::instance()->param('mail');
        $password = empty(Request::instance()->param('password'))?"":Request::instance()->param('password');
        $phone = empty(Request::instance()->param('phone'))?"":Request::instance()->param('phone');


        if(empty($username) || empty($mail) || empty($password) || empty($phone)){
            return json(["code"=>20001,"desc"=>"参数出错"]);
        }

        $user = $db->where('username', $username)->find();
        if(empty($user)){

            $data = [
                "username"=>$username,
                "password"=>md5($password),
                "mail"=>$mail,
                "phone"=>$phone,
                "status"=>1
            ];
            $result = DataService::save($db, $data, $pk, []);
            if ($result !== false) {
                return json(["code"=>10000,"desc"=>"success"]);
            }else{
                return json(["code"=>20001,"desc"=>"添加失败"]);
            }

        }else{
            return json(["code"=>20001,"desc"=>"用户已经存在"]);
        }

    }

    /**
     * @return mixed
     */
    public function login(){
        $db = Db::name($this->table);
        $pk = $db->getPk() ? $db->getPk() : 'id';
        $username = empty(Request::instance()->param('username'))?"":Request::instance()->param('username');
        $password = empty(Request::instance()->param('password'))?"":Request::instance()->param('password');
        if(empty($username) || empty($password) ){
            return json(["code"=>20001,"desc"=>"参数出错"]);
        }
        $user = $db->where('username', $username)->find();

        /*
        $user =  Db::field('a.*,count(b.*) history,count(c.*) like ')
            ->table("t_user")
            ->alias('a')
            ->join(' t_user_tv b ',' b.type_id = 15 and a.id=b.user_id ','left')
            ->join(' t_user_tv c ',' a.type_id = 14 and a.id=c.user_id ','left')
            ->where($where)
            ->group('a.id')
            ->order('a.id asc')->select();*/

        if(empty($user)){
            return json(["code"=>20001,"desc"=>"登录账号不存在，请重新输入!"]);
        }
        if(($user['password'] !== md5($password))){
            return json(["code"=>20001,"desc"=>"登录密码与账号不匹配，请重新输入!"]);
        }
        if(empty($user['status'])){
            return json(["code"=>20001,"desc"=>"账号已经被禁用，请联系管理!"]);
        }
        // 更新登录信息
        $data = ['login_at' => ['exp', 'now()'], 'login_num' => ['exp', 'login_num+1']];
        Db::name('user')->where(['id' => $user['id']])->update($data);

        return json(["code"=>10000,"desc"=>"登录成功","data"=>$this->_login_filter($user)]);
    }

    private function _login_filter($vo){

        $sql = 'select count(1) as history from t_user_tv a inner join t_television b on b.id=a.tv_id where a.user_id='.$vo['id'].' '.
               ' and a.type_id=15 ';
        $row =Db::query($sql);
        $vo['history'] = $row[0]['history'];
        $sql = 'select count(1) as likenum from t_user_tv a inner join t_television b on b.id=a.tv_id where a.user_id='.$vo['id'].' '.
               ' and a.type_id=14 ';
        $row2 = Db::query($sql);
        $vo['likenum'] =$row2[0]['likenum'];
        $sql = 'select count(1) as notices from t_notice';
        $row3 = Db::query($sql);
        $vo['notices'] =$row3[0]['notices'];
        return $vo;
    }

    public function profile(){

        try{

            $id = empty(Request::instance()->param('id'))?"":Request::instance()->param('id');

            if(empty($id)){
                return json(["code"=>20001,"desc"=>"ID不能为空","data"=>[]]);
            }
            if(empty(Request::instance()->file())){
                return json(["code"=>20001,"desc"=>"上传失败,请选择文件上传","data"=>[]]);
            }

            if(empty(Request::instance()->file('profile'))){
                return json(["code"=>20001,"desc"=>"上传失败,参数不存在","data"=>[]]);
            }else{
                //$image = Request::instance()->file('profile');
                $file = Image::open(Request::instance()->file('profile'));
                $filemimes = explode('|',Config::get('filemime'));

                if(empty($file)){
                    return json(["code"=>20001,"desc"=>"上传失败,文件无法打开","data"=>[]]);
                }

                if(!in_array($file->mime(),$filemimes)){
                    return json(["code"=>20001,"desc"=>"类型错误","data"=>[]]);
                }

                $ext = Config::get('filemimes')[$file->mime()];
                $md51 = join('/',str_split(md5(mt_rand(10000,99999)),16));
                $md52 = join('/',str_split(md5(mt_rand(10000,99999)),16));
                $filePath = 'static' . DS . 'upload'  .DS.$md51.$md52;
                if(!file_exists($filePath)){
                    mkdir($filePath,'0755', true);
                }

                $filePath = $filePath.".".$ext;
                $file->save($filePath);
                $fileurl = FileService::getBaseUriLocal().$md51.$md52.".".$ext;
                $data = [
                    "id"=>$id,
                    "profile"=>$fileurl
                ];

                $db = Db::name($this->table);
                $pk = $db->getPk() ? $db->getPk() : 'id';
                $result = DataService::save($db, $data, $pk, []);

                if($result !== false){
                    return json(["code"=>10000,"desc"=>"上传成功","data"=>$data]);
                }else{
                    return json(["code"=>20001,"desc"=>"更新失败","data"=>$data]);
                }

            }

        }catch(\Exception $e){
            return json(["code"=>20001,"desc"=>"上传异常","data"=>[]]);
        }

    }

    public function getFriendsForPage($userId,$page = 1,$pageSize = 15){

        $options=[
            'page'=>$page
        ];
        $where = [
            'b.user_id'=>$userId
        ];
        //select a.* from t_user a
        //left join t_user_friend b on b.friend_id = a.id
        //where b.user_id=10000

        $lists = Db::field('a.* ')
            ->table("t_user")
            ->alias('a')
            ->join(' t_user_friend b ',' a.id = b.friend_id ','left')
            ->where($where)
            ->order('a.id asc')
            ->paginate($pageSize,false,$options);

        $result = [
            "code"=>"10000",
            "desc"=>"",
            "data"=>$lists->all()
        ];

        return json($result);
    }

    public function updateGoodLog(){

        $uid = empty(Request::instance()->param('uid'))?0:Request::instance()->param('uid');
        $type_id = empty(Request::instance()->param('type_id'))?0:Request::instance()->param('type_id');
        $userId = empty(Request::instance()->param('userId'))?0:Request::instance()->param('userId');
        $type = empty(Request::instance()->param('type'))?0:Request::instance()->param('type');

        $db= Db::name("good_log") ;
        $pk ='id';

        $data = array();
        $data=[
            'user_id'=>$userId,
            'uid'=>$uid,
            'type_id'=>$type_id

        ];

        $id = Db::name("good_log")->where("uid=".$uid." and user_id=".$userId." and type_id=".$type_id)->column('id');

        if(is_array($id)){
            if($id[0]>0){
                $data['id'] = $id[0];
            }
        }

        if($type > 0){
            $data['good'] = 1;
        }else{
            $data['good'] = 0;
        }

        $result = DataService::save($db, $data, $pk, []);

        $result = [
            "code"=>"10000",
            "desc"=>""
        ];

        return json($result);

    }

//    public function getAllUsers(){
//        $model = new UserModel();
//        $data = $model->all();
//        $result = [
//            "code"=>10000,
//            "desc"=>"",
//            "data"=>$data
//        ];
//        return json($result);
//    }
//
//    public function getPageForUser($page = 1,$pageSize = 10){
//        $model = new UserModel();
//        //$count = $model->count();
//        $options=[
//            'page'=>$page
//        ];
//
//        $list  = $model->paginate($pageSize,false,$options);
//        $result = [
//            "code"=>10000,
//            "desc"=>"",
//            "data"=>$list
//        ];
//        return json($result);
//    }
//
//    public function getUserById($id){
//        $model = db('user');
//        $data  = $model->where("id",$id)->find();
//
//        $result = [
//            "code"=>10000,
//            "desc"=>"",
//            "data"=>$data
//        ];
//        return json($result);
//    }



}