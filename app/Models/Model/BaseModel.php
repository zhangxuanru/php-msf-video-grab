<?php
/**
 * 基础模型
 * Created by PhpStorm.
 * User: zxr
 * Date: 2017/12/12
 * Time: 15:21
 */
namespace App\Models\Model;
use PG\MSF\Models\Model;

class BaseModel extends Model
{
    protected  static  $tableName = '';
    /**
     * 从库连接信息
     * @var string
     */
     public static $slave = 'slave1';

     /**
      * [$master 主库配置信息]
      * @var string
      */
     public static $master = "master";

    /**
     * @var null|\PG\AOP\Wrapper|\PG\MSF\Pools\Miner|\PG\MSF\Pools\MysqlAsynPool
     */
     protected $db = null;

     protected $condition = [];

     protected $where = [];

     public function __construct()
     {
        parent::__construct();
        $this->db = $this->getMysqlPool(self::$slave);
     }

    /**
     * 根据条件查询列表数据
     * @param $field
     * @param $where
     * @param $condition
     * @return mixed
     */
    public function getList($field='*',$where=[],$condition=[])
    {
        $this->condition = $condition;
        $this->where  = $where;
        $this->bindQuery();
        $data  =  yield $this->db->select($field)->from(self::$tableName)->go();
        return $data['result'];
    }

    /**
     * [save 保存数据]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public function save($data = array())
    {
        $poolObj = $this->getMysqlPool(self::$master);
        $result  = yield  $poolObj->insert(self::$tableName)->set($data)->go();
        if($result['result']){
            return $result['insert_id'];
        }
        return 0;
    }
 
    /**
     * [update 修改数据]
     * @param  [type] $setData [description]
     * @param  array  $where   [description]
     * @return [type]          [description]
     */
    public function update($setData,$where=array())
    {
       $this->where = $where; 
       $poolObj = $this->getMysqlPool(self::$master);
       $this->bindWhere($poolObj);
       $ret = yield $poolObj->update(self::$tableName)->set($setData)->go(); 
       return $ret; 
     }


    /**
     * 获取总数
     * @param array $where
     * @return string
     */
    public function getCount($where = array())
    {
        $this->where  = $where;
        $this->condition = [];
        $this->bindQuery();
        $data  =  yield $this->db->select('count(*) AS count')->from(self::$tableName)->go();
        $this->where = null;
        return $data['result'][0]['count'];
    }

     /**
     * 绑定执行条件
     */
     protected function bindQuery()
     {
        $this->bindCondition();
        $this->bindWhere();
     }

    /**
     * 绑定Condition条件
     * @param $condition
     */
    private function bindCondition()
    {
        $condition = $this->condition ;
        if(empty($condition)){
              return true;
        }
        if(isset($condition['limit']) && isset($condition['offset']) && $condition['limit'] > 0 ){
            $this->db->limit($condition['limit'],$condition['offset']);
        }
        if(isset($condition['group'])){
            $this->db->groupBy($condition['group']);
        }
        if(isset($condition['order']) && isset($condition['sort']) && !empty($condition['order']) ){
            $this->db->orderBy($condition['order'],$condition['sort']);
        }
    }

    /**
     * 绑定Where条件
     * @param $where
     */
    private function bindWhere($dbMiner = null )
    {
        $where = $this->where;
        if(empty($where)){
            return true;
        }
        if(empty($dbMiner)){
            $dbMiner  = $this->db;
        }
        foreach($where as $key => $val){ 
             if( $val['symbol'] == 'in'){
                     $dbMiner->whereIn($key,$val['value']);
                     continue;
              }
              if( $val['symbol']== 'between'){
                      $dbMiner->whereBetween($key,$val['min'],$val['max']);
                      continue;
               } 
            $dbMiner->where($key,$val['value'],$val['symbol']);
        }
    }

    /**
     * [destroy 垃圾回收]
     * @return [type] [description]
     */
     public function destroy()
     {
        $this->db = null;
        $this->condition = null;
        $this->where = null;
        parent::destroy();
     }



//
//    /**
//     * 获取所有数据
//     * @param $field
//     * @param $where
//     * @return mixed
//     */
//    public function fetchAll($field = '*',$where= [],$condition=[])
//    {
//        // SQL DBBuilder更多参考 https://github.com/jstayton/Miner
//        $obj = yield $this->getMysqlPool(self::$slave)->select($field)->from(self::$tableName);
//        if(!empty($where)){
//            $num = 0;
//            foreach($where as $key => $val){
//                $num++;
//                if( $val['symbol'] == 'in'){
//                     $obj  =  yield $obj->whereIn($key,$val['value']);
//                     continue;
//                }
//                if( $val['symbol']== 'between'){
//                     $obj = yield $obj->whereBetween($key,$val['min'],$val['max']);
//                     continue;
//                }
//                if($num == 1 ) {
//                    $obj = yield $obj->where($key, $val['value'], $val['symbol']);
//                }else{
//                    $obj =  yield $obj->andWhere($key,$val['value'],$val['symbol']);
//                }
//           }
//        }
//       if(isset($condition['limit']) && isset($condition['offset'])){
//          $obj = yield $obj->limit($condition['limit'],$condition['offset']);
//       }
//       if(isset($condition['group'])){
//           $obj = yield $obj->groupBy($condition['group']);
//       }
//       if(isset($condition['order']) && isset($condition['sort'])){
//           $obj = yield $obj->orderBy($condition['order'],$condition['sort']);
//        }
//       $data  = yield $obj->go();
//       return isset($data['result']) ? $data['result'] : [];
//    }
//
//
//    /**
//     * 执行查询
//     * @param bool $count
//     * @param string $fields
//     * @param int $offset
//     * @param int $limit
//     * @param string $order
//     * @param string $sort
//     * @param array $where
//     * @return bool|int
//     */
//    public  function getLists($count=false,$fields='*',$offset=0,$limit=100,$order='id',$sort='DESC',$where = [])
//    {
//        $condition = [
//            'limit'  => $limit,
//            'offset' => $offset,
//            'order'  => $order,
//            'sort'   => $sort
//        ];
//        if($count == false){
//            $data = yield $this->fetchAll($fields,$where,$condition);
//            return $data;
//        }else{
//            $data =  yield $this->fetchAll('count(*) as c',$where);
//            $count = isset($data[0]) ? $data[0]['c'] : 0;
//            return $count;
//        }
//    }
//
//
//    /**
//     * 获取列表总数
//     * @param array $params
//     * @return int
//     */
//    public function getCounts($where = array())
//    {
//        $data =  yield $this->fetchAll('count(*) as c',$where);
//        $count = isset($data[0]) ? $data[0]['c'] : 0;
//        return $count;
//    }
//
//
//
//    /**
//     * 直接执行SQL
//     * @param $sql
//     * @param bool $is_master｜是否走主库
//     * @return mixed
//     */
//    public  function querySql($sql,$is_master = false)
//    {
//        if($is_master){
//            $data = yield $this->getMysqlPool('master')->go(null, $sql);
//        }else{
//            $data = yield $this->getMysqlPool(self::$slave)->go(null, $sql);
//        }
//        return $data;
//    }
//
//    /**
//     * 更新数据
//     * @param $setData
//     * @param null $where
//     * @return mixed
//     */
//    public function update($setData,$where=array())
//    {
//         $poolObj = yield $this->getMysqlPool('master')->update(self::$tableName)->set($setData);
//         $num = 0;
//        if(!empty($where)){
//           foreach($where as $key => $val){
//               $num++;
//               if( $val['symbol'] == 'in'){
//                   $poolObj  =  yield $poolObj->whereIn($key,$val['value']);
//                   continue;
//               }
//               if($num == 1){
//                    $poolObj =  yield $poolObj->where($key,$val['value'],$val['symbol']);
//               }else{
//                    $poolObj =  yield $poolObj->andWhere($key,$val['value'],$val['symbol']);
//               }
//           }
//      }
//     $ret = yield  $poolObj->go();
//     return $ret;
//    }
//
//     /**
//     * 插入数据
//     * @param $data
//     * @return mixed
//     * @throws \PG\MSF\Base\Exception
//     */
//    public function insert($data)
//    {
//        $poolObj = yield $this->getMysqlPool('master');
//        $result  = yield  $poolObj->insert(self::$tableName)->set($data)->go();
//        return $result;
//    }
//
//
//    /**
//     * MySQL代理使用示例 查询数据方法
//     * @param string $field
//     * @param null $where
//     * @return mixed
//     * @throws \PG\MSF\Base\Exception
//     */
//    public function fetchAllByProxy($field = '*',$where=[],$offset=0,$limit=5)
//    {
//        //andWhere
//        $mysqlProxy = yield $this->getMysqlProxy('master_slave');
//        if(empty($where)){
//            $bizLists = yield $mysqlProxy->select($field)->from(self::$tableName)->limit($limit,$offset)->go();
//        }else{
//            $proxy = yield $mysqlProxy->select($field)->from(self::$tableName);
//            $num = 0;
//            foreach($where as $key => $val){
//                $num++;
//                if($num == 1){
//                    $proxy =  yield $proxy->where($key,$val['value'],$val['symbol']);
//                }else{
//                    $proxy =  yield $proxy->andWhere($key,$val['value'],$val['symbol']);
//                }
//            }
//            $bizLists = yield $proxy->limit($limit,$offset)->go();
//        }
//        return $bizLists;
//    }
//
//
//    /**
//     * MySQL代理使用 直接执行SQL
//     * @param $sql
//     * @return mixed
//     */
//    public function querySqlByProxy($sql)
//    {
//        $mysqlProxy = $this->getMysqlProxy('master_slave');
//        $data = yield $mysqlProxy->go(null, $sql);
//        return $data;
//    }
//
//    /**
//     * MySQL代理使用  更新数据
//     * @param $setData
//     * @param null $where
//     * @return mixed
//     * @throws \PG\MSF\Base\Exception
//     */
//    public function updateByProxy($setData,$where=[])
//    {
//        $mysqlProxy = yield $this->getMysqlProxy('master_slave');
//        $mysqlProxy = yield $mysqlProxy->update(self::$tableName)->set($setData);
//        $num = 0;
//        if(!empty($where)){
//           foreach($where as $key => $val){
//                $num++;
//               if($num == 1){
//                    $mysqlProxy =  yield $mysqlProxy->where($key,$val['value'],$val['symbol']);
//               }else{
//                    $mysqlProxy =  yield $mysqlProxy->andWhere($key,$val['value'],$val['symbol']);
//               }
//           }
//      }
//     $result = yield $mysqlProxy->go();
//     return $result;
//    }
//
//
//    /**
//     * 插入数据
//     * @param $data
//     * @return mixed
//     * @throws \PG\MSF\Base\Exception
//     */
//    public function insertByProxy($data)
//    {
//        $mysqlProxy =  yield $this->getMysqlProxy('master_slave');
//        $result = yield  $mysqlProxy->insert(self::$tableName)->set($data)->go();
//        return $result;
//    }
//
//    // 通过Task，同步执行MySQL查询（连接池）
//    public function syncMySQLPoolTask($tableName,$field='*',$where=null)
//    {
//        $dbTask = $this->getObject(DbTask::class);
//        $data     = yield $dbTask->syncMySQLPool($tableName,$field,$where);
//        return $data;
//    }
//
//    // 通过Task，同步执行MySQL查询（代理）
//    public function actionSyncMySQLProxyTask($tableName,$field='*',$where=null)
//    {
//        $demoTask = $this->getObject(DbTask::class);
//        $data     = yield $demoTask->syncMySQLProxy($tableName,$field,$where);
//        return $data;
//    }


}
