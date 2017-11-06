<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2017/11/6
 * Time: 9:18
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

class Video extends BaseApiRest{

    public $table = 'video';

    public function uploadVideo(){

        try{
            $file = Request::instance()->file('vfile');

            //$info = $file->validate(['size'=>156780,'ext'=>'mp4']);
            /*
            if($info){
                $md51 = join('/',str_split(md5(mt_rand(10000,99999)),16));
                $filePath = 'static' . DS . 'upload'  .DS.$md51;

            }*/

            return json(["code"=>10000,"desc"=>"上传成功","data"=>$file]);

        }catch(\Exception $e){
            return json(["code"=>20001,"desc"=>"上传异常","data"=>[]]);
        }


    }
}