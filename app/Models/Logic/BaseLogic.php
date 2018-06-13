<?php
/**
 * 基础Logic模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/9
 * Time: 15:21
 */
namespace App\Models\Logic;
use PG\MSF\Models\Model;

class BaseLogic extends Model
{
   
    public $objPool = null;

    public function __construct()
    {
        parent::__construct();
    }
 
    /**
     * [parseCondition 解析分页条件]
     * @param  array  $pageInfo [description]
     * @return [type]           [description]
     */
    public function parseCondition($pageInfo = array())
    {
        $condition = array(
           'limit'  => isset($pageInfo['limit']) ? $pageInfo['limit'] : 0,
           'offset' => isset($pageInfo['offset']) ? $pageInfo['offset'] : 0,
           'order'  => isset($pageInfo['field']) ? $pageInfo['field'] : '',
           'sort'   => isset($pageInfo['sort']) ? $pageInfo['sort'] : ''
          ); 
        return $condition;
    }


    /**
     * 保存数据
     * @param array $data
     */
    public function saveData($data = [])
    {
        if(empty($data)){
            throw new \Exception('empty data');
        }        
        try{
            $ret =  yield $this->objPool->save($data); 
            return $ret;
        }catch(\Exception $e){
            throw new \Exception($e->getMessage(), 1);
        }
    }

     /**
     * 修改数据
     * @param $id
     * @param $setData
     * @return bool
     */
    public function updateById($id,$setData)
    {
        $where = [
            'id' => ['symbol' => '=','value' => $id]
        ];
        $ret =  yield $this->objPool->update($setData,$where); 
        return $ret;
    }

    /**
     * 批量删除分类
     * @param array $idList
     */
    public function batchDelByIdList($idList = array())
    {
        $idList = array_filter($idList);
        if(empty($idList)){
            throw new \Exception("ID为空", 1);
        }
        $data = ['is_del' => 1];
        $where = [
            'id' => ['symbol' => 'in','value' => $idList]
        ];
        try{
            $ret =  yield $this->objPool->update($data,$where);
            if($ret == false){
                throw new \Exception("数据更新失败！", 1);
            }
            return true;
        }catch(\Exception $e){
            throw new \Exception($e->getMessage(), 1);
        }
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
          if(is_string( $val)){
                $where[$key] = ['symbol' => '=','value' => $val];   
            }
            if(is_array( $val)){
                 $where[$key] = ['symbol' => 'in','value' => $val];  
            } 
        }
        return $where;
    }


  
   public function  destroy()
   {

   }


}
