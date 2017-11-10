<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2017/11/10
 * Time: 14:37
 */

namespace app\api\controller;

use think\Request;
use think\Db;
use think\db\Query;
use think\Image;
use think\Config;
use think\File;
use app\common\service\DataService;
use app\common\service\FileService;
use app\admin\model\User as UserModel;
use app\common\controller\BaseApiRest;
use org\Upload;

class Chat extends BaseApiRest
{
    public $table = 'chat';

    public function addChat(){

        $type_id = empty(Request::instance()->param('type_id'))?0:Request::instance()->param('type_id');
        $context = empty(Request::instance()->param('context'))?"":Request::instance()->param('context');
        $user_id = empty(Request::instance()->param('user_id'))?0:Request::instance()->param('user_id');

        if(empty($type_id) || empty($context) || empty($user_id)){
            return json(["code"=>20001,"desc"=>"参数不能为空","data"=>[]]);
        }

        $filecount = count($_FILES);
        $rootpath = 'static' . DS . 'upload'  .DS;
        $savepath = 'chat/';
        //$savename = '';
        if($filecount > 0){
            $config = [
                'exts'=>['mp4','jpg','png'],
                'rootPath'=> $rootpath,
                'savePath'=>$savepath
                //'saveName'=>date('YmdHis')
            ];
            $upload = new Upload($config,'LOCAL');
            $info   =   $upload->upload();
            return json(["code"=>10000,"desc"=>"上传成功","data"=>$info]);
        }

        return json(["code"=>10000,"desc"=>"上传成功","data"=>count($_FILES)]);

    }

}