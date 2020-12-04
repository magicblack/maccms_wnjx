<?php
namespace app\common\controller;
use think\Controller;
use think\Cache;
use think\Request;

class All extends Controller
{
    var $_ref;
    var $_cl;
    var $_ac;
    var $_tsp;
    var $_url;

    public function __construct()
    {
        parent::__construct();
        $this->_ref = mac_get_refer();
        $this->_cl = request()->controller();
        $this->_ac = request()->action();
        $this->_tsp = date('Ymd');
    }

    protected function load_page_cache($tpl,$type='html')
    {
        if(defined('ENTRANCE') && ENTRANCE == 'index' && $GLOBALS['config']['app']['cache_page'] ==1  && $GLOBALS['config']['app']['cache_time_page'] ) {
            $cach_name = $_SERVER['HTTP_HOST']. '_'. MAC_MOB . '_'. $GLOBALS['config']['app']['cache_flag']. '_' .$tpl .'_'. http_build_query(mac_param_url());
            $res = Cache::get($cach_name);
            if ($res) {
                if($type=='json'){
                    $res = json_encode($res);
                }
                echo $res;
                die;
            }
        }
    }

}