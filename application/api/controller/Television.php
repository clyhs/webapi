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
    public function getTvForPageByType($page = 1,$pageSize = 15,$typeId){
        $options=[
            'page'=>$page
        ];
        $map[]=['exp','FIND_IN_SET('+$typeId+',type_ids)'];

        $lists = Db::name("television")->where($map)->order('id asc')
            ->paginate($pageSize,false,$options)->getLastSql();

        return json($lists);
    }

    public function getTvByProperty($page = 1,$pageSize = 15,$typeId=0){

        $lists = array();
        $where = array();
        if( $typeId> 0){

            switch($typeId){
                case 1:
                    $where=array(
                        "is_new"=>1
                    );
                    break;
                case 2:
                    $where=array(
                        "is_hot"=>1
                    );
                    break;
                case 3:
                    $where=array(
                        "is_recommend"=>1
                    );
                    break;
                default :
                    $where=array();
                    break;
            }
        }
        $options=[
            'page'=>$page
        ];
        $lists = Db::name("television")->where($where)->order('id asc')
            ->paginate($pageSize,false,$options);
        return json($lists);

    }
}