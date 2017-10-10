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


    public static function save($dbQuery, $data, $key = 'id', $where = [])
    {
        $db = is_string($dbQuery) ? Db::name($dbQuery) : $dbQuery;
        $where[$key] = isset($data[$key]) ? $data[$key] : '';
        if ($db->where($where)->count() > 0) {
            return $db->where($where)->update($data) !== false;
        }
        return $db->insert($data) !== false;
    }
}