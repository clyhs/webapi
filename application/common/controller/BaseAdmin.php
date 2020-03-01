<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2017/10/10
 * Time: 16:15
 */
namespace app\common\controller;

use think\Controller;
use think\Db;
use think\db\Query;
use app\common\service\DataService;

class BaseAdmin extends Controller{

    public $title;

    public $table;

    protected function _form($dbQuery = null, $tplFile = '', $pkField = '', $where = [], $extendData = [])
    {
        $db = is_null($dbQuery) ? Db::name($this->table) : (is_string($dbQuery) ? Db::name($dbQuery) : $dbQuery);
        $pk = empty($pkField) ? ($db->getPk() ? $db->getPk() : 'id') : $pkField;
        $pkValue = $this->request->request($pk, isset($where[$pk]) ? $where[$pk] : (isset($extendData[$pk]) ? $extendData[$pk] : null));
        // 非POST请求, 获取数据并显示表单页面
        if (!$this->request->isPost()) {
            $vo = ($pkValue !== null) ? array_merge((array)$db->where($pk, $pkValue)->where($where)->find(), $extendData) : $extendData;
            if (false !== $this->_callback('_form_filter', $vo)) {
                empty($this->title) || $this->assign('title', $this->title);
                return $this->fetch($tplFile, ['vo' => $vo]);
            }
            return $vo;
        }
        // POST请求, 数据自动存库
        $data = array_merge($this->request->post(), $extendData);
        if (false !== $this->_callback('_form_filter', $data)) {
            $result = DataService::save($db, $data, $pk, $where);
            if (false !== $this->_callback('_form_result', $result)) {
                if ($result !== false) {
                    $this->success('恭喜, 数据保存成功!', '');
                }
                $this->error('数据保存失败, 请稍候再试!');
            }
        }


    }

    protected function _myform($dbQuery = null, $tplFile = '', $pkField = '', $where = [], $extendData = [],$function='')
    {
        $db = is_null($dbQuery) ? Db::name($this->table) : (is_string($dbQuery) ? Db::name($dbQuery) : $dbQuery);
        $pk = empty($pkField) ? ($db->getPk() ? $db->getPk() : 'id') : $pkField;
        $pkValue = $this->request->request($pk, isset($where[$pk]) ? $where[$pk] : (isset($extendData[$pk]) ? $extendData[$pk] : null));
        // 非POST请求, 获取数据并显示表单页面
        if (!$this->request->isPost()) {
            $vo = ($pkValue !== null) ? array_merge((array)$db->where($pk, $pkValue)->where($where)->find(), $extendData) : $extendData;
            if (false !== $this->_callback('_'.$function.'_my_filter', $vo)) {
                empty($this->title) || $this->assign('title', $this->title);
                return $this->fetch($tplFile, ['vo' => $vo]);
            }
            return $vo;
        }
        // POST请求, 数据自动存库
        $data = array_merge($this->request->post(), $extendData);

        if (false !== $this->_callback('_'.$function.'_my_filter', $data)) {

            if(is_array($data)){
                for($i=0;$i<count($data);$i++) {
                    $result = DataService::save($db, $data[$i], $pk, $where);
                }
            }

            if (false !== $this->_callback('_'.$function.'_my_result', $result)) {
                if ($result !== false) {
                    $this->success('恭喜, 数据保存成功!', '');
                }
                $this->error('数据保存失败, 请稍候再试!');
            }
        }


    }

    protected function _list($dbQuery = null, $isPage = true, $isDisplay = true, $total = false, $result = [])
    {
        $db = is_null($dbQuery) ? Db::name($this->table) : (is_string($dbQuery) ? Db::name($dbQuery) : $dbQuery);
        // echo $db;
        // 列表排序默认处理
        if ($this->request->isPost() && $this->request->post('action') === 'resort') {
            $data = $this->request->post();
            unset($data['action']);
            foreach ($data as $key => &$value) {
                if (false === $db->where('id', intval(ltrim($key, '_')))->setField('sort', $value)) {
                    $this->error('列表排序失败, 请稍候再试');
                }
            }
            $this->success('列表排序成功, 正在刷新列表', '');
        }
        // 列表数据查询与显示
        if (null === $db->getOptions('order')) {
            $fields = $db->getTableFields($db->getTable());
            in_array('sort', $fields) && $db->order('sort asc');
        }
        if (false) {
            /*
            echo '1----';
            $rows = intval($this->request->get('rows', cookie('rows')));
            echo '11----';
            cookie('rows', $rows >= 10 ? $rows : 20);
            echo '111----';
            //$page = $db->paginate($rows, $total, ['query' => $this->request->get('', '', 'urlencode')]);
            $page = $db->paginate($rows,false,[]);
            echo '2----';
            list($pattern, $replacement) = [['|href="(.*?)"|', '|pagination|'], ['data-open="$1"', 'pagination pull-right']];
            echo '3----';
            list($result['list'], $result['page']) = [$page->all(), preg_replace($pattern, $replacement, $page->render())];
            echo '4----';
            var_dump($page->all());
            echo '5----';*/
            echo '1----';
            $rows = intval($this->request->get('rows', cookie('rows')));
            echo '2----';
            cookie('rows', $rows >= 10 ? $rows : 20);
            echo '3----';
            $page = $db->paginate($rows, $total, []);
            echo '4----';
            $result['list'] = $page->all();
            echo '5----';
            $result['page'] = preg_replace(['|href="(.*?)"|', '|pagination|'], ['data-open="$1" href="javascript:void(0);"', 'pagination pull-right'], $page->render());
            echo '6----';
        } else {
            $result['list'] = $db->select();
        }

        if (false !== $this->_callback('_data_filter', $result['list']) && $isDisplay) {
            !empty($this->title) && $this->assign('title', $this->title);
            return $this->fetch('', $result);
        }

        return $result;
    }

    protected function _callback($method, &$data)
    {
        foreach ([$method, "_" . $this->request->action() . "{$method}"] as $_method) {
            if (method_exists($this, $_method) && false === $this->$_method($data)) {
                return false;
            }
        }
        return true;
    }

}
