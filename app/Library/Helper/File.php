<?php
namespace App\Library\Helper;

use App\Library\Qiniu\Upload;

/**
 * 文件操作助手
 * Class File
 * @package App\Library\Grab\Helper
 */

class  File{

    //文件名前缀
    public $Prefix = '';

    //临时存放视频和图片的目录
    const VIDEOTMPDIR = "/data/video/download/";

    public $logPath = "/data/video/download/log/";

    /**
     * 生成保存在本地的视频文件名
     */
    public function generatingVideoFileName($container=null)
    {
        $fileName  = $this->Prefix.time().mt_rand(0,1000);
        $preFileName = $fileName.'.'.$container;
        $filePath  = self::VIDEOTMPDIR.$preFileName;
        return ['filePath' => $filePath,'fileName' => $fileName,'container' => $container,'preFileName' => $preFileName];
    }

    /**
     * 生成保存在本地的图片文件名
     * @return array
     */
    public function generatingImageFileName($container=null)
    {
        $mic = strrchr(microtime(true),'.');
        $mic = str_replace('.','_', $mic);
        $fileName = $this->Prefix.$mic.'_'.$container;
        $filePath = self::VIDEOTMPDIR.$fileName;
        return ['filePath' => $filePath,'fileName' => $fileName,'container' => $container];
    }

    /**
     * 获取图片URL后缀
     * @param $url
     * @return mixed|string
     */
    public function getImageExtension($url)
    {
        if(empty($url)){
            return '';
        }
        $info = parse_url($url,PHP_URL_PATH);
        $infoExplode = explode('/',$info);
        return array_pop($infoExplode);
    }

    /**
     * 删除目录文件
     * @param string $filePath
     */
    public  function rmFile($filePath='')
    {
        if(empty($filePath)){
            return false;
        }
        if(file_exists($filePath)){
            exec("rm -rf ".$filePath);
            return true;
        }else{
            return false;
        }
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


    /**
     * 视频文件更新到HLS格式
     * @param $fileName
     * @return bool
     */
    public function modifyFileByHls($fileName){
        $qiniu = new Upload();
        $ret = $qiniu->modifyFileByHls($fileName);
        return $ret;
    }

    /**
     * 记录日志
     * @param string $content
     */
  public function writeLog($content='')
  {
      $logFile = $this->logPath.'grab_'.date('Ymd').".log";
      file_put_contents($logFile,$content,FILE_APPEND);
  }



}
