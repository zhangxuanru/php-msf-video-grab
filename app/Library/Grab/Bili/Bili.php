<?php 
/**
 * Bili 视频下载助手类
 *
 * Created by PhpStorm.
 * User: zxr
 * Date: 2017/11/28
 * Time: 14:36
 */

namespace App\Library\Grab\Bili;

use PG\MSF\Client\Http\Client;

use App\Library\Grab\Video;

/// 
use App\Models\Logic\VideoInfoLogic;
use App\Models\Logic\VideoImagesLogic;
use App\Models\Logic\CateGoryLogic;
use App\Models\Logic\VideoExtendLogic;
use App\Models\Logic\GrabTagsLogic;
use App\Models\Logic\VideoTagsLogic;





class Bili extends Video
{
    public $season_id = 0;

    const VIDEOTYPE = '2';

    const VIDEOPREFIX = 'bili'; 

    public function __construct($grab_address = '')
    {
         $this->grab_address = $grab_address;
         $this->type = self::VIDEOTYPE;
         $this->Prefix = self::VIDEOPREFIX;

    }

    /**
     * 获取视频的默认下载格式
     * @param $arr
     */
    public function getVideoQuality()
    {
        $videoInfo = yield $this->getVideoUrlByJson();
        if(empty($videoInfo) || !isset($videoInfo['streams']) ||  empty($videoInfo['streams'])){
            throw  new \Exception($this->grab_address.'--JSON信息获取失败');
        }
        $streams = $videoInfo['streams'];
        $this->streams = $streams;
        $this->quality = array_shift($streams);
        return  $this->quality;
    }

    /**
     * [getVideoIdByUrl 获取视频VID]
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
     public function getVideoIdByUrl($url = null )
     {
        if(empty($url)){
            $url = $this->grab_address;
        }
        preg_match('/https:\/\/www.bilibili.com\/video\/av(\d+)/',$url,$arr);
        if (!empty($arr) && !empty($arr[1])){
              $vid = $arr[1];
        }else{
            $urlPath = parse_url($url,PHP_URL_PATH);
            $urlPath = trim($urlPath,'/');
            $vid = str_replace('video/av','',$urlPath);
        }
       $this->video_id = $vid;
       return $vid;
  }

    /**
     * [getVideoInfo 获取视频具体信息]
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    public function getVideoInfo($url = null )
    {
        if(empty($url)){
             $url = $this->grab_address;
        }
        $interfaceUrl = null;
        if(!empty($this->season_id)){
            $interfaceUrl = sprintf("https://api.imjad.cn/bilibili/v2/?get=playurl&season_id=%s", $this->season_id);
        }
        if(empty($this->video_id) && empty($interfaceUrl) ){
              $vid = $this->getVideoIdByUrl($url);
              $interfaceUrl = sprintf("https://api.imjad.cn/bilibili/v2/?get=playurl&aid=%s", $vid);
        }
        if(!empty($this->video_id) && empty($interfaceUrl)){
             $interfaceUrl = sprintf("https://api.imjad.cn/bilibili/v2/?get=playurl&aid=%s", $this->video_id);
        }
        $client = $this->getObject(Client::class);
        $result = yield $client->goSingleGet($interfaceUrl);
        if($result['statusCode'] == '200' && !empty($result['body']) && $result['errCode'] == '0'){
            $body = json_decode($result['body'],true);
            $videoInfo = isset($body['data']) ? $body['data'] : [];
        }
        if(!isset($videoInfo) || empty($videoInfo)){
            throw new  \Exception('videoInfo 为空,不能下载视频--API:'.$interfaceUrl);
        }
        $this->videoInfo = $videoInfo;
        return $videoInfo;
    }

    /**
     *  执行下载， 在视频数据表逻辑处理之前的一些基础处理
     */
    public  function runBeforeDownloading()
    {
        //直接执行下载
        try{
            yield $this->getVideoQuality();
           $downloadInfo =  yield $this->downloadVideo();
        }catch(\Exception $e){
            $msg = "ID:".$this->infoData['id']."--".$e->getMessage();
            throw  new \Exception($msg);
        }
        if($downloadInfo['ret'] == false){
            $msg = "ID:".$this->infoData['id']."--".$downloadInfo['msg'];
            throw  new \Exception($msg);
        }
        //上传七牛
        $result =  $this->getFileHelper()->uploadQiniu($this->filePath,$this->fileName);
        if(empty($result['key'])){
            echo "ID:--";
            var_dump($result);
            $msg =   "ID:--".$this->infoData['id'].'--'.$this->filePath.'上传文件到七牛失败';
            $this->getFileHelper()->rmFile($this->filePath);
            throw  new \Exception($msg);
        }else{
            try{
                $hls = $this->getFileHelper()->modifyFileByHls($this->fileName);
                $this->hls_id = $hls['id'];
                if(isset($hls['key'])){
                    $this->hls_key = $hls['key'];
                }
                //$this->hls_key = $this->getFileHelper()->modifyFileByHls($this->fileName);
            }catch(\Exception $e){
                throw  new \Exception($e->getMessage());
            }
        }
    }

    /**
     * 执行下载逻辑处理
     * @return bool
     */
    public  function runDownload()
    {
        try{
            $info = yield $this->getVideoInfo();
        }catch(\Exception $e){
           throw  new \Exception($e->getMessage());
        }
        $vid  = yield $this->runHandleVideo();
        $thumbnails = $info['pic'];
        yield $this->runHandleImages($thumbnails,$vid);
        $this->getFileHelper()->rmFile($this->filePath);
        return $vid;
    }

    /**
     * 单个视频下载
     */
    public function runSingleVideo()
    {
        $vid = 0;
        try{
            $exec_id = yield $this->addVideoCountInfo();
            $this->exec_id = $exec_id;
            $vid = $this->getVideoIdByUrl();
            $is_existence = yield $this->checkRepData();
            if($is_existence){
                yield $this->getVideoInfo();
                yield $this->updateDownload();
            }else{
                yield  $this->runBeforeDownloading();
                $vid = yield $this->runDownload();
                yield $this->logRecord($vid,[],true);
            }
        }catch(\Exception $e){
            $log['content'] = $e->getMessage();
            yield $this->logRecord($vid,$log,false);
            yield $this->updateBaseData(false);
            yield $this->updateVideoCountInfo();
            throw  new \Exception($e->getMessage());
        }
        yield $this->updateBaseData();
        yield $this->updateVideoCountInfo();
        $data['success_number'] = $this->success_number;
        $data['fail_number'] = $this->fail_number;
        return $data;
    }

    /**
     * 页面视频下载，执行的方法
     */
    public function runPageVideoDownload()
    {
        $vid = 0;
        try{
            $exec_id = yield $this->addVideoCountInfo(); 
            $this->exec_id = $exec_id;
            $list = yield $this->getPageVideoList();
            if($list['code'] == '-1' || empty($list['data'])){
                throw new \Exception($list['msg'].'--data 为空,请检查PYTHON');
            }
           $data = $list['data'];
           $idType = $data['idType'];
           foreach($data['ids'] as $key => $val){
               if($key >= $this->infoData['grab_number'] && !empty($this->infoData['grab_number'])){
                     break;
               }
               switch($idType){
                   case 'sid':
                        $this->season_id = $val;
                        $info = yield $this->getVideoInfo();
                        if(is_object($info)){
                            $info = get_object_vars($info);
                        }
                        $aid  = isset($info['aid']) ? $info['aid']:0;
                        break;
                    case 'aid':
                        $aid = $val;
                        break;
               }
               if(empty($aid)){
                   throw new \Exception('$aid 为空 season_id:'.$this->season_id.'--val:--'.$val);
               }
               $grab_address = sprintf("https://www.bilibili.com/video/av%s/",$aid);
               $this->grab_address = $grab_address;
               $this->video_id = $aid;              
               try{
                   $is_existence = yield $this->checkRepData();
                   if($is_existence){
                         yield $this->getVideoInfo();
                         yield $this->updateDownload(); 
                   }else{
                       yield  $this->runBeforeDownloading();
                       $vid = yield $this->runDownload();
                       yield $this->logRecord($vid,[],true);
                   }
               }catch(\Exception $e){
                   $log['content'] = $e->getMessage();
                   yield $this->logRecord($vid,$log,false);
                   continue;
               }
               usleep(100);
            }
          yield $this->updateBaseData();
          yield $this->updateVideoCountInfo();
        }catch(\Exception $e){
             $log['content'] = $e->getMessage();
             yield $this->logRecord($vid,$log,false);
             yield $this->updateBaseData(false);
             yield $this->updateVideoCountInfo();
             throw  new \Exception($e->getMessage());
        }
        $data['success_number'] = $this->success_number;
        $data['fail_number'] = $this->fail_number;
        return $data;
    }

    /**
     * 页面抓取，获取页面视频数组列表
     * @return array
     */
    public function getPageVideoList()
    {
        $grab_number = $this->infoData['grab_number'];
        $return = ['code' => -1,'msg' => '','data' => [] ];
        try{  
          $pyUrl = sprintf('http://127.0.0.1:5000/?total=%s&url=%s',$grab_number,base64_encode($this->grab_address));
          $client = $this->getObject(Client::class);
          $result = yield $client->goSingleGet($pyUrl);
          $body = '';
          if($result['statusCode'] == '200' && !empty($result['body']) && $result['errCode'] == '0'){
              $body = isset($result['body']) ? $result['body'] : '' ;
              $body = str_replace(array('[',']'),'',$body);
          }
          $arr = explode(',',$body);
          # exec('python '.APP_DIR.'/Console/Bili.py '."'".$this->grab_address."'".' '.$grab_number,$arr);
          if(empty($arr) || empty($body)){
              $msg = "ID:--".$this->infoData['id'].'--python程序出错，抓取为空--'.var_dump($result);
              throw new \Exception($msg);
             }
        }catch(\Exception $e){
            $msg = "ID:--".$this->infoData['id'].'--'.$e->getMessage();
            $return['msg'] = $msg;
            return  $return;
        }
        $data = array();
        foreach($arr as $key => $val){
                list($idType,$id) = explode('_',$val);
                if(!isset($data['idType'])){
                    $data['idType'] = trim($idType,'"');
                }
                $data['ids'][] = trim($id,'"');
        }
        $this->pageVideoInfo = $data;
        $this->grab_number = !empty($this->infoData['grab_number']) ? $this->infoData['grab_number'] : count($data['ids']);
        $return['code'] = '1';
        $return['data'] = $data;
        return $return;
    }


    /**
     * 修改本地视频相关表数据
     * @param $info
     * @return bool|\Generator
     * @throws \Exception
     */
    public function runHandleVideo()
    {
        $info = $this->videoInfo;
        $quality = $this->quality ;
        $tag = array_column($info['tag'],'tag_name');
        $keywords =  implode(',',$tag);
        $db = array(
            'info_id' => $this->infoData['id'],
            'av_id'   => $this->video_id,
            'title'   => $info['title'],
            'type'    => $this->type,
            'keywords' => $keywords,
            'description'  => $info['desc'],
            'qiniu_upload' => 1,
            'addDate' => time()
        );
         //扩展表
        $extend = array(
            'filename' => $this->filePath,
            'hls_key'  => $this->hls_key,
            'hls_id'   => $this->hls_id,
            'view_count' => $info['stat']['view'],
            'author' => $info['owner']['name'],
            'published_at'   => $info['pubdate'],
            'like_number'    => $info['stat']['like'],
            'reviews_number' => $info['stat']['reply'],
            'length_seconds' => 0,
            'video_size' => isset($quality['size']) ? $quality['size'] : 0
        );
        //写入分类表
        $catData = array(
            'pid' => $this->infoData['category'],
            'category_name' => $info['tname'],
            'type' => $this->type,
            'categoryId' => $info['tid'],
            'cat_crcid'  => crc32($info['tname']),
            'addDate'    => time()
        );
        $cateModel = $this->getObject(CateGoryLogic::class);
        $db['category'] =  yield $cateModel->judInsertion($catData);
        //写入视频表
        $videoModel = $this->getObject(VideoInfoLogic::class);
        $ret = yield $videoModel->saveData($db);
        if($ret == 0 || $ret == false){
            throw new \Exception('写入video_info表失败'.var_export($db,true));
        }
        $video_id = $ret;
        //写入视频扩展表
        $extend['video_id'] = $video_id;
        $extendModel = $this->getObject(VideoExtendLogic::class);
        yield $extendModel->saveData($extend);

        //写入tags表
        $tagsModel = $this->getObject(GrabTagsLogic::class);
        if(!empty($info['tag'])){
            $batchTagIds =  yield $tagsModel->batchInsert($info['tag']);
        }
        //写入video_tags表
        if(isset($batchTagIds) && !empty($batchTagIds)){
            $tagsVideoModel = $this->getObject(VideoTagsLogic::class);
            yield $tagsVideoModel->batchInsert($video_id,$batchTagIds);
        }
        return $video_id;
    }




    /**
     * 下载图片并上传到七牛，并处理本地数据库逻辑
     * @param $thumbnails
     */
    public  function runHandleImages($picUrl,$vid)
    {
        // 上传图片
         $imgObj = $this->getObject(VideoImagesLogic::class);
         $imgInfo = yield $this->downloadImages($picUrl);
          if (empty($imgInfo['filePath'])) {
              throw new \Exception("图片下载失败--".$picUrl);
           }
            $result = $this->getFileHelper()->uploadQiniu($imgInfo['filePath'], $imgInfo['fileName'], 'images');
            $qiniu_upload = 0;
            $imgSize = [];
            if (!empty($result['hash']) && !empty($result['key'])) {
                $imgSize  = getimagesize($imgInfo['filePath']);
                $this->getFileHelper()->rmFile($imgInfo['filePath']);
                $qiniu_upload = 1;
            }
            $imgDb = [
                'video_id' => $vid,
                'img_source_url' => $picUrl,
                'fillename' => $imgInfo['fileName'],
                'width'  => isset($imgSize[0]) ? $imgSize[0] : 0,
                'height' => isset($imgSize[1]) ? $imgSize[1] : 0,
                'qiniu_upload' => $qiniu_upload,
                'is_cover' => 1
            ];
            $ret = yield $imgObj->saveData($imgDb);
            return $ret;
    } 

}