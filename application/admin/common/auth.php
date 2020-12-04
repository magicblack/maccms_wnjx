<?php
return array(

    '1' => array('name' => lang('menu/index'), 'icon' => 'xe625', 'sub' => array(
        '11' => array("show"=>1,"name" =>lang('menu/welcome'), 'controller' => 'index', 'action' => 'welcome'),
        '21' => array("show"=>1,'name' => lang('menu/config'), 'controller' => 'system',				'action' => 'config'),
        '23' => array("show"=>1,"name" => lang('menu/spider'), 'controller' => 'system',				'action' => 'spider'),
        '24' => array("show"=>1,"name" => lang('menu/rep'), 'controller' => 'rep',				'action' => 'index'),
        '25' => array("show"=>1,"name" => lang('menu/template'), 'controller' => 'template',				'action' => 'index'),
        '26' => array("show"=>1,"name" => lang('menu/admin'), 'controller' => 'system',				'action' => 'admin'),
        '27' => array("show"=>1,"name" => lang('menu/sync'), 'controller' => 'sync',				'action' => 'index'),

        '1001' => array("show"=>0,"name" => '--切换布局', 'controller' => 'index', 'action' => 'iframe'),
        '1002' => array("show"=>0,"name" => '--清理缓存', 'controller' => 'index', 'action' => 'clear'),
        '1003' => array("show"=>0,"name" => '--锁屏解锁', 'controller' => 'index', 'action' => 'unlocked'),

    )),

);