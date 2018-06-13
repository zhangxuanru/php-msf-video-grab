<?php
/**
 * 视频主表
 * Created by PhpStorm.
 * User: zxr
 * Date: 2017/11/21
 * Time: 18:00
 */

namespace App\Models\Model;

class VideoInfoModel extends  BaseModel
{
    public static $tableName = 'grab_video_info';
    public static $extendTableName = 'grab_video_extend';

    public function __construct()
    {
        parent::__construct();
        parent::$tableName = self::$tableName;
    }

    /**
     * 关联扩展表查询
     * @param array $where
     * @param array $condition
     */
    public function getVideoListJoinData($where=[],$condition=[])
    {
        $this->where  = $where;
        $this->condition = $condition;
        $this->bindQuery();
        $field = 'info.id,info.av_id,info.info_id,info.title,info.category,info.keywords,info.description,info.qiniu_upload,info.addDate,
        info.sort,info.type,info.status,info.is_top,info.is_recommend,ext.video_id,ext.view_count,ext.published_at,ext.length_seconds,ext.like_number,ext.reviews_number,
        ext.video_size';
        $onStr = "info.id = ext.video_id";
        $data =  yield $this->db->select($field)->from(self::$tableName,'info')->leftJoin(self::$extendTableName,$onStr,'ext')->go();
        return isset($data['result']) ? $data['result'] : [];
    }

    /**
     * [batchDelByIdList 批量删除]
     * @param  array  $idList [description]
     */
    public function batchDelByIdList($idList = array())
    {
        if(empty($idList)){
            return false;
        }
        $where = [
            'id' => ['symbol' => 'in','value' => $idList]
        ];
        $setData = ['status' => 0];
        $ret =  yield $this->update($setData,$where);
        if(isset($ret['result']) && $ret['result'] == true){
            return true;
        }
        return false;
    }


}


