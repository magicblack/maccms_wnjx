<?php
namespace app\index\controller;

use think\Controller;
use think\Request;

class MyError extends Controller
{
    public function _empty()
    {
        if(empty($msg)){
            $msg=lang('an_error_occurred');
        }
        $url = Request::instance()->isAjax() ? '' : 'javascript:history.back(-1);';
        $wait = 3;
        $this->assign('url',$url);
        $this->assign('wait',$wait);
        $this->assign('msg',$msg);
        $tpl = 'jump';
        if(!empty($GLOBALS['config']['app']['page_404'])){
            $tpl = $GLOBALS['config']['app']['page_404'];
        }
        $html = $this->fetch('public/'.$tpl);
        header("HTTP/1.1 404 Not Found");
        header("Status: 404 Not Found");
        exit($html);
    }
}