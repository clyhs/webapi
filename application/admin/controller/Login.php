<?php
/**
 * Created by PhpStorm.
 * User: clyhs
 * Date: 2017/10/14
 * Time: 15:50
 */

namespace app\admin\controller;

use app\common\controller\BaseAdmin;
use app\common\service\NodeService;
use think\Db;

class Login extends BaseAdmin{

    public function _initialize()
    {
        if (session('user') && $this->request->action() !== 'out') {
            $this->redirect('@admin');
        }
    }

    public function index()
    {
        if ($this->request->isGet()) {
            return $this->fetch('', ['title' => '用户登录']);
        }
        // 输入数据效验
        $username = $this->request->post('username', '', 'trim');
        $password = $this->request->post('password', '', 'trim');
        strlen($username) < 4 && $this->error('登录账号长度不能少于4位有效字符!');
        strlen($password) < 4 && $this->error('登录密码长度不能少于4位有效字符!');
        // 用户信息验证
        $user = Db::name('user')->where('username', $username)->find();
        empty($user) && $this->error('登录账号不存在，请重新输入!');
        ($user['password'] !== md5($password)) && $this->error('登录密码与账号不匹配，请重新输入!');

        if($user['authorize']!=''){
            $authorizes = explode(",",$user['authorize']);
            if(!in_array("1",$authorizes)){
                $this->error('你没有登录权限!');
            }
        }else{
            $this->error('你没有登录权限!');
        }
        empty($user['status']) && $this->error('账号已经被禁用，请联系管理!');
        // 更新登录信息
        $data = ['login_at' => ['exp', 'now()'], 'login_num' => ['exp', 'login_num+1']];
        Db::name('user')->where(['id' => $user['id']])->update($data);
        session('user', $user);
        !empty($user['authorize']) && NodeService::applyAuthNode();
        //LogService::write('系统管理', '用户登录系统成功');
        $this->success('登录成功，正在进入系统...', '@admin');
    }

    /**
     * 退出登录
     */
    public function out()
    {
        if (session('user')) {
            //LogService::write('系统管理', '用户退出系统成功');
        }
        session('user', null);
        session_destroy();
        $this->success('退出登录成功！', '@admin/login');
    }

}