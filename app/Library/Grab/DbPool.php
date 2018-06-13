<?php
namespace App\Library\Grab;

use PG\MSF\Base\Core;
// use App\Models\Grab as GrabModel;
// use App\Models\VideoCount;
// use App\Models\Grablog;
// use App\Models\VideoInfo;
// use App\Models\VideoExtend; 

////////
use App\Models\Logic\VideoCountLogic;
use App\Models\Logic\GrabLogic;
use App\Models\Logic\VideoLogLogic;
use App\Models\Logic\VideoInfoLogic;
use App\Models\Logic\VideoExtendLogic;
use App\Models\Logic\VideoPlayListLogic;




/**
 * 数据库操作
 * Class DbPool
 * @package App\Library\Grab
 */
class  DbPool extends Core{

    /**
     * 实例化抓取模块
     * @return mixed
     */
    protected  function getGrabModelInstance()
    {
        $grabModel = $this->getObject(GrabLogic::class);
        return $grabModel;
    }

    /**
     * 实例化日志模块
     * @return mixed|\stdClass
     */
    public  function getGrabLogModel()
    {
        $grabLogModel = $this->getObject(VideoLogLogic::class);
        return $grabLogModel;
    }


    public function getGrabVideoModel()
    {
        $videoModel = $this->getObject(VideoInfoLogic::class);
        return $videoModel;
    }


    /**
     * 视频扩展表
     * @return mixed|\stdClass
     */
    public function getVideoExtendModel()
    {
        $videoModel = $this->getObject(VideoExtendLogic::class);
        return $videoModel;
    }


    /**
     * 根据AV_ID 查询视频信息， 看看是否已经下载过
     * @param $av_id
     * @return \Generator
     */
    public function getVideoInfoByAvid($av_id)
    {
        $row =  yield $this->getGrabVideoModel()->getVideoDataByAvid($av_id,'',false);
        return $row;
    }


    /**
     * 修改grab_video_info表
     * @param $id
     * @param $setData
     * @return mixed
     */
    public function updateById($id,$setData)
    {
        $ret = yield $this->getGrabVideoModel()->updateById($id,$setData);
        return $ret;
    }
 

    /**
     * 修改grab_video_extend表
     * @param $id
     * @param $setData
     * @return mixed
     */
    public function updateExtendByVideoId($id,$setData)
    {
        $ret = yield $this->getVideoExtendModel()->updateExtendByVideoId($id,$setData);
        return $ret;
    }
  
    /**
     * 获取之前抓取的pageVideoInfo
     * @param $info_id
     * @return array|mixed
     */
    public function getLogVideoInfo($info_id)
    { 
        $row = yield $this->getGrabLogModel()->getLogDataByInfoId($info_id);
        if(empty($row)){
            return [];
        }
        $content = json_decode($row['content'],true);
        $pageVideoInfo = isset($content['pageVideoInfo']) ? $content['pageVideoInfo'] : [];
        return $pageVideoInfo;
    }
 
    /**
     * 写入抓取计数表
     * @param $Data
     * @return mixed
     */
    public  function addGrabVideoCount($Data)
    {
        $grabModel = $this->getObject(VideoCountLogic::class);
        $ret = yield $grabModel->saveData($Data);
        return $ret;
    }


    /**
     * 写入播放列表 表
     * @param $Data
     * @return mixed
     */
    public function addPlaylist($Data)
    {
        $grabModel = $this->getObject(VideoPlayListLogic::class);
        //如果存在播放列表ID，就不再添加了
        $isExists = yield $grabModel->checkIdExists($Data['playlistId']);
        if(empty($isExists)){
          $ret = yield $grabModel->saveData($Data);
          return $ret;
        }
    }



    /**
     * [updateGrabVideoCount 修改统计表]
     * @param  [type] $id   [description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function updateGrabVideoCountById($id,$data)
    {
        $grabModel = $this->getObject(VideoCountLogic::class);        
        $ret = yield $grabModel->updateById($id,$data);
        return $ret;
    }



/**
     * [updateGrabVideoCountByExecId 修改统计表]
     * @param  [type] $id   [description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function updateGrabVideoCountByExecId($exec_id,$data)
    {
        $grabModel = $this->getObject(VideoCountLogic::class); 
        $ret = yield $grabModel->updateByexecId($exec_id,$data);
        return $ret;
    }


 

    /**
     * [updateGrabVideoCountByInfoId 修改统计表]
     * @param  [type] $id   [description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function updateGrabVideoCountByInfoId($info_id,$data)
    {
        $grabModel = $this->getObject(VideoCountLogic::class); 
        $ret = yield $grabModel->updateByInfoId($info_id,$data);
        return $ret;
    }
 
    /**
     * 根据ID修改grab_information表数据
     * @param $id
     * @param $data
     */
    public function updateGranInfoData($id,$data)
    {
        $grabModel = $this->getGrabModelInstance(); 
        try{
           $ret  = yield $grabModel->updateById($id,$data);  //updateData
        }catch(\Exception $e){
          $ret = false;
        }
        return $ret;
    }

}
