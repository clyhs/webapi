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

        $data = [
            'context'=>$context,
            'user_id'=>$user_id,
            'type_id'=>$type_id
        ];

        $db = Db::name($this->table);
        $pk = $db->getPk() ? $db->getPk() : 'id';
        $result = DataService::save($db, $data, $pk, []);
        $chat_id = Db::name($this->table)->getLastInsID();

        if($filecount > 0 && $result){
            $config = [
                'exts'=>['mp4','jpg','png'],
                'rootPath'=> $rootpath,
                'savePath'=>$savepath
                //'saveName'=>date('YmdHis')
            ];
            $upload = new Upload($config,'LOCAL');
            $info   =   $upload->upload();
            //return json(["code"=>10000,"desc"=>"上传成功","data"=>$info]);
            if($info && $chat_id>0){
                $info_num = count($info);
                for($i=1;$i<$info_num+1;$i++){

                    $f = $info["file_$i"];
                    $filename = $f["savename"];
                    $filepath = $f["savepath"];
                    $size = $f["size"];
                    $url =  FileService::getBaseUriLocal().$filepath.$filename;
                    $chat_info = [
                        'chat_id'=>$chat_id,
                        'url'=>$url,
                        'size'=>$size
                    ];
                    $chat_info_db = Db::name("chat_info");
                    $chat_result = $chat_info_db->insert($chat_info);
                }
                return json(["code"=>10000,"desc"=>"成功"]);
            }else{
                return json(["code"=>20002,"desc"=>"文件没有上传成功"]);
            }

        }

        return json(["code"=>10000,"desc"=>"保存成功"]);

    }

}