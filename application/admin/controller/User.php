<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2017/10/11
 * Time: 11:08
 */

namespace app\admin\controller;

use app\common\controller\BaseAdmin;
use app\common\service\DataService;
use think\Db;

class User extends BaseAdmin{

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'user';

    /**
     * 用户列表
     */
    public function index()
    {
        $this->title = '系统用户管理';
        $get = $this->request->get();
        $db = Db::name($this->table)->where(['is_deleted' => '0']);
        foreach (['username', 'phone', 'mail'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                $db->where($key, 'like', "%{$get[$key]}%");
            }
        }
        if (isset($get['date']) && $get['date'] !== '') {
            list($start, $end) = explode('-', str_replace(' ', '', $get['date']));
            $db->whereBetween('login_at', ["{$start} 00:00:00", "{$end} 23:59:59"]);
        }
        return parent::_list($db);
    }

    /**
     * 授权管理
     * @return array|string
     */
    public function auth()
    {
        return $this->_form($this->table, 'auth');
    }

    /**
     * 用户添加
     */
    public function add()
    {
        return $this->_form($this->table, 'form');
    }

    /**
     * 用户编辑
     */
    public function edit()
    {
        return $this->_form($this->table, 'form');
    }

    /**
     * 用户密码修改
     */
    public function pass()
    {
        if ($this->request->isGet()) {
            $this->assign('verify', false);
            return $this->_form($this->table, 'pass');
        }
        $data = $this->request->post();
        if ($data['password'] !== $data['repassword']) {
            $this->error('两次输入的密码不一致！');
        }
        if (DataService::save($this->table, ['id' => $data['id'], 'password' => md5($data['password'])], 'id')) {
            $this->success('密码修改成功，下次请使用新密码登录！', '');
        }
        $this->error('密码修改失败，请稍候再试！');
    }

    /**
     * 表单数据默认处理
     * @param array $data
     */
    public function _form_filter(&$data)
    {
        if ($this->request->isPost()) {
            if (isset($data['authorize']) && is_array($data['authorize'])) {
                $data['authorize'] = join(',', $data['authorize']);
            }
            if (isset($data['id'])) {
                unset($data['username']);
            } elseif (Db::name($this->table)->where(['username' => $data['username']])->find()) {
                $this->error('用户账号已经存在，请使用其它账号！');
            }
        } else {
            $data['authorize'] = explode(',', isset($data['authorize']) ? $data['authorize'] : '');
            $this->assign('authorizes', Db::name('SystemAuth')->where(['status' => '1'])->select());
        }
    }

    /**
     * 删除用户
     */
    public function del()
    {
        if (in_array('10000', explode(',', $this->request->post('id')))) {
            $this->error('系统超级账号禁止删除！');
        }
        if (DataService::update($this->table)) {
            $this->success("用户删除成功！", '');
        }
        $this->error("用户删除失败，请稍候再试！");
    }

    /**
     * 用户禁用
     */
    public function forbid()
    {
        if (in_array('10000', explode(',', $this->request->post('id')))) {
            $this->error('系统超级账号禁止操作！');
        }
        if (DataService::update($this->table)) {
            $this->success("用户禁用成功！", '');
        }
        $this->error("用户禁用失败，请稍候再试！");
    }

    /**
     * 用户禁用
     */
    public function resume()
    {
        if (DataService::update($this->table)) {
            $this->success("用户启用成功！", '');
        }
        $this->error("用户启用失败，请稍候再试！");
    }
}