<?php
/**
 * Created by PhpStorm.
 * User: chenly
 * Date: 2017/10/12
 * Time: 8:53
 */

namespace app\admin\controller;

use app\common\controller\BaseAdmin;

class Television extends BaseAdmin{

    public $table = 'television';

    public function index(){
        return view('', ['title' => '电台管理']);
    }

}