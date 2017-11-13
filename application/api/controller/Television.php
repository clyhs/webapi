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
use think\db\Query;
use think\Image;
use think\Config;
use app\common\service\DataService;
use app\common\service\FileService;

class Television extends Rest{

    public $table = 'television';

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

        $map[]=['exp','FIND_IN_SET('.$typeId.',a.type_ids)'];
        $lists = Db::field('a.*,b.name as countryName,c.name as provinceName,GROUP_CONCAT(d.name) AS typeNames')
            ->table("t_television")
            ->alias('a')
            ->join(' t_region b ',' a.country = b.code ','left')
            ->join(' t_region c ',' a.province = c.code ','left')
            ->join(' t_dict d','FIND_IN_SET(d.id , a.type_ids) ','left')
            ->where($map)
            ->group('a.id')
            ->order('a.id asc')
            ->paginate($pageSize,false,$options);

        $result = [
            "code"=>"10000",
            "desc"=>"",
            "data"=>$lists->all()
        ];
        return json($result);
    }

    /**
     *
     * 根据属性获取电视台记录
     * @param int $page
     * @param int $pageSize
     * @param int $typeId
     * @return mixed
     */
    public function getTvByProperty($page = 1,$pageSize = 15,$typeId=0){

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

        $lists = Db::field('a.*,b.name as countryName,c.name as provinceName,GROUP_CONCAT(d.name) AS typeNames')
            ->table("t_television")
            ->alias('a')
            ->join(' t_region b ',' a.country = b.code ','left')
            ->join(' t_region c ',' a.province = c.code ','left')
            ->join(' t_dict d','FIND_IN_SET(d.id , a.type_ids) ','left')
            ->where($where)
            ->group('a.id')
            ->order('a.id asc')
            ->paginate($pageSize,false,$options);


        $result = [
            "code"=>"10000",
            "desc"=>"",
            "data"=>$lists->all()
        ];
        return json($result);

    }


    /**
     * APP首页数据
     * @return mixed
     */
    public function getTvForIndex(){

        $hot=array(
            "is_hot"=>1
        );
        $recommend=array(
            "is_recommend"=>1
        );
        $new=array(
            "is_new"=>1
        );

        $lists_hot = Db::field('a.*,b.name as countryName,c.name as provinceName,GROUP_CONCAT(d.name) AS typeNames')
            ->table("t_television")
            ->alias('a')
            ->join(' t_region b ',' a.country = b.code ','left')
            ->join(' t_region c ',' a.province = c.code ','left')
            ->join(' t_dict d','FIND_IN_SET(d.id , a.type_ids) ','left')
            ->where($hot)
            ->group('a.id')
            ->order('a.hit desc')
            ->limit(4)->select();
        $lists_new = Db::field('a.*,b.name as countryName,c.name as provinceName,GROUP_CONCAT(d.name) AS typeNames')
            ->table("t_television")
            ->alias('a')
            ->join(' t_region b ',' a.country = b.code ','left')
            ->join(' t_region c ',' a.province = c.code ','left')
            ->join(' t_dict d','FIND_IN_SET(d.id , a.type_ids) ','left')
            ->where($new)
            ->group('a.id')
            ->order('a.id desc')
            ->limit(4)->select();
        $lists_recommend = Db::field('a.*,b.name as countryName,c.name as provinceName,GROUP_CONCAT(d.name) AS typeNames')
            ->table("t_television")
            ->alias('a')
            ->join(' t_region b ',' a.country = b.code ','left')
            ->join(' t_region c ',' a.province = c.code ','left')
            ->join(' t_dict d','FIND_IN_SET(d.id , a.type_ids) ','left')
            ->where($recommend)
            ->group('a.id')
            ->order('a.id desc')
            ->limit(4)->select();
        $result_array = [
            "hots"=>$lists_hot,
            "recommends"=>$lists_recommend,
            "news"=>$lists_new
        ];
        $result = [
            "code"=>"10000",
            "desc"=>"",
            "data"=>$result_array
        ];
        return json($result);
    }

    /**获取用户历史 或者 关注的记录
     * @param int $page
     * @param int $pageSize
     * @param $userId
     * @param $typeId
     * @return mixed
     */
    public function getTvByUserIdAndTypeId($page = 1,$pageSize = 15,$userId,$typeId){

        $where = [
            "e.user_id"=>$userId,
            "e.type_id"=>$typeId
        ];

        $options=[
            'page'=>$page
        ];

        $lists = Db::field('a.*,b.name as countryName,c.name as provinceName,GROUP_CONCAT(d.name) AS typeNames')
            ->table("t_television")
            ->alias('a')
            ->join(' t_region b ',' a.country = b.code ','left')
            ->join(' t_region c ',' a.province = c.code ','left')
            ->join(' t_dict d','FIND_IN_SET(d.id , a.type_ids) ','left')
            ->join(' t_user_tv e','e.tv_id = a.id ')
            ->where($where)
            ->group('a.id')
            ->order('a.id asc')
            ->paginate($pageSize,false,$options);

        $result = [
            "code"=>"10000",
            "desc"=>"",
            "data"=>$lists->all()
        ];
        return json($result);
    }

    /**上传封面图
     * @return mixed
     */
    public function uploadbg(){

        try{

            $id = empty(Request::instance()->param('id'))?"":Request::instance()->param('id');

            if(empty($id)){
                return json(["code"=>20001,"desc"=>"ID不能为空","data"=>[]]);
            }
            if(empty(Request::instance()->file())){
                return json(["code"=>20001,"desc"=>"上传失败,请选择文件上传","data"=>[]]);
            }

            if(empty(Request::instance()->file('bg'))){
                return json(["code"=>20001,"desc"=>"上传失败,参数不存在","data"=>[]]);
            }else{
                //$image = Request::instance()->file('profile');
                $file = Image::open(Request::instance()->file('bg'));
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
                    "bg"=>$fileurl
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

    public function updateHit($id){

        $sql = 'update t_television set hit=hit+1 where id='.$id;

        Db::query($sql);

        $result = [
            "code"=>10000,
            "desc"=>"success"
        ];

        return json($result);

    }

    public function getalltv(){

        $db = Db::name("dict")
            ->where('pid','=',1)
            ->where('id','<>',16)
            ->order('sort asc,id asc');

        $result = [
            "code"=>10000,
            "desc"=>"",
            "data"=>$this->filterData($db)
        ];
        return json($result);
    }

    protected function filterData(&$db){

        $lists = $db->select();
        foreach ($lists as $key => &$item) {
            $sql = 'select a.* from t_television a '.
                ' where FIND_IN_SET('.$item['id'].' , a.type_ids) order by a.id asc';
            $childrens =Db::query($sql);
            $lists[$key]['tvs'] = $childrens;
        }
        return $lists;
    }

    public function getTvForPageByProvince($page = 1,$pageSize = 15,$code){
        $where=[
            'province'=>$code
        ];

        $options=[
            'page'=>$page
        ];

        $lists = Db::field('a.*,b.name as countryName,c.name as provinceName,GROUP_CONCAT(d.name) AS typeNames')
            ->table("t_television")
            ->alias('a')
            ->join(' t_region b ',' a.country = b.code ','left')
            ->join(' t_region c ',' a.province = c.code ','left')
            ->join(' t_dict d','FIND_IN_SET(d.id , a.type_ids) ','left')
            ->where($where)
            ->group('a.id')
            ->order('a.id asc')
            ->paginate($pageSize,false,$options);

        $result = [
            "code"=>"10000",
            "desc"=>"",
            "data"=>$lists->all()
        ];
        return json($result);
    }

    public function addTVByUserId($type_id,$userId,$tv_id){
        if(empty($type_id) || empty($userId) || empty($tv_id)){
            return json(["code"=>20001,"desc"=>"参数不能为空","data"=>[]]);
        }

        $data=[
            'user_id'=>$userId,
            'tv_id'=>$tv_id,
            'type_id'=>$type_id
        ];

        $id = Db::name('user_tv')->where($data)->value('id');
        if($id){
            return json(["code"=>20001,"desc"=>"已经收藏","data"=>[]]);
        }
        $db = Db::name('user_tv');
        $pk = $db->getPk() ? $db->getPk() : 'id';
        $result = DataService::save($db, $data, $pk, []);

        if($result !== false){
            return json(["code"=>10000,"desc"=>"关注成功","data"=>$data]);
        }else{
            return json(["code"=>20001,"desc"=>"失败","data"=>$data]);
        }

    }
}