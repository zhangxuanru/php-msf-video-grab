<?php
/**
 * video 逻辑层
 * Created by PhpStorm.
 * User: zxr
 * Date: 2017/12/22
 * Time: 14:21
 */
namespace App\Models\Logic;
use App\Models\Model\VideoCountModel;

class VideoCountLogic extends BaseLogic
{ 
    public function __construct()
    {
        parent::__construct();
        $this->objPool = $this->getObject(VideoCountModel::class);
    }

    /**
     * 获取抓取统计列表关联数据
     * @param array $pageInfo
     * @param array $search
     */
    public function getVideoCountList($field='',$pageInfo = array(),$search = array())
    {
        if(empty($field)){
             $field = '*';
        }
         $where = $this->parseSearch($search); 
         $condition = $this->parseCondition($pageInfo);
        try{
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
    public function getVideoCount($where)
    {
        $where = $this->parseSearch($where); 
        $data =  yield $this->objPool->getCount($where);
        return $data;
    }

   

    /**
     * 修改数据
     * @param $info_id
     * @param $setData
     * @return bool
     */
    public function updateByInfoId($info_id,$setData)
    {
       $where = [
            'info_id'=>['symbol' => '=','value' => $info_id]
        ];
        $ret =  yield $this->objPool->update($setData,$where); 
        return $ret;
    }

    /**
     * 修改数据
     * @param $exec_id
     * @param $setData
     * @return bool
     */
    public function updateByexecId($exec_id,$setData)
    {
       $where = [
            'exec_id'=>['symbol' => '=','value' => $exec_id]
        ];
        $ret =  yield $this->objPool->update($setData,$where); 
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
            if(empty($val) || $val =='undefined'){
                 continue;
            }
            switch($key){
                case 'info_id':  
                     $where[$key] = ['symbol' => '=','value' => $val];
                     break; 
                 case 'datemin':
                  $where['date'] = ['symbol' => 'between','min' => strtotime($val." 00:00:00")];  
                 case 'datemax':
                     $where['date']['max'] = strtotime($val." 23:59:59"); 
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
