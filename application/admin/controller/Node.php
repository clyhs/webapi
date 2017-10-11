<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2017/10/11
 * Time: 10:19
 */

namespace app\admin\controller;

use app\common\controller\BaseAdmin;
use app\common\service\NodeService;
use app\common\service\ToolService;
use app\common\service\DataService;

class Node extends BaseAdmin{


    /**
     * 指定当前默认模型
     * @var string
     */
    public $table = 'node';

    /**
     * 显示节点列表
     */
    public function index()
    {
        $nodes = ToolService::arr2table(NodeService::get(), 'node', 'pnode');
        $alert = ['type' => 'danger', 'title' => '安全警告', 'content' => '结构为系统自动生成, 状态数据请勿随意修改!'];
        return view('', ['title' => '系统节点管理', 'nodes' => $nodes, 'alert' => $alert]);
    }

    /**
     * 保存节点变更
     */
    public function save()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            if (isset($post['list'])) {
                $data = [];
                foreach ($post['list'] as $vo) {
                    $data['node'] = $vo['node'];
                    $data[$vo['name']] = $vo['value'];
                }
                !empty($data) && DataService::save($this->table, $data, 'node');
                $this->success('参数保存成功！', '');
            }
        } else {
            $this->error('访问异常，请重新进入...');
        }
    }
}