<?php
/**
 * videoInfo  视频表逻辑层
 * Created by PhpStorm.
 * User: zxr
 * Date: 2017/12/22
 * Time: 14:21
 */
namespace App\Models\Logic;
use App\Models\Model\VideoInfoModel;

class VideoInfoLogic extends BaseLogic
{ 
    private $field = 'id,av_id,info_id,title,category,keywords,description,qiniu_upload,addDate,sort,type,status,is_reviews';

    public function __construct()
    {
        parent::__construct();
        $this->objPool = $this->getObject(VideoInfoModel::class);
    }


    /**
     * 根据info_id批量获取视频信息
     * @param array $infoIdArr
     * @param string $field
     */
    public function getVideoDataByInfoIdList(array $infoIdArr,$field='')
    {
        if(empty($field)){
            $field = $this->field;
        }
        $where = ['info_id' => ['symbol' => 'in','value' => $infoIdArr]];
        try {
            $data = yield $this->objPool->getList($field, $where);
        }catch(\Exception $e){
            return [];
        }
        return $data;
    }



    /**
     * 根据ID批量获取视频信息
     * @param array $idArr
     * @param string $field
     */
    public function getVideoDataByIdList(array $idArr,$field='')
    {
        if(empty($field)){
           $field = $this->field;
        }
        $where = ['id' => ['symbol' => 'in','value' => $idArr]];
        try {
            $data = yield $this->objPool->getList($field, $where);
        }catch(\Exception $e){
            return [];
        }
        return $data;
    }


    /**
     * 根据视频ID获取具体视频数据，包括括展表里的数据
     * @param array $idArr
     * @return array
     */
    public function getVideoData(array $idArr)
    {
         if(empty($idArr)){
             return [];
         }
       $videoData  = yield  $this->getVideoDataByIdList($idArr);
       if(empty($videoData)){
            return [];
       }
       $extendObj  =  $this->getObject(VideoExtendLogic::class);
       $extendData =  yield  $extendObj->getVideoExtendByIdList($idArr);
       if(empty($extendData)){
            return [];
        }
       foreach($videoData as $key => $val){
            foreach($extendData as $k => $v){
                if($v['video_id'] == $val['id'] ){
                    $val = array_merge($v,$val);
                    unset($extendData[$k],$videoData[$key]);
                }
            }
          $videoData[$key] = $val;
        }
        return $videoData;
    }

    /**
     * 整理LOG，数组格式
     * @param $data
     * @param $logData
     * @return mixed
     */
    public function formatArray($data,$logData)
    {
        if(empty($data)){
            return $data;
        }
        $grabAddressList = array_column($logData,'grab_address','video_id');
        foreach($data as $key => $val) {
             $video_id = $val['video_id'];
             $val['grab_address'] = isset($grabAddressList[$video_id]) ? $grabAddressList[$video_id] : '';
             $data[$key] = $val;
        }
        return $data;
    }

    /**
     * 根据条件获取视频表数据
     * @param array $search
     * @param array $pageInfo
     * @param string $field
     * @param bool $is_extend
     * @return array
     */
    public function getVideoListData($search = array(),$pageInfo = array(),$field='',$is_extend = false)
    {
        if(empty($field)){
            $field =  $this->field;
        }
        try{
            $where = $this->parseSearch($search);
            $condition = $this->parseCondition($pageInfo);
            $data =  yield $this->objPool->getList($field,$where,$condition);
            if($is_extend){
                $data = yield $this->getExtendByData($data);
            }
        }catch(\Exception $e){
            return [];
        }
        return $data;
    }

    /**
     * 根据视频ID查询具体视频信息
     * @param $id
     * @param string $field
     * @return mixed
     */
    public function getVideoDataById($id,$field='')
    {
        if(empty($field)){
            $field = $this->field;
        }
        $where = [
            'id' => ['symbol' => '=','value' => $id]
           ];
        $data =  yield $this->objPool->getList($field,$where);
        $data =  yield $this->getExtendByData($data);
        return isset($data[0]) ? $data[0] : $data;
    }

    /**
     * 根据AV_ID获取具体视频信息
     * @param $av_id
     * @param string $field
     * @param bool $is_extend
     * @return mixed
     */
    public function getVideoDataByAvid($av_id,$field='',$is_extend = true)
    {
        if(empty($field)){
            $field =  $this->field;
        }
        $where = [
            'av_id' => ['symbol' => '=','value' => $av_id]
        ];
        $data =  yield $this->objPool->getList($field,$where);
        if($is_extend){
           $data =  yield $this->getExtendByData($data);
        }
        return isset($data[0]) ?  $data[0] : $data;
    }


    /**
     * 根据$info_id获取具体视频信息
     * @param $info_id
     * @param string $field
     * @return mixed
     */
    public function getVideoDataByInfoId($info_id,$field='')
    {
        if(empty($field)){
            $field =  $this->field;
        }
        $where = [
            'info_id' => ['symbol' => '=','value' => $info_id]
        ];
        $data =  yield $this->objPool->getList($field,$where);
        return isset($data[0]) ?  $data[0] : $data;
    }



    /**
     * 获取视频扩展表数据
     * @param array $data
     */
    public function getExtendByData($data = array())
    {
        if(empty($data)){
             return [];
        }
        $extendObj   =  $this->getObject(VideoExtendLogic::class);
        $videoIdList =  array_column($data,'id');
        $extendData  =  yield  $extendObj->getVideoExtendByIdList($videoIdList);
        foreach($extendData as $exk => $exv){
            $video_id = $exv['video_id'];
            foreach($data as $key => $val) {
               if($val['id'] == $video_id){
                   $data[$key] = array_merge($val,$exv);
                    unset($extendData[$exk]);
                }
            }
        }
        unset($extendObj,$extendData,$videoIdList);
        return $data;
    }

    /**
     * [getVideoCount 获取抓取统计列表关联数据]
     * @param  [type] $search [description]
     * @return [type]         [description]
     */
    public function getVideoCount($where)
    {
        $where = $this->parseSearch($where);
        $data =  yield $this->objPool->getCount($where);
        return $data;
    }


    /**
     * 后台视频列表
     * @param $where
     * @param $condition
     * @return \Generator
     */
    public function getVideoListJoinData($where,$condition)
    {
        $where = $this->parseSearch($where);
        $condition = $this->parseCondition($condition);
        $data =  yield $this->objPool->getVideoListJoinData($where,$condition);
        $grabObj = $this->getObject(GrabLogic::class);
        foreach($data as $key  => $val){
            $val['type']  = $grabObj->getVideoTypeByType($val['type']);
            $catObj = $this->getObject(CateGoryLogic::class);
            $categoryData =  yield $catObj->getCateGoryDataById($val['category']);
            $val['category'] = $categoryData['category_name'];
            $val['category_id'] = $categoryData['id'];
            $val['video_size'] = number_format($val['video_size']/1024/1024,2) .'M';
            $val['length_seconds'] = number_format($val['length_seconds']/60,2).'分';
            $val['addDate'] = date('Y-m-d H:i:s', $val['addDate']);
            $data[$key] = $val;
        }
        return $data;
    }


    /**
     * [batchDelByIdList 批量删除]
     * @param  array  $idList [description]
     */
    public function batchDelByIdList($idList=array() )
    {
        if(empty($idList)){
            throw new \Exception("ID为空", 1);
        }
        $idList = array_filter($idList);
        $ret =  yield  $this->objPool->batchDelByIdList($idList);
        return  $ret;
    }

    /**
     * 设置视频推荐或者置顶
     * @param $idStr
     * @param $type
     * @param $val
     */
    public function setState($idStr,$type,$val=0)
    {
        if(empty($idStr) || empty($type) || $val > 1 || $val < 0){
            throw  new \Exception("参数错误");
        }
        $data = [];
        if($type == 'top'){
            $data['is_top'] = $val;
        }
        if($type == 'recommend'){
             $data['is_recommend'] = $val;
        }
        if($type == 'status'){
            $data['status'] = $val;
        }
        if(empty($data)){
            throw  new \Exception("类型错误");
        }
        $idArr = explode(",",$idStr);
        $where = [
            'id' => ['symbol' => 'in','value' => $idArr ]
        ];
       $ret = yield  $this->objPool->update($data,$where);
       return $ret;
    }

    /**
     * 更新视频信息
     * @param array $data
     */
     public function updateVideoInfo($data = [])
     {
         if(empty($data) || !isset($data['video_id']) ||  empty($data['video_id'])){
             throw new \Exception("empty data");
         }
         $data['video_id'] = intval($data['video_id']);
         $db = array(
             'title' => $data['title'],
             'keywords' => $data['keywords'],
             'description' => $data['editorValue'],
             'category' => $data['catSubData'],
             'sort' => $data['sort'],
             'is_reviews' => isset($data['is_reviews']) ? '1' : 0
         );
         $extend = array(
             'hls_key'  => $data['hls_key'],
             'view_count' => $data['view_count'],
             'reviews_number' => $data['reviews_number'],
             'like_number'    => $data['like_number']
         );
         try{
            yield $this->updateById($data['video_id'],$db);
            $extObj = $this->getObject(VideoExtendLogic::class);
            yield $extObj->updateExtendByVideoId($data['video_id'],$extend);
           return true;
         }catch(\Exception $e){
              return false;
         }
     }


    /**
     * 获取视频详情信息
     * @param int $video_id
     * @param  int $info_id
      */
    public function getVideoDetailData($video_id=0,$info_id=0)
    {
        if(empty($video_id)){
            throw new \Exception('video_id is empty');
        }
        $videoInfo = yield $this->getVideoDataById($video_id);
        if(empty($videoInfo)){
              return $videoInfo;
        }
        if(empty($info_id)){
            $info_id = $videoInfo['info_id'];
        }
        //video_info视频信息
        $data['videoInfo'] = $videoInfo;
        //grab基础信息
        $grabObj = $this->getObject(GrabLogic::class);
        $data['rows'] = yield $grabObj->getInfoDataById($info_id);
        //查询日志信息
        $logObj = $this->getObject(VideoLogLogic::class);
        $data['log']  = yield $logObj->getLogDataByVideoId($video_id);
        //分类信息
        $cateObj = $this->getObject(CateGoryLogic::class);
        $data['catgory'] = yield $cateObj->getCateGoryDataById($videoInfo['category']);
        $data['pre_catgory'] = yield $cateObj->getCateGoryDataById($data['rows']['category']);
        //查询标签
        $data['tags'] = array();
        $videoTagObj = $this->getObject(VideoTagsLogic::class);
        $tagInfo =  yield $videoTagObj->getVideoTagByVideoId($video_id);
        if($tagInfo){
            $tagIdList = array_column($tagInfo,'tag_id');
            $tagObj =  $this->getObject(GrabTagsLogic::class);
            $tagData =  yield  $tagObj->getTagsByIdList($tagIdList);
            $data['tags'] = array_column($tagData,'tag');
        }
        //查询图片
        $imagesObj = $this->getObject(VideoImagesLogic::class);
        $data['images'] = yield  $imagesObj->getVideoImagesByVideoId($video_id);
        return $data;
    }


    /**
     * 根据分类ID数组查询视频总数
     * @param array $catIdList
     */
    public function getVideoCountByCatIdList($catIdList = [])
    {
         $where = array('category' => $catIdList);
         $count =  yield $this->getVideoCount($where);
         return $count;
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
            if($key != 'status' && empty($val)){
                  continue;
            }
            switch($key){
                case 'vid' :
                    $where['video_id'] = ['symbol' => '=','value' => $val];
                    break;
                case 'category':
                      $where['category'] = ['symbol' => 'in','value' => $val];
                    break;
                case 'videoType':
                      $where['type'] = ['symbol' => '=','value' => $val];
                    break;
                case 'title':
                    $where['title'] = ['symbol' => 'like','value' => '%'.$val.'%'];
                    break;
                case 'classId':
                      $where['category'] = ['symbol' => '=','value' =>$val];
                    break;
                case 'classPid':
                    $where['category'] = ['symbol' => 'in','value' => $val ];
                    break;
                case 'is_top':
                    $where['is_top'] = ['symbol' => '=','value' => $val];
                    break;
                case 'is_recommend':
                    $where['is_recommend'] = ['symbol' => '=','value' => $val];
                    break;
                case 'status':
                     $where['status'] = ['symbol' => '=','value' => $val];
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
