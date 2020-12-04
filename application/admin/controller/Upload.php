<?php
namespace app\admin\controller;

class Upload extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $param = input();
        $this->assign('path',$param['path']);
        $this->assign('id',$param['id']);

        $this->assign('title',lang('upload_pic'));
        return $this->fetch('admin@upload/index');
    }

    public function test()
    {
        $temp_file = tempnam(sys_get_temp_dir(), 'Tux');
        if($temp_file){
            echo lang('admin/upload/test_write_ok').'：' . $temp_file;
        }
        else{
            echo lang('admin/upload/test_write_err').'：' . sys_get_temp_dir() ;
        }
    }

    public function upload()
    {
        
		$param = input();
        $param['from'] = empty($param['from']) ? '' : $param['from'];
        $param['input'] = empty($param['input']) ? 'file' : $param['input'];
        $param['flag'] = empty($param['flag']) ? 'vod' : $param['flag'];

        $config = config('maccms.site');
        $pre= $config['install_dir'];

        if(!empty($param['from'])){
            $cp = 'app\\common\\extend\\editor\\' . ucfirst($param['from']);
            if (class_exists($cp)) {
                $c = new $cp;
                $c->front($param);
            }
            else{
                return self::upload_return(lang('admin/upload/not_find_extend'), '');
            }
        }
        else{
            $pre='';
        }

        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file($param['input']);

        $data = [];
        if (empty($file)) {
            return self::upload_return(lang('admin/upload/no_input_file'), $param['from']);
        }
        if ($file->getMime() == 'text/x-php') {
            return self::upload_return(lang('admin/upload/forbidden_ext'), $param['from']);
        }

        $upload_image_ext = 'jpg,jpeg,png,gif';
        $upload_file_ext = 'doc,docx,xls,xlsx,ppt,pptx,pdf,wps,txt,rar,zip,torrent';
        $upload_media_ext = 'rm,rmvb,avi,mkv,mp4,mp3';
        $sys_max_filesize = ini_get('upload_max_filesize');
        $config = config('maccms.upload');

        // 格式、大小校验
        if ($file->checkExt($upload_image_ext)) {
            $type = 'image';
        }
        elseif ($file->checkExt($upload_file_ext)) {
            $type = 'file';
        }
        elseif ($file->checkExt($upload_media_ext)) {
            $type = 'media';
        }
        else {
            return self::upload_return(lang('admin/upload/forbidden_ext'), $param['from']);
        }

        // 上传附件路径
        $_upload_path = ROOT_PATH . 'upload' . '/' . $param['flag'] . '/' ;
        // 附件访问路径
        $_save_path = 'upload'. '/' . $param['flag'] . '/';
        $ymd = date('Ymd');

        $n_dir = $ymd;
        for($i=1;$i<=100;$i++){
            $n_dir = $ymd .'-'.$i;
            $path1 = $_upload_path . $n_dir. '/';
            if(file_exists($path1)){
                $farr = glob($path1.'*.*');
                if($farr){
                    $fcount = count($farr);
                    if($fcount>999){
                        continue;
                    }
                    else{
                        break;
                    }
                }
                else{
                    break;
                }
            }
            else{
                break;
            }
        }

        $savename = $n_dir . '/' . md5(microtime(true));
        $upfile = $file->move($_upload_path,$savename);

        if (!is_file($_upload_path.$upfile->getSaveName())) {
            return self::upload_return(lang('admin/upload/upload_faild'), $param['from']);
        }

        //附件访问地址
        //$_file_path = $_save_path.$upfile->getSaveName();

        $file_count = 1;
        $file_size = round($upfile->getInfo('size')/1024, 2);
        $data = [
            'file'  => $_save_path.str_replace('\\', '/', $upfile->getSaveName()),
            'type'  => $type,
            'size'  => $file_size,
            'flag' => $param['flag'],
            'ctime' => request()->time()
        ];
		unset($upfile);

        return self::upload_return(lang('admin/upload/upload_success'), $param['from'], 1, $data);
    }


    private function upload_return($info='',$from='',$status=0,$data=[])
    {
        $arr = [];
        $arr['msg'] = $info;
        $arr['code'] = $status;
        $arr['data'] = $data;
        return $arr;
    }

}
