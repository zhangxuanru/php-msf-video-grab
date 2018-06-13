<?php
/**
 * 登录
 *
 * @author strive965432@gmail.com
 * @copyright zxr Technology Co.,Ltd.
 */

namespace App\Controllers;

use App\Library\Helper\Csrf;

class Login extends Base{

    public function actionIndex()
    {
        $csrF = $this->getObject(Csrf::class);
        $csrFToken =  yield $csrF->generatingCSRFToken();
        $this->assign('csrfToken',$csrFToken);
        $this->display();
    }

    /**
     * 登录
     */
    public function actionSignIn()
    {
        $postData =  $this->context->getInput()->getAllPost();
        $postData = array_map('trim',$postData);
        $msgData = ['code'=> '-1','message' => '非法操作!'];
        try{
             yield $this->getLoginLogicInstance()->checkLoginData($postData);
             $userInfo =  yield $this->getLoginLogicInstance()->inspectUserPass($postData['userName'],$postData['passWd']);
             if(empty($userInfo)){
                throw new \Exception("用户不存在");
             }
            yield $this->getLoginLogicInstance()->setLogin($userInfo);
            $msgData = ['code'=> '1','message' => '登录成功！！！'];
            $this->outputJson($msgData);
        }catch(\Exception $e){
            $msgData['message'] = $e->getMessage();
            $this->outputJson($msgData);
            return true;
        }
    }

    /**
     * 退出登录
     * @return \Generator
     */
    public function actionLogOut()
    {
        yield $this->getLoginLogicInstance()->delLogin();
        $this->render("/login");
    }


    /**
     * [actionAdd 修改个人资料]
     * @return [type] [description]
     */
    public function actionInfo()
    {
        $data = yield $this->getLoginLogicInstance()->getLoginUser();
        $this->assign('data',$data);
        $this->display();
    }

    /**
     * 更新用户信息
     */
    public function actionUpdateInfo()
    {
        $data = yield $this->getLoginLogicInstance()->getLoginUser();
        $postData = $this->getContext()->getInput()->getAllPost();
        $postData = array_map('trim',$postData);
        if(empty($postData['repasswd']) && !empty($postData['passwd'])){
            $msgData = ['code'=> '-1', 'message' => '请设置新密码'];
            $this->outputJson($msgData);
            return true;
        }
        $postData['passwd'] = $this->getAdminLogicInstance()->generatingCipher($postData['passwd']);
        if(!empty($postData['passwd']) &&  $postData['passwd'] != $data['passwd']){
            $msgData = ['code'=> '-1', 'message' => '原密码错误'];
            $this->outputJson($msgData);
            return true;
        }
        if(!empty($postData['repasswd'])){
            $postData['passwd'] = $this->getAdminLogicInstance()->generatingCipher($postData['repasswd']);
            unset($postData['repasswd']);
        }
        unset($postData['repasswd']);
        if(empty($postData['passwd'])){
              unset($postData['passwd']);
        }
         $ret =  yield $this->getAdminLogicInstance()->updateById($data['id'],$postData);
         $msgData = ['code'=> '1', 'message' => '修改失败'];
         if($ret){
            $msgData = ['code'=> '1', 'message' => '修改成功'];
            yield $this->getLoginLogicInstance()->delLogin();
        }
        $this->outputJson($msgData);
    }




}


