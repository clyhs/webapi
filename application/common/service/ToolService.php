<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2017/10/10
 * Time: 17:15
 */
namespace app\common\service;

use think\Db;
use think\Request;

class ToolService {


    public static function arr2tree($list, $id = 'id', $pid = 'pid', $son = 'sub')
    {
        list($tree, $map) = [[], []];
        foreach ($list as $item) {
            $map[$item[$id]] = $item;
        }
        foreach ($list as $item) {
            if (isset($item[$pid]) && isset($map[$item[$pid]])) {
                $map[$item[$pid]][$son][] = &$map[$item[$id]];
            } else {
                $tree[] = &$map[$item[$id]];
            }
        }
        unset($map);
        return $tree;
    }

    public static function arr2table(array $list, $id = 'id', $pid = 'pid', $path = 'path', $ppath = '')
    {
        $tree = [];
        foreach (self::arr2tree($list, $id, $pid) as $attr) {
            $attr[$path] = "{$ppath}-{$attr[$id]}";
            $attr['sub'] = isset($attr['sub']) ? $attr['sub'] : [];
            $attr['spl'] = str_repeat("&nbsp;&nbsp;&nbsp;├&nbsp;&nbsp;", substr_count($ppath, '-'));
            $sub = $attr['sub'];
            unset($attr['sub']);
            $tree[] = $attr;
            if (!empty($sub)) {
                $tree = array_merge($tree, (array)self::arr2table($sub, $id, $pid, $path, $attr[$path]));
            }
        }
        return $tree;
    }

    public static function getArrSubIds($list, $id = 0, $key = 'id', $pkey = 'pid')
    {
        $ids = [intval($id)];
        foreach ($list as $vo) {
            if (intval($vo[$pkey]) > 0 && intval($vo[$pkey]) === intval($id)) {
                $ids = array_merge($ids, self::getArrSubIds($list, intval($vo[$key]), $key, $pkey));
            }
        }
        return $ids;
    }
}