<?php
return array (
  'admin' => 
  array (
    'name' => 'admin1',
    'pass' => 'admin1',
  ),
  'site' => 
  array (
    'install_dir' => '/',
    'site_url' => 'http://c.cn',
    'site_tourl' => 'https://top.baidu.com',
    'site_charset' => 'GBK',
    'site_status' => '1',
    'site_logo' => '',
    'template_dir' => 'default',
    'html_dir' => 'html',
    'site_close_tip' => '站点暂时关闭，请稍后访问',
  ),
  'app' => 
  array (
    'useragent' => 'Sosospider+(+http://help.soso.com/webspider.htm)
Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)
Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)
Baiduspider-image+(+http://www.baidu.com/search/spider.htm)
Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0); 360Spider',
    'img_status' => '1',
    'zy_status' => '0',
    'ip_status' => '0',
    'cache_type' => 'file',
    'cache_host' => '127.0.0.1',
    'cache_port' => '11211',
    'cache_username' => '',
    'cache_password' => '',
    'cache_flag' => 'a6bcf9aa58',
    'cache_page' => '0',
    'cache_time_page' => '3600',
    'compress' => '0',
    'browser_junmp' => '0',
    'page_404' => '404',
    'lang' => 'zh-cn',
    'useragent_arr' => 
    array (
      0 => 'Sosospider+(+http://help.soso.com/webspider.htm)',
      1 => 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)',
      2 => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
      3 => 'Baiduspider-image+(+http://www.baidu.com/search/spider.htm)',
      4 => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0); 360Spider',
    ),
  ),
  'rewrite' => 
  array (
    'route' => '<p?>   => index/index',
  ),
  'view' => NULL,
  'path' => NULL,
  'spider' => 
  array (
    'status' => '0',
    'rule' => 'yahoo|bing',
  ),
);