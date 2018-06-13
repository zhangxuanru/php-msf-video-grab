<?php
/**
 * 后台视频模块
 *
 * @author camera360_server@camera360.com
 * @copyright Chengdu zxr Technology Co.,Ltd.
 */

namespace App\Controllers;

class Video extends Base
{
    /**
     * 后台视频列表数据源， 分页请求
     */
    public function actionIndex()
    {
        $aoData = $this->getContext()->getInput()->post('aoData');
        if(empty($aoData)){
            //获取父分类数据
            $catData =  yield $this->getCateModelInstance()->getCateDataByPidData(0);
            $videoType = $this->getGrabLogicInstance()->getVideoType();
            $this->assign('videotype',$videoType);
            $this->assign('catData',$catData);
            $this->display();
            return true;
        }
        //列表数据
        $search = $this->getContext()->getInput()->getAllGet();
        $pageInfo =  $this->parseAoData($aoData);
        if(!empty($search['classId'])){
             unset($search['classPid']);
        }
        if(!empty($search['classPid'])){
            $subCate = yield $this->getCateModelInstance()->getCateDataByPidData($search['classPid']);
            $search['classPid'] = array_column($subCate,'id');
        }
        $search['status'] = '1';
        if(isset($search['is_del']) && !empty($search['is_del'])){
              $search['status'] = '0';
        }
        $data   = yield $this->getVideoInfoLogicInstance()->getVideoListJoinData($search,$pageInfo);
        $count  = yield $this->getVideoInfoLogicInstance()->getVideoCount($search);
        $this->pageJson($pageInfo['sEcho'],$count,$data);
    }


    /**
     * 获取分类的视频数据，从导航页跳过来的
     */
    public function actionCatData()
    {
        $catId =   $this->getContext()->getInput()->get('catId');
        $aoData = $this->getContext()->getInput()->post('aoData');
        if(empty($aoData)){
            $videoType = $this->getGrabLogicInstance()->getVideoType();
            $this->assign('videotype',$videoType);
            $this->assign('catId',$catId);
            $this->display('/Video/Cate');
            return true;
        }
        //列表数据
        $search = $this->getContext()->getInput()->getAllGet();
        $pageInfo =  $this->parseAoData($aoData);
        $search['classPid'] = explode(',',$search['catId']);
        $search['status'] = '1';
        if(isset($search['is_del']) && !empty($search['is_del'])){
            $search['status'] = '0';
        }
        $data   = yield $this->getVideoInfoLogicInstance()->getVideoListJoinData($search,$pageInfo);
        $count  = yield $this->getVideoInfoLogicInstance()->getVideoCount($search);
        $this->pageJson($pageInfo['sEcho'],$count,$data);
    }


    /**
     * 删除数据
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
            $ret =  yield $this->getVideoInfoLogicInstance()->batchDelByIdList($idArr);
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
     * 设置视频推荐或者置顶
     */
    public function actionSetState()
    {
      $postData = $this->getContext()->getInput()->getAllPost();
     try{
         yield $this->getVideoInfoLogicInstance()->setState($postData['id'],$postData['type'],$postData['val']);
         $msgData = ['code'=> '1','message' => '设置成功'];
      }catch(\Exception $e){
         $msgData = ['code'=> '-1','message' => $e->getMessage()];
     }
      $this->outputJson($msgData);
    }

    /**
     * 编辑视频内容
     */
    public function actionEdit()
    {
        $info_id  = $this->getContext()->getInput()->get('info_id');
        $video_id = $this->getContext()->getInput()->get('vid');
        $data = yield $this->getVideoInfoLogicInstance()->getVideoDetailData($video_id,$info_id);
        $catData =  yield $this->getCateModelInstance()->getCateDataByPidData(0);
        $this->assign('data',$data);
        $this->assign('catData',$catData);
        $this->assign('video_id',$video_id);
        $this->assign('info_id',$info_id);
        $this->display();
    }

    /**
     * 保存视频数据
     */
    public function actionSave()
    {
        $postData = $this->getContext()->getInput()->getAllPost();
        try{
           yield $this->getVideoInfoLogicInstance()->updateVideoInfo($postData);
           $msgData = ['code'=> '1','message' => '设置成功'];
        }catch(\Exception $e){
            $msgData = ['code'=> '-1','message' => '保存失败'];
        }
        $this->outputJson($msgData);
    }

    /**
     * 单个视频执行结果详情页
     */
    public  function actionDetail()
    {
        $id = $this->getContext()->getInput()->get('info_id');
        $video_id = $this->getContext()->getInput()->get('vid');
       try{
           $data = yield $this->getVideoInfoLogicInstance()->getVideoDetailData($video_id,$id);
       }catch(\Exception $e){
           $data = [];
       }
        $this->getQiniuDomain();
        $this->assign('statusArr',$this->getGrabLogicInstance()->getGrabStatus());
        $this->assign('videoType',$this->getGrabLogicInstance()->getVideoType());
        $this->assign('data',$data);
        $this->display();
    }


}

