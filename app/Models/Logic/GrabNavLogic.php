<?php
/**
 * 导航 逻辑层
 * Created by PhpStorm.
 * User: zxr
 * Date: 2017/12/22
 * Time: 14:21
 */
namespace App\Models\Logic;
use App\Models\Model\GrabNavModel;

class GrabNavLogic extends BaseLogic
{
    private  $field = 'id,pid,nav_name,url,sort,cat_id,region';
    public function __construct()
    {
        parent::__construct();
        $this->objPool = $this->getObject(GrabNavModel::class);
    }

    /**
     * 根据条件获取导航数据
     * @param string $field
     * @param array $where
     * @param array $pageInfo
     * @return array
     */
    public function getAllData($field='',$where = [],$pageInfo=[])
    {
        $where = $this->parseSearch($where);
        $condition = $this->parseCondition($pageInfo);
        if(empty($field)){
             $field =  $this->field;
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
     * 获取所有的导航,如果$pid为空，则查的是所有父分类数据
     * @param int $pid
     * @param string $field
     * @return mixed
     */
    public function getNavDataByPidData($pid = 0,$field='')
    {
        if (empty($field)) {
            $field =  $this->field;
        }
        if (empty($pid)) {
             $pid = 0;
        }
        $where['pid'] = ['symbol' => '=','value' => $pid];
        $where['is_del'] = ['symbol' => '=','value' => 0];
        $data =  yield $this->objPool->getList($field,$where);
        return $data;
    }


    /**
     * 根据ID获取导航数据
     * @param int $id
     * @param string $field
     * @return mixed
     */
    public function getNavDataByIdData($id = 0,$field='')
    {
        if (empty($field)) {
            $field =  $this->field;
        }
        if (empty($id)) {
            $id = 0;
        }
        $where['id'] =['symbol' => '=','value' => $id];
        $where['is_del'] =['symbol' => '=','value' => 0];
        $data =  yield $this->objPool->getList($field,$where);
        return isset($data[0]) ? $data[0] : $data;
    }


    public function  destroy()
    {
        $this->objPool = null;
        parent::destroy();
    }


}
