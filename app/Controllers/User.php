<?php
/**
 * 管理员
 *
 * @author strive965432@gmail.com
 * @copyright zxr Technology Co.,Ltd.
 */

namespace App\Controllers;


class User extends Base{
    /**
     * 角色
     * @var array
     */
    public $role = array(
              '超级管理员'
        );

    /**
     * 列表页
     */
    public function actionIndex()
    {
        $aoData = $this->getContext()->getInput()->post('aoData');
        if(empty($aoData)){
            $this->display();
            return true;
        }
        $search = $this->getContext()->getInput()->getAllGet();
        $pageInfo  =  $this->parseAoData($aoData);
        $adminList = yield $this->getAdminLogicInstance()->getAllData('',$search,$pageInfo);
        $role  = $this->role;
        foreach($adminList as $key => $val){
            $val['addDate'] = date('Y-m-d H:i:s',$val['addDate']);
            $vRole = $val['role'];
            $val['roleName'] = isset($role[$vRole]) ? $role[$vRole] : '';
            $adminList[$key] = $val;
        }
        $count = yield $this->getAdminLogicInstance()->getCount($search);
        $this->pageJson($pageInfo['sEcho'],$count,$adminList);
    }


    /**
     * [actionAdd 添加|编辑管理员]
     * @return [type] [description]
     */
    public function actionAdd()
    {
        $id = $this->getContext()->getInput()->get('id');
        $list = [];
        if(!empty($id)){
            $list = yield $this->getAdminLogicInstance()->getUserDataById($id);
        }
        $this->assign('data',$list);
        $this->display();
    }


    /**
     * 保存管理员
     * @return bool
     */
    public function actionSave()
    {
        $postData = $this->getContext()->getInput()->getAllPost();
        $postData = array_map('trim',$postData);
        $postData = array_filter($postData);
        $msgData  = ['code'=> '-1', 'message' => 'error'];
        try{
            $id = isset($postData['id']) ?  $postData['id'] : 0;
            if(empty($id) &&  (empty($postData['passwd'])  || empty($postData['repasswd']))){
                $msgData = ['code'=> '-1', 'message' => '请输入密码'];
                $this->outputJson($msgData);
                return true;
            }
            if(isset($postData['passwd']) && $postData['passwd'] != $postData['repasswd']){
                $msgData = ['code'=> '-1', 'message' => '两次密码输入不一致'];
                $this->outputJson($msgData);
                return true;
            }
            if(!empty($postData['passwd'])){
               $postData['passwd'] = $this->getAdminLogicInstance()->generatingCipher($postData['passwd']);
               unset($postData['repasswd']);
            }
            if( $id > 0){
                unset($postData['id']);
                yield $this->getAdminLogicInstance()->updateById($id,$postData);
                $message = '编辑成功!';
            }else{
                //判断用户名是否存在
                $exists =  yield $this->getAdminLogicInstance()->checkUserExists($postData['userName']);
                if($exists > 0 ){
                    $msgData = ['code'=> '-1', 'message' => '用户已存在'];
                    $this->outputJson($msgData);
                    return false;
                }
                $postData['addDate'] = time();
                yield $this->getAdminLogicInstance()->saveData($postData);
                $message = '添加成功!';
            }
            $msgData = ['code'=> '1', 'message' => $message];
            $this->outputJson($msgData);
        }catch(\Exception $e){
            $msgData['message'] = $e->getMessage();
            $this->outputJson($msgData);
        }
    }

    /**
     * [actionDel 删除管理员]
     * @return [type] [description]
     */
    public function actionDel()
    {
        $idStr = $this->getContext()->getInput()->get('id');
        $msgData = ['code'=> '-1','message' => ''];
        try{
            if(empty($idStr)){
                throw new \Exception("参数错误", 1);
            }
            $idArr = explode(",",$idStr);
            $ret =  yield $this->getAdminLogicInstance()->batchDelByIdList($idArr);
            if($ret == false){
                throw new \Exception("删除失败", 1);
            }
            $msgData = ['code'=> '1','message' => '删除成功'];
        }catch(\Exception $e){
            $msgData['message'] = $e->getMessage();
        }
        $this->outputJson($msgData);
    }

    /**
     * 修改状态
     */
    public function actionUpdateStart()
    {
        $id = $this->getContext()->getInput()->get('id');
        $type = $this->getContext()->getInput()->get('type');
        $msgData = ['code'=> '-1','message' => ''];
        $setData = array( 'disable' => $type);
        try{
            yield $this->getAdminLogicInstance()->updateById($id,$setData);
            $msgData = ['code'=> '1','message' => '修改成功'];
        }catch(\Exception $e){
            $msgData['message'] = $e->getMessage();
        }
        $this->outputJson($msgData);
    }

}


