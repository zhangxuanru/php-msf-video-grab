<?php
/**
 * 视频分类管理
 *
 * @author strive965432@gmail.com
 * @copyright zxr Technology Co.,Ltd.
 */

namespace App\Controllers;

class Cate extends Base{

  public  static $pid  = 0;
    /**
     * 列表页
     */
    public function actionIndex()
    {
        $id = $this->getContext()->getInput()->get('id');
        $aoData = $this->getContext()->getInput()->post('aoData');
        if(empty($aoData)){
            $this->assign('id',$id);
             $this->display();
             return true;
        }
        self::$pid = $id;
        $pageInfo  =  $this->parseAoData($aoData);
        $cateList  = yield $this->getCateModelInstance()->getCateDataByPid($id,$pageInfo);
        $count     = yield $this->getCateModelInstance()->geCateCountByPid($id);
        $this->pageJson($pageInfo['sEcho'],$count,$cateList);
    }

    /**
     * [actionDel 删除分类]
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
            $ret =  yield $this->getCateModelInstance()->batchDelByIdList($idArr);
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
     * [actionAdd 添加|编辑分类页]
     * @return [type] [description]
     */
    public function actionAdd()
    {
       $id = $this->getContext()->getInput()->get('id');  
       $list = [];
       if(!empty($id)){
           $list = yield $this->getCateModelInstance()->getCateGoryDataById($id);
        }
        $this->assign('data',$list);
        $this->display();
    }

    /**
     * 保存分类数据
     * @return bool
     */
    public function actionSave()
    {
        $postData = $this->getContext()->getInput()->getAllPost();
        $postData = array_map('trim',$postData);
        $msgData = ['code'=> '-1', 'message' => 'error'];
        $postData['pid'] =  self::$pid;
        try{
            $id = isset($postData['id']) ?  $postData['id'] : 0;
            if( $id > 0){
                 unset($postData['id']);
                 yield $this->getCateModelInstance()->updateById($id,$postData);
                 $message = '编辑成功!';
            }else{
                 yield $this->getCateModelInstance()->saveData($postData);
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
     * 获取父ID获取子分类，并返回JSON
     */
    public function actionGetSubClass()
    {
        $id = $this->getContext()->getInput()->get('id');
        $data = [];
        if(empty($id)){
            $this->outputJson($data);
            return true;
        }
        $data = yield $this->getCateModelInstance()->getCateDataByPidData($id);
        $this->outputJson($data);
    }

}


