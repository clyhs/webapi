<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2017/10/13
 * Time: 9:22
 */

namespace app\admin\controller;

use app\common\controller\BaseAdmin;
use app\common\service\ToolService;
use app\common\service\DataService;
use think\Db;

class Dict extends BaseAdmin{

    public $table = 'dict';

    public function index()
    {
        $this->title = '系统字典管理';
        $db = Db::name($this->table)->order('sort asc,id asc');
        return parent::_list($db, false);
    }

    protected function _index_data_filter(&$data)
    {
        foreach ($data as &$vo) {
            $vo['ids'] = join(',', ToolService::getArrSubIds($data, $vo['id']));
        }
        $data = ToolService::arr2table($data);
    }

    public function add()
    {
        return $this->_form($this->table, 'form');
    }

    /**
     * 编辑菜单
     */
    public function edit()
    {
        return $this->_form($this->table, 'form');
    }

    protected function _form_filter(&$vo)
    {
        if ($this->request->isGet()) {
            // 上级菜单处理
            $_dicts = Db::name($this->table)->order('sort asc,id asc')->select();
            $_dicts[] = ['title' => '无', 'id' => '0', 'pid' => '-1'];
            $dicts = ToolService::arr2table($_dicts);
            foreach ($dicts as $key => &$dict) {
                if (substr_count($dict['path'], '-') > 3) {
                    unset($dicts[$key]);
                    continue;
                }
                if (isset($vo['pid'])) {
                    $current_path = "-{$vo['pid']}-{$vo['id']}";
                    if ($vo['pid'] !== '' && (stripos("{$dict['path']}-", "{$current_path}-") !== false || $dict['path'] === $current_path)) {
                        unset($dict[$key]);
                    }
                }
            }

            $this->assign('dicts', $dicts);
        }
    }

    public function del()
    {
        if (DataService::update($this->table)) {
            $this->success("删除成功!", '');
        }
        $this->error("删除失败, 请稍候再试!");
    }

}