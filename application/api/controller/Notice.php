<?php
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

class Notice extends BaseApiRest{
    public $table = 'notice';

    public function getNoticeForPage($page = 1,$pageSize = 15){
        $options=[
            'page'=>$page
        ];
        $lists = Db::field('a.*,b.name as typeName')
            ->table("t_notice")
            ->alias('a')
            ->join(' t_dict b','b.id=a.type_id ','left')
            ->group('a.id')
            ->order('a.id desc')
            ->paginate($pageSize,false,$options);

        $result = [
            "code"=>"10000",
            "desc"=>"",
            "data"=>$lists->all()
        ];
        return json($result);
    }

}