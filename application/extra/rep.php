<?php
return array (
  0 => 
  array (
    'id' => '0',
    'status' => '0',
    'name' => '360baike',
    'des' => '360百科测试-baike.so.com-utf8',
    'tourl' => 'https://baike.so.com',
    'charset' => 'UTF-8',
    'domain_list' => 'test.a.cn',
    'rule_all' => '<div class="logo fl">([\\s\\S]+?)</div>[to]<div class="fl" style="padding-right:30px"><a href="/"><img src="/static/images/logo.png"></a></div>
<div id="g-hd" style="display:block">  <div id="g-hd-inner"> <div id="g-hd-nav"> <ul id="g-hd-tabs">(.*?)</ul></div></div>  </div>[to]
<div id="g-hd-tools" class="">(.*?)</div></div></div> </div>[to]
<div class="entry-plus wrap">(.*?)</div> </div> </div> </div> </div>[to]
<span class="opt js-edittext">(.*?)</span>[to]
<div class=rewardmodbk> <div class="h2-big">([\\s\\S]+?)换一批</a> </div> </div>[to]
<div class="entry-sense wrap">([\\s\\S]+?)</ul> </div> </div> </div>[to]
<div class="entry-banner"> ([\\s\\S]+?) </div> </div>     </div>[to]
<!--(.*?)-->[to]
<script(.*?)</script>[to]
<iframe(.*?)</iframe>[to]
360百科[to]测试网站名称',
    'rule_domain' => '',
    'time' => 1607339194,
    'domain_arr' => 
    array (
      0 => 'test.a.cn',
    ),
    'rule_all_arr' => 
    array (
      '<div class="logo fl">([\\s\\S]+?)</div>' => '<div class="fl" style="padding-right:30px"><a href="/"><img src="/static/images/logo.png"></a></div>',
      '<div id="g-hd" style="display:block">  <div id="g-hd-inner"> <div id="g-hd-nav"> <ul id="g-hd-tabs">(.*?)</ul></div></div>  </div>' => '',
      '<div id="g-hd-tools" class="">(.*?)</div></div></div> </div>' => '',
      '<div class="entry-plus wrap">(.*?)</div> </div> </div> </div> </div>' => '',
      '<span class="opt js-edittext">(.*?)</span>' => '',
      '<div class=rewardmodbk> <div class="h2-big">([\\s\\S]+?)换一批</a> </div> </div>' => '',
      '<div class="entry-sense wrap">([\\s\\S]+?)</ul> </div> </div> </div>' => '',
      '<div class="entry-banner"> ([\\s\\S]+?) </div> </div>     </div>' => '',
      '<!--(.*?)-->' => '',
      '<script(.*?)</script>' => '',
      '<iframe(.*?)</iframe>' => '',
      '360百科' => '测试网站名称',
    ),
    'rule_domain_arr' => 
    array (
    ),
  ),
  1 => 
  array (
    'id' => '1',
    'status' => '0',
    'name' => 'baidubaike',
    'des' => '百度百科测试-baike.baidu.com-utf8',
    'tourl' => 'https://baike.baidu.com',
    'charset' => 'UTF-8',
    'domain_list' => 'test.b.cn',
    'rule_all' => '<div class="topbar cmn-clearfix">([\\s\\S]+?)</div>[to]
<div class="wiki-common-headTabBar">([\\s\\S]+?)</div>[to]
<div class="right-list">([\\s\\S]+?)</div>[to]
<a class="help" href="/help" nslog="normal" nslog-type="10080010" target="_blank">帮助</a>[to]
<a href="/page/createintro" target="_blank" class="statistics_create">创建词条</a>[to]
<!--(.*?)-->[to]
<iframe(.*?)</iframe>[to]',
    'rule_domain' => '',
    'time' => 1607339173,
    'domain_arr' => 
    array (
      0 => 'test.b.cn',
    ),
    'rule_all_arr' => 
    array (
      '<div class="topbar cmn-clearfix">([\\s\\S]+?)</div>' => '',
      '<div class="wiki-common-headTabBar">([\\s\\S]+?)</div>' => '',
      '<div class="right-list">([\\s\\S]+?)</div>' => '',
      '<a class="help" href="/help" nslog="normal" nslog-type="10080010" target="_blank">帮助</a>' => '',
      '<a href="/page/createintro" target="_blank" class="statistics_create">创建词条</a>' => '',
      '<!--(.*?)-->' => '',
      '<iframe(.*?)</iframe>' => '',
    ),
    'rule_domain_arr' => 
    array (
    ),
  ),
  2 => 
  array (
    'id' => '2',
    'status' => '1',
    'name' => 'baidutop',
    'des' => '百度风云榜测试-top.baidu.com-gbk',
    'tourl' => 'https://top.baidu.com',
    'charset' => 'GBK',
    'domain_list' => '',
    'rule_all' => '<!--(.*?)-->[to]
<iframe(.*?)</iframe>[to]
<div id="ba_sc">([\\s\\S]+?) </div>[to]<center><font style="font-size:40px;">苹果cms万能镜像系统测试</font></center>
<p class="ba_hp">([\\s\\S]+?) </p>[to]
<div class="lg">([\\s\\S]+?) </div>[to]
百度搜索风云榜[to]我的搜索风云榜-苹果cms万能镜像系统\'',
    'rule_domain' => 'c.cn
实时热点[to](c.cn)
d.cn
实时热点[to](d.cn)',
    'time' => 1607339146,
    'domain_arr' => 
    array (
      0 => '',
    ),
    'rule_all_arr' => 
    array (
      '<!--(.*?)-->' => '',
      '<iframe(.*?)</iframe>' => '',
      '<div id="ba_sc">([\\s\\S]+?) </div>' => '<center><font style="font-size:40px;">苹果cms万能镜像系统测试</font></center>',
      '<p class="ba_hp">([\\s\\S]+?) </p>' => '',
      '<div class="lg">([\\s\\S]+?) </div>' => '',
      '百度搜索风云榜' => '我的搜索风云榜-苹果cms万能镜像系统\'',
    ),
    'rule_domain_arr' => 
    array (
      'c.cn' => 
      array (
        '实时热点' => '(c.cn)',
      ),
      'd.cn' => 
      array (
        '实时热点' => '(d.cn)',
      ),
    ),
  ),
);