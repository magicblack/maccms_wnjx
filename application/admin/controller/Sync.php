<?php
namespace app\admin\controller;

class Sync extends Base
{

    public function __construct()
    {
        parent::__construct();
        header('X-Accel-Buffering: no');
    }

    public function index()
    {
        $param = input();
        if($param['ck']){
            $this->exec($param['url']);
            exit;
        }
        $config = config('maccms');
        $this->assign('config',$config);
        return $this->fetch('admin@sync/index');
    }

    public function exec($url='')
    {
        mac_echo('<style type="text/css">body{font-size:12px;color: #333333;line-height:21px;}span{font-weight:bold;color:#FF0000}</style>');

        if(empty($url)) {
            $config = config('maccms');
            $url = $config['site']['site_tourl'];
        }
        $html = mac_curl_get($url);

        if(empty($html)){
            mac_echo('获取目标网址源码失败，请重试');
            exit;
        }

        $urlinfo = parse_url($url);
        $baseurl = $urlinfo['scheme'].'://'.$urlinfo['host'] ;

        mac_echo('开始检测并同步css、ico等资源文件...');
        $arr = [];
        $preg = '/<link([\s\S]+?)href=[\'"]?([^>\'"\s]*)([\s\S]+?)>/is';
        preg_match_all($preg, $html, $out);
        foreach($out[2] as $k=>$v){
            if(strlen($v)>5 && substr($v,0,1)==='/'){
                if(empty($arr[$v])) {
                    $from = $baseurl . $v;
                    $to = str_replace('//','/',ROOT_PATH . $v);
                    if(!file_exists($to)) {
                        $hh = mac_curl_get($from);
                        $r = mac_write_file($to,$hh);
                        if($r){
                            mac_echo('<a target="_blank" href="'.$v.'">'.$v.'</a>&nbsp;<font color=green>ok</font>');
                        }
                        else{
                            mac_echo('<a target="_blank" href="'.$from.'">'.$v.'</a>&nbsp;<font color=red>err</font>');
                        }
                    }
                    else{
                        mac_echo('<a target="_blank" href="'.$v.'">'.$v.'</a>&nbsp;<font color=#9acd32>haved</font>');
                    }
                }
            }
        }
        //dump($out);die;
        //echo($html);die;

        //
        mac_echo('开始检测并同步js等资源文件...');
        $arr = [];
        $preg = '/<script([\s\S]+?)src=[\'"]?([^>\'"\s]*)([\s\S]+?)>/is';
        preg_match_all($preg, $html, $out);
        foreach($out[2] as $k=>$v){
            if(strlen($v)>5 && substr($v,0,1)==='/'){
                if(empty($arr[$v])) {
                    $from = $baseurl . $v;
                    $to = str_replace('//','/',ROOT_PATH . $v);
                    if(strpos($to,'?')!==false){
                        $to = substr($to,0,strpos($to,'?'));
                    }
                    if(!file_exists($to)) {
                        $hh = mac_curl_get($from);
                        $r = mac_write_file($to,$hh);
                        if($r){
                            mac_echo('<a target="_blank" href="'.$v.'">'.$v.'</a>&nbsp;<font color=green>ok</font>');
                        }
                        else{
                            mac_echo('<a target="_blank" href="'.$from.'">'.$v.'</a>&nbsp;<font color=red>err</font>');
                        }
                    }
                    else{
                        mac_echo('<a target="_blank" href="'.$v.'">'.$v.'</a>&nbsp;<font color=#9acd32>haved</font>');
                    }
                }
            }
        }
        //dump($out);die;
        //echo($html);die;
        mac_echo('资源同步完成，如果发生错误请多次尝试同步!');
    }
}
