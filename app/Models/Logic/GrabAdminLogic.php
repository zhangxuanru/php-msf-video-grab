<?php
/**
 * 后台管理员 逻辑层
 * Created by PhpStorm.
 * User: zxr
 * Date: 2017/12/22
 * Time: 14:21
 */
namespace App\Models\Logic;
use App\Models\Model\GrabAdminModel;

class GrabAdminLogic extends BaseLogic
{
    private  $field = 'id,userName,passwd,phone,email,role,is_del,addDate,disable';
    private  $key = 'php';
    public   $sesUserKey = 'grab-admin-user';
    public   $sesUserIdKey = 'admin-user-%s';

    public function __construct()
    {
        parent::__construct();
        $this->objPool = $this->getObject(GrabAdminModel::class);
    }

    /**
     * 根据条件获取管理员数据
     * @param string $field
     * @param array $where
     * @param array $pageInfo
     * @return array
     */
    public function getAllData($field='',$where = [],$pageInfo=[])
    {
        $where = $this->parseSearch($where);
        $condition = $this->parseCondition($pageInfo);
        if(empty($field)){
             $field =  $this->field;
        }
        $where['is_del'] = ['symbol' => '=','value' => 0];
        try{
            $data =  yield $this->objPool->getList($field,$where,$condition);
        }catch(\Exception $e){
               return [];
        }
        return $data;
    }

    /**
     * 获取总数
     * @param $where
     */
    public function getCount($where = array())
    {
        $where = $this->parseSearch($where);
        $where['is_del'] = ['symbol' => '=','value' => 0];
        $data =  yield $this->objPool->getCount($where);
        return $data;
    }


    /**
     * 检查用户是否存在
     * @param $userName
     */
    public function checkUserExists($userName)
    {
        $where['userName'] = ['symbol' => '=','value' => $userName ];
        $data =  yield $this->objPool->getCount($where);
        return $data;
    }

    /**
     * 根据用户ID获取用户信息
     * @param $userId
     */
    public function getUserDataById($userId)
    {
        $where['id'] = ['symbol' => '=','value' => $userId ];
        $data =  yield $this->objPool->getList($this->field,$where);
        return isset($data[0]) ?  $data[0] : $data;
    }

    /**
     * 生成用户密码
     * @param $pass
     * @return string
     */
    public function generatingCipher($pass)
    {
        if(empty($pass)){
            return '';
        }
        $passWord = $pass.$this->key;
        return md5($passWord);
    }



    public function  destroy()
    {
        $this->objPool = null;
        parent::destroy();
    }


}
