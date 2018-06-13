<?php
namespace App\Library\Grab\Helper;

use PG\MSF\Base\Core;
use App\Models\Grablog;
use App\Models\Grab as GrabModel;
use App\Models\GrabVideoCount;

class  DbPool extends Core{

    //执行成功的状态
    const EXEC_SUCCESS_STATUS = 2;

    //执行失败的状态
    const EXEC_FAILURE_STATUS = 3;

    public $success_number;

    public $fail_number;

    public $info_id;

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
        yield $this->updateGranInfoData($this->info_id,$db);
    }

    /**
     * 修改grab_video_count表
     * @return mixed
     */
    public function updateVideoCountInfo()
    {
        $countArr = array(
            'info_id' => $this->info_id,
            'total' => $this->grab_number,
            'success_number' => $this->success_number,
            'fail_number' => $this->fail_number,
            'date' => time()
        );
        $ret = yield $this->updateGrabVideoCount($this->exec_id,$countArr);
        return $ret;
    }

    /**
     * [updateGrabVideoCount 修改统计表]
     * @param  [type] $id   [description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function updateGrabVideoCount($id,$data)
    {
        $grabModel = $this->getObject(GrabVideoCount::class);
        $where = [
            'id'=>['symbol' => '=','value' => $id]
        ];
        $ret = yield $grabModel->update($data,$where);
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
        $where = [
            'id'=>['symbol' => '=','value' => $id]
        ];
        $ret = yield $grabModel->update($data,$where);
        return $ret;
    }

    /**
     * [addVideoCountInfo 写入grab_video_count表]
     */
    public function addVideoCountInfo()
    {
        $countArr = array(
            'info_id' => $this->info_id,
            'date' => time()
        );
        $ret = yield $this->addGrabVideoCount($countArr);
        if($ret['result']){
            return $ret['insert_id'];
        }
        return 0;
    }


    /**
     * 实例化抓取模块
     * @return mixed
     */
    protected  function getGrabModelInstance()
    {
        $grabModel = $this->getObject(GrabModel::class);
        return $grabModel;
    }

    /**
     * 写入抓取计数表
     * @param $Data
     * @return mixed
     */
    public  function addGrabVideoCount($Data)
    {
        $grabModel = $this->getObject(GrabVideoCount::class);
        $ret = yield $grabModel->insert($Data);
        return $ret;
    }

}
