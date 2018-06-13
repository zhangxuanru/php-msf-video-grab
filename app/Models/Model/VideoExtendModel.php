<?php
/**
 * 视频扩展表
 * Created by PhpStorm.
 * User: zxr
 * Date: 2017/12/11
 * Time: 18:00
 */

namespace App\Models\Model;


class VideoExtendModel extends  BaseModel
{
    public static $tableName = 'grab_video_extend';

    public function __construct()
    {
        parent::__construct();
        parent::$tableName = self::$tableName;
    }


    /**
     * 修改扩展表数据
     * @param $id
     * @param $setData
     * @return bool
     */
    public function updateExtendById($id,$setData)
    {
        $where = [
            'id'=>['symbol' => '=','value' => $id]
        ];
        $ret =  yield $this->update($setData,$where);
        if(isset($ret['result']) && $ret['result'] == true){
            return true;
        }
        return false;
    }
 

}


