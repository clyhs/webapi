<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2017/10/11
 * Time: 9:42
 */

namespace app\common\hook;

use think\Request;

class FilterView{

    protected $request;

    public function run(&$params)
    {
        $this->request = Request::instance();
        list($appRoot, $uriSelf) = [$this->request->root(true), $this->request->url(true)];
        $uriRoot = preg_match('/\.php$/', $appRoot) ? dirname($appRoot) : $appRoot;
        $uriStatic = "{$uriRoot}/static";
        $replace = ['__APP__' => $appRoot, '__SELF__' => $uriSelf, '__PUBLIC__' => $uriRoot, '__STATIC__' => $uriStatic];
        $params = str_replace(array_keys($replace), array_values($replace), $params);
        //!IS_CLI && $this->baidu($params);
    }
}