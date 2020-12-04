<?php
namespace app\index\controller;
use think\Controller;
use app\common\controller\All;

class Base extends All
{
    public function __construct()
    {
        parent::__construct();
        $this->check_site_status();
        $this->check_browser_jump();
    }

    protected function check_site_status()
    {
        //站点关闭中
        if ($GLOBALS['config']['site']['site_status'] == 0) {
            $this->assign('close_tip',$GLOBALS['config']['site']['site_close_tip']);
            echo $this->fetch('public/close');
            die;
        }
    }

    protected function check_browser_jump()
    {
        if (ENTRANCE=='index' && $GLOBALS['config']['app']['browser_junmp'] == 1) {
            $agent = $_SERVER['HTTP_USER_AGENT'];
            if(strpos($agent, 'QQ/')||strpos($agent, 'MicroMessenger')!==false){
                echo $this->fetch('public/browser');
                die;
            }
        }
    }
}