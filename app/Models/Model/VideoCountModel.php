<?php
/**
 * 视频模块
 * Created by PhpStorm.
 * User: zxr
 * Date: 2017/11/21
 * Time: 18:00
 */

namespace App\Models\Model;


class VideoCountModel extends  BaseModel
{
    public static $tableName = 'grab_video_count';

    public function __construct()
    {
       parent::__construct();
       parent::$tableName = self::$tableName;
    }
 
}


