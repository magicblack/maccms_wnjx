<?php
namespace app\admin\controller;
use think\Controller;
use app\common\controller\All;
use think\Cache;
use app\common\util\Dir;

class Base extends All
{
    public function __construct()
    {
        parent::__construct();

        //判断用户登录状态

        if(in_array($this->_cl,['Index']) && in_array($this->_ac,['login','logout'])) {

        }
        else {
            if(empty(session('adminauth'))){
                return $this->redirect('index/login');
            }
        }
        $this->assign('cl',$this->_cl);
    }


    public function _cache_clear()
    {
        Dir::delDir(RUNTIME_PATH.'cache/');
        Dir::delDir(RUNTIME_PATH.'log/');
        Dir::delDir(RUNTIME_PATH.'temp/');

        Cache::clear();

        return true;
    }

}