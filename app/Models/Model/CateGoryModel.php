<?php
/**
 * 分类模块
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/9
 * Time: 16:40
 */

namespace App\Models\Model;

class CateGoryModel extends  BaseModel
{
    public static $tableName = 'grab_category';

    public function __construct()
    {
        parent::__construct();
        parent::$tableName = self::$tableName;
    }

    public function JudInsertion($catData)
    {
        $where = [
            'pid' => ['symbol' => '=','value' => $catData['pid']],
            'cat_crcid' => ['symbol' => '=','value' => $catData['cat_crcid']]
        ];
        if(isset($catData['type'])){
            $where['type'] = ['symbol' => '=','value' => $catData['type']];
        }
        $data =  yield $this->fetchAll('id',$where);
        if(!empty($data)){
            return $data[0]['id'];
        }
       $ret = yield $this->insert($catData);
        if($ret['result']){
             return $ret['insert_id'];
        }
        return 0;
    }


    /**
     * 根据分类ID获取分类数据
     * @param $id
     */
    public function getCateGoryData($id)
    {
        $where = [
            'id'=>['symbol' => '=','value' => $id]
        ];
        $catgory = yield $this->fetchAll('category_name,categoryId as youtube_categoryId,channelId',$where);
        return  isset($catgory[0]) ? $catgory[0] : [];
    }




    /**
     * 获取所有没删除的分类信息
     * @return array
     */
    public function getAllCateData()
    {
        $where = [
            'is_del'=>['symbol' => '=','value' => 0]
        ];
        $cateList = yield $this->fetchAll('id,category_name',$where);
        $cateList = array_column($cateList,'category_name','id');
        return $cateList;
    }

}


