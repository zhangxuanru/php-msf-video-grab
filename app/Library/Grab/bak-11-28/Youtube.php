<?php 
/**
 * YOUTUBE 视频下载助手类
 *
 * Created by PhpStorm.
 * User: zxr
 * Date: 2017/11/17
 * Time: 14:36
 */

namespace App\Library\Grab;

use PG\MSF\Base\Exception;
use PG\MSF\Client\Http\Client;
use App\Models\GrabTags;
use App\Models\GrabVideoTags;
use App\Models\GrabVideoInfo;
use App\Models\GrabImages;
use App\Models\Cate;

class  Youtube extends Video
{
    /**
     * 保存的grab_information里的数据，
     * @var
     */
    public $infoData;
    /**
     * 文件名前缀
     */
    private $Prefix = 'you';

    //要下载的视频格式
    public $quality = [];

    //可下载的视频格式
    public $streams = [];

    /**
     * 记录视频的所有信息，记在日志表中，方便查看
     * @var array
     */
    public $videoInfo = [];

     //完整的保存视频路径
     public $filePath;

     //临时保存视频文件名
     public $fileName;

     public $video_id;

    //临时存放视频和图片的目录
    const VIDEOTMPDIR = "/data/video/download/";

    //页面抓取，一次最多可抓取的条数
    const  MAXRESULTS = 50;

    private $apiUrl = 'https://www.googleapis.com/youtube/v3/';

    private $key = null;

    public function __construct($grab_address = '')
    {
         $this->grab_address = $grab_address;
         $this->key = $this->getConfig()->get('constant.YOUTUBE_API_KEY');
    }

    /**
     * 获取视频的默认下载格式
     * @param $arr
     */
    public function getVideoQuality($url=null)
    {
        $videoInfo = $this->getVideoUrlByJson($url);
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
        return  $this->quality;
    }

    /**
     * 执行下载视频操作
     * @return array
     */
    public function downloadVideo($url=null)
    {
        try{
            $quality =  $this->getVideoQuality($url);
        }catch(\Exception $e){
            return  ['ret'=>false,'msg' => $e->getMessage()];
        }
        if(empty($quality)){
            return  ['ret'=>false,'msg' => '$quality为空'];
        }
        $this->setLocale();
        $fileName = $this->Prefix.time().mt_rand(0,1000);
        $filePath = self::VIDEOTMPDIR.$fileName.'.'.$quality['container'];
        $itag = $quality['itag'];
        exec("you-get --no-caption  --itag=".$itag." -o ". self::VIDEOTMPDIR." -O $fileName '{$this->grab_address}' ",$info);

        if(!file_exists($filePath)){
          sleep(1);
          exec("you-get --no-caption  --itag=".$itag." -o ". self::VIDEOTMPDIR." -O $fileName '{$this->grab_address}' ",$info);
        }

        if(!file_exists($filePath)){
            $cmd = "you-get --no-caption  --itag=".$itag." -o ". self::VIDEOTMPDIR." -O $fileName '{$this->grab_address}' ";
            return  ['ret'=>false,'msg' => $this->grab_address.'--下载失败'.'--'.$cmd.'--'.var_export($info,true)];
        }
        $this->filePath = $filePath;
        $this->fileName = $fileName;
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
       $video_id = $this->video_id;
       $mic = strrchr(microtime(true),'.');
       $mic = str_replace('.','_', $mic);
       $info = parse_url($url,PHP_URL_PATH);
       $infoExplode = explode('/',$info);
       $fileName = $this->Prefix.'_'.$video_id.$mic.'_'.array_pop($infoExplode);
       $filePath = self::VIDEOTMPDIR.$fileName;
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
     * 删除目录文件
     * @param string $filePath
     */
    public  function rmFile($filePath='')
    {
        if(empty($filePath)){
            $filePath = $this->filePath;
        }
        if(file_exists($filePath)){
            exec("rm -rf ".$filePath);
            return true;
        }else{
            return false;
        }
    }

    /**
     *  初始化命令行环境
     */
    public function setLocale()
    {
        setlocale(LC_ALL,  'en_US.utf8');
        setlocale(LC_CTYPE, 'en_US.utf8');
    }

    /**
     * 设置属性值
     * @param $key
     * @param $val
     */
    public function setAttr($key,$val)
    {
        $this->$key = $val;
    }

    public function __set($name, $value)
    {
        // TODO: Implement __set() method.
        $this->$name = $value;
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
        $vid  = yield $this->runHandleVideo($info);
        $thumbnails = $info['player_response']['videoDetails']['thumbnail']['thumbnails'];
        yield $this->runHandleImages($thumbnails,$vid);
        $this->rmFile();
        return $vid;
    }

    /**
     *  执行下载， 在视频数据表逻辑处理之前的一些基础处理
     */
    public  function runBeforeDownloading()
    {
        //直接执行下载
        $downloadInfo =  $this->downloadVideo();
        if($downloadInfo['ret'] == false){
            $msg = "ID:".$this->infoData['id']."--".$downloadInfo['msg'];
            throw  new \Exception($msg);
        }
        //上传七牛
        $result =  $this->uploadQiniu($this->filePath,$this->fileName);
        if(empty($result['key'])){
            $msg =   "ID:--".$this->infoData['id'].'--'.$this->filePath.'上传文件到七牛失败';
            $this->rmFile();
            throw  new \Exception($msg);
        }
    }


    /**
     * 页面视频下载时，先初始执行的方法
     */
    public function runBeforeSinglePageDownloading()
    {
        $list = yield $this->getPageVideoList();
        return $list;
    }

    /**
     * 页面抓取，获取页面视频数组列表
     * @return array
     */
    public function getPageVideoList()
    {
        $channelId = $this->infoData['channelId'];
        $grab_number = $this->infoData['grab_number'];
        $params = ['channelId' => $channelId,
                   'order'     => 'date'
                  ];
        if ($grab_number > 0 && $grab_number <= self::MAXRESULTS) {
            $params['maxResults'] = $grab_number;
        }
        if($grab_number > self::MAXRESULTS ){
            $params['maxResults'] = self::MAXRESULTS;
        }
        $data = yield $this->call('search', $params, 'id');//'snippet,id'
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
            'title' => $info['title'],
            'filename' => $this->filePath,
            'type' => 1,
            'view_count' => $info['view_count'],
            'author' => $info['author'],
            'keywords' => $info['keywords'],
            'description' => $info['videoapi']['description'],
            'channelId' => $info['videoapi']['channelId'],
            'channelTitle' => $info['videoapi']['channelTitle'],
            'publishedAt'  => $info['videoapi']['publishedAt'],
            'length_seconds' => $info['length_seconds'],
            'qiniu_upload' => 1,
            'addDate' => time()
        );
        //写入分类表
        $catData = array(
            'pid' => $this->infoData['category'],
            'category_name' => $info['videoapi']['categories']['title'],
            'channelId' =>  $info['videoapi']['categories']['channelId'],
            'youtube_categoryId' => $info['videoapi']['categoryId'],
            'cat_crcid' => crc32($info['videoapi']['categories']['title']),
            'addDate' => time()
        );
        $cateModel = $this->getObject(Cate::class);
        $db['category'] =  yield $cateModel->JudInsertion($catData);
        //写入视频表
        $videoModel = $this->getObject(GrabVideoInfo::class);
        $ret = yield $videoModel->insert($db);
        if($ret['result'] == false){
            throw new \Exception('写入video_info表失败'.var_export($db,true));
        }
        //写入tags表
        $tagsModel = $this->getObject(GrabTags::class);
        if(!empty($info['videoapi']['tags'])){
            $batchTagIds =  yield $tagsModel->batchInsert($info['videoapi']['tags']);
            unset($info['videoapi']['tags']);
        }
        //写入video_tags表
        if(isset($batchTagIds) && !empty($batchTagIds)){
            $tagsVideoModel = $this->getObject(GrabVideoTags::class);
            yield $tagsVideoModel->batchInsert($ret['insert_id'],$batchTagIds);
        }
        return $ret['insert_id'];
    }

    /**
     * 下载图片并上传到七牛，并处理本地数据库逻辑
     * @param $thumbnails
     */
    public  function runHandleImages($thumbnails,$vid)
    {
        // 上传图片
        $imgObj = $this->getObject(GrabImages::class);
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
            $result = $this->uploadQiniu($imgInfo['filePath'], $imgInfo['fileName'], 'images');
            $qiniu_upload = 0;
            if (!empty($result['hash']) && !empty($result['key'])) {
                $this->rmFile($imgInfo['filePath']);
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
            $ret = yield $imgObj->insert($imgDb);
        }
       return true;
    }

}