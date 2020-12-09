<?php
/*
'软件名称：苹果CMS  源码库：https://github.com/magicblack
'--------------------------------------------------------
'Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
'遵循Apache2开源协议发布，并提供免费使用。
'--------------------------------------------------------
*/

use think\View;

error_reporting(E_ERROR | E_PARSE );

//访问日志记录，根目录创建log目录
function slog($logs)
{
    $ymd = date('Y-m-d-H');
    $now = date('Y-m-d H:i:s');
    $toppath = "./log/$ymd.txt";
    $ts = @fopen($toppath,"a+");
    @fputs($ts, $now .' '. $logs ."\r\n");
    @fclose($ts);
}
//foreach($_GET as $k=>$v){ $getData .= $k.'='.$v.'&'; }
//foreach($_POST as $k=>$v){ $postData .= $k.'='.$v.'&'; }
//foreach($_COOKIE as $k=>$v){ $cookieData .= $k.'='.$v.'&'; }
//$log = $_SERVER['PHP_SELF'] . '---get:' .$getData .'---post:' . $postData .'---'. json_encode($_POST).'---cookie:' . $cookieData ;
//slog($log);

// 应用公共文件
function mac_return($msg,$code=1,$data=''){
    if(is_array($msg)){
        return json_encode($msg);
    }
    else {
        $rs = ['code' => $code, 'msg' => $msg, 'data'=>'' ];
        if(is_array($data)) $rs['data'] = $data;
        return json_encode($rs);
    }
}

function mac_run_statistics()
{
    $t2 = microtime(true) - MAC_START_TIME;
    $size = memory_get_usage();
    $memory = mac_format_size($size);
    unset($unit);
    return 'Processed in: '.round($t2,4).' second(s),&nbsp;' . $memory . ' Mem On.';
}

function mac_format_size($s=0)
{
	if($s==0){ return '0 kb'; }
	$unit=array('b','kb','mb','gb','tb','pb');
	return round($s/pow(1024,($i=floor(log($s,1024)))),2).' '.$unit[$i];
}

function mac_read_file($f)
{
    return @file_get_contents($f);
}

function mac_write_file($f,$c='')
{
    $dir = dirname($f);
    if(!is_dir($dir)){
        mac_mkdirss($dir);
    }
    return @file_put_contents($f, $c);
}

function mac_mkdirss($path,$mode=0777)
{
    if (!is_dir(dirname($path))){
        mac_mkdirss(dirname($path));
    }
    if(!file_exists($path)){
        return mkdir($path,$mode);
    }
    return true;
}

function mac_rmdirs($dirname, $withself = true)
{
    if (!is_dir($dirname))
        return false;
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dirname, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $fileinfo)
    {
        $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
        $todo($fileinfo->getRealPath());
    }
    if ($withself)
    {
        @rmdir($dirname);
    }
    return true;
}

function mac_copydirs($source, $dest)
{
    if (!is_dir($dest))
    {
        mkdir($dest, 0755);
    }
    foreach (
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item
    )
    {
        if ($item->isDir())
        {
            $sontDir = $dest . DS . $iterator->getSubPathName();
            if (!is_dir($sontDir))
            {
                mkdir($sontDir);
            }
        }
        else
        {
            copy($item, $dest . DS . $iterator->getSubPathName());
        }
    }
}

function mac_arr2file($f,$arr='')
{
    if(is_array($arr)){
        $con = var_export($arr,true);
    } else{
        $con = $arr;
    }
    $con = "<?php\nreturn $con;";
    mac_write_file($f, $con);
}

function mac_replace_text($txt,$type=1)
{
    if($type==1){
        return str_replace('#',Chr(13),$txt);
    }
    return str_replace(chr(13),'#',str_replace(chr(10),'',$txt));
}

function mac_compress_html($s){
    $s = str_replace(array("\r\n","\n","\t"), array('','','') , $s);
    $pattern = array (
        "/> *([^ ]*) *</",
        "/[\s]+/",
        "/<!--[\\w\\W\r\\n]*?-->/",
        // "/\" /",
        "/ \"/",
        "'/\*[^*]*\*/'"
    );
    $replace = array (
        ">\\1<",
        " ",
        "",
        //"\"",
        "\"",
        ""
    );
    return preg_replace($pattern, $replace, $s);
}

function mac_build_regx($regstr,$regopt)
{
    return '/'.str_replace('/','\/',$regstr).'/'.$regopt;
}

function mac_reg_replace($str,$rule,$value)
{
    $res='';
    $rule = mac_build_regx($rule,"is");
    if (!empty($str)){
        $res = preg_replace($rule,$value,$str);
    }
    return $res;
}

function mac_reg_match($str,$rule)
{
    $res='';
    $rule = mac_build_regx($rule,"is");
    preg_match_all($rule,$str,$mc);
    $mfv=$mc[1];
    foreach($mfv as $f=>$v){
        $res = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$v));
        break;
    }
    unset($mc);
    return $res;
}

function mac_redirect($url,$obj='')
{
    echo '<script>'.$obj.'location.href="' .$url .'";</script>';
    exit;
}

function mac_alert($str)
{
    echo '<script>alert("' .$str. '\t\t");history.go(-1);</script>';
}

function mac_alert_url($str,$url)
{
    echo '<script>alert("' .$str. '\t\t");location.href="' .$url .'";</script>';
}

function mac_jump($url,$sec=0)
{
    echo '<script>setTimeout(function (){location.href="'.$url.'";},'.($sec*1000).');</script><span>'.lang('pause').''.$sec.''.lang('continue_in_second').'  >>>  </span><a href="'.$url.'" >'.lang('browser_jump').'</a><br>';
}

function mac_echo($str)
{
    echo $str.'<br>';
    ob_flush();flush();
}

function mac_day($t,$f='',$c='#FF0000')
{
    if(empty($t)) { return ''; }
    if(is_numeric($t)){
        $t = date('Y-m-d H:i:s',$t);
    }
    $now = date('Y-m-d',time());
    if($f=='color' && strpos(','.$t,$now)>0){
        return '<font color="' .$c. '">' .$t. '</font>';
    }
    return  $t;
}

function mac_friend_date($time)
{
    if (!$time)
        return false;
    $fdate = '';
    $d = time() - intval($time);
    $ld = $time - mktime(0, 0, 0, 0, 0, date('Y')); //得出年
    $md = $time - mktime(0, 0, 0, date('m'), 0, date('Y')); //得出月
    $byd = $time - mktime(0, 0, 0, date('m'), date('d') - 2, date('Y')); //前天
    $yd = $time - mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')); //昨天
    $dd = $time - mktime(0, 0, 0, date('m'), date('d'), date('Y')); //今天
    $td = $time - mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')); //明天
    $atd = $time - mktime(0, 0, 0, date('m'), date('d') + 2, date('Y')); //后天
    if ($d == 0) {
        $fdate = lang('just');
    } else {
        switch ($d) {
            case $d < $atd:
                $fdate = date('Y'.lang('year').'m'.lang('month').'d'.lang('day'), $time);
                break;
            case $d < $td:
                $fdate = lang('day_after_tomorrow') . date('H:i', $time);
                break;
            case $d < 0:
                $fdate = lang('tomorrow') . date('H:i', $time);
                break;
            case $d < 60:
                $fdate = $d . lang('seconds_ago');
                break;
            case $d < 3600:
                $fdate = floor($d / 60) . lang('minutes_ago');
                break;
            case $d < $dd:
                $fdate = floor($d / 3600) . lang('hours_ago');
                break;
            case $d < $yd:
                $fdate = lang('yesterday') . date('H:i', $time);
                break;
            case $d < $byd:
                $fdate = lang('day_before_yesterday') . date('H:i', $time);
                break;
            case $d < $md:
                $fdate = date('m'.lang('month').'d'.lang('day').' H:i', $time);
                break;
            case $d < $ld:
                $fdate = date('m'.lang('month').'d'.lang('day'), $time);
                break;
            default:
                $fdate = date('Y'.lang('year').'m'.lang('month').'d'.lang('day'), $time);
                break;
        }
    }
    return $fdate;
}

function mac_get_time_span($sn)
{
    $lastTime = session($sn);

    if(empty($lastTime)){
        $lastTime= "1228348800";
    }
    $res = time() - intval($lastTime);
    session($sn,time());
    return $res;
}

function mac_get_rndstr($length=32,$f='')
{
    $pattern = "234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    if($f=='num'){
        $pattern = '1234567890';
    }
    elseif($f=='letter'){
        $pattern = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    }
    $len = strlen($pattern) -1;
    $res='';
    for($i=0; $i<$length; $i++){
        $res .= $pattern[mt_rand(0,$len)];
    }
    return $res;
}

function mac_convert_encoding($str,$nfate,$ofate){
    if ($ofate=="UTF-8"){ return $str; }
    if ($ofate=="GB2312"){ $ofate="GBK"; }

    if(function_exists("mb_convert_encoding")){
        $str=mb_convert_encoding($str,$nfate,$ofate);
    }
    else{
        $ofate.="//IGNORE";
        $str=iconv($nfate ,$ofate ,$str);
    }
    return $str;
}

function mac_get_refer()
{
    return trim(urldecode($_SERVER["HTTP_REFERER"]));
}

function mac_list_to_tree($list, $pk='id',$pid = 'pid',$child = 'child',$root=0)
{
    $tree = array();
    if(is_array($list)) {
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }

        foreach ($list as $key => $data) {
            $parentId = $data[$pid];

            if ($root == $parentId) {
                $tree[] =& $list[$key];

            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

function mac_str_correct($str,$from,$to)
{
    return str_replace($from,$to,$str);
}

function mac_buildregx($regstr,$regopt)
{
    return '/'.str_replace('/','\/',$regstr).'/'.$regopt;
}

function mac_curl_post($url,$data,$heads=array(),$cookie='')
{
    $ch = @curl_init();
    $uar = $GLOBALS['config']['app']['useragent_arr'];
    $ua = $uar[array_rand($uar)];
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLINFO_CONTENT_LENGTH_UPLOAD,strlen($data));
    curl_setopt($ch, CURLOPT_HEADER,0);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    if(!empty($cookie)){
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    }
    if(count($heads)>0){
        curl_setopt ($ch, CURLOPT_HTTPHEADER , $heads );
    }
    $response = @curl_exec($ch);
    if(curl_errno($ch)){//出错则显示错误信息
        //print curl_error($ch);
    }
    curl_close($ch); //关闭curl链接
    return $response;//显示返回信息
}

function mac_curl_get($url,$heads=array(),$cookie='')
{
    $ch = @curl_init();

    $uar = $GLOBALS['config']['app']['useragent_arr'];
    $ua = $uar[array_rand($uar)];

    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HEADER,0);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
    if(!empty($cookie)){
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    }
    if(count($heads)>0){
        curl_setopt ($ch, CURLOPT_HTTPHEADER , $heads );
    }
    $response = @curl_exec($ch);
    if(curl_errno($ch)){//出错则显示错误信息
        //print curl_error($ch);die;
    }
    curl_close($ch); //关闭curl链接
    return $response;//显示返回信息
}

function mac_substring($str, $lenth, $start=0)
{
    $len = strlen($str);
    $r = array();
    $n = 0;
    $m = 0;

    for($i=0;$i<$len;$i++){
        $x = substr($str, $i, 1);
        $a = base_convert(ord($x), 10, 2);
        $a = substr( '00000000 '.$a, -8);

        if ($n < $start){
            if (substr($a, 0, 1) == 0) {
            }
            else if (substr($a, 0, 3) == 110) {
                $i += 1;
            }
            else if (substr($a, 0, 4) == 1110) {
                $i += 2;
            }
            $n++;
        }
        else{
            if (substr($a, 0, 1) == 0) {
                $r[] = substr($str, $i, 1);
            }else if (substr($a, 0, 3) == 110) {
                $r[] = substr($str, $i, 2);
                $i += 1;
            }else if (substr($a, 0, 4) == 1110) {
                $r[] = substr($str, $i, 3);
                $i += 2;
            }else{
                $r[] = ' ';
            }
            if (++$m >= $lenth){
                break;
            }
        }
    }
    return  join('',$r);
}

function mac_array2xml($arr,$level=1)
{
    $s = $level == 1 ? "<xml>" : '';
    foreach($arr as $tagname => $value) {
        if (is_numeric($tagname)) {
            $tagname = $value['TagName'];
            unset($value['TagName']);
        }
        if(!is_array($value)) {
            $s .= "<{$tagname}>".(!is_numeric($value) ? '<![CDATA[' : '').$value.(!is_numeric($value) ? ']]>' : '')."</{$tagname}>";
        } else {
            $s .= "<{$tagname}>" . $this->array2xml($value, $level + 1)."</{$tagname}>";
        }
    }
    $s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);
    return $level == 1 ? $s."</xml>" : $s;
}

function mac_xml2array($xml)
{
    libxml_disable_entity_loader(true);
    $result= json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    return $result;
}

function mac_array_rekey($arr,$key)
{
    $list = [];
    foreach($arr as $k=>$v){
        $list[$v[$key]] = $v;
    }
    return $list;
}

function mac_array_filter($arr,$str)
{
    if(!is_array($arr)){
        $arr = explode(',',$arr);
    }
    $arr = array_filter($arr);
    if(empty($arr)){
        return false;
    }
    //方式一
    $new_str = str_replace($arr,'*',$str);
    //$badword1 = array_combine($arr,array_fill(0,count($arr),'*'));
    //$new_str = strtr($str, $badword1);
    return $new_str != $str;
}

function mac_escape($string, $in_encoding = 'UTF-8',$out_encoding = 'UCS-2') {
    $return = '';
    if (function_exists('mb_get_info')) {
        for($x = 0; $x < mb_strlen ( $string, $in_encoding ); $x ++) {
            $str = mb_substr ( $string, $x, 1, $in_encoding );
            if (strlen ( $str ) > 1) { // 多字节字符
                $return .= '%u' . strtoupper ( bin2hex ( mb_convert_encoding ( $str, $out_encoding, $in_encoding ) ) );
            } else {
                $return .= '%' . strtoupper ( bin2hex ( $str ) );
            }
        }
    }
    return $return;
}

function mac_unescape($str)
{
    $ret = '';
    $len = strlen($str);
    for ($i = 0; $i < $len; $i ++)
    {
        if ($str[$i] == '%' && $str[$i + 1] == 'u')
        {
            $val = hexdec(substr($str, $i + 2, 4));
            if ($val < 0x7f)
                $ret .= chr($val);
            else
                if ($val < 0x800)
                    $ret .= chr(0xc0 | ($val >> 6)) .
                        chr(0x80 | ($val & 0x3f));
                else
                    $ret .= chr(0xe0 | ($val >> 12)) .
                        chr(0x80 | (($val >> 6) & 0x3f)) .
                        chr(0x80 | ($val & 0x3f));
            $i += 5;
        } else
            if ($str[$i] == '%')
            {
                $ret .= urldecode(substr($str, $i, 3));
                $i += 2;
            } else
                $ret .= $str[$i];
    }
    return $ret;
}

function mac_filter_html($str)
{
    return strip_tags($str);
}

function mac_format_text($str)
{
    return str_replace(array('/','，','|','、',' ',',,,'),',',$str);
}

function mac_format_count($str)
{
    $arr = explode(',',$str);
    return count($arr);
}

function mac_parse_sql($sql='',$limit=0,$prefix=[])
{
    // 被替换的前缀
    $from = '';
    // 要替换的前缀
    $to = '';

    // 替换表前缀
    if (!empty($prefix)) {
        $to   = current($prefix);
        $from = current(array_flip($prefix));
    }

    if ($sql != '') {
        // 纯sql内容
        $pure_sql = [];

        // 多行注释标记
        $comment = false;

        // 按行分割，兼容多个平台
        $sql = str_replace(["\r\n", "\r"], "\n", $sql);
        $sql = explode("\n", trim($sql));

        // 循环处理每一行
        foreach ($sql as $key => $line) {
            // 跳过空行
            if ($line == '') {
                continue;
            }

            // 跳过以#或者--开头的单行注释
            if (preg_match("/^(#|--)/", $line)) {
                continue;
            }

            // 跳过以/**/包裹起来的单行注释
            if (preg_match("/^\/\*(.*?)\*\//", $line)) {
                continue;
            }

            // 多行注释开始
            if (substr($line, 0, 2) == '/*') {
                $comment = true;
                continue;
            }

            // 多行注释结束
            if (substr($line, -2) == '*/') {
                $comment = false;
                continue;
            }

            // 多行注释没有结束，继续跳过
            if ($comment) {
                continue;
            }

            // 替换表前缀
            if ($from != '') {
                $line = str_replace('`'.$from, '`'.$to, $line);
            }
            if ($line == 'BEGIN;' || $line =='COMMIT;') {
                continue;
            }
            // sql语句
            array_push($pure_sql, $line);
        }

        // 只返回一条语句
        if ($limit == 1) {
            return implode($pure_sql, "");
        }

        // 以数组形式返回sql语句
        $pure_sql = implode($pure_sql, "\n");
        $pure_sql = explode(";\n", $pure_sql);
        return $pure_sql;
    } else {
        return $limit == 1 ? '' : [];
    }
}

function mac_long2ip($ip){
    $ip = long2ip($ip);
    $reg2 = '~(\d+)\.(\d+)\.(\d+)\.(\d+)~';
    return preg_replace($reg2, "$1.$2.*.*", $ip);
}

function mac_default($s,$def='')
{
    if(empty($s)){
        return $def;
    }
    return $s;
}

function mac_num_fill($num)
{
    if($num<10){
        $num = '0' . $num;
    }
    return $num;
}

function mac_multisort($arr,$col_sort,$sort_order,$col_status='',$status_val='')
{
    $sort=[];
    foreach($arr as $k=>$v){
        $sort[] = $v[$col_sort];
        if($col_status!='' && $v[$col_status] != $status_val){
            unset($arr[$k]);
        }
    }
    array_multisort($sort, SORT_DESC, SORT_FLAG_CASE, $arr);
    return $arr;
}

function mac_get_body($text,$start,$end)
{
    if(empty($text)){ return false; }
    if(empty($start)){ return false; }
    if(empty($end)){ return false; }

    $start=stripslashes($start);
    $end=stripslashes($end);

    if(strpos($text,$start)!=""){
        $str = substr($text,strpos($text,$start)+strlen($start));
        $str = substr($str,0,strpos($str,$end));
    }
    else{
        $str='';
    }
    return $str;
}

function mac_find_array($text,$start,$end)
{
    $start=stripslashes($start);
    $end=stripslashes($end);
    if(empty($text)){ return false; }
    if(empty($start)){ return false; }
    if(empty($end)){ return false; }

    $start = str_replace(["(",")","'","?"],["\(","\)","\'","\?"],$start);
    $end = str_replace(["(",")","'","?"],["\(","\)","\'","\?"],$end);

    $labelRule = $start."(.*?)".$end;
    $labelRule = mac_buildregx($labelRule,"is");
    preg_match_all($labelRule,$text,$tmparr);
    $tmparrlen=count($tmparr[1]);
    $rc=false;
    $str='';
    $arr=[];
    for($i=0;$i<$tmparrlen;$i++) {
        if($rc){ $str .= "{array}"; }
        $str .= $tmparr[1][$i];
        $rc=true;
    }

    if(empty($str)) { return false ;}
    $str=str_replace($start,"",$str);
    $str=str_replace($end,"",$str);
    //$str=str_replace("\"\"","",$str);
    //$str=str_replace("'","",$str);
    //$str=str_replace(" ","",$str);
    if(empty($str)) { return false ;}
    return $str;
}

function mac_url_check($url,$baseurl)
{
    $urlinfo = parse_url($baseurl);
    $baseurl = $urlinfo['scheme'].'://'.$urlinfo['host'].(substr($urlinfo['path'], -1, 1) === '/' ? substr($urlinfo['path'], 0, -1) : str_replace('\\', '/', dirname($urlinfo['path']))).'/';
    if(strpos($url, '://') === false) {
        if($url[0]=='/'){
            $url = $urlinfo['scheme'].'://'.$urlinfo['host'].$url;
        }
        elseif(substr($url,0,2)=='./'){
            $url = substr($url,1);
        }
        else{
            $url = $baseurl.$url;
        }
    }
    return $url;
}

function mac_content_type($u)
{
    $dir = pathinfo($u);
    $ext = $dir['extension'];
    $types = array(
        'js'=>'text/javascript',
        'css'=>'text/css',
        'txt'=>'text/plain',
        'xml'=>'text/xml',
        'json'=>'application/json',
        'gif'=>'image/gif',
        'jpeg'=>'image/jpeg',
        'jpg'=>'image/jpeg',
        'jpe'=>'image/jpeg',
        'png'=>'image/png',

        'pdf'=>'application/pdf',

    );
    return $types[$ext] ? $types[$ext] : 'text/html';
}
