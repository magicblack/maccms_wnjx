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
        $tpu = $request->url();
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

        //预留
        if(empty($config['app']['img_file'])){
            $config['app']['img_file'] = 'img.php';
        }
        if(empty($config['external']['file'])){
            $config['external']['file'] = 'link.php';
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
        if(!empty($tpu) && $tpu!='/' && $tpu!='/index.php'){
            $url .= ''. $tpu;
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

        //img
        $preg = '/<img([\s\S]+?)src=[\'"]?([^>\'"\s]*)[\'"]([\s\S]+?)>/is';
        preg_match_all($preg,$html,$out);
        foreach($out[2] as $k=>$v){
            if(!empty($arr[$v])) {
                continue;
            }
            $arr[$v] = $v;
            if(substr($v,0,4)=='http' || substr($v,0,2)=='//'){
                $img = $v;
                if($config['app']['img_status'] =='1'){
                    $img = '/'.$config['app']['img_file'].'?url='. $img;
                }
                $html = str_replace($v,$img, $html);
            }
            elseif(in_array(substr($v,0,1),['/','.'])){
                if(empty($arr[$v])) {
                    $link = mac_url_check($v, $url);

                    $img = $rep['tourl'] . $link;
                    if($config['app']['img_status'] =='1'){
                        $img = '/'.$config['app']['img_file'].'?url='. $img;
                    }
                    $html = str_replace($v, $img, $html);
                }
            }
            else{

            }
        }
        //dump($arr);die;
        //echo($html);die;

        //css
        $arr = [];
        $preg = '/<link([\s\S]+?)href=[\'"]?([^>\'"\s]*)([\s\S]+?)>/is';
        preg_match_all($preg, $html, $out);
        foreach ($out[2] as $k => $v) {
            if(!empty($arr[$v])) {
                continue;
            }
            if(strpos($v,'rel=')===false){
                continue;
            }
            $arr[$v] = $v;
            if(substr($v,0,4)=='http' || substr($v,0,2)=='//'){

            }
            elseif(in_array(substr($v,0,1),['/','.'])){
                if($config['app']['zy_status'] =='1'){
                    $link = mac_url_check($v, $url);
                    $html = str_replace($v, $link, $html);
                }
            }
            else{

            }
        }
        //dump($out);die;
        //echo($html);die;

        //js
        $arr = [];
        $preg = '/<script([\s\S]+?)src=[\'"]?([^>\'"\s]*)([\s\S]+?)>/is';
        preg_match_all($preg, $html, $out);
        foreach ($out[2] as $k => $v) {
            if(!empty($arr[$v])) {
                continue;
            }
            $arr[$v] = $v;
            if(substr($v,0,4)=='http' || substr($v,0,2)=='//'){

            }
            elseif(in_array(substr($v,0,1),['/','.'])) {
                if($config['app']['zy_status'] =='1'){
                    $link = mac_url_check($v, $url);
                    $html = str_replace($v, $link, $html);
                }
            }
            else{

            }
        }
        //dump($out);die;
        //echo($html);die;

        //替换url
        $pi = pathinfo($rep['tourl']);
        $html = str_replace($pi['basename'],$domain,$html);
        $html = str_replace($rep_url.$rep_url,$rep_url,$html);
        //dump($pi);die;
        //echo($html);die;

        //a
        $arr = [];
        $preg = '/<a([\s\S]+?)href=[\'"]?([^>\'" ]*)[\'"]([\s\S]+?)>/is';
        preg_match_all($preg, $html, $out);
        foreach($out[2] as $k=>$v){
            if(empty($v) || !empty($arr[$v])){
                continue;
            }
            $arr[$v] = $v;
            if(substr($v,0,4)=='http' || substr($v,0,2)=='//') {
                if(empty($config['external']['ignore_domain']) || !preg_match('/'.$config['external']['ignore_domain'].'/',$v)){
                    $v_ec = urlencode($v);
                    if($config['external']['encode']==1){
                        $v_ec = base64_encode($v);
                    }
                    $link = '/'.$config['external']['file'].'?mode='.$config['external']['mode'].'&url='.$v_ec;
                    $html = str_replace($v, $link, $html);
                }
            }
            else{
                if(preg_match('/'.$config['app']['ignore_suffix'].'/is',$v)){
                    $link = mac_url_check($v,$rep['tourl']);
                    $html = str_replace($v, $link, $html);
                }
            }
        }
        //dump($arr);die;
        //echo($html);die;

        //type
        header("Content-type: ".mac_content_type($tpu));

        //compress
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
