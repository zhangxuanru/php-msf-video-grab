<?php
/**
 * GRAB
 *
 * 注意理论上本文件代码应该在Tasker进程中执行
 */

namespace App\Tasks;

use \PG\MSF\Tasks\Task;

/**
 * Class GRAB
 * @package App\Tasks
 */
class Grab extends Task
{
    private static $slave ="slave1";

    public function execTask($taskInfo)
    {
        $file = '/data/test/a.log';
        for($i = 1; $i < 10; $i++){
             file_put_contents($file,var_export($taskInfo,true),FILE_APPEND);
             sleep(4);
             }
        return true;
    }


    /**
     * 连接池执行同步查询
     *
     * @return array
     */
    public function syncMySQLPool($tableName,$field='*',$where=null)
    {
        $user = $this->getMysqlPool(self::$slave)->select($field)->from($tableName)->where($where)->go();
        return $user;
    }

    /**
     * 代理执行同步查询
     *
     * @return array
     */
    public function syncMySQLProxy($tableName,$field='*',$where=null)
    {
        $data = $this->getMysqlProxy('master_slave')->select($field)->from($tableName)->where($where)->go();
        return $data;
    }


    /**
     * 连接池执行同步事务
     *
     * @return boolean
     */
    public function syncMySQLPoolTransaction()
    {
        $mysqlPool = $this->getMysqlPool('master');
        $id = $mysqlPool->begin();
        // 开启一个事务，并返回事务ID
        $up = $mysqlPool->update('user')->set('name', '徐典阳-1')->where('id', 3)->go($id);
        $ex = $mysqlPool->select('*')->from('user')->where('id', 3)->go($id);
        if ($ex['result']) {
            $mysqlPool->commit();
            return true;
        } else {
            $mysqlPool->rollback();
            return false;
        }
    }

    /**
     * 代理执行同步事务
     *
     * @return boolean
     */
    public function syncMySQLProxyTransaction()
    {
        $mysqlPool = $this->getMysqlProxy('master_slave');
        $id = $mysqlPool->begin();
        // 开启一个事务，并返回事务ID
        $up = $mysqlPool->update('user')->set('name', '徐典阳-1')->where('id', 3)->go($id);
        $ex = $mysqlPool->select('*')->from('user')->where('id', 3)->go($id);
        if ($ex['result']) {
            $mysqlPool->commit();
            return true;
        } else {
            $mysqlPool->rollback();
            return false;
        }
    }
}
