<?php
/**
 * videoLOG   视频扩展表逻辑层
 * Created by PhpStorm.
 * User: zxr
 * Date: 2017/12/22
 * Time: 14:21
 */
namespace App\Models\Logic;
use App\Models\Model\VideoExtendModel;

class VideoExtendLogic extends BaseLogic
{ 
    private $field = 'video_id,view_count,channel_id,channel_title,published_at,length_seconds,
                       like_number,reviews_number,video_size,hls_key,author,filename';


    public function __construct()
    {
        parent::__construct();
        $this->objPool = $this->getObject(VideoExtendModel::class);
    }

    /**
     * 根据视频ID列表获取扩展表里的信息
     * @param array $idArr
     * @param string $field
     * @return array
     */
    public function getVideoExtendByIdList(array $idArr,$field='')
    {
        if(empty($field)){
            $field =  $this->field;
        }
        $where = [
            'video_id'=>['symbol' => 'in','value' => $idArr]
        ];
        try {
            $data = yield $this->objPool->getList($field, $where);
        }catch(\Exception $e){
            return [];
        }
        return $data;
    }

    /**
     * 根据ID获取视频扩展表里数据
     * @param $id
     * @param string $field
     * @return array
     */
    public function getVideoExtendDataById($id,$field='')
    {
        if(empty($field)){
            $field =  $this->field;
        }
        $where = [
            'id'=>['symbol' => '=','value' => $id]
        ];
        try {
            $data = yield $this->objPool->getList($field, $where);
        }catch(\Exception $e){
            return [];
        }
        return $data;
    }


    /**
     * 根据video_id获取视频扩展表里数据
     * @param $video_id
     * @param string $field
     * @return array
     */
    public function getVideoExtendDataByAvId($video_id,$field='')
    {
        if(empty($field)){
            $field =  $this->field;
        }
        $where = [
            'video_id' => ['symbol' => '=','value' => $video_id]
        ];
        try {
            $data = yield $this->objPool->getList($field, $where);
        }catch(\Exception $e){
            return [];
        }
        return isset($data[0]) ?  $data[0] : $data;
    }

    /**
     * 根据条件获取视频扩展表数据
     * @param array $search
     * @param array $pageInfo
     * @param string $field
     * @return array
     */
    public function getVideoExtendListData($search = array(),$pageInfo = array(),$field='')
    {
        if(empty($field)){
             $field = $this->field;
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
     * [getVideoCount 获取抓取统计列表关联数据]
     * @param  [type] $search [description]
     * @return [type]         [description]
     */
    public function getExtendCount($where)
    {
        $where = $this->parseSearch($where);
        $data =  yield $this->objPool->getCount($where);
        return $data;
    }

     
    /**
     * [updateExtendByVideoId 根据video_id修改扩展表数据]
     * @param  [type] $video_id [description]
     * @param  array  $data     [description]
     * @return [type]           [description]
     */
    public function updateExtendByVideoId($video_id,$data=[])
    {
       $where = [
            'video_id'=>['symbol' => '=','value' => $video_id]
        ];
       $ret =  yield $this->objPool->update($data,$where);
       return $ret;
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
            if($val =='undefined'){
                  continue;
            }
            switch($key){
                case 'hls_key':
                    $where['hls_key'] = ['symbol' => '=','value' => $val];
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
