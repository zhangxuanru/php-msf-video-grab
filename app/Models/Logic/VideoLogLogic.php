<?php
/**
 * videoLOG   日志表逻辑层
 * Created by PhpStorm.
 * User: zxr
 * Date: 2017/12/22
 * Time: 14:21
 */
namespace App\Models\Logic;
use App\Models\Model\VideoLogModel;

class VideoLogLogic extends BaseLogic
{ 
    public function __construct()
    {
        parent::__construct();
        $this->objPool = $this->getObject(VideoLogModel::class);
    }

    /**
     * 获取日志表数据
     * @param array $search
     * @param array $pageInfo
     * @param string $field
     * @return array
     */
    public function getLogListData($search = array(),$pageInfo = array(),$field='')
    {
        if(empty($field)){
             $field = 'id,video_id,grab_address,info_id,content';
        }
        try{
            $where = $this->parseSearch($search);
            $condition = $this->parseCondition($pageInfo);
            $data =  yield $this->objPool->getList($field,$where,$condition);
        }catch(\Exception $e){
            return [];
        }
        return $data;
    }

    /**
     * 根据日志ID查询具体日志信息
     * @param $id
     * @param string $field
     * @return mixed
     */
    public function getLogDataById($id,$field='')
    {
        if(empty($field)){
            $field = 'id,video_id,content,grab_address,info_id';
        }
        $where = [
            'id' => ['symbol' => '=','value' => $id]
           ];
        $data =  yield $this->objPool->getList($field,$where);
        return isset($data[0]) ?  $data[0] : $data;
    }

    /**
     * 根据视频ID查询具体日志信息
     * @param $video_id
     * @param string $field
     * @return mixed
     */
    public function getLogDataByVideoId($video_id,$field='')
    {
        $where = [
            'video_id' => ['symbol' => '=','value' => $video_id]
        ];
        if(empty($field)){
            $field = 'id,video_id,content,grab_address,info_id,download_info,streams_info';
        }
        $data =  yield $this->objPool->getList($field,$where);
        return isset($data[0]) ?  $data[0] : $data;
    }

    /**
     * 根据info_ID查询具体日志信息
     * @param $info_id
     * @param string $field
     * @return mixed
     */
    public function getLogDataByInfoId($info_id,$field='')
    {
        if(empty($info_id)){
            return [];
        }
        $where = [
            'info_id' => ['symbol' => '=','value' => $info_id]
        ];
        if(empty($field)){
            $field = 'id,video_id,content,grab_address,info_id';
        }
        $data =  yield $this->objPool->getList($field,$where);
        return isset($data[0]) ?  $data[0] : $data;
    }


    /**
     * [getVideoCount 获取抓取统计列表关联数据]
     * @param  [type] $search [description]
     * @return [type]         [description]
     */
    public function getLogCount($where)
    {
        $where = $this->parseSearch($where);
        $data =  yield $this->objPool->getCount($where);
        return $data;
    }
 
    /**
     * [parseSearch 解析搜索条件]
     * @param  [type] $search [description]
     * @return [type]         [description]
     */
    public function parseSearch($search)
    {
        $where = [];
        if(empty($search)){
             return $where;
        }
        foreach($search as $key => $val){
            if(is_string($val) && !empty($val) && $val =='undefined'){
                 continue;
            }
            switch($key){
                case 'execid':
                    $where['exec_id'] = ['symbol' => '=','value' => $val];
                    break;
                case 'type':
                     $where['status'] = ['symbol' => '=','value' => $val];
                     break;
                case 'id':
                    $where['info_id'] = ['symbol' => '=','value' => $val ];
                    break;
            } 
        }
        return $where;
    }



    public function  destroy()
    {
        $this->objPool = null;
        parent::destroy();
    }


}
