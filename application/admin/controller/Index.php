<?php
namespace app\admin\controller;

class Index extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function login()
    {
        if(Request()->isPost()) {
            $data = input('post.');

            if(empty($data['admin_name']) || empty($data['admin_pwd']) || empty($data['verify']) ){
                return $this->error(lang('param_err'));
            }

            if(!captcha_check($data['verify'])){
                return $this->error(lang('verify_err'));
            }

            if($data['admin_name'] !== $GLOBALS['config']['admin']['name'] || $data['admin_pwd'] !== $GLOBALS['config']['admin']['pass']){
                return $this->error(lang('login_err'));
            }
            session('adminauth','1');
            return $this->success('login_ok');
        }
        return $this->fetch('admin@index/login');
    }

    public function logout()
    {
        session('adminauth',null);
        $this->redirect('index/login');
    }

    public function index()
    {
        $menus = @include MAC_ADMIN_COMM . 'auth.php';

        foreach($menus as $k1=>$v1){
            foreach($v1['sub'] as $k2=>$v2){
                if($v2['show'] == 1) {
                    if(strpos($v2['action'],'javascript')!==false){
                        $url = $v2['action'];
                    }
                    else {
                        $url = url('admin/' . $v2['controller'] . '/' . $v2['action']);
                    }
                    if (!empty($v2['param'])) {
                        $url .= '?' . $v2['param'];
                    }
                    $menus[$k1]['sub'][$k2]['url'] = $url;
                }
                else{
                    unset($menus[$k1]['sub'][$k2]);
                }
            }

            if(empty($menus[$k1]['sub'])){
                unset($menus[$k1]);
            }
        }

        $this->assign('menus',$menus);

        $this->assign('title',lang('admin/index/title'));
        return $this->fetch('admin@index/index');
    }

    public function welcome()
    {
        $version = config('version');
        $this->assign('version',$version);
        $this->assign('mac_lang',config('default_lang'));

        $this->assign('info',$this->_admin);
        $this->assign('title',lang('admin/index/welcome/title'));
        return $this->fetch('admin@index/welcome');
    }

    public function quickmenu()
    {
        if(Request()->isPost()){
            $param = input();
            $validate = \think\Loader::validate('Token');
            if(!$validate->check($param)){
                return $this->error($validate->getError());
            }
            $quickmenu = input('post.quickmenu');
            $quickmenu = str_replace(chr(10),'',$quickmenu);
            $menu_arr = explode(chr(13),$quickmenu);
            $res = mac_arr2file(APP_PATH . 'extra/quickmenu.php', $menu_arr);
            if ($res === false) {
                return $this->error(lang('save_err'));
            }
            return $this->success(lang('save_ok'));
        }
        else{
            $config_menu = config('quickmenu');
            if(empty($config_menu)){
                $quickmenu = mac_read_file(APP_PATH.'data/config/quickmenu.txt');
            }
            else{
                $quickmenu = array_values($config_menu);
                $quickmenu = join(chr(13),$quickmenu);
            }
            $this->assign('quickmenu',$quickmenu);
            $this->assign('title',lang('admin/index/quickmenu/title'));
            return $this->fetch('admin@index/quickmenu');
        }
    }

    public function clear()
    {
        $res = $this->_cache_clear();
        //运行缓存
        if(!$res) {
            $this->error(lang('admin/index/clear_err'));
        }
        return $this->success(lang('admin/index/clear_ok'));
    }

    public function iframe()
    {
        $val = input('post.val', 0);
        if ($val != 0 && $val != 1) {
            return $this->error(lang('admin/index/clear_ok'));
        }
        if ($val == 1) {
            cookie('hisi_iframe', 'yes');
        } else {
            cookie('hisi_iframe', null);
        }
        return $this->success(lang('admin/index/iframe'));
    }

    public function unlocked()
    {
        $param = input();
        $password = $param['password'];

        if($GLOBALS['config']['admin']['pass'] != $password){
            return $this->error(lang('admin/index/pass_err'));
        }

        return $this->success(lang('admin/index/unlock_ok'));
    }

    public function check_back_link()
    {
        $param = input();
        $res = mac_check_back_link($param['url']);
        return json($res);
    }

}
