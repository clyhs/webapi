<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2017/10/11
 * Time: 10:07
 */

namespace app\common\hook;

use think\Config;
use think\Db;
use think\exception\HttpResponseException;
use think\Request;
use think\View;

class AccessAuth {

    protected $request;


    public function run(&$params)
    {
        $this->request = Request::instance();
        list($module, $controller, $action) = [$this->request->module(), $this->request->controller(), $this->request->action()];
        $node = strtolower("{$module}/{$controller}/{$action}");
        $info = Db::name('node')->where('node', $node)->find();
        $access = [
            'is_menu'  => intval(!empty($info['is_menu'])),
            'is_auth'  => intval(!empty($info['is_auth'])),
            'is_login' => empty($info['is_auth']) ? intval(!empty($info['is_login'])) : 1
        ];
        // 用户登录状态检查
        /*
        if (!empty($access['is_login']) && !session('user')) {
            if ($this->request->isAjax()) {
                $this->response('抱歉，您还没有登录获取访问权限！', 0, url('@admin/login'));
            }
            throw new HttpResponseException(redirect('@admin/login'));
        }*/
        // 访问权限节点检查
        if (!empty($access['is_auth']) && !auth($node)) {
            $this->response('抱歉，您没有访问该模块的权限！', 0);
        }
        // 权限正常, 默认赋值
        $view = View::instance(Config::get('template'), Config::get('view_replace_str'));
        $view->assign('classuri', strtolower("{$module}/{$controller}"));
    }

    protected function response($msg, $code = 0, $url = '', $data = [], $wait = 3)
    {
        $result = ['code' => $code, 'msg' => $msg, 'data' => $data, 'url' => $url, 'wait' => $wait];
        throw new HttpResponseException(json($result));
    }

}