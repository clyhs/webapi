<?php
namespace app\admin\controller;

use app\common\controller\BaseAdmin;
use think\Db;
use think\View;
use app\common\service\NodeService;
use app\common\service\ToolService;

class Index extends BaseAdmin
{
    public function index()
    {
        $list = (array)Db::name('menu')->where(['status' => '1'])->order('sort asc,id asc')->select();

        $menus = $this->_filterMenuData(ToolsService::arr2tree($list), NodeService::get(), true);
        return view('', ['title' => '系统管理', 'menus' => $menus]);
    }

    private function _filterMenuData($menus, $nodes, $isLogin)
    {
        foreach ($menus as $key => &$menu) {
            !empty($menu['sub']) && $menu['sub'] = $this->_filterMenuData($menu['sub'], $nodes, $isLogin);
            if (!empty($menu['sub'])) {
                $menu['url'] = '#';
            } elseif (preg_match('/^https?\:/i', $menu['url'])) {
                continue;
            } elseif ($menu['url'] !== '#') {
                $node = join('/', array_slice(explode('/', preg_replace('/[\W]/', '/', $menu['url'])), 0, 3));
                $menu['url'] = url($menu['url']);
                if (isset($nodes[$node]) && $nodes[$node]['is_login'] && empty($isLogin)) {
                    unset($menus[$key]);
                } elseif (isset($nodes[$node]) && $nodes[$node]['is_auth'] && $isLogin && !auth($node)) {
                    unset($menus[$key]);
                }
            } else {
                unset($menus[$key]);
            }
        }
        return $menus;
    }
}
