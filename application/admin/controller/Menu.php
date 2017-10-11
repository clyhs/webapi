<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2017/10/11
 * Time: 10:58
 */

namespace app\admin\controller;

use app\common\controller\BaseAdmin;
use app\common\service\NodeService;
use app\common\service\ToolService;
use app\common\service\DataService;

class Menu extends BaseAdmin{

    public $table = 'menu';

    public function index()
    {
        $this->title = '系统菜单管理';
        $db = Db::name($this->table)->order('sort asc,id asc');
        return parent::_list($db, false);
    }

    /**
     * 列表数据处理
     * @param array $data
     */
    protected function _index_data_filter(&$data)
    {
        foreach ($data as &$vo) {
            ($vo['url'] !== '#') && ($vo['url'] = url($vo['url']));
            $vo['ids'] = join(',', ToolService::getArrSubIds($data, $vo['id']));
        }
        $data = ToolService::arr2table($data);
    }

    /**
     * 添加菜单
     */
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

    /**
     * 表单数据前缀方法
     * @param array $vo
     */
    protected function _form_filter(&$vo)
    {
        if ($this->request->isGet()) {
            // 上级菜单处理
            $_menus = Db::name($this->table)->where(['status' => '1'])->order('sort asc,id asc')->select();
            $_menus[] = ['title' => '顶级菜单', 'id' => '0', 'pid' => '-1'];
            $menus = ToolService::arr2table($_menus);
            foreach ($menus as $key => &$menu) {
                if (substr_count($menu['path'], '-') > 3) {
                    unset($menus[$key]);
                    continue;
                }
                if (isset($vo['pid'])) {
                    $current_path = "-{$vo['pid']}-{$vo['id']}";
                    if ($vo['pid'] !== '' && (stripos("{$menu['path']}-", "{$current_path}-") !== false || $menu['path'] === $current_path)) {
                        unset($menus[$key]);
                    }
                }
            }
            // 读取系统功能节点
            $nodes = NodeService::get();
            foreach ($nodes as $key => $_vo) {
                if (empty($_vo['is_menu'])) {
                    unset($nodes[$key]);
                }
            }
            $this->assign('nodes', array_column($nodes, 'node'));
            $this->assign('menus', $menus);
        }
    }

    /**
     * 删除菜单
     */
    public function del()
    {
        if (DataService::update($this->table)) {
            $this->success("菜单删除成功!", '');
        }
        $this->error("菜单删除失败, 请稍候再试!");
    }

    /**
     * 菜单禁用
     */
    public function forbid()
    {
        if (DataService::update($this->table)) {
            $this->success("菜单禁用成功!", '');
        }
        $this->error("菜单禁用失败, 请稍候再试!");
    }

    /**
     * 菜单禁用
     */
    public function resume()
    {
        if (DataService::update($this->table)) {
            $this->success("菜单启用成功!", '');
        }
        $this->error("菜单启用失败, 请稍候再试!");
    }

}