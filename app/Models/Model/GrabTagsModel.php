<?php
/**
 * 标签表
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/9
 * Time: 16:40
 */

namespace App\Models\Model;


class GrabTagsModel extends  BaseModel
{
    public static $tableName = 'grab_tags';

    public function __construct()
    {
        parent::__construct();
        parent::$tableName = self::$tableName;
    }

}


