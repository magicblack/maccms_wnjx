<?php
/*
'软件名称：苹果CMS万能镜像系统 源码库：https://github.com/magicblack
'--------------------------------------------------------
'Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
'遵循Apache2开源协议发布，并提供免费使用。
'--------------------------------------------------------
*/
error_reporting(E_ERROR | E_PARSE );
@ini_set('max_execution_time', '0');
@ini_set("memory_limit",'-1');
$url = $_GET["url"];
$mode = intval($_GET['mode']);
$bu = base64_decode($url);
if($url == base64_encode($bu)){
    $url = $bu;
}
if(empty($url) || substr($url,0,4)!=='http') {
    exit;
}
if($mode==1){
    header("Location: ".$url);
    exit;
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<style>body {margin-left: 0px;margin-top: 0px;margin-right: 0px;margin-bottom: 0px;overflow: hidden;}</style>
</head>
<body>
<iframe src="<?php echo $url;?>" width="100%" height="100%" frameborder="0"></iframe>
</body>
</html>