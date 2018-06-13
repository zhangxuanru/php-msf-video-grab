<?php 
/**
 * YOUTUBE 视频下载助手类
 *
 * Created by PhpStorm.
 * User: zxr
 * Date: 2017/11/17
 * Time: 14:36
 */

namespace App\Library\Grab\Youtube;


use PG\MSF\Base\Exception;
use PG\MSF\Client\Http\Client;

//////////
use App\Library\Grab\Video;
use App\Models\Logic\VideoInfoLogic;
use App\Models\Logic\VideoImagesLogic;
use App\Models\Logic\CateGoryLogic;
use App\Models\Logic\VideoExtendLogic;
use App\Models\Logic\GrabTagsLogic;
use App\Models\Logic\VideoTagsLogic;


class  Youtube extends Video
{
    //页面抓取，一次最多可抓取的条数
    const  MAXRESULTS = 50;

    const  VIDEOTYPE = '1';

    const  VIDEOPREFIX = 'you';

    private $apiUrl = 'https://www.googleapis.com/youtube/v3/';

    private $key = null;

    public $repData = [];

    public $playlistId = 0;

    public $videoSize = 0;



    public function __construct($grab_address = '')
    {
         $this->grab_address = $grab_address;
         $this->key = $this->getConfig()->get('constant.YOUTUBE_API_KEY');
         $this->type = self::VIDEOTYPE;
         $this->Prefix = self::VIDEOPREFIX;

        $content = "grab_address:{$this->grab_address}--start -- \r\n";
        $this->getFileHelper()->writeLog($content);
    }

    /**
     * 获取视频的默认下载格式
     * @param $arr
     */
    public function getVideoQuality($url=null)
    {
        $videoInfo = yield $this->getVideoUrlByJson($url);
        if(empty($videoInfo) || !isset($videoInfo['streams']) ||  empty($videoInfo['streams'])){
            throw  new \Exception($this->grab_address.'--JSON信息获取失败');
        }
        $streams = $videoInfo['streams'];
        foreach ($streams as $key => $value) {
            $quality[] = $value['quality'];
            $mime[] = $value['mime'];
        }
        array_multisort($quality, SORT_NATURAL , $mime, SORT_STRING, $streams);
        $this->streams = $streams;
        $this->quality = array_shift($streams);

        $quaLog = var_export($this->quality,true);
        $content = "url:{$url}--quality:{$quaLog}  \r\n";
        $this->getFileHelper()->writeLog($content);

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
        $vid = '';
        if (preg_match('#youtu.be/([^/]+)#', $url, $result)) {
          $vid = $result[1];
        } elseif (preg_match('#youtube.com/embed/([^/]+)#', $url, $result)) {
          $vid = $result[1];
        } elseif (preg_match('#https?://www.youtube.com/watch\?v=#', $url)) {
          $urlArgs = parse_url($url, PHP_URL_QUERY);
          parse_str($urlArgs,$uInfo);
          $vid =isset($uInfo['v']) ? $uInfo['v'] : '0';
        }
       $this->video_id = $vid;

      $content = "url:{$url}--video_id:{$vid}  \r\n";
      $this->getFileHelper()->writeLog($content);
      return $vid;
  }
 
   /**
     * [getVideoInfo 获取youtube视频具体信息]
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
   public function getVideoInfo($url = null )
   {
    //https://www.youtube.com/get_video_info?video_id=s00ATQ_EwX0
    //https://www.youtube.com/watch?v=s00ATQ_EwX0
     if(empty($url)){
         $url = $this->grab_address;
      }   
     $vid = $this->getVideoIdByUrl($url);
     $interfaceUrl = sprintf("http://www.youtube.com/get_video_info?video_id=%s", $vid);
     $client = $this->getObject(Client::class);
     $result = yield $client->goSingleGet($interfaceUrl);
     if(empty($result) || $result['statusCode'] != '200' || empty($result['body'])){
         throw  new \Exception($url.'--result:'.json_encode($result,JSON_UNESCAPED_UNICODE));
     } 
     $content = $result['body']; 
     parse_str($content,$info);
     if(isset($info['player_response'])){
         $info['player_response'] = json_decode($info['player_response'],true);
       }
     $info['videoapi'] = yield $this->getVideoApiData($vid);
     $this->videoInfo = $info;

     $logInfo = var_export($info,true);
     $content = "url:{$url}--interfaceUrl:{$interfaceUrl}---videoInfo:{$logInfo}  \r\n";
     $this->getFileHelper()->writeLog($content);
     return $info; 
   }

    /**
     * 通过API，获取视频说明信息
     * @param $url
     * @return \DOMDocument
     */
  public  function getVideoApiData($video_id)
  {
      $info  = yield $this->call('videos',['id' => $video_id]);
      if(empty($info) || !isset($info['items'][0]) || empty($info['items'][0])){
          return [];
      }
      $items =  $info['items'][0];
      $data = array(
          'id' => $items['id'],
          'publishedAt' => $items['snippet']['publishedAt'],
          'channelId' => $items['snippet']['channelId'],
          'channelTitle' => $items['snippet']['channelTitle'],
          'tags' => isset($items['snippet']['tags']) ? $items['snippet']['tags'] : [] ,
          'categoryId' => $items['snippet']['categoryId'],
          'description' => $items['snippet']['localized']['description'],
          'categories' => yield $this->getVideoCategories($items['snippet']['categoryId'])
      );
      unset($info);

      $logData = var_export($data,true);
      $content = "video_id:{$video_id}--data:{$logData}  \r\n";
      $this->getFileHelper()->writeLog($content);
      return $data;
  }

    /**
     * 根据分类ID获取分类信息
     * @param $cat_id
     */
  public  function getVideoCategories($cat_id)
  {
      $data = yield $this->call('videoCategories',['id' => $cat_id]);
      if(empty($data)){
          return $data;
      }
      return isset($data['items']) ? $data['items'][0]['snippet']:[];
  }

    /**
     * 请求youtube API接口
     * @param $service
     * @param $part
     * @param $params
     * @return mixed
     */
    public function call($service, $params, $part='snippet')
    {
        $interfaceUrl = $this->buildUrl($service,$params, $part);
        file_put_contents('/data/video/download/qiniu/youtubeApi.log',$interfaceUrl."\r\n",FILE_APPEND);
        $client = $this->getObject(Client::class);
        $result = yield $client->goSingleGet($interfaceUrl);
        $info = !empty($result['body']) ? json_decode($result['body'],true) : [];
        return $info;
    }

    /**
     * 构造API URL
     * @param $service
     * @param $params
     * @param $part
     * @return string
     */
    public function buildUrl($service,$params,$part)
    {
        $apiUrl = $this->apiUrl.$service.'?part='.$part.'&'.http_build_query($params).'&key='.$this->key;
        return $apiUrl;
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
            if($is_existence) {
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
            if(empty($list)){
                throw  new \Exception($this->infoData['id'].'--'.$this->infoData['grab_address'].'--'.'获取视频列表信息错误');
            }
            $this->grab_number = $list['grab_number'];
            foreach($list['items'] as $key => $val){
                $vid = 0;
                if(isset($val['videoId'])){
                    $grab_address =  sprintf('https://www.youtube.com/watch?v=%s',$val['videoId']);
                    $this->video_id = $val['videoId'];
                    $this->playlistId = isset($val['playlistId']) ? $val['playlistId'] : '0';
                }else{
                    $grab_address =  sprintf('https://www.youtube.com/watch?v=%s',$val['id']['videoId']);
                    $this->video_id = $val['id']['videoId'];
                }
                $this->grab_address = $grab_address;
                if(!empty($this->playlistId)){
                    unset($val['videoId']);
                    yield $this->getDbPool()->addPlaylist($val);
                }
                try{
                    $is_existence = yield $this->checkRepData();
                    if($is_existence) {
                        yield $this->getVideoInfo();
                        yield $this->updateDownload();
                    }else{
                        try{
                            $info =  yield $this->getVideoInfo();
                            if(empty($info) || !is_array($info)){
                                throw  new \Exception($grab_address."--iNFO--ERROR:".var_export($info,true));
                            }
                        }catch(\Exception $e){
                             throw new \Exception($e->getMessage());
                        }
                        yield  $this->runBeforeDownloading();
                        $vid = yield $this->runDownload();
                        yield $this->logRecord($vid,[],true);
                    }
                }catch(\Exception $e){
                    $log['content'] = $e->getMessage();
                    yield $this->logRecord($vid,$log,false);
                    continue;
                }
                usleep(10);
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
            }catch(\Exception $e){
                throw  new \Exception($e->getMessage());
            }
            $this->getFileHelper()->rmFile($this->filePath);
        }
    }

    /**
     * 执行下载逻辑处理
     * @return bool
     */
    public  function runDownload()
    {
        echo "runDownload-------\r\n";
        try{
            $info = yield $this->getVideoInfo();
        }catch(\Exception $e){
            throw  new \Exception($e->getMessage());
        }
        try{
           $vid  = yield $this->runHandleVideo($info);
        }catch(\Exception $e){
            throw  new \Exception($e->getMessage());
        }
        $thumbnails = $info['player_response']['videoDetails']['thumbnail']['thumbnails'];
        yield $this->runHandleImages($thumbnails,$vid);
        $this->getFileHelper()->rmFile($this->filePath);
        return $vid;
    }

    /**
     * 根据播放列表ID获取视频ID
     * @param $playlistId
     * @return array
     */
    public function getVideoListByPlaylistId($playlistId)
    {
        $saveData = [];
        $grab_number = $this->infoData['grab_number'];
        $params = [
            'playlistId' => $playlistId
        ];
        if ($grab_number > 0 && $grab_number <= self::MAXRESULTS) {
            $params['maxResults'] = $grab_number;
        }
        if($grab_number > self::MAXRESULTS ){
            $params['maxResults'] = self::MAXRESULTS;
        }
        $data = yield $this->call('playlistItems', $params, 'snippet,id');//'snippet,id'
        if(empty($data) || empty($data['items'])){
              return $saveData;
        }
        $items = $data['items'];
        foreach($items as $k => $v){
            $published_at = $v['snippet']['publishedAt'];
            $dateObj  = new \DateTime($published_at);
            $published_at = strtotime($dateObj->format('Y-m-d H:i:s'));
            $saveData[] = [
                'playlist_title' => $v['snippet']['title'],
                'description'    => $v['snippet']['description'],
                'playlistId'     => $v['snippet']['playlistId'],
                'thumbnails'     => json_encode($v['snippet']['thumbnails']),
                'channelId'      => $v['snippet']['channelId'],
                'channelTitle'   => $v['snippet']['channelTitle'],
                'publishedAt'    => $published_at,
                'addDate'        => time(),
                'videoId'        => $v['snippet']['resourceId']['videoId']
            ];
        }
        if (empty($grab_number) && !empty($data)) {
            $grab_number = $data['pageInfo']['totalResults']; //总数
        }
        $data['grab_number'] = $grab_number;
        if ($grab_number > self::MAXRESULTS){
            $grab_number-= self::MAXRESULTS;
            $pageCount = ceil($grab_number / self::MAXRESULTS);
            $nextPageToken = $data['nextPageToken'] ;
            for ($i = 1; $i <= $pageCount && !empty($nextPageToken); $i++) {
                $params['pageToken'] = $data['nextPageToken'];
                if($grab_number <= self::MAXRESULTS){
                    $params['maxResults'] = $grab_number;
                    $grab_number = 0;
                }else{
                    $params['maxResults'] = self::MAXRESULTS;
                    $grab_number-= self::MAXRESULTS;
                }
                $rows = yield $this->call('playlistItems', $params, 'snippet,id');//'snippet,id'
                $nextPageToken = $rows['nextPageToken'];
                $items = $rows['items'];
                foreach($items as $k => $v){
                    $published_at = $v['snippet']['publishedAt'];
                    $dateObj  = new \DateTime($published_at);
                    $published_at = strtotime($dateObj->format('Y-m-d H:i:s'));
                    $saveData[] = [
                        'playlist_title' => $v['snippet']['title'],
                        'description'    => $v['snippet']['description'],
                        'playlistId'     => $v['snippet']['playlistId'],
                        'thumbnails'     => json_encode($v['snippet']['thumbnails']),
                        'channelId'      => $v['snippet']['channelId'],
                        'channelTitle'   => $v['snippet']['channelTitle'],
                        'publishedAt'    => $published_at,
                        'addDate'        => time(),
                        'videoId'        => $v['snippet']['resourceId']['videoId']
                    ];
                }
                usleep(5);
                if($grab_number <= 0){
                    break;
                }
            }
        }
        return $saveData;
    }


    /**
     * 根据渠道ID获取视频ID
     * @return mixed
     */
    public function getPageVideoList()
    {
        echo "getPageVideoList-------\r\n";
       $channelId = $this->infoData['channelId'];
       $grab_number = $this->infoData['grab_number'];
       $params = [
           'channelId' => $channelId,
           'order'     => 'date',
           'type'      => 'video'
       ];
       if ($grab_number > 0 && $grab_number <= self::MAXRESULTS) {
           $params['maxResults'] = $grab_number;
       }
       if($grab_number > self::MAXRESULTS ){
           $params['maxResults'] = self::MAXRESULTS;
       }
       $data = yield $this->call('search', $params, 'id');//'snippet,id'
       //走播放列表
       if(!empty($data) && empty($data['items'])){
            $params['type'] = 'playlist';
            $data = yield $this->call('search', $params, 'id');//'snippet,id'
            $playList = array('items'=> array());
            foreach($data['items'] as $key => $val){
               $items =  yield $this->getVideoListByPlaylistId($val['id']['playlistId']);
               $playList['items'] = array_merge($items,$playList['items']);
               if($grab_number > 0 && count( $playList['items']) > $grab_number ){
                   $playList['items'] = array_slice($playList['items'],0,$grab_number);
                   break;
                }
                if(empty($grab_number) && count( $playList['items']) > 30 ){
                    $playList['items'] = array_slice($playList['items'],0,30);
                    break;
                }
            }
           $playList['grab_number'] = count($playList['items']);
           return $playList;
       }
       if (empty($grab_number) && !empty($data)) {
           $grab_number = $data['pageInfo']['totalResults']; //总数
       }
       $data['grab_number'] = $grab_number;
       if ($grab_number > self::MAXRESULTS){
           $grab_number-= self::MAXRESULTS;
           $pageCount = ceil($grab_number / self::MAXRESULTS);
           $nextPageToken = $data['nextPageToken'] ;
           for ($i = 1; $i <= $pageCount && !empty($nextPageToken); $i++) {
               $params['pageToken'] = $data['nextPageToken'];
               if($grab_number <= self::MAXRESULTS){
                   $params['maxResults'] = $grab_number;
                   $grab_number = 0;
               }else{
                   $params['maxResults'] = self::MAXRESULTS;
                   $grab_number-= self::MAXRESULTS;
               }
               $rows = yield $this->call('search', $params, 'snippet,id');
               $nextPageToken = $rows['nextPageToken'];
               $data['items'] = array_merge($data['items'], $rows['items']);
               usleep(10);
               if($grab_number <= 0){
                   break;
               }
           }
       }
       return $data;
   }
    /////




    /**
     * 修改本地视频相关表数据
     * @param $info
     * @return bool|\Generator
     * @throws \Exception
     */
    public function runHandleVideo($info)
    {
        $db = array(
            'info_id' => $this->infoData['id'],
            'av_id'   => !empty($this->video_id) ? $this->video_id : $info['videoapi']['id'],
            'title' => $info['title'],
            'type' => self::VIDEOTYPE,
            'keywords' => $info['keywords'],
            'description' => $info['videoapi']['description'],
            'qiniu_upload' => 1,
            'addDate' => time()
        );
        //扩展表
        $published_at = $info['videoapi']['publishedAt'];
        $dateObj  = new \DateTime($published_at);
        $published_at = strtotime($dateObj->format('Y-m-d H:i:s'));
        if(!empty($this->filePath) && file_exists($this->filePath)){
             $this->videoSize = filesize($this->filePath);
        }
        $extend = array(
            'filename' => $this->filePath,
            'hls_key'  => $this->hls_key,
            'hls_id'   => $this->hls_id,
            'view_count' => $info['view_count'],
            'author' => $info['author'],
            'channel_id' => $info['videoapi']['channelId'],
            'playlistId' => $this->playlistId,
            'channel_title' => $info['videoapi']['channelTitle'],
            'published_at'  => $published_at,
            'length_seconds' => isset($info['length_seconds']) ? $info['length_seconds'] : 0,
            'video_size' => $this->videoSize
        );
        //写入分类表
        $catData = array(
            'pid' => $this->infoData['category'],
            'category_name' => $info['videoapi']['categories']['title'],
            'channelId' =>  $info['videoapi']['categories']['channelId'],
            'categoryId' => $info['videoapi']['categoryId'],
            'cat_crcid' => crc32($info['videoapi']['categories']['title']),
            'type' =>  self::VIDEOTYPE,
            'addDate' => time()
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
        if(!empty($info['videoapi']['tags'])){
            $batchTagIds =  yield $tagsModel->batchInsert($info['videoapi']['tags']);
            unset($info['videoapi']['tags']);
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
    public  function runHandleImages($thumbnails,$vid)
    {
        // 上传图片
        $imgObj = $this->getObject(VideoImagesLogic::class);
        foreach ($thumbnails as $k => $val) {
            $info = parse_url($val['url']);
            $url = $val['url'];
            if (!isset($info['scheme']) || empty($info['scheme'])) {
                $url = "https:" . $val['url'];
            }
            $imgInfo = yield $this->downloadImages($url);
            if (empty($imgInfo['filePath'])) {
                continue;
            }
            $result =  $this->getFileHelper()->uploadQiniu($imgInfo['filePath'], $imgInfo['fileName'], 'images');
            $qiniu_upload = 0;
            if (!empty($result['hash']) && !empty($result['key'])) {
                $this->getFileHelper()->rmFile($imgInfo['filePath']);
                $qiniu_upload = 1;
            }
            $is_cover = 0;
            if ($val['width'] > 200 && $val['width'] < 300) {
                $is_cover = 1;
            }
            $imgDb = [
                'video_id' => $vid,
                'img_source_url' => $url,
                'fillename' => $imgInfo['fileName'],
                'width' => $val['width'],
                'height' => $val['height'],
                'qiniu_upload' => $qiniu_upload,
                'is_cover' => $is_cover
            ];
             yield $imgObj->saveData($imgDb);
        }
       return true;
    }

}