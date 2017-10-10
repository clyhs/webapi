<?php
/**
 * Created by PhpStorm.
 * User: chenliyu
 * Date: 17/10/10
 * Time: 下午10:26
 */

namespace app\admin\controller;

use app\common\controller\BaseAdmin;

class Config extends BaseAdmin{


    /**
     * 当前默认数据模型
     * @var string
     */
    public $table = 'config';

    /**
     * 当前页面标题
     * @var string
     */
    public $title = '网站参数配置';

    /**
     * 显示系统常规配置
     */
    public function index()
    {
        if (!$this->request->isPost()) {
            return view('', ['title' => $this->title]);
        }
        foreach ($this->request->post() as $key => $vo) {
            sysconf($key, $vo);
        }
        //LogService::write('系统管理', '系统参数配置成功');
        $this->success('系统参数配置成功！', '');
    }

    /**
     * 文件存储配置
     */
    public function file()
    {
        $this->title = '文件存储配置';
        $alert = [
            'type'    => 'success', 'title' => '操作提示',
            'content' => '文件引擎参数影响全局文件上传功能，请勿随意修改！'
        ];
        $this->assign('alert', $alert);
        return $this->index();
    }

    /**
     * 短信参数配置
     */
    public function sms()
    {
        $this->title = '短信服务配置';
        //$this->assign('result', ExtendService::querySmsBalance());
        return $this->index();
    }


}