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
use org\Upload;

class Video extends BaseApiRest{

    public $table = 'video';

    public function uploadVideo(){


        try{

            //$tempFile = $_FILES['vfile']['name'];

            $id = empty(Request::instance()->param('id'))?"":Request::instance()->param('id');
            if(empty($id)){
                return json(["code"=>20001,"desc"=>"ID不能为空","data"=>[]]);
            }
            if(empty(Request::instance()->file())){
                return json(["code"=>20001,"desc"=>"上传失败,请选择文件上传","data"=>[]]);
            }

            if(empty(Request::instance()->file('vfile'))){
                return json(["code"=>20001,"desc"=>"上传失败,参数vfile不存在","data"=>[]]);
            }

            if(empty(Request::instance()->file('cover'))){
                return json(["code"=>20001,"desc"=>"上传失败,参数cover不存在","data"=>[]]);
            }

            //$file = Request::instance()->file('vfile');

            /*********vfile start********/

            $config = [
                'exts'=>['mp4'],
                'rootPath'=> 'static' . DS . 'upload'  .DS,
                'savePath'=>'video/',
                'saveName'=>date('YmdHis')
            ];
            $upload = new Upload($config,'LOCAL');
            $info   =   $upload->upload();

            /*********vfile end ********/
            /*********cover start********/
            $file = Image::open(Request::instance()->file('cover'));
            $filemimes = explode('|',Config::get('filemime'));
            if(empty($file)){
                return json(["code"=>20001,"desc"=>"上传失败,文件无法打开","data"=>[]]);
            }
            if(!in_array($file->mime(),$filemimes)){
                return json(["code"=>20001,"desc"=>"类型错误","data"=>[]]);
            }
            $ext = Config::get('filemimes')[$file->mime()];

            /*********cover end********/
            if($info){

                $filename = $info['vfile']['savename'];
                $uploadPath = FileService::getBaseUriLocal().$info['vfile']['savepath'];
                $fullpath = $uploadPath.$filename;
                $size = $info['vfile']['size'];

                //$md51 = join('/',str_split(md5(mt_rand(10000,99999)),16));

                $coverFilePath = $uploadPath.date('YmdHis').".".$ext;
                $file->save($coverFilePath);

                $data = [
                    'url'=> $fullpath,
                    'hit'=>0,
                    'status'=>0,
                    'title'=>'test',
                    'size'=>$size,
                    'cover'=>$coverFilePath,
                    'user_id'=>$id
                ];

                $db = Db::name($this->table);
                $pk = $db->getPk() ? $db->getPk() : 'id';
                $result = DataService::save($db, $data, $pk, []);

                if($result !== false){
                    return json(["code"=>10000,"desc"=>"上传成功","data"=>$data]);
                }else{
                    return json(["code"=>20001,"desc"=>"保存成功","data"=>$data]);
                }
            }else{
                return json(["code"=>20001,"desc"=>"上传失败","data"=>[]]);
            }


            //$info = $file->validate(['size'=>156780,'ext'=>'mp4']);
            /*
            if($info){
                $md51 = join('/',str_split(md5(mt_rand(10000,99999)),16));
                $filePath = 'static' . DS . 'upload'  .DS.$md51;

            }*/
        }catch(\Exception $e){
            return json(["code"=>20001,"desc"=>"上传异常","data"=>$e->getMessage()]);
        }


    }
}