<?php
/**
 * 导航模块
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/9
 * Time: 16:40
 */

namespace App\Models\Model;

class GrabNavModel extends  BaseModel
{
    public static $tableName = 'grab_nav';

    public function __construct()
    {
        parent::__construct();
        parent::$tableName = self::$tableName;
    }

}


