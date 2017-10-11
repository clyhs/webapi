<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2017/10/11
 * Time: 9:50
 */
namespace app\admin\controller;

use app\common\controller\BaseAdmin;
use app\common\service\NodeService;
use app\common\service\ToolService;
use app\common\service\DataService;

class Auth extends BaseAdmin{

    public $table = 'auth';


    public function index()
    {
        $this->title = '系统权限管理';
        return parent::_list($this->table);
    }

    public function apply()
    {
        $auth_id = $this->request->get('id', '0');
        $method = '_apply_' . strtolower($this->request->get('action', '0'));
        if (method_exists($this, $method)) {
            return $this->$method($auth_id);
        }
        $this->assign('title', '节点授权');
        return $this->_form($this->table, 'apply');
    }


    protected function _apply_getnode($auth_id)
    {
        $nodes = NodeService::get();
        $checked = Db::name('auth_node')->where(['auth' => $auth_id])->column('node');
        foreach ($nodes as $key => &$node) {
            $node['checked'] = in_array($node['node'], $checked);
        }
        $all = $this->_apply_filter(ToolService::arr2tree($nodes, 'node', 'pnode', '_sub_'));
        $this->success('获取节点成功！', '', $all);
    }

    protected function _apply_save($auth_id)
    {
        list($data, $post) = [[], $this->request->post()];
        foreach (isset($post['nodes']) ? $post['nodes'] : [] as $node) {
            $data[] = ['auth' => $auth_id, 'node' => $node];
        }
        Db::name('auth_node')->where(['auth' => $auth_id])->delete();
        Db::name('auth_node')->insertAll($data);
        $this->success('节点授权更新成功！', '');
    }

    protected function _apply_filter($nodes, $level = 1)
    {
        foreach ($nodes as $key => &$node) {
            if (!empty($node['_sub_']) && is_array($node['_sub_'])) {
                $node['_sub_'] = $this->_apply_filter($node['_sub_'], $level + 1);
            }
        }
        return $nodes;
    }

    /**
     * 权限添加
     */
    public function add()
    {
        return $this->_form($this->table, 'form');
    }

    /**
     * 权限编辑
     */
    public function edit()
    {
        return $this->_form($this->table, 'form');
    }

    /**
     * 权限禁用
     */
    public function forbid()
    {
        if (DataService::update($this->table)) {
            $this->success("权限禁用成功！", '');
        }
        $this->error("权限禁用失败，请稍候再试！");
    }

    /**
     * 权限恢复
     */
    public function resume()
    {
        if (DataService::update($this->table)) {
            $this->success("权限启用成功！", '');
        }
        $this->error("权限启用失败，请稍候再试！");
    }

    /**
     * 权限删除
     */
    public function del()
    {
        if (DataService::update($this->table)) {
            $id = $this->request->post('id');
            Db::name('auth_node')->where(['auth' => $id])->delete();
            $this->success("权限删除成功！", '');
        }
        $this->error("权限删除失败，请稍候再试！");
    }
}