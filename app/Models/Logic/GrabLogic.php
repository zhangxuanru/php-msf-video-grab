<?php
/**
 * grab 逻辑层
 * Created by PhpStorm.
 * User: zxr
 * Date: 2017/12/22
 * Time: 14:21
 */
namespace App\Models\Logic;
use App\Models\Model\InfoModel;

class GrabLogic extends BaseLogic
{
    public function __construct()
    {
        parent::__construct();
        $this->objPool = $this->getObject(InfoModel::class);
    }

    /**
     * 获取抓取列表关联数据
     * @param string $field
     * @param array $pageInfo
     * @param array $search
     */
    public function getInfoList($field='',$pageInfo = array(),$search = array())
    {
        if(empty($field)){
            $field = 'id,video_type,type,category,status,grab_title,grab_address,grab_number,success_number,fail_number,implement_date';
        }
         $where = $this->parseSearch($search);
         $where['status'] = ['symbol' => '!=','value' => 0 ];
         $condition = $this->parseCondition($pageInfo);
        try{
            $data =  yield  $this->objPool->getList($field,$where,$condition);
        }catch(\Exception $e){
             return [];
        }
        return $data;
    }

    
    /**
     * [getInfoDataById 根据ID查询数据]
     * @param  [type] $id    [description]
     * @param  string $field [description]
     * @return [type]        [description]
     */
    public function getInfoDataById($id,$field='')
    {
        if(empty($id)){
            return [];
        }
        if(empty($field)){
            $field = 'id,video_type,type,category,status,grab_title,grab_address,grab_number,success_number,fail_number,implement_date,channelId';
        }
        $where['id'] = ['symbol' => '=','value' => $id ];
        try{
            $data =  yield  $this->objPool->getList($field,$where);
        }catch(\Exception $e){
             return [];
        }
        return isset($data[0]) ? $data[0] : $data;
    }


    /**
     * 获取总数
     * @param $where
     */
    public function getInfoCount($where = array())
    {
        $where = $this->parseSearch($where);
        $where['status'] = ['symbol' => '!=','value' => 0];
        $data =  yield  $this->objPool->getCount($where);
        return $data;
    }


    /**
     * 格式化数组
     * @param $field
     * @param $pageInfo
     * @param  $search
     */
    public function getInfoListData($field='',$pageInfo,$search)
    {
        //获取抓取列表数据
        $data =  yield $this->getInfoList($field,$pageInfo,$search);
        //获取分类数据
        $catObj = $this->getObject(CateGoryLogic::class);
        $cateList  = yield $catObj->getFormatCateGoryData();

        //如果是单个视频则把对应的视频ID也查出来
        $infoIdList = [];
        $videoData = [];
        foreach($data as $key => $val){
            if($val['type'] == '1' && $val['status'] == '2'){
                $infoIdList[] = $val['id'];
            }
        }
        if(!empty($infoIdList)){
            $videoObj = $this->getObject(VideoInfoLogic::class);
            $videoData =  yield  $videoObj->getVideoDataByInfoIdList($infoIdList,'id,info_id');
            $videoData = array_column($videoData,'id','info_id');
        }
        foreach($data as $k => $val) {
            $category_id = $val['category'];
            $type  = $val['type'];
            $id  = $val['id'];
            $val['video_id'] = isset($videoData[$id])  ? $videoData[$id] : 0;
            $url ='/grab/page/' ;
            if($type == '1'){
                $url = '/video/detail/';
            }
            if(empty($val['video_id']) && $type == '1'){
                $url = '/grab/fail/';
            }
            $val['url'] = $url;
            $val['statusText'] = $this->getGrabStatus($val['status']);
            $val['category'] =  isset($cateList[$category_id]) ? $cateList[$category_id] : '';
            $val['videoTypeName'] = $this->getVideoTypeByType($val['video_type']);
            $val['typeName'] =  $this->getGrabType($type);
            $val['exec_date'] = !empty($val['implement_date']) ? date('Y-m-d H:i:s',$val['implement_date']) : '' ;
            $val['operation'] = '';
            if( $val['status'] == '2' || $val['status'] == '3' ){
                $val['operation'] = sprintf("<a href='%s'>查看</a>&nbsp;&nbsp;",$url.'?info_id='.$val['id'].'&vid='.$val['video_id']);
            }
            if($val['type'] =='2' &&  $val['status']!='4'  && $val['type'] != '3' ){
                $val['operation'].= sprintf("<a href='javascript:;' class='execgrab' data-type='%s' data-id='%s' onclick='execData(%s,%s)'>执行</a>&nbsp;&nbsp;", $val['type'],$val['id'],$val['id'], $val['type']);
            }
            if( $val['type'] =='1' && !in_array($val['status'],[2,4])  && $val['type'] != '3' ){
                $val['operation'] .= sprintf("<a href='javascript:;' class='execgrab' data-type='%s' data-id='%s' onclick='execData(%s,%s)'>执行</a>&nbsp;&nbsp;", $val['type'], $val['id'],$val['id'],$val['type']);
            }
            $val['operation'].= sprintf("<a href='javascript:;' data-id='%s' class='del' onclick='delData(%s)' >删除</a>",$val['id'],$val['id']);
            $data[$k] = $val;
        }
        return $data;
    }


    /**
     * [saveData 保存数据]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public function saveData($data = array() )
    {
      try{ 
           $this->checkSaveData($data);
           $grab_address = $data['grab_address'];
           $video_type = $data['video_type'];
           $type = $data['type'];
          if(!empty($grab_address)){
               $this->checkDomainIsSet($grab_address); 
               if($video_type =='1' && $type == '2'){ 
                   $channelId  = $this->getChannelId($grab_address);
               }
            }    
            $data = [
                'user_id'    => 1,
                'video_type' => $video_type,
                'category'   => $data['category'],
                'type'       => $type,
                'grab_title' => $data['grab_title'],
                'grab_address' => $grab_address,
                'grab_number'  => $data['grabnum'],
                'channelId'    => isset($channelId) ? $channelId : 0,
                'add_date'     => time()
            ]; 

            $ret =  yield  $this->objPool->save($data);  
            if($ret == false){
                 throw new \Exception("数据添加失败！", 1);
            } 
            return true;
        }catch(\Exception $e){
            throw new \Exception($e->getMessage(), 1); 
        }
    }

    /**
     * [batchDelByIdList 批量删除]
     * @param  array  $idList [description]
     * @return [type]         [description]
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
     * 更新数据
     * @param $setData
     * @param $id
     * @return bool
     * @throws \Exception
     */
//    public function updateData($id,$setData)
//    {
//        if(empty($setData) || empty($id)){
//            throw new \Exception('empty data or empty id');
//        }
//        try{
//            $where = [
//                 'id' => ['symbol' => '=','value' => $id]
//             ];
//            $ret =  yield  $this->objPool->update($setData,$where);
//            if($ret == false){
//                throw new \Exception("数据更新失败！", 1);
//            }
//            return true;
//        }catch(\Exception $e){
//            throw new \Exception($e->getMessage(), 1);
//        }
//    }




    /**
     * [pageFormatData  获取执行的详情数据-格式化数据]
     * @param  [type] $into_id [description]
     * @return [type]          [description]
     */
    public function pageFormatData($infoData=[],$countData=[],$cateList=[])
    { 
        if(empty($infoData) && empty($countData)){
            return [];
        }
          $rows = isset($infoData[0]) ? $infoData[0] : $infoData;
          $videoTypeName = $this->getVideoTypeByType($rows['video_type']);
          $category_id = $rows['category'];
          $category = isset($cateList[$category_id]) ? $cateList[$category_id] : ''; 
          foreach ($countData as $key => $val) {
                 $val['grab_title'] = $rows['grab_title'];
                 $val['videoTypeName'] =  $videoTypeName;
                 $val['category']  = $category;
                 $val['successUrl'] = sprintf('/grab/pageList/?id=%s&type=1&execid=%s',$val['info_id'],$val['id']);
                 $val['failUrl'] = sprintf('/grab/pageList/?id=%s&type=0&execid=%s',$val['info_id'],$val['id']);
                 $val['repUrl'] = sprintf("/grab/pageList/?id=%d&type=2&execid=%d",$val['info_id'],$val['id']);
                 $val['execDate'] = date('Y-m-d H;i;s',$val['date']);
                 $countData[$key] = $val;
            } 
        return $countData;
    }
 

    /**
     * [checkSaveData 保存数据前检查基本数据格式]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    private function checkSaveData($data = array() )
    {
          if(empty($data['grab_address']) && empty($data['channelId'])){
              throw new \Exception("抓取地址为空,请重新输入");
          } 
    }
 
     /**
      * [$grab_address 从地址中获取渠道ID，针对YOUTUBE]
      * @var string
      */
    private function getChannelId($grab_address = '' )
    {
        if(empty($grab_address)){
             throw  new \Exception("来源页地址不正确");
        } 
        $path  = parse_url($grab_address,PHP_URL_PATH);
        $pattern='/\/channel\/.*/';
        preg_match($pattern, $path,$arr);
        if(empty($arr)){
             throw  new \Exception("来源页地址不正确");
        }
        $channelId = str_replace('/channel/', '', $path);
        return  $channelId; 
    }


    /**
     * [checkDomainIsSet 判断域名是否正确]
     * @param  string $grab_address [description]
     * @return [type]               [description]
     */
    private function checkDomainIsSet($grab_address = '')
    {
        if(empty($grab_address)){
             throw  new \Exception("来源页地址非法");
        }
         //判断域名是否正确
         $videoType = $this->getVideoType();
         $domain    = parse_url($grab_address,PHP_URL_HOST);
         $domainArr = array_column($videoType,'domain');
         $domainExists = in_array($domain,$domainArr);
         if($domainExists === false){
             throw  new \Exception("来源页地址不正确");
          }
          return true;
    }
 
    /**获取执行状态
     * @param int $status
     * @return array
     */
    public function getGrabStatus($status = 0 )
    {
          $statusArr = array(  1 => '未执行',
                               2 => '执行成功',
                               3 => '执行失败',
                               4 => '正在执行中'
                           );
        return isset($statusArr[$status]) ?  $statusArr[$status] : $statusArr;
    }

    /**
     * 获取抓取类型
     * @param int $type
     * @return array
     */
    public function getGrabType($type = 0)
    {
          $typeArr   = array(   1 => '单个视频',
                                2 => '单页视频',
                                3 => '计划任务'
          );
        return isset($typeArr[$type]) ?  $typeArr[$type] : $typeArr;
    }

    /**
     * 获取视频类别
     * @return mixed|null
     */
    public function getVideoType()
    {
        $videoType = $this->getConfig()->get('constant.VIDEO_TYPE');
        $ret = [];
        foreach($videoType as $key => $val){
            $id = $val['id'];
            $ret[$id] = $val;
            unset($videoType[$key]);
        }
        return $ret;
    }

    /**
     * 根据ID获取视频类别
     * @param $id
     * @return string
     */
    public function getVideoTypeByType($id)
    {
        $list =  $this->getVideoType();
        return isset($list[$id]) ?  $list[$id]['type'] : '';
    }


 /**
     * 解析搜索条件
     * @param $search
     * @return array
     */
    public function parseSearch($search)
    {
        $where = [];
        if(empty($search)){
             return $where;
        }
        foreach($search as $key => $val){
            if(empty($val)){
                 continue;
            }
            if(is_string($val) && !empty($val) &&  $val =='undefined'){
                continue;
            }
            switch($key){
                case 'videoType':
                    $key = 'video_type';
                    $where[$key] = ['symbol' => '=','value' => $val]; 
                    break;
                case 'grab_address':
                    $where[$key] = ['symbol' => 'like','value' => "%".$val."%"];
                    continue 2;
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
