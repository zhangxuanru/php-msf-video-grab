<?php
/**
 * 分类 逻辑层
 * Created by PhpStorm.
 * User: zxr
 * Date: 2017/12/22
 * Time: 14:21
 */
namespace App\Models\Logic;
use App\Models\Model\CateGoryModel;

class CateGoryLogic extends BaseLogic
{
    public function __construct()
    {
        parent::__construct();
        $this->objPool = $this->getObject(CateGoryModel::class);
    }

    /**
     * 根据条件获取分类数据
     * @param string $field
     * @param array $where
     * @param array $pageInfo
     * @return array
     */
    public function getAllCateGoryData($field='',$where = [],$pageInfo=[])
    { 
        $where = $this->parseSearch($where); 
        $condition = $this->parseCondition($pageInfo);
        if(empty($field)){
             $field = 'id,category_name';
        }
        $where['is_del'] =['symbol' => '=','value' => 0];
        try{
            $data =  yield $this->objPool->getList($field,$where,$condition);
        }catch(\Exception $e){
               return [];
        }
        return $data;
    }

    /**
     * 获取总数
     * @param $where
     */
    public function getCateCount($where = array())
    {
        $where = $this->parseSearch($where);
        $where['is_del'] = ['symbol' => '=','value' => 0];
        $data =  yield $this->objPool->getCount($where);
        return $data;
    }

    /**
     * 获取所有的分类,如果$pid为空，则查的是所有父分类数据
     * @param int $pid
     * @param array $pageInfo
     * @param string $field
     * @return mixed
     */
    public function getCateDataByPid($pid = 0, $pageInfo=[],$field='')
    {
        if(empty($field)){
           $field = 'id,pid,category_name,is_display,type,sort,addDate';
        }
        if(empty($pid)){
            $pid = 0;
        }
        $where['pid'] = $pid;
        $data = yield  $this->getAllCateGoryData($field,$where,$pageInfo); 
        foreach($data as $key => $val){
            $val['cat_count'] = 0;
            if(empty($pid)){
                $where['pid'] = $val['id'];
                $val['cat_count'] = yield $this->getCateCount($where);
            }
            $val['type_id'] =  $val['type'] ;
            $val['addDate'] = date('Y-m-d H:i:s');
            $data[$key] = $val;
        }
        foreach($data as $key => $val){
            $val['video_count']  =  yield $this->getVideoCountByCateId($val['id']);
            $data[$key] = $val;
        }
        return $data;
    }

    /**
     * 获取所有的分类,如果$pid为空，则查的是所有父分类数据
     * @param int $pid
     * @param array $pageInfo
     * @param string $field
     * @return mixed
     */
    public function getCateDataByPidData($pid = 0,$field='')
    {
        if (empty($field)) {
            $field = 'id,pid,category_name,is_display,type,sort,addDate';
        }
        if (empty($pid)) {
             $pid = 0;
        }
        $where['pid'] = $pid;
        $data = yield  $this->getAllCateGoryData($field, $where);
        return $data;
    }

    /**
     * 根据分类ID获取分类下的视频数
     * @param int $catId
     * @return mixed
     */
    public function getVideoCountByCateId($catId = 0 )
    {
        $where['pid'] = $catId;
        $field = 'id';
        $catObj = $this->getObject(CateGoryLogic::class);
        $catList =  yield $catObj->getAllCateGoryData($field,$where);
        if(empty($catList)){
            $catIdList = [$catId];
        }else{
            $catIdList =  array_column($catList,'id');
        }
       $videoObj  = $this->getObject(VideoInfoLogic::class);
       $videoCount = yield $videoObj->getVideoCountByCatIdList($catIdList);
       return $videoCount;
    }

    /**
     * 获取所有的分类总数,如果$pid为空，则查的是所有父分类总数
     * @param int $pid
     * @return mixed
     */
    public function geCateCountByPid($pid = 0)
    {
        if(empty($pid)){
            $pid = 0;
        }
        $where['pid'] = $pid;
        $data =  yield $this->getCateCount($where);
        return $data;
    }

    /**
     * 格式化分类数据
     * @param $data
     * @return array
     */
    public function getFormatCateGoryData($field='',$where = [])
    {
       $data = yield $this->getAllCateGoryData($field,$where);
       $data = array_column($data,'category_name','id');
       return $data;
    }


    /**
     * 根据分类ID获取分类数据
     * @param $id
     */
    public function getCateGoryDataById($id,$field='')
    {
        if(empty($id)){
             return [];
        }
        if(empty($field)){
            $field = 'id,pid,category_name,categoryId as youtube_categoryId,channelId,sort,is_display';
        }
        if(is_array($id)){
            $where['ids'] = $id;
        }else{
           $where['id'] = $id;
        }
        $data = yield $this->getAllCateGoryData($field,$where);
        if(is_array($id)){
            return $data;
        }
        return isset($data[0]) ?  $data[0] : $data;
    }


    /**
     * 新增分类数据
     * @param array $data
     */
    public function saveData($data = [])
    {
        if(empty($data)){
            throw new \Exception('empty data');
        }
        $data = [
            'category_name' => $data['category_name'],
            'sort'    => !empty($data['sort']) ? $data['sort']: 1,
            'is_display' => $data['is_display'],
            'pid' => isset($data['pid']) ? $data['pid'] : 0,
            'addDate' => time()
        ];
        try{
            $ret =  yield $this->objPool->save($data);
            if($ret == false){
                throw new \Exception("数据添加失败！", 1);
            }
            return true;
        }catch(\Exception $e){
            throw new \Exception($e->getMessage(), 1);
        }
    }


    
    /**
     * [JudInsertion 新增分类，但判断是否已经存在]
     * @param [type] $catData [description]
     */
     public function judInsertion($catData=[])
     {
        $where = [
            'pid' => ['symbol' => '=','value' => $catData['pid']],
            'cat_crcid' => ['symbol' => '=','value' => $catData['cat_crcid']]
        ];
        if(isset($catData['type'])){
            $where['type'] = ['symbol' => '=','value' => $catData['type']];
        } 
        $data = yield $this->objPool->getList('id',$where); 
        if(!empty($data)){
            return $data[0]['id'];
        }
        $ret = yield $this->objPool->save($catData);
        return $ret;
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
            if(is_string($val)){
                settype($val,'string');
            }
            if(!empty($val) &&  is_string($val) && $val == 'undefined'){
                 continue;
            }
            switch($key){
                case 'ids':
                    $where['id'] = ['symbol' => 'in','value' => $val];
                    break;
                default:
                    $where[$key] = ['symbol' => '=','value' => $val];
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
