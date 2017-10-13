<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2017/10/13
 * Time: 14:21
 */

namespace app\api\controller;

use think\Request;
use think\controller\Rest;
use think\Db;

class Television extends Rest{
    /**
     * @param int $page
     * @param int $pageSize
     * @param $typeId
     * @return mixed
     */
    public function getTvForPage($page = 1,$pageSize = 15,$typeId){
        $options=[
            'page'=>$page
        ];
        $db = Db::name("television")->where("type_id",$typeId)->order('id asc')
            ->paginate($pageSize,false,$options);

        return json($db->select());
    }
}