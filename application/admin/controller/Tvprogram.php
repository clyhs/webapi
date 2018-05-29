<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2018/2/1
 * Time: 10:02
 */
namespace app\admin\controller;

use app\common\controller\BaseAdmin;
use think\Db;
use app\common\service\DataService;
use QL\QueryList;

class Tvprogram extends BaseAdmin{
    public $table = 'television_program';

    public function index(){

        $this->title = '电视台节目管理';

        $get = $this->request->get();
        $db = Db::field('a.tv_id,a.play_date,count(1) as count,b.name ')
            ->table("t_television_program")
            ->alias('a')
            ->join(' t_television b ',' a.tv_id = b.id ','left');


        foreach ([ 'name'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                $db->where('b.'.$key, 'like', "%{$get[$key]}%");
            }
        }

        if (isset($get['date']) && $get['date'] !== '') {
            $play_date=   $get['date'];
            $db->where('a.play_date', $play_date);
        }

        foreach (['type_id'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {

                if($get[$key]>0){
                    //$db->where('a.'.$key, '=', "{$get[$key]}");
                    $map[]=['exp','FIND_IN_SET('.$get[$key].',a.type)'];
                    $db->where($map);
                }

            }
        }

        $db->group('a.play_date,b.name,a.tv_id')
            ->order('a.id desc');

        $where = [
            "char"=>"CHANNEL",
            "pid"=>1
        ];

        $channels = Db::name("dict")->where($where)->order('id asc')->select();
        $this->assign('channels', $channels);

        return parent::_list($db);

    }

    protected function _index_data_filter(&$data)
    {
    }

    public function importpg()
    {
        return $this->_form($this->table, 'importpg');
    }

    public function delall()
    {
        $sql = "truncate table t_television_program";
        Db::query($sql);
        $this->success('恭喜, 清空节目表成功', '');
    }

    public function importsubmit(){
        $post = $this->request->post();
        $date = $post['date2'];
        $tvtype = $post['tvtype'];

        $type = 0;

        if($tvtype == 'weishi'){
            $type = 7;
        }else if($tvtype == 'yangshi'){
            $type = 2;
        }else{

        }
        $where = [
            "play_date"=>$date,
            "type"=>$type
        ];
        if(Db::name($this->table)->where($where)->find()){
            Db::name($this->table)->where($where)->delete();
        }
        /*
        if(Db::name($this->table)->where($where)->find()){
            $this->error('节目已经存在, 请清空再试!');
        }else{
            $len = $this->getProgramForType($date,$tvtype,0);
            $this->success('恭喜, 成功导入'.$len.'条数据!', '');
        }*/
        $len = $this->getProgramForType($date,$tvtype,0);
        $this->success('恭喜, 成功导入'.$len.'条数据!', '');



    }

    public function getProgramForType($date,$class,$debug){
        //https://www.tvsou.com/epg/HNTV-1/20171218?class=weishi
        //https://www.tvsou.com/epg/CCTV-1/20171218?class=yangshi
        //$url = "https://m.tvsou.com/epg/CCTV-1/20171218";
        $type = 0;
        //$date = empty(Request::instance()->param('date'))?"":Request::instance()->param('date');
        //$class = empty(Request::instance()->param('class'))?"":Request::instance()->param('class');
        //$debug = empty(Request::instance()->param('debug'))?"":Request::instance()->param('debug');

        if(empty($date) || empty($class)){
            $this->error('参数失败, 请稍候再试!');
        }

        $len = 0;

        $url = "https://m.tvsou.com/api/ajaxGetPlay";
        //$data['date']=$date;
        //$data['channelid']=$channelid;



        if($class!='' && $date!=''){
            $data = array();
            if($class == 'weishi'){
                $type = 7;

            }else if($class == 'yangshi'){
                $type = 2;
            }
            //$map['a.type_ids']=['exp','FIND_IN_SET('.$type.',a.type_ids)'];
            //$map['a.channelid']=['a.channelid',' is not null'];
            $lists = Db::field('a.channelid,a.id')
                ->table("t_television")
                ->alias('a')
                ->where('FIND_IN_SET('.$type.',a.type_ids) and a.channelid is not null ')
                ->group('a.id')
                ->order('a.id asc');
            $data = $lists->select();

            print_r($data);

            $db= Db::name("television_program") ;
            $pk ='id';
            //20171218
            $year=((int)substr($date,0,4));//取得年份
            $month=((int)substr($date,4,2));//取得月份
            $day=((int)substr($date,6,2));//取得几号
            for($i=0;$i<count($data);$i++){
                //$url = "https://m.tvsou.com/epg/".$data[$i]['keyword']."/".$date."?class=".$class;
                /*
                $programs = QueryList::Query($url,array(
                    'name' => array('span.name','text'),
                    'starttime' => array('span','text')
                ),'.list>a')->data;*/
                if(''!=$data[$i]['channelid']){
                    $params = [
                        'date'=>$date,
                        'channelid'=>$data[$i]['channelid']
                    ];
                    $tv_id = $data[$i]['id'];
                    if($i>0 && $i%3 == 0){
                        usleep(1000000);
                    }
                    $result = $this->http($url,$params,'POST',array());

                    //$programs = array();

                    $programs = $result['list'];
                    $len = $len+count($programs);
                    if(!empty($tv_id) && count($programs)>0){
                        for($j=0;$j<count($programs);$j++){
                            $content = "";
                            if(""!=$programs[$j]['content']){
                                $content = $programs[$j]['content'];
                            }
                            $insertData = [
                                'title'=>$programs[$j]['title'],
                                'tv_id'=>$tv_id,
                                'play_time'=>$programs[$j]['playtime'],
                                'end_time'=>$programs[$j]['endtime'],
                                'play_times'=>$programs[$j]['playtimes'],
                                'end_times'=>$programs[$j]['endtimes'],
                                'content'=>$content,
                                'play_date'=>$date,
                                'type'=>$type,
                                'play_at'=>$year."-".$month."-".$day." ".$programs[$j]['playtime'].":00"
                            ];
                            if($debug == 1){
                                // echo $data[$i]['keyword'].$programs[$j]['name'].$programs[$j]['starttime'].'<br>';
                            }else{
                                $result = DataService::save($db, $insertData, $pk, []);
                                if($result){
                                    //echo $data[$i]['keyword'].$programs[$j]['name'].$programs[$j]['starttime'].'<br>';
                                }
                            }
                        }
                    }
                }

            }

        }
        return $len;
    }

    private function http($url, $params, $method = 'GET', $header = array(), $multi = false){
        $opts = array(
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER     => $header
        );
        /* 根据请求类型设置特定参数 */
        switch(strtoupper($method)){
            case 'GET':
                $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
                break;
            case 'POST':
                //判断是否传输文件
                $params = $multi ? $params : http_build_query($params);
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_CUSTOMREQUEST] = "POST";
                //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                $opts[CURLOPT_POSTFIELDS] = $params;
                $opts[CURLOPT_RETURNTRANSFER]= 1;
                break;
            default:
                throw new Exception('不支持的请求方式！');
        }
        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data  = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        if($error) throw new Exception('请求发生错误：' . $error);
        return json_decode($data,true);
    }

}