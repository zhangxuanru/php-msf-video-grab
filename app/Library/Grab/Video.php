<?php

namespace App\Library\Grab;

use PG\MSF\Base\Core;
use App\Models\Logic\VideoLogLogic;
use PG\MSF\Client\Http\Client;
use App\Library\Helper\File;

class  Video extends Core{

    //要下载的视频地址
     public $grab_address = null;

    //实际视频地址,由于B站视频详情页有跳转，所以这里需要存上实际的视频地址
    public $actualDownloadAddress =  null;

    //视频的所有JSON信息
    public $videoJsonInfo = [];

    /**
     * 记录视频的所有信息，记在日志表中，方便查看
     * @var array
     */
    public $videoInfo = [];

    /**
     * 保存重复数据信息
     * @var array
     */
    public $repData = [];

    /**
     * 保存的grab_information里的数据，
     * @var
     */
    public $infoData;

    //当前下载的视频格式
    public $quality = [];

    //可下载的视频格式
    public $streams = [];

    public $pageVideoInfo = [];

    //完整的保存视频路径
    public  $filePath;

    //临时保存视频文件名
    public  $fileName;

    //文件名前缀
    public $Prefix='';

    //抓取总数
    public $grab_number = 0;

    //抓取成功数
    public $success_number = 0;

    //抓取失败数
    public $fail_number = 0;

    // 视频类型，1：youtube,2:bilibili
    public $type = '';

    /**
     * 当前的执行ID
     * @var int
     */
    public $exec_id = 0;


    /**解析URL得出的视频ID
     * @var int
     */
    public $video_id = 0;

    /**
     * 上传到七牛的视频文件对应的HLS名
     * @var string
     */
    public $hls_key = '';

    /**
     * 上传到七牛的视频文件对应的HLS ID
     * @var string
     */
    public $hls_id = '';

    /**
     * [$rep_number 重复数]
     * @var integer
     */
    public $rep_number = 0; 

    //临时存放视频和图片的目录
    const VIDEOTMPDIR = "/data/video/download/";

    //执行成功的状态
    const EXEC_SUCCESS_STATUS = 2;

    //执行失败的状态
    const EXEC_FAILURE_STATUS = 3;

    /**
     * 检查是否要去验证302跳转，目前只有B站需要验证
     */
    public function checkDownloadUrl()
    {
        switch($this->type) {
            case '2':
                yield $this->checkAddressJump();
                break;
            case '1':
                $this->actualDownloadAddress = $this->grab_address;
                break;
        }
    }

    /**
     * 检查视频地址是否有302跳转，B站的URL都需要检查
     */
    public function checkAddressJump()
    {
        $grab_address = $this->grab_address;
        if(empty($grab_address)){
             return false;
        }
        $actualDownloadAddress = null;
        $client = $this->getObject(Client::class);
        $result = yield $client->goSingleGet($grab_address);
        if($result['errCode'] == '0' && $result['statusCode'] == '302'){
            $actualDownloadAddress = isset($result['headers']['location']) ? $result['headers']['location'] : '';
        }
        if(!empty($actualDownloadAddress)){
             $this->actualDownloadAddress = $actualDownloadAddress;
        }
        if(empty($actualDownloadAddress) || empty($this->actualDownloadAddress)){
            $this->actualDownloadAddress = $this->grab_address;
        }
    }



    /**
     * 获取实际视频的下载地址
     * @return null
     */
    public function getDownloadUrl()
    {
        $actualDownloadAddress = $this->actualDownloadAddress ;
        if(!empty($actualDownloadAddress)){
            $grab_address = $actualDownloadAddress;
        }else{
            $grab_address = $this->grab_address;
        }
        return $grab_address;
    }



    /**
     * 获取URL JSON数据  仅用于单个视频
     * @param $url
     * @return mixed
     */
    public function getVideoUrlByJson()
    {
        if(empty($this->grab_address)){
               return [];
        }
        yield $this->checkDownloadUrl();
        $grab_address = $this->getDownloadUrl();
        exec("you-get --json  '{$grab_address}' ",$info);
        if(!empty($info)){
            $jsonStr = implode('',$info);
            $info = json_decode($jsonStr,true);
        }
        $this->videoJsonInfo = $info;
        return $info;
    }
 

    /**
     * 执行下载视频操作
     * @return array
     */
    public function downloadVideo()
    {
        $quality = $this->quality;
        if(empty($quality)){
            return  ['ret'=>false,'msg' => '$quality为空'];
        }
        $this->setLocale();
        $fileInfo =  $this->getFileHelper()->generatingVideoFileName($quality['container']);
        $filePath = $fileInfo['filePath'];
        $fileName = $fileInfo['fileName'];
        $grab_address = $this->getDownloadUrl();
        exec("you-get -o ". self::VIDEOTMPDIR." -O $fileName '{$grab_address}' ",$info); //--no-caption
        if(!file_exists($filePath)){
            sleep(1);
            exec("you-get  -o ". self::VIDEOTMPDIR." -O $fileName '{$grab_address}' ",$info); //--no-caption
        }
        if(!file_exists($filePath)){
            sleep(2);
            exec("you-get  -o ". self::VIDEOTMPDIR." -O $fileName '{$grab_address}' ",$info); //--no-caption
        }
        if(!file_exists($filePath)){
            $cmd = "you-get  -o ". self::VIDEOTMPDIR." -O $fileName '{$grab_address}' "; //--no-caption
            return  ['ret'=>false,'msg' => $grab_address.'--下载失败'.'--'.$cmd.'--filepath--'.$filePath.'--'.var_export($info,true)];
        }
        $this->filePath = $filePath;
        $this->fileName = $fileInfo['preFileName'];
        return  ['ret'=>true,'msg' => 'success'];
    }


    /**
     * 下载图片操作
     * @param $url
     */
    public  function downloadImages($url)
    {
        $client = $this->getObject(Client::class);
        $result = yield $client->goSingleGet($url);
        $data = ['fileName' => '','filePath' => ''];
        if(empty($result) || empty($result['body'])){
            return $data;
        }
        $extension  =  $this->getFileHelper()->getImageExtension($url);
        $fileInfo   =  $this->getFileHelper()->generatingImageFileName($extension);
        $fileName = $fileInfo['fileName'];
        $filePath = $fileInfo['filePath'];
        $i = 1;
        while($i <=3 && !file_exists($filePath)){
            file_put_contents($filePath,$result['body']);
            $i++;
            usleep(10);
        }
        if(!file_exists($filePath)){
            return $data;
        }
        $data['fileName'] = $fileName;
        $data['filePath'] = $filePath;
        return $data;
    }


    /**
     * 记日志的方法
     * @param $vid
     * @param array $data
     * @param bool $ret
     * @return bool
     */
    public function logRecord($vid,$data=[],$ret=true)
    {
        $grab_address = $this->getDownloadUrl(); 
        $grabLogModel = $this->getObject(VideoLogLogic::class);
        $logData = [
            'info_id' => $this->infoData['id'],
            'video_id' => 0,
            'status'  => 1,
            'exec_time' => time()
        ];
        if(!empty($this->pageVideoInfo)){
             $this->videoInfo['pageVideoInfo'] = $this->pageVideoInfo;
        }
        $logData['exec_id'] = $this->exec_id;
        if($ret){
            $logData['grab_address'] = $grab_address;
            $logData['video_id'] = $vid;
            $logData['content'] = json_encode($this->videoInfo);
            $logData['download_info'] = json_encode($this->quality,JSON_UNESCAPED_UNICODE);
            $logData['streams_info']  = json_encode($this->streams,JSON_UNESCAPED_UNICODE);
            $this->success_number++;
        }else{
            $logData['grab_address'] = $grab_address;
            $logData['status'] = 0;
            $logData['content'] = '';
            $this->fail_number++;
        }
        if(!empty($data)){
            $logData = array_merge($logData,$data);
        }
        //写入LOG表
        $ret = yield $grabLogModel->saveData($logData);
        return  $ret;
    }



    /**
     * 修改基础表和视频统计表数据
     * @param $id
     * @return \Generator
     */
    public function updateBaseData($status = true)
    {
        if($status) {
            $status = self::EXEC_SUCCESS_STATUS;
        }else{
            $status = self::EXEC_FAILURE_STATUS;
        }
        //修改抓取配置表基础数据
        $db['implement_date'] = time();
        $db['status'] =  $status;
        $db['success_number'] = $this->success_number + $this->infoData['success_number'];
        $db['fail_number']    = $this->fail_number + $this->infoData['fail_number'];
        yield $this->getDbPool()->updateGranInfoData($this->infoData['id'],$db);
    }


    /**
     * 修改grab_video_count表
     * @return mixed
     */
    public function updateVideoCountInfo()
    { 
        $rep_number = $this->rep_number; 
        $countArr = array(
            'info_id' => $this->infoData['id'],
            'total' => $this->grab_number,
            'success_number' => $this->success_number,
            'fail_number' => $this->fail_number,
            'rep_number' => $rep_number,
            'date' => time()
        );
        $ret = yield $this->getDbPool()->updateGrabVideoCountById($this->exec_id,$countArr);
        return $ret;
    }


    /**
     * 判断是否已经下载过
     */
    public function checkRepData()
    {
        $video  = yield $this->getDbPool()->getVideoInfoByAvid($this->video_id);
        if(empty($video)){
            return false;
        }
        $this->repData = $video;
        return true;
    }


    /**
     * 判断新下载的数据与老数据是否相同。 相同则不更新， 不相同则更新video_info
     * @return bool
     */
    public  function checkInfoRepData()
    {
        $video  =  $this->repData;
        if(empty($video)){
            return false;
        }
        $info  = $this->videoInfo;
        if(empty($info)){
            return false;
        }
        if($info['title'] != $video['title']){
             return true;
        }
        if(isset($info['length_seconds']) && isset($video['length_seconds']) && $info['length_seconds'] != $video['length_seconds'] ){
            return true;
        }
        return false;
    }


   /**
    * 更新video_info 和 video_count 表
    * [UpdateDownload description]
    */
    public  function updateDownload()
    {
        $ret = $this->checkInfoRepData();
        $video  =  $this->repData;
        $info   =  $this->videoInfo;
        switch($this->type){
            case '1':
                $db = array(
                    'title' => isset($info['title']) ? $info['title'] : '',
                    'keywords' => isset($info['keywords']) ? $info['keywords'] : '',
                    'description' => $info['videoapi']['description'],
                );
                break;
            case '2':
                $tag = array_column($info['tag'],'tag_name');
                $keywords =  implode(',',$tag);
                $db = array(
                    'title' => $info['title'],
                    'keywords' => $keywords,
                    'description' => $info['desc']
                );
                break;
        }
        if($ret == true){
            yield  $this->getDbPool()->updateById($video['id'],$db);
        }
        //写日志表,记录重复ID
        $grabLogModel = $this->getObject(VideoLogLogic::class);
        $logList = yield $grabLogModel->getLogDataByVideoId($video['id']);
        $logData = [
            'info_id' => $this->infoData['id'],
            'video_id' => $video['id'],
            'status'  => 2,
            'exec_time' => time(),
            'exec_id'  => $this->exec_id,
            'grab_address' => $logList['grab_address'],
            'content'  => $logList['content'],
            'download_info' => $logList['download_info'],
            'streams_info'  => $logList['streams_info']
        ];
        yield $grabLogModel->saveData($logData);
        $this->rep_number++;
    }



   /**
    * [addVideoCountInfo 写入grab_video_count表]
    */
    public function addVideoCountInfo()
    {
         $countArr = array(
            'info_id' => $this->infoData['id'], 
            'date' => time()
        );
        $ret = yield $this->getDbPool()->addGrabVideoCount($countArr); 
        return $ret; 
     }


    /**
     * 获取文件助手
     * @return mixed|\stdClass
     */
    public function getFileHelper()
    {
       $obj = $this->getObject(File::class);
       $obj->Prefix = $this->Prefix;
       return $obj;
    }


    /**
     * 获取DBPOOL对象
     * @return mixed|\stdClass
     */
    public function getDbPool()
    {
        $obj = $this->getObject(DbPool::class);
        return $obj;
    }

    /**
     *  初始化命令行环境
     */
    public function setLocale()
    {
        setlocale(LC_ALL,  'en_US.utf8');
        setlocale(LC_CTYPE, 'en_US.utf8');
    }



}
