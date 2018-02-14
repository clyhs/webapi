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
use QL\QueryList;

class Television extends Rest{

    public $table = 'television';

    /**
     * @param int $page
     * @param int $pageSize
     * @param $typeId
     * @return mixed
     */
    public function getTvForPageByType($page = 1,$pageSize = 20,$typeId){
        $options=[
            'page'=>$page
        ];
        $date = date('Ymd',time());
        $map[]=['exp','FIND_IN_SET('.$typeId.',a.type_ids)'];
        $lists = Db::field('a.*,b.name as countryName,c.name as provinceName,GROUP_CONCAT(d.name) AS typeNames,
        (select count(1) from t_comment e where e.uid=a.id and e.type_id=11 and e.pid=0 ) as commentNum,
        (select sum(good) from t_good_log f where f.uid=a.id and f.type_id=11 ) as goodNum ')
            ->table("t_television")
            ->alias('a')
            ->join(' t_region b ',' a.country = b.code ','left')
            ->join(' t_region c ',' a.province = c.code ','left')
            ->join(' t_dict d','FIND_IN_SET(d.id , a.type_ids) ','left')
            //->join(' t_comment e',' e.uid=a.id and e.type_id=11 and e.pid=0 ','left')
            ->where($map)
            ->group('a.id')
            ->order('a.id,a.name asc')
            ->paginate($pageSize,false,$options);
        $data = $lists->all();

        for($i = 0;$i<count($data);$i++){
             $sql = "select a.title from t_television_program a ".
                   " where a.tv_id=".$data[$i]['id']." and a.play_date='".$date."'".
                   " and a.play_at < now() order by a.play_at desc limit 1";
            $row =Db::query($sql);
            if(count($row) == 1){
                $data[$i]['playtitle'] = $row[0]['title'];
            }else{
                $data[$i]['playtitle'] = '';
            }

        }

        $result = [
            "code"=>"10000",
            "desc"=>count($data),
            "data"=>$data
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
    public function getTvByProperty($page = 1,$pageSize = 20,$typeId=0){

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

        $lists = Db::field('a.*,b.name as countryName,c.name as provinceName,GROUP_CONCAT(d.name) AS typeNames ,
        (select count(1) from t_comment e where e.uid=a.id and e.type_id=11 and e.pid=0 ) as commentNum,
        (select sum(good) from t_good_log f where f.uid=a.id and f.type_id=11 ) as goodNum  ')
            ->table("t_television")
            ->alias('a')
            ->join(' t_region b ',' a.country = b.code ','left')
            ->join(' t_region c ',' a.province = c.code ','left')
            ->join(' t_dict d','FIND_IN_SET(d.id , a.type_ids) ','left')
            //->join(' t_comment e',' e.uid=a.id and e.type_id=11 and e.pid=0 ','left')
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

        $code = empty(Request::instance()->param('postcode'))?"110000":Request::instance()->param('postcode');

        $hot=array(
            "is_hot"=>1
        );
        $recommend=array(
            "a.province"=>$code

        );
        $new=array(
            "is_new"=>1
        );

        $lists_hot = Db::field('a.*,b.name as countryName,c.name as provinceName,GROUP_CONCAT(d.name) AS typeNames ,
        (select count(1) from t_comment e where e.uid=a.id and e.type_id=11 and e.pid=0 ) as commentNum ,
        (select sum(good) from t_good_log f where f.uid=a.id and f.type_id=11 ) as goodNum ')
            ->table("t_television")
            ->alias('a')
            ->join(' t_region b ',' a.country = b.code ','left')
            ->join(' t_region c ',' a.province = c.code ','left')
            ->join(' t_dict d','FIND_IN_SET(d.id , a.type_ids) ','left')
            //->join(' t_comment e',' e.uid=a.id and e.type_id=11 and e.pid=0 ','left')
            //->where($hot)
            ->group('a.id')
            ->order('a.hit desc')
            ->limit(4)->select();
        $lists_new = Db::field('a.*,b.name as countryName,c.name as provinceName,GROUP_CONCAT(d.name) AS typeNames,
        (select count(1) from t_comment e where e.uid=a.id and e.type_id=11 and e.pid=0 ) as commentNum,
        (select sum(good) from t_good_log f where f.uid=a.id and f.type_id=11 ) as goodNum   ')
            ->table("t_television")
            ->alias('a')
            ->join(' t_region b ',' a.country = b.code ','left')
            ->join(' t_region c ',' a.province = c.code ','left')
            ->join(' t_dict d','FIND_IN_SET(d.id , a.type_ids) ','left')
            //->join(' t_comment e',' e.uid=a.id and e.type_id=11 and e.pid=0 ','left')
            //->where($new)
            ->group('a.id')
            ->order(' rand() ')
            //->order('a.id desc')
            ->limit(2)->select();
        $lists_recommend = Db::field('a.*,b.name as countryName,c.name as provinceName,GROUP_CONCAT(d.name) AS typeNames ,
        (select count(1) from t_comment e where e.uid=a.id and e.type_id=11 and e.pid=0 ) as commentNum ,
        (select sum(good) from t_good_log f where f.uid=a.id and f.type_id=11 ) as goodNum ')
            ->table("t_television")
            ->alias('a')
            ->join(' t_region b ',' a.country = b.code ','left')
            ->join(' t_region c ',' a.province = c.code ','left')
            ->join(' t_dict d','FIND_IN_SET(d.id , a.type_ids) ','left')
            //->join(' t_comment e',' e.uid=a.id and e.type_id=11 and e.pid=0 ','left')
            ->where($recommend)
            ->group('a.id')
            ->order(' rand() ')
            //->order('a.id desc')
            ->limit(4)->select();
        $lists_cartoon = Db::field('a.*,b.name as countryName,c.name as provinceName,GROUP_CONCAT(d.name) AS typeNames ,
        (select count(1) from t_comment e where e.uid=a.id and e.type_id=11 and e.pid=0 ) as commentNum,
        (select sum(good) from t_good_log f where f.uid=a.id and f.type_id=11 ) as goodNum  ')
            ->table("t_television")
            ->alias('a')
            ->join(' t_region b ',' a.country = b.code ','left')
            ->join(' t_region c ',' a.province = c.code ','left')
            ->join(' t_dict d','FIND_IN_SET(d.id , a.type_ids) ','left')
            //->join(' t_comment e',' e.uid=a.id and e.type_id=11 and e.pid=0 ','left')
            ->where(' d.id = 23')
            ->group('a.id')
            ->order(' rand() ')
            //->order('a.id desc')
            ->limit(4)->select();
        $lists_foreign = Db::field('a.*,b.name as countryName,c.name as provinceName,GROUP_CONCAT(d.name) AS typeNames,
        (select count(1) from t_comment e where e.uid=a.id and e.type_id=11 and e.pid=0 ) as commentNum ,
        (select sum(good) from t_good_log f where f.uid=a.id and f.type_id=11 ) as goodNum  ')
            ->table("t_television")
            ->alias('a')
            ->join(' t_region b ',' a.country = b.code ','left')
            ->join(' t_region c ',' a.province = c.code ','left')
            ->join(' t_dict d','FIND_IN_SET(d.id , a.type_ids) ','left')
            ->join(' t_comment e',' e.uid=a.id and e.type_id=11 and e.pid=0 ','left')
            ->where(' d.id = 17')
            ->group('a.id')
            ->order(' rand() ')
            //->order('a.id desc')
            ->limit(2)->select();
        $lists_hongkong = Db::field('a.*,b.name as countryName,c.name as provinceName,GROUP_CONCAT(d.name) AS typeNames,
        (select count(1) from t_comment e where e.uid=a.id and e.type_id=11 and e.pid=0 ) as commentNum ,
        (select sum(good) from t_good_log f where f.uid=a.id and f.type_id=11 ) as goodNum ')
            ->table("t_television")
            ->alias('a')
            ->join(' t_region b ',' a.country = b.code ','left')
            ->join(' t_region c ',' a.province = c.code ','left')
            ->join(' t_dict d','FIND_IN_SET(d.id , a.type_ids) ','left')
            ->join(' t_comment e',' e.uid=a.id and e.type_id=11 and e.pid=0 ','left')
            ->where(' d.id = 18')
            ->group('a.id')
            ->order(' rand() ')
            //->order('a.id desc')
            ->limit(4)->select();
        $result_array = [
            "hots"=>$lists_hot,
            "recommends"=>$lists_recommend,
            "news"=>$lists_new,
            "cartoons"=>$lists_cartoon,
            "foreigns"=>$lists_foreign,
            "hongkongs"=>$lists_hongkong
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
    public function getTvByUserIdAndTypeId($userId,$typeId,$page = 1,$pageSize = 20){

        $where = [
            "a.user_id"=>$userId,
            "a.type_id"=>$typeId
        ];

        $options=[
            'page'=>$page
        ];
        /*
        $lists = Db::field('a.*,b.name as countryName,c.name as provinceName,GROUP_CONCAT(d.name) AS typeNames')
            ->table("t_television")
            ->alias('a')
            ->join(' t_region b ',' a.country = b.code ','left')
            ->join(' t_region c ',' a.province = c.code ','left')
            ->join(' t_dict d','FIND_IN_SET(d.id , a.type_ids) ','left')
            ->join(' t_user_tv e','e.tv_id = a.id ','left')
            ->where($where)
            ->group('a.id')
            ->order('a.id desc')
            ->paginate($pageSize,false,$options);*/
        $lists = Db::field('b.*,c.name as countryName,d.name as provinceName,GROUP_CONCAT(e.name) AS typeNames,
        (select count(1) from t_comment e where e.uid=a.id and e.type_id=11 and e.pid=0 ) as commentNum ,
        (select sum(good) from t_good_log f where f.uid=a.id and f.type_id=11 ) as goodNum ')
            ->table("t_user_tv")
            ->alias('a')
            ->join(' t_television b ',' a.tv_id = b.id ')
            ->join(' t_region c ',' b.country = c.code ','left')
            ->join(' t_region d ',' b.province = d.code ','left')
            ->join(' t_dict e','FIND_IN_SET(e.id , b.type_ids) ','left')
            ->where($where)
            ->group('a.id')
            ->order('a.create_at desc')
            ->paginate($pageSize,false,$options);


        $result = [
            "code"=>"10000",
            "desc"=>"",
            "data"=>$lists->all()
        ];
        return json($result);
    }

    public function deleteTvByUserIdAndTypeId($userId,$typeId,$ids){

        if(empty($userId) || empty($typeId) || empty($ids)){
            return json(["code"=>20001,"desc"=>"不能为空","data"=>[]]);
        }
        $where = [
            "user_id"=>$userId,
            "type_id"=>$typeId
        ];
        $where['tv_id'] = ['in', $ids];
        $res = Db::name("user_tv")->where($where)->delete();

        $result = [
            "code"=>"10000",
            "desc"=>"",
            "data"=>$res
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
            ->where('id not in (16,18,4)')
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

    public function getTvForPageByProvince($page = 1,$pageSize = 20,$code){
        $where=[
            'province'=>$code
        ];

        $options=[
            'page'=>$page
        ];

        $lists = Db::field('a.*,b.name as countryName,c.name as provinceName,GROUP_CONCAT(d.name) AS typeNames,
        (select count(1) from t_comment e where e.uid=a.id and e.type_id=11 and e.pid=0 ) as commentNum,
        (select sum(good) from t_good_log f where f.uid=a.id and f.type_id=11 ) as goodNum  ')
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
            $update_arr = [
                'id'=>$id,
                'create_at'=>date("Y-m-d H:i:s" ,time())
            ];
            $db = Db::name('user_tv');
            $pk = $db->getPk() ? $db->getPk() : 'id';
            $result = DataService::save($db, $update_arr, $pk, []);
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
    public function getTvForPageBySearch(){

        $name = empty(Request::instance()->param('name'))?"":Request::instance()->param('name');
        $page = empty(Request::instance()->param('page'))?1:Request::instance()->param('page');
        $pageSize = empty(Request::instance()->param('pageSize'))?15:Request::instance()->param('pageSize');
        if(empty($name)){
            return json(["code"=>20001,"desc"=>"参数不能为空","data"=>[]]);
        }

        $options=[
            'page'=>$page
        ];

        $lists = Db::field('a.*,b.name as countryName,c.name as provinceName,GROUP_CONCAT(d.name) AS typeNames,
        (select count(1) from t_comment e where e.uid=a.id and e.type_id=11 and e.pid=0 ) as commentNum,
        (select sum(good) from t_good_log f where f.uid=a.id and f.type_id=11 ) as goodNum  ')
            ->table("t_television")
            ->alias('a')
            ->join(' t_region b ',' a.country = b.code ','left')
            ->join(' t_region c ',' a.province = c.code ','left')
            ->join(' t_dict d','FIND_IN_SET(d.id , a.type_ids) ','left')
            ->where(' a.name like "%'.$name.'%" ')
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

    public function getProgram(){
        //https://www.tvsou.com/epg/HNTV-1/20171218?class=weishi
        //https://www.tvsou.com/epg/CCTV-1/20171218?class=yangshi
        //$url = "https://m.tvsou.com/epg/CCTV-1/20171218";

        $name = empty(Request::instance()->param('name'))?"":Request::instance()->param('name');
        $date = empty(Request::instance()->param('date'))?"":Request::instance()->param('date');
        $class = empty(Request::instance()->param('class'))?"":Request::instance()->param('class');
        $debug = empty(Request::instance()->param('debug'))?"":Request::instance()->param('debug');

        if(empty($name) || empty($date) || empty($class)){
            return json(["code"=>20001,"desc"=>"参数不能为空","data"=>[]]);
        }

        $where = [
            'name' => $name
        ];
        $tv_id = Db::name($this->table)->where("name='".$name."' or keyword='".$name."'")->column('id');

        $url = "https://m.tvsou.com/epg/".$name."/".$date."?class=".$class;
        $data = QueryList::Query($url,array(
            'name' => array('span.name','text'),
            'starttime' => array('span.start','text')
        ),'.list>a')->data;

        $db= Db::name("television_program") ;
        $pk ='id';
        //20171218
        $year=((int)substr($date,0,4));//取得年份
        $month=((int)substr($date,4,2));//取得月份
        $day=((int)substr($date,6,2));//取得几号
        if(is_array($tv_id) && count($data)>0){
            for($i=0;$i<count($data);$i++){

                $insertData = [
                    'title'=>$data[$i]['name'],
                    'tv_id'=>$tv_id[0],
                    'play_time'=>$data[$i]['starttime'],
                    'play_date'=>$date,
                    'play_at'=>$year."-".$month."-".$day." ".$data[$i]['starttime'].":00"
                ];
                if($debug == 1){

                }else{
                    $result = DataService::save($db, $insertData, $pk, []);
                }
            }
        }

        //print_r($data);
        $result = [
            "code"=>"10000",
            "desc"=>$url.',id='.$tv_id[0].strtotime($year."-".$month."-".$day." ".$data[0]['starttime'].":00"),
            "data"=>$data
        ];
        return json($result);
    }

    public function getProgramById(){
        //https://www.tvsou.com/epg/HNTV-1/20171218?class=weishi
        //https://www.tvsou.com/epg/CCTV-1/20171218?class=yangshi
        //$url = "https://m.tvsou.com/epg/CCTV-1/20171218";

        $id = empty(Request::instance()->param('id'))?"":Request::instance()->param('id');
        $date = empty(Request::instance()->param('date'))?"":Request::instance()->param('date');

        if(empty($id) || empty($date)){
            return json(["code"=>20001,"desc"=>"参数不能为空","data"=>[]]);
        }

        $where = [
            'tv_id' => $id,
            'play_date'=>$date
        ];

        $lists = Db::field('a.*')
            ->table("t_television_program")
            ->alias('a')
            ->where($where)
            ->group('a.id')
            ->order('a.id asc');

        $result = [
            "code"=>"10000",
            "desc"=>"",
            "data"=>$lists->select()
        ];

        return json($result);
    }

    public function getProgramForType(){
        //https://www.tvsou.com/epg/HNTV-1/20171218?class=weishi
        //https://www.tvsou.com/epg/CCTV-1/20171218?class=yangshi
        //$url = "https://m.tvsou.com/epg/CCTV-1/20171218";
        $type = 0;
        $date = empty(Request::instance()->param('date'))?"":Request::instance()->param('date');
        $class = empty(Request::instance()->param('class'))?"":Request::instance()->param('class');
        $debug = empty(Request::instance()->param('debug'))?"":Request::instance()->param('debug');

        if(empty($date) || empty($class)){
            return json(["code"=>20001,"desc"=>"参数不能为空","data"=>[]]);
        }

        if($class!='' && $date!=''){
            $data = array();
            if($class == 'weishi'){
                $type = 7;
                $map[]=['exp','FIND_IN_SET('.$type.',a.type_ids)'];
                $lists = Db::field('a.keyword,a.id')
                    ->table("t_television")
                    ->alias('a')
                    ->where($map)
                    ->group('a.id')
                    ->order('a.id asc');
                $data = $lists->select();
            }else if($class == 'yangshi'){
                $type = 2;
                $map[]=['exp','FIND_IN_SET('.$type.',a.type_ids)'];
                $lists = Db::field('a.name as keyword,a.id')
                    ->table("t_television")
                    ->alias('a')
                    ->where($map)
                    ->group('a.id')
                    ->order('a.id asc');
                $data = $lists->select();
            }

            $db= Db::name("television_program") ;
            $pk ='id';
            //20171218
            $year=((int)substr($date,0,4));//取得年份
            $month=((int)substr($date,4,2));//取得月份
            $day=((int)substr($date,6,2));//取得几号
            for($i=0;$i<count($data);$i++){
                $url = "https://m.tvsou.com/epg/".$data[$i]['keyword']."/".$date."?class=".$class;
                $programs = QueryList::Query($url,array(
                    'name' => array('span.name','text'),
                    'starttime' => array('span.start','text')
                ),'.list>a')->data;
                $tv_id = $data[$i]['id'];
                if(!empty($tv_id) && count($programs)>0){
                    for($j=0;$j<count($programs);$j++){
                        $insertData = [
                            'title'=>$programs[$j]['name'],
                            'tv_id'=>$tv_id,
                            'play_time'=>$programs[$j]['starttime'],
                            'play_date'=>$date,
                            'play_at'=>$year."-".$month."-".$day." ".$programs[$j]['starttime'].":00"
                        ];
                        if($debug == 1){
                            echo $data[$i]['keyword'].$programs[$j]['name'].$programs[$j]['starttime'].'<br>';
                        }else{
                            $result = DataService::save($db, $insertData, $pk, []);
                            //echo $data[$i]['keyword'].'<br>';
                            if($result){
                                echo $data[$i]['keyword'].$programs[$j]['name'].$programs[$j]['starttime'].'<br>';
                            }
                        }
                    }
                }
            }

            $result = [
                "code"=>"10000",
                "desc"=>"",
                "data"=>$data
            ];
            return json($result);
        }




        //return json([]);
    }
}

