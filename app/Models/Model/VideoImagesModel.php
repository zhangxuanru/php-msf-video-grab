<?php
/**
 * 抓取图片表
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/9
 * Time: 16:40
 */

namespace App\Models\Model;

class VideoImagesModel extends  BaseModel
{
    public static $tableName = 'grab_video_images';

    public function __construct()
    {
        parent::__construct();
        parent::$tableName = self::$tableName;
    }

}


