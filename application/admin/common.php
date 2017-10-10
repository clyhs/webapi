<?php

use think\Db;
use app\common\service\DataService;
use app\common\service\NodeService;

function sysconf($name, $value = null)
{
    static $config = [];
    if ($value !== null) {
        list($config, $data) = [[], ['name' => $name, 'value' => $value]];
        return DataService::save('config', $data, 'name');
    }
    if (empty($config)) {
        $config = Db::name('config')->column('name,value');
    }
    return isset($config[$name]) ? $config[$name] : '';
}

function auth($node)
{
    //return NodeService::checkAuthNode($node);
    return true;
}