<?php
/**
 * 抓取模块
 * Created by PhpStorm.
 * User: zxr
 * Date: 2017/12/22
 * Time: 16:40
 */

namespace App\Models\Model;

class InfoModel extends  BaseModel
{
    public static $tableName = 'grab_information';

    public function __construct()
    {
        parent::__construct();
        parent::$tableName = self::$tableName;
    }


    /**
     * [batchDelByIdList 批量删除]
     * @param  array  $idList [description]
     * @return [type]         [description]
     */
    public function batchDelByIdList($idList = array())
    {
      if(empty($idList)){
            return false;
        } 
       $where = [
           'id' => ['symbol' => 'in','value' => $idList]
                ];
       $setData = ['status' => 0];
       $ret =  yield $this->update($setData,$where);
       if(isset($ret['result']) && $ret['result'] == true){
           return true;
       }
       return false; 
    }



//
//    /**
//     * 根据ID获取INFO数据
//     * @param $id
//     * @return array
//     */
//    public  function  getInfoById($id)
//    {
//        $where = [
//            'id' => ['symbol' => '=','value' => $id]
//        ];
//        $rows = yield $this->fetchAll('id,type,status,video_type,grab_address,category,grab_title',$where);
//        return isset($rows[0]) ? $rows[0] : [];
//    }
//
//    /**
//     * 根据ID删除记录
//     * @param $id
//     * @return bool
//     */
//    public  function deleteById($id)
//    {
//        $where = [
//            'id'=>['symbol' => '=','value' => $id]
//        ];
//        $setData = ['status' => 0];
//        $ret =  yield $this->update($setData,$where);
//        if(isset($ret['result']) && $ret['result'] == true){
//            return true;
//        }
//        return false;
//    }
//
//    /**
//     * 批量删除
//     * @param $idStr
//     * @return bool
//     */
//    public function batchDelByIdArr($idArr)
//    {
//        $where = [
//            'id'=>['symbol' => 'in','value' => $idArr]
//        ];
//        $setData = ['status' => 0];
//        $ret =  yield $this->update($setData,$where);
//        if(isset($ret['result']) && $ret['result'] == true){
//            return true;
//        }
//        return false;
//    }
//
//
//    /**
//     * 查询列表
//     * @param bool $count
//     * @param string $fields
//     * @param int $offset
//     * @param int $limit
//     * @param string $order
//     * @param string $sort
//     * @param array $params
//     * @return mixed
//     */
//    public  function getList($count=false,$fields='*',$offset=0,$limit=100,$order='id',$sort='DESC',$params = [])
//    {
//        $where = [
//            'status'=>['symbol' => '!=','value' => 0]
//        ];
//        if(!empty($params)){
//            $where = array_merge($where,$params);
//        }
//        $data = yield $this->getLists($count,$fields,$offset,$limit,$order,$sort,$where);
//        return  $data;
//    }
//
//    /**
//     * 获取列表总数
//     * @param array $params
//     * @return int
//     */
//    public function getCount($params = array())
//    {
//        $where = [
//            'status'=>['symbol' => '!=','value' => 0]
//        ];
//        if(!empty($params)){
//            $where = array_merge($where,$params);
//        }
//        $count = yield $this->getCounts($where);
//        return $count;
//    }
//
//
//    /**
//     * 投放任务到task
//     * @param $taskInfo
//     */
//    public function launchTask($taskInfo)
//    {
//        $grabTask = $this->getObject(GrabTask::class);
//        $grabTask->setTimeout(40001);
//        $taskId   =  yield $grabTask->execTask($taskInfo);
//        return $taskId;
//    }
//
//    // 通过Task，同步执行抓取
//    public function syncMySQLPoolTask($tableName,$field='*',$where=null)
//    {
//        $dbTask = $this->getObject(DbTask::class);
//        $data     = yield $dbTask->syncMySQLPool($tableName,$field,$where);
//        return $data;
//    }


}


