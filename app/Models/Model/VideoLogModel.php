<?php
/**
 * 抓取日志模块
 * Created by PhpStorm.
 * User: zxr
 * Date: 2017/11/9
 * Time: 16:40
 */

namespace App\Models\Model;


class VideoLogModel extends  BaseModel
{
    public static $tableName = 'grab_info_log';

    public function __construct()
    {
        parent::__construct();
        parent::$tableName = self::$tableName;
    }

}


