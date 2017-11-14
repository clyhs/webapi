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
        $list = Db::name($this->table)
            ->where("parentCode","100000")
            ->where('code not in (900000,820000,810000,710000)')
            ->order('code asc')->select();
        $result = [
            "code"=>10000,
            "desc"=>"",
            "data"=>$list
        ];
        return json($result);
    }

}