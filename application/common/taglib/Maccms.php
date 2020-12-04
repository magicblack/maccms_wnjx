<?php
namespace app\common\taglib;
use think\template\TagLib;
use think\Db;

class Maccms extends Taglib {

	protected $tags = [
        'foreach' => ['attr'=>'name,id,key'],
        'for' => ['attr'=>'start,end,comparison,step,name'],
    ];

    public function tagFor($tag,$content)
    {
        if(empty($tag['start'])){
            $tag['start'] = 1;
        }
        if(empty($tag['end'])){
            $tag['end'] = 5;
        }
        if(empty($tag['comparison'])){
            $tag['comparison'] = 'elt';
        }
        if(empty($tag['step'])){
            $tag['step'] = 1;
        }
        if(empty($tag['name'])){
            $tag['name'] = 'i';
        }

        $parse='';
        $parse .= '{for start="'.$tag['start'].'" end="'.$tag['end'].'" comparison="'.$tag['comparison'].'" step="'.$tag['step'].'" name="'.$tag['name'].'"}';
        $parse .= $content;
        $parse .= '{/for}';

        return $parse;
    }

    public function tagForeach($tag,$content)
    {
        if(empty($tag['id'])){
            $tag['id'] = 'vo';
        }
        if(empty($tag['key'])){
            $tag['key'] = 'key';
        }
        $parse='';
        $parse .= '{foreach name="'.$tag['name'].'" id="'.$tag['id'].'" key="'.$tag['key'].'"}';
        $parse .= $content;
        $parse .= '{/foreach}';

        return $parse;
    }

}
