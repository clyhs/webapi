<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2017/10/10
 * Time: 16:57
 */

namespace app\common\service;

use think\Db;
use think\Request;

class DataService{

    public static function sign(&$data, $apikey = '', $prefix = '')
    {
        $data['_SIGNSTR_'] = strtoupper(isset($data['_SIGNSTR_']) ? $data['_SIGNSTR_'] : substr(md5(uniqid()), 22));
        ksort($data);
        foreach (array_values($data) as $string) {
            is_array($string) || ($prefix .= "{$string}");
        }
        return strtoupper(md5($prefix . $apikey . $data['_SIGNSTR_']));
    }


    public static function save($dbQuery, $data, $key = 'id', $where = [])
    {
        $db = is_string($dbQuery) ? Db::name($dbQuery) : $dbQuery;
        $where[$key] = isset($data[$key]) ? $data[$key] : '';
        if ($db->where($where)->count() > 0) {
            return $db->where($where)->update($data) !== false;
        }
        return $db->insert($data) !== false;
    }

    public static function update(&$dbQuery, $where = [])
    {
        $request = Request::instance();
        $db = is_string($dbQuery) ? Db::name($dbQuery) : $dbQuery;
        $ids = explode(',', $request->post('id', ''));
        $field = $request->post('field', '');
        $value = $request->post('value', '');
        $pk = $db->getPk(['table' => $db->getTable()]);
        $where[empty($pk) ? 'id' : $pk] = ['in', $ids];
        // 删除模式，如果存在 is_deleted 字段使用软删除
        if ($field === 'delete') {
            if (method_exists($db, 'getTableFields')) {
                if (in_array('is_deleted', $db->getTableFields($db->getTable()))) {
                    return false !== $db->where($where)->update(['is_deleted' => 1]);
                }
            }
            return false !== $db->where($where)->delete();
        }
        // 更新模式，更新指定字段内容
        return false !== $db->where($where)->update([$field => $value]);
    }
}