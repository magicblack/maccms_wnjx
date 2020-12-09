<?php
namespace app\admin\controller;

class Rep extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $list = config('rep');
        $this->assign('list',$list);
        $this->assign('title',lang('admin/rep/title'));
        return $this->fetch('admin@rep/index');
    }

    public function info()
    {
        $param = input();
        $list = config('rep');
        if (Request()->isPost()) {
            $validate = \think\Loader::validate('Token');
            if(!$validate->check($param)){
                return $this->error($validate->getError());
            }
            $key = $param['key'];
            unset($param['__token__'],$param['key']);
            $param['time'] = time();

            $param['domain_list'] = str_replace(chr(13),'',trim($param['domain_list']));
            $param['domain_arr']  = explode(chr(10), $param['domain_list']);

            $param['rule_all'] = str_replace(chr(13),'',$param['rule_all']);
            $rows = explode(chr(10), $param['rule_all']);
            $arr=[];
            foreach ($rows as $r) {
                if (!empty($r)){
                    $a = explode('[to]', $r);
                    $arr[$a[0]] = $a[1];
                }
            }
            $param['rule_all_arr'] = $arr;

            $param['rule_domain'] = str_replace(chr(13),'',$param['rule_domain']);
            $rows = explode(chr(10), $param['rule_domain']);
            $arr=[];
            $domain='';
            foreach ($rows as $r) {
                if (!empty($r)){
                    $a = explode('[to]', $r);
                    if(count($a)==1){
                        $domain = trim($a[0]);
                        continue;
                    }
                    $arr[$domain][$a[0]] = $a[1];
                }
            }
            $param['rule_domain_arr'] = $arr;

            if(empty($key)){
                $list[] = $param;
            }
            else{
                $list[$key] = $param;
            }

            $res = mac_arr2file( APP_PATH .'extra/rep.php', $list);
            if($res===false){
                return $this->error(lang('write_err_config'));
            }

            return $this->success(lang('save_ok'));
        }
        $info = $list[$param['id']];
        $this->assign('param',$param);
        $this->assign('info',$info);
        $this->assign('title',lang('admin/rep/title'));
        return $this->fetch('admin@rep/info');
    }

    public function del()
    {
        $param = input();
        $list = config('rep');
        unset($list[$param['ids']]);
        $res = mac_arr2file(APP_PATH. 'extra/rep.php', $list);
        if($res===false){
            return $this->error(lang('del_err'));
        }

        return $this->success(lang('del_ok'));
    }

    public function field()
    {
        $param = input();
        $ids = $param['ids'];
        $col = $param['col'];
        $val = $param['val'];

        if(!empty($ids) && in_array($col,['status'])){
            $list = config('rep');
            $ids = explode(',',$ids);
            foreach($list as $k=>&$v){
                if(in_array($k,$ids)){
                    $v[$col] = $val;
                }
            }
            $res = mac_arr2file(APP_PATH. 'extra/rep.php', $list);
            if($res===false){
                return $this->error(lang('save_err'));
            }
            return $this->success(lang('save_ok'));
        }
        return $this->error(lang('param_err'));
    }

    public function export()
    {
        $param = input();
        $list = config('rep');
        $info = $list[$param['id']];

        header("Content-type: application/octet-stream");
        if(strpos($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
            header("Content-Disposition: attachment; filename=macwnjx_" . urlencode($info['name']) . '.txt');
        }
        else{
            header("Content-Disposition: attachment; filename=macwnjx_" . $info['name'] . '.txt');
        }
        echo base64_encode(json_encode($info));
    }

    public function import()
    {
        $file = $this->request->file('file');
        $info = $file->rule('uniqid')->validate(['size' => 10240000, 'ext' => 'txt']);
        if ($info) {
            $data = json_decode(base64_decode(file_get_contents($info->getpathName())), true);
            @unlink($info->getpathName());
            if($data){

                if(!isset($data['status']) || empty($data['name']) || empty($data['rule_all']) || empty($data['tourl']) || empty($data['charset']) || empty($data['rule_all_arr'])   ){
                    return $this->error(lang('format_err'));
                }
                $data['time'] = 0;

                $list = config('rep');
                $list[] = $data;
                $res = mac_arr2file( APP_PATH .'extra/rep.php', $list);
                if($res===false){
                    return $this->error(lang('write_err_config'));
                }


            }
            return $this->success(lang('import_err'));
        }
        else{
            return $this->error($file->getError());
        }
    }
}
