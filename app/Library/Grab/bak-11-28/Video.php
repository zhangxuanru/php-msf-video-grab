<?php
namespace App\Library\Grab;

use PG\MSF\Base\Core;
use App\Library\Qiniu\Upload;

class  Video_test extends Core{

    //要下载的视频地址
     public $grab_address = null;

    //视频的所有JSON信息
    public $videoJsonInfo = [];


    /**
     * 获取URL JSON数据  仅用于单个视频
     * @param $url
     * @return mixed
     */
    public function getVideoUrlByJson($grab_address=null)
    {
        if(empty($grab_address)){
            $grab_address = $this->grab_address;
        }
        if(empty($grab_address)){
             return [];
        }

        echo $grab_address."\r\n";

        exec("you-get --json  '{$grab_address}' ",$info);
        if(!empty($info)){
            $jsonStr = implode('',$info);
            $info = json_decode($jsonStr,true);
        }
        $this->videoJsonInfo = $info;
        return $info;
    }





    /**
     * 上传文件到七牛
     */
    public  function  uploadQiniu($filePath,$fileName,$type='')
    {
        $qiniu = new Upload();
        $ret = $qiniu->uploadFile($filePath,$fileName,$type);
        return $ret;
    }

}
