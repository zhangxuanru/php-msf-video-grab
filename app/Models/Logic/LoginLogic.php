<?php
/**
 * 登录 逻辑层
 * Created by PhpStorm.
 * User: zxr
 * Date: 2017/12/22
 * Time: 14:21
 */
namespace App\Models\Logic;
use App\Models\Model\GrabAdminModel;
use App\Library\Helper\Csrf;

use \PG\MSF\Session\Session;
use App\Library\Helper\Redis;

class LoginLogic extends BaseLogic
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
     * 检查登录数据
     * @param $data
     */
    public function checkLoginData($data)
    {
        $data = array_filter($data);
        if(empty($data)){
              return false;
        }
        if(!isset($data['csrfToken']) || !isset($data['userName']) || !isset($data['passWd'])){
           throw  new \Exception("非法操作!");
        }
        if(empty($data['csrfToken']) || empty($data['userName']) || empty($data['passWd'])){
            throw  new \Exception("非法操作!");
        }
        $csrF = $this->getObject(Csrf::class);
        $ret = yield $csrF->inspectCsrFToken($data['csrfToken']);
        if( $ret == false){
            throw  new \Exception("恶意操作!");
        }
        return $ret;
    }

    /**
     * 验证用户名与密码
     * @param string $userName
     * @param string $passWd
     */
    public function inspectUserPass($userName='',$passWd='')
    {
        $passWord = $this->generatingCipher($passWd);
        $where['userName'] = ['symbol' => '=','value' => $userName ];
        $where['passwd']   = ['symbol' => '=','value' => $passWord ];
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
        $passWord = $pass.$this->key;
        return md5($passWord);
    }


    /**
     * 设置登录状态
     * @param array $userInfo
     */
    public function setLogin($userInfo = [])
    {
        $session = $this->getObject(Session::class);
        $redis = $this->getObject(Redis::class);
        yield $session->set($this->sesUserKey,json_encode($userInfo));
        $sessionId  = $session->sessionId;
        $sesUserIdKey = sprintf($this->sesUserIdKey,$userInfo['id']);
        yield $redis->getRedisInstance()->set($sesUserIdKey,$sessionId);
        $csrF = $this->getObject(Csrf::class);
        yield $csrF->delAllCsrF();
        //记录登录日志
     }

    /**
     * 检查登录状态并获取登录用户信息
     * @throws \Exception
     */
    public function checkLoginUser()
    {
        $session = $this->getObject(Session::class);
        $redis = $this->getObject(Redis::class);
        $userInfo = yield $this->getLoginUser();
        if(empty($userInfo)){
            throw new \Exception("请登录");
        }
        $sesUserIdKey = sprintf($this->sesUserIdKey,$userInfo['id']);
        $sesId = yield $redis->getRedisInstance()->get($sesUserIdKey);
        $sessionId  = $this->getContext()->getUserDefined('sessionId');
        if($sessionId != $sesId){
            yield $session->delete($this->sesUserKey);
            throw new \Exception("你的帐号在别处登录，请你重新登录！！！");
        }
        return $userInfo;
    }

    /**
     * 获取当前登录用户
     * @return array|mixed
     */
    public function getLoginUser()
    {
        $session = $this->getObject(Session::class);
        $userInfoStr  = yield  $session->get($this->sesUserKey);
        if(empty($userInfoStr)){
             return [];
        }
        $userInfo = json_decode($userInfoStr,true);
        return $userInfo;
    }


    /**
     * 退出登录
     * @return \Generator
     */
    public function delLogin()
    {
        $session = $this->getObject(Session::class);
        yield $session->delete($this->sesUserKey);
   }


    public function  destroy()
    {
        $this->objPool = null;
        parent::destroy();
    }


}
