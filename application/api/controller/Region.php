<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2017/11/13
 * Time: 9:31
 */
namespace app\api\controller;

use think\Request;
use think\controller\Rest;
use think\Db;
use think\db\Query;
use think\Image;
use think\Config;
use app\common\service\DataService;
use app\common\service\FileService;

class Region extends Rest{

    public $table = 'region';

    public function getProvinces(){
        $list = Db::name($this->table)->where("parentCode","100000")->order('code asc')->select();
        $result = [
            "code"=>10000,
            "desc"=>"",
            "data"=>$this->$list->all()
        ];
        return json($result);
    }

}