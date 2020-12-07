<?php
namespace app\index\controller;

use think\Cache;
class Index extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $request = request();
        $p = $request->url();
        $config = config('maccms');
        $domain = $_SERVER['HTTP_HOST'];
        //spider
        if($config['spider']['status']==1) {
            $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
            $preg = '/' . strtolower($config['spider']['rule']) . '/is';
            if(preg_match($preg,$ua)){
                header("HTTP/1.1 404 Not Found");
                header("Status: 404 Not Found");
                exit;
            }
        }

        //匹配规则
        $rep_list = config('rep');
        $rc=false;
        foreach($rep_list as $k=>$v){
            if($rc){
                break;
            }
            if($v['status'] =='1'){
                if(empty($v['domain_list'])){
                    $rep = $v;
                    break;
                }
                else{
                    foreach($v['domain_arr'] as $k2=>$v2){
                        if(strpos($domain,$v2)!==false){
                            $rep = $v;
                            $rc=true;
                            break;
                        }
                    }
                }
            }
        }
        if(empty($rep)){
            echo lang('matching_rule_failed');
            exit;
        }

        $url = $rep['tourl'];
        if($_SERVER['PHP_SELF']!='/index.php'){
            $url .= $_SERVER['PHP_SELF'];
        }
        if(!empty($p) && $p!='/' && $p!='/index.php'){
            $url .= ''. $p;
        }

        $rep_url = $GLOBALS['http_type'] . $domain . '';
        //cache
        if($config['app']['cache_page']=='1' && $config['app']['cache_time_page']){
            $this->load_page_cache($url);
        }

        //ip
        $heads=[];
        if($config['app']['ip_status']==1){
            $heads[] = 'X-FORWARDED-FOR:'.request()->ip();
            $heads[] = 'CLIENT-IP'. request()->ip();
        }

        $html = mac_curl_get($url,$heads);
        $html = mac_convert_encoding($html,'UTF-8',$rep['charset']);

        //替换url
        $p = pathinfo($rep['tourl']);
        $html = str_replace($p['basename'],$domain,$html);
        $html = str_replace($rep_url.$rep_url,$rep_url,$html);
        //dump($p);die;
        //echo($html);die;

        //替换配置
        foreach ($rep['rule_all_arr'] as $k=>$v) {
            $k = mac_build_regx($k, 'is');
            $html = preg_replace($k, $v, $html);
        }
        foreach($rep['rule_domain_arr'] as  $k=>$v){
            if(strpos($domain,$k)!==false){
                foreach($rep['rule_domain_arr'][$k] as $k2 => $v2) {
                    $k2 = mac_build_regx($k2, 'is');
                    $html = preg_replace($k2, $v2, $html);
                }
            }
        }
        //echo($html);die;

        if($config['app']['zy_status'] =='0') {
            //css
            $arr = [];
            $preg = '/<link([\s\S]+?)href=[\'"]?([^>\'"\s]*)([\s\S]+?)>/is';
            preg_match_all($preg, $html, $out);
            foreach ($out[2] as $k => $v) {
                $a = substr($v,0,1);
                if(strlen($v)>5 && in_array($a,['/','.'])) {
                    if (empty($arr[$v])) {
                        $link = mac_url_check($v, $url);
                        $html = str_replace($v, $link, $html);
                    }
                }
            }
            //dump($out);die;

            //js
            $arr = [];
            $preg = '/<script([\s\S]+?)src=[\'"]?([^>\'"\s]*)([\s\S]+?)>/is';
            preg_match_all($preg, $html, $out);
            foreach ($out[2] as $k => $v) {
                $a = substr($v,0,1);
                if(strlen($v)>5 && in_array($a,['/','.'])) {
                    if (empty($arr[$v])) {
                        $link = mac_url_check($v, $url);
                        $html = str_replace($v, $link, $html);
                    }
                }
            }
            //dump($out);die;
        }

        //a
        $arr = [];
        $preg = '/<a([\s\S]+?)href=[\'"]?([^>\'" ]*)[\'"]([\s\S]+?)>/is';
        preg_match_all($preg, $html, $out);
        foreach($out[2] as $k=>$v){
            $a = substr($v,0,1);
            if(strlen($v)>5 && in_array($a,['/','.'])){
                if(empty($arr[$v])) {
                    $link = mac_url_check($v, $url);
                    $arr[$v] = $rep_url.$link;
                    $html = str_replace($v, $rep_url. $link, $html);
                }
            }
        }
        //dump($out);die;

        //img
        $preg = '/<img([\s\S]+?)src=[\'"]?([^>\'"\s]*)[\'"]([\s\S]+?)>/is';
        preg_match_all($preg,$html,$out);
        foreach($out[2] as $k=>$v){
            $a = substr($v,0,1);
            if(strlen($v)>5 && in_array($a,['/','.'])){
                if(empty($arr[$v])) {
                    $link = mac_url_check($v, $url);
                    $arr[$v] = $rep_url . $link;
                    $img = $rep['tourl'] . $link;
                    if($config['app']['img_status'] =='1'){
                        $img = '/img.php?url='. $img;
                    }
                    $html = str_replace($v, $img, $html);
                }
            }
            elseif(substr($v,0,4)==='http'){
                $img = $v;
                if($config['app']['img_status'] =='1'){
                    $img = '/img.php?url='. $img;
                }
                $html = str_replace($v,$img, $html);
            }
        }
        //dump($arr);die;

        if($config['app']['compress'] == 1){
            $html = mac_compress_html($html);
        }

        //cache
        if($config['app']['cache_page']=='1' && $config['app']['cache_time_page']){
            $cach_name = $_SERVER['HTTP_HOST']. '_'. MAC_MOB . '_'. $config['app']['cache_flag']. '_' . $url .'_'. http_build_query(mac_param_url());
            Cache::set($cach_name,$html,$config['app']['cache_time_page']);
        }

        echo $html;exit;
    }

}
