<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // 生成应用公共文件
    '__file__' => ['common.php', 'config.php', 'database.php'],

    //公共模块目录
    'common' => [
        '__file__'   => ['common.php'],
        '__dir__'    => ['controller', 'model','lang'],
        'controller' => ['Index'],
        'model'      => ['Base'],
    ],
    // 其他更多的模块定义
    // Admin 模块
    'admin'     => [
        '__file__'   => ['common.php'],
        '__dir__'    => ['behavior', 'controller', 'model', 'view','lang'],
        'controller' => ['Index'],
        'model'      => ['Test'],
        'view'       => ['index/index'],
    ],
    // Index模块
    'index'     => [
        '__file__'   => ['common.php'],
        '__dir__'    => ['behavior', 'controller', 'model', 'view','lang'],
        'controller' => ['Index'],
        'model'      => ['Test'],
        'view'       => ['index/index'],
    ],

    // Index模块
    'api'     => [
        '__dir__'    => [ 'controller', 'model','lang'],
        'controller' => ['Index'],
    ],
];
