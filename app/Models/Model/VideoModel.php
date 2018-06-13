<?php
/**
 * 视频模块
 * Created by PhpStorm.
 * User: zxr
 * Date: 2017/11/21
 * Time: 18:00
 */

namespace App\Models\Model;


class VideoModel extends  BaseModel
{
    public static $video_count_tableName = 'grab_video_count';

    public function __construct()
    {
       parent::__construct();
       parent::$tableName = self::$tableName;
    }

 
    /**
     * [getVideoCountList 获取视频统计表数据]
     * @param  string $field     [description]
     * @param  array  $where     [description]
     * @param  array  $condition [description]
     * @return [type]            [description]
     */
    public function getVideoCountList($field='*',$where=[],$condition=[])
    {
       parent::$tableName = self::$video_count_tableName;
       return  parent::getList($field,$where,$condition);
    }

     public function getVideoCount($where)
     {
         parent::$tableName = self::$video_count_tableName;
         return  parent::getCount($where);
     }






    /**
     * 根据INFO_ID 获取grab_video_count统计表中的数据，并按时间数组
     * @param $info_id
     * @return mixed
     */
    public  function getGroupDateInfoByInfoId($info_id)
    {
        $where = [
            'info_id' => ['symbol' => '=','value' => $info_id]
        ];
        $condition = array(
           // 'group' => "FROM_UNIXTIME(date,'%Y-%m-%d')",
            'order' => 'date',
            'sort'  => 'DESC'
        );
        $list = yield $this->fetchAll('*',$where,$condition);
        return $list;
    }


    public function getMaxId()
    {
        $condition = array(
            'order' => 'id',
            'sort'  => 'DESC',
            'limit'  => 1,
            'offset' => 0,
        );
        $list = yield $this->fetchAll('id',[],$condition);
        return isset($list[0]) ? $list[0]['id'] : 0;
    }


}


