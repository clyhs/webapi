<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2017/10/10
 * Time: 17:10
 */
namespace app\common\service;

use think\Db;
use think\Request;

class NodeService
{
    public static function applyAuthNode()
    {
        cache('need_access_node', null);
        if (($userid = session('user.id'))) {
            session('user', Db::name('user')->where('id', $userid)->find());
        }
        if (($authorize = session('user.authorize'))) {
            $where = ['id' => ['in', explode(',', $authorize)], 'status' => '1'];
            $authorizeids = Db::name('auth')->where($where)->column('id');
            if (empty($authorizeids)) {
                return session('user.nodes', []);
            }
            $nodes = Db::name('auth_node')->whereIn('auth', $authorizeids)->column('node');
            return session('user.nodes', $nodes);
        }
        return false;
    }

    public static function getAuthNode()
    {
        $nodes = cache('need_access_node');
        if (empty($nodes)) {
            $nodes = Db::name('node')->where(['is_auth' => '1'])->column('node');
            cache('need_access_node', $nodes);
        }
        return $nodes;
    }

    public static function checkAuthNode($node)
    {
        list($module, $controller, $action) = explode('/', str_replace(['?', '=', '&'], '/', $node . '///'));
        $auth_node = strtolower(trim("{$module}/{$controller}/{$action}", '/'));
        if (session('user.username') === 'admin' || stripos($node, 'admin/index') === 0) {
            return true;
        }
        if (!in_array($auth_node, self::getAuthNode())) {
            return true;
        }
        return in_array($auth_node, (array)session('user.nodes'));
    }

    private static function _getFilePaths($path, $data = [], $ext = 'php')
    {
        foreach (scandir($path) as $dir) {
            if ($dir[0] === '.') {
                continue;
            }
            if (($tmp = realpath($path . DS . $dir)) && (is_dir($tmp) || pathinfo($tmp, 4) === $ext)) {
                is_dir($tmp) ? $data = array_merge($data, self::_getFilePaths($tmp)) : $data[] = $tmp;
            }
        }
        return $data;
    }

    public static function getNodeTree($path, $nodes = [])
    {
        foreach (self::_getFilePaths($path) as $vo) {
            if (!preg_match('|/(\w+)/controller/(\w+)|', str_replace(DS, '/', $vo), $matches) || count($matches) !== 3) {
                continue;
            }
            $className = config('app_namespace') . str_replace('/', '\\', $matches[0]);
            if (!class_exists($className)) {
                continue;
            }
            foreach (get_class_methods($className) as $actionName) {
                if ($actionName[0] !== '_') {
                    $nodes[] = strtolower("{$matches[1]}/{$matches[2]}/{$actionName}");
                }
            }
        }
        return $nodes;
    }

    public static function get($nodes = [])
    {
        $alias = Db::name('node')->column('node,is_menu,is_auth,is_login,title');
        $ignore = ['index', 'wechat/api', 'wechat/notify', 'wechat/review', 'admin/plugs', 'admin/login', 'admin/index'];
        foreach (self::getNodeTree(APP_PATH) as $thr) {
            foreach ($ignore as $str) {
                if (stripos($thr, $str) === 0) {
                    continue 2;
                }
            }
            $tmp = explode('/', $thr);
            list($one, $two) = ["{$tmp[0]}", "{$tmp[0]}/{$tmp[1]}"];
            $nodes[$one] = array_merge(isset($alias[$one]) ? $alias[$one] : ['node' => $one, 'title' => '', 'is_menu' => 0, 'is_auth' => 0, 'is_login' => 0], ['pnode' => '']);
            $nodes[$two] = array_merge(isset($alias[$two]) ? $alias[$two] : ['node' => $two, 'title' => '', 'is_menu' => 0, 'is_auth' => 0, 'is_login' => 0], ['pnode' => $one]);
            $nodes[$thr] = array_merge(isset($alias[$thr]) ? $alias[$thr] : ['node' => $thr, 'title' => '', 'is_menu' => 0, 'is_auth' => 0, 'is_login' => 0], ['pnode' => $two]);
        }
        foreach ($nodes as &$node) {
            list($node['is_auth'], $node['is_menu'], $node['is_login']) = [
                intval($node['is_auth']), intval($node['is_menu']),
                empty($node['is_auth']) ? intval($node['is_login']) : 1
            ];
        }
        return $nodes;
    }
}