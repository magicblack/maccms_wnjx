<?php
namespace app\admin\controller;
use think\Db;
use think\Config;
use think\Cache;
use think\View;

class System extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function test_email()
    {
        $post = input();
        $conf = [
            'host' => $post['host'],
            'username' => $post['username'],
            'password' => $post['password'],
            'port' => $post['port'],
            'nick' => $post['nick'],
            'test' => $post['test'],
        ];
        $this->label_maccms();
        $res = mac_send_mail($conf['test'], $GLOBALS['config']['email']['tpl']['test_title'], $GLOBALS['config']['email']['tpl']['test_body'], $conf);
        if ($res['code']==1) {
            return json(['code' => 1, 'msg' => lang('test_ok')]);
        }
        return json(['code' => 1001, 'msg' => lang('test_err').'：'.$res['msg']]);
    }

    public function test_cache()
    {
        $param = input();

        if (!isset($param['type']) || empty($param['host']) || empty($param['port'])) {
            return $this->error(lang('param_err'));
        }

        $options = [
            'type' => $param['type'],
            'port' => $param['port'],
            'username' => $param['username'],
            'password' => $param['password']
        ];

        $hd = Cache::connect($options);
        $hd->set('test', 'test');

        return json(['code' => 1, 'msg' => lang('test_ok')]);
    }

    public function config()
    {
        if (Request()->isPost()) {
            $config = input();

            $validate = \think\Loader::validate('Token');
            if(!$validate->check($config)){
                return $this->error($validate->getError());
            }
            unset($config['__token__']);

            if(empty($config['app']['cache_flag'])){
                $config['app']['cache_flag'] = substr(md5(time()),0,10);
            }

            $config['app']['useragent_arr'] = array_filter(explode(chr(10),$config['app']['useragent']));

            $config_new['site'] = $config['site'];
            $config_new['app'] = $config['app'];

            $config_old = config('maccms');
            $config_new = array_merge($config_old, $config_new);

            $res = mac_arr2file(APP_PATH . 'extra/maccms.php', $config_new);
            if ($res === false) {
                return $this->error(lang('save_err'));
            }
            return $this->success(lang('save_ok'));
        }

        $templates = glob('./template' . '/*', GLOB_ONLYDIR);
        foreach ($templates as $k => &$v) {
            $v = str_replace('./template/', '', $v);
        }
        $this->assign('templates', $templates);


        $langs = glob('./application/lang/*.php');
        foreach ($langs as $k => &$v) {
            $v = str_replace(['./application/lang/','.php'],['',''],$v);
        }
        $this->assign('langs', $langs);

        $config = config('maccms');
        $this->assign('config', $config);
        $this->assign('title', lang('admin/system/config/title'));
        return $this->fetch('admin@system/config');
    }

    public function url()
    {
        if (Request()->isPost()) {
            $config = input();
            $config_new['rewrite'] = $config['rewrite'];

            //写路由规则文件
            $route = [];
            $route['__pattern__'] = [

                'p'=>'[\s\S]*?',
            ];
            $rows = explode(chr(13), str_replace(chr(10), '', $config['rewrite']['route']));
            foreach ($rows as $r) {
                if (strpos($r, '=>') !== false) {
                    $a = explode('=>', $r);
                    $rule = [];
                    if (strpos($a, ':id') !== false) {
                        //$rule['id'] = '\w+';
                    }
                    $route[trim($a[0])] = [trim($a[1]), [], $rule];
                }
            }

            $res = mac_arr2file(APP_PATH . 'route.php', $route);
            if ($res === false) {
                return $this->error(lang('write_err_route'));
            }

            //写扩展配置
            $config_old = config('maccms');
            $config_new = array_merge($config_old, $config_new);
            $res = mac_arr2file(APP_PATH . 'extra/maccms.php', $config_new);
            if ($res === false) {
                return $this->error(lang('write_err_config'));
            }
            return $this->success(lang('save_ok'));
        }

        $this->assign('config', config('maccms'));
        return $this->fetch('admin@system/url');
    }

    public function spider()
    {
        if (Request()->isPost()) {
            $config = input();
            $config_new['spider'] = $config['spider'];

            $config_old = config('maccms');
            $config_new = array_merge($config_old, $config_new);
            $res = mac_arr2file(APP_PATH . 'extra/maccms.php', $config_new);
            if ($res === false) {
                return $this->error(lang('write_err_config'));
            }
            return $this->success(lang('save_ok'));
        }

        $this->assign('config', config('maccms'));
        return $this->fetch('admin@system/spider');
    }

    public function admin()
    {
        if (Request()->isPost()) {
            $config = input();

            if(empty($config['admin']['name']) || empty($config['admin']['pass'])){
                return $this->error(lang('param_err'));
            }
            $config_new['admin'] = $config['admin'];

            $config_old = config('maccms');
            $config_new = array_merge($config_old, $config_new);
            $res = mac_arr2file(APP_PATH . 'extra/maccms.php', $config_new);
            if ($res === false) {
                return $this->error(lang('write_err_config'));
            }
            return $this->success(lang('save_ok'));
        }

        $this->assign('config', config('maccms'));
        return $this->fetch('admin@system/admin');
    }


    public function external()
    {
        if (Request()->isPost()) {
            $config = input();
            $config_new['external'] = $config['external'];

            $config_old = config('maccms');
            $config_new = array_merge($config_old, $config_new);
            $res = mac_arr2file(APP_PATH . 'extra/maccms.php', $config_new);
            if ($res === false) {
                return $this->error(lang('write_err_config'));
            }
            return $this->success(lang('save_ok'));
        }

        $this->assign('config', config('maccms'));
        return $this->fetch('admin@system/external');
    }
}
