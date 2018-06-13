<?php
/**
 * Base
 *
 * @author strive965432@gmail.com
 * @copyright Chengdu zxr Technology Co.,Ltd.
 */ 

namespace App\Console;

use PG\MSF\Console\Controller;
use App\Library\Grab\Youtube\Youtube;
use App\Library\Grab\Bili\Bili;

use App\Models\Logic\GrabLogic;
use App\Models\Logic\VideoExtendLogic;

class Base extends Controller
{
    //执行失败的状态
    const EXEC_FAILURE_STATUS = 3;

    const EXEC_SUCCESS_STATUS = 2;

    const EXEC_RUN_STATUS   = 4;

    const VIDEO_YOUTUBE_TYPE = 1;

    const VIDEO_BILIBILI_TYPE = 2;

   public function __construct($controllerName, $methodName)
   {
        parent::__construct($controllerName,$methodName);
        date_default_timezone_set("Asia/Shanghai");
   }

    /**
     * 根据ID查询基础数据
     * @param $id
     * @return \Generator
     * @throws \Exception
     */
    public function getBasicData($id)
    {
        if(empty($id)){
            throw new \Exception('ID为空');
        }
        $grabModel = $this->getGrabModelInstance(); 
        $data = yield $grabModel->getInfoDataById($id);
        if(empty($data)){
            throw new \Exception("ID:--".$id."--没有此条记录");
        }
        if($data['status'] == self::EXEC_SUCCESS_STATUS && $data['type'] == '1'){
            throw new \Exception("ID:--".$id."--已执行成功，不能重复执行");
        }
        if($data['status'] == self::EXEC_RUN_STATUS){
            throw new \Exception("ID:--".$id."--正在执行中，不能重复执行");
        }
        return $data;
    }

    /**
     * 获取下载基类
     * @param $data
     * @return mixed|\stdClass
     */
    public function getBasicModel($data)
    {
        switch ($data['video_type']) {
            case self::VIDEO_YOUTUBE_TYPE:
                $downloadObj =  $this->getObject(Youtube::class);
                break;
            case self::VIDEO_BILIBILI_TYPE:
                $downloadObj =  $this->getObject(Bili::class);
                break;
        }
        $downloadObj->infoData = $data;
        $downloadObj->grab_address = $data['grab_address'];
        return $downloadObj;
    } 


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
     * 实例化视频扩展表模块
     * @return mixed|\stdClass
     */
  public function getVideoExtendInstance()
  {
      $model = $this->getObject(VideoExtendLogic::class);
      return $model;
  }

    /**
     * [updateGranInfoStatus 修改表状态]
     * @param  [type] $id     [description]
     * @param  [type] $status [description]
     * @return [type]         [description]
     */
    public function updateGranInfoStatus($id,$status)
    {
       $data = ['status' => $status];
       $ret = yield $this->getGrabModelInstance()->updateById($id,$data);
       return $ret; 
    }

    /**
     * 销毁,解除引用
     */
    public function destroy()
    {
        parent::destroy();
    }

}

