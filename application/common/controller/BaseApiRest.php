<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2017/10/24
 * Time: 9:02
 */

namespace app\common\controller;


use think\controller\Rest;


class BaseApiRest extends Rest{

    public $table;



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