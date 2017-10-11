<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2017/10/11
 * Time: 11:21
 */

namespace app\admin\controller;

use app\common\controller\BaseAdmin;
use app\common\service\DataService;
use think\Db;

class Log extends BaseAdmin{

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'log';

    /**
     * 日志列表
     * @return array|string
     */
    public function index()
    {
        $this->title = '系统操作日志';
        $get = $this->request->get();
        // 日志行为类别
        $actions = Db::name($this->table)->group('action')->column('action');
        $this->assign('actions', $actions);
        // 日志数据库对象
        $db = Db::name($this->table)->order('id desc');
        foreach (['action', 'content', 'username'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                $db->where($key, 'like', "%{$get[$key]}%");
            }
        }
        if (isset($get['date']) && $get['date'] !== '') {
            list($start, $end) = explode('-', str_replace(' ', '', $get['date']));
            $db->whereBetween('create_at', ["{$start} 00:00:00", "{$end} 23:59:59"]);
        }
        return parent::_list($db);
    }

    /**
     * 列表数据处理
     * @param array $data
     */
    protected function _index_data_filter(&$data)
    {
        $ip = new \Ip2Region();
        foreach ($data as &$vo) {
            $result = $ip->btreeSearch($vo['ip']);
            $vo['isp'] = isset($result['region']) ? $result['region'] : '';
            $vo['isp'] = str_replace(['|0|0|0|0', '0', '|'], ['', '', ' '], $vo['isp']);
        }
    }

    /**
     * 日志删除操作
     */
    public function del()
    {
        if (DataService::update($this->table)) {
            $this->success("日志删除成功!", '');
        }
        $this->error("日志删除失败, 请稍候再试!");
    }
}