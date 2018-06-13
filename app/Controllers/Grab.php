<?php
/**
 * 后台抓取模块
 *
 * @author camera360_server@camera360.com
 * @copyright Chengdu zxr Technology Co.,Ltd.
 */

namespace App\Controllers;

class Grab extends Base
{
    /**
     * 后台抓取列表数据源， 分页请求
     */
    public function actionIndex()
    {
        $aoData = $this->getContext()->getInput()->post('aoData');
        if(empty($aoData)){
            $videoType = $this->getGrabLogicInstance()->getVideoType();
            $this->assign('videotype',$videoType);
            $this->display(); 
            return true;
        }
        //列表数据
        $search = $this->getContext()->getInput()->getAllGet();
        $pageInfo =  $this->parseAoData($aoData);
        $data   = yield $this->getGrabLogicInstance()->getInfoListData('',$pageInfo,$search);
        $count  = yield $this->getGrabLogicInstance()->getInfoCount($search);
        $this->pageJson($pageInfo['sEcho'],$count,$data);
    }

    /**
     * [actionAdd 添加抓取任务]
     * @return [type] [description]
     */
    public function actionAdd()
    {
       $data  = yield $this->getCateModelInstance()->getCateDataByPid();
       $list = array_column($data,'category_name','id');
       $videoType = $this->getGrabLogicInstance()->getVideoType();
       $typeArr = $this->getGrabLogicInstance()->getGrabType();
       $this->assign('videotype', $videoType);
       $this->assign('catList',$list);
       $this->assign('typeList',$typeArr);
       $this->display();
    }
 
    /**
     * 保存抓取任务
     */
    public function actionSave()
    {
      $postData = $this->getContext()->getInput()->getAllPost();
      $postData = array_map('trim',$postData);
      $msgData = ['code'=> '-1', 'message' => 'error'];
      try{
           yield $this->getGrabLogicInstance()->saveData($postData);
           $msgData = ['code'=> '1', 'message' => '添加成功!'];
      }catch(\Exception $e){
             $msgData['message'] = $e->getMessage();
         }
      $this->outputJson($msgData);
    }

    /**
     * [actionDel 删除抓取任务]
     * @return [type] [description]
     */
    public  function actionDel()
    {
        $idStr = $this->getContext()->getInput()->get('id');
        $msgData = ['code'=> '-1','message' => ''];
        try{
            if(empty($idStr)){
                throw new \Exception("参数错误", 1); 
            }
             $idArr = explode(",",$idStr);
             $ret =  yield $this->getGrabLogicInstance()->batchDelByIdList($idArr);
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
     * 手工执行抓取命令
     */
    public function actionImplement()
    {
        $id = $this->getContext()->getInput()->post('id');
        $type = $this->getContext()->getInput()->post('type');
        if(empty($id) || intval($id) == 0 ){
            $msgData = [ 'code' => '-1','msg' => '参数错误'];
            $this->outputJson($msgData);
            return false;
        }
        $log = '/data/video/download/grab_'.date('Ymd').'.log';
        if($type == '1'){
            exec('nohup  php '.ROOT_PATH.'/console.php  grabexec/run -id='.$id.'  >> '.$log.'  2>&1 &');
        }else{
            exec('nohup  php '.ROOT_PATH.'/console.php  grabexec/page -id='.$id.'  >> '.$log.'  2>&1 &');
        }
        sleep(5);
        exec('nohup  php '.ROOT_PATH.'/console.php  hls/checkhls >> '.$log.'  2>&1 &');
        $msgData = [ 'code' => '1','msg' => '后台正在执行中'];
        $this->outputJson($msgData);
    }


    /**
     * 单页面视频执行详情页
     */
    public function actionPage()
    {
        $id = $this->getContext()->getInput()->get('info_id'); 
        $aoData = $this->getContext()->getInput()->post('aoData');
        if(empty($aoData)){
            $this->assign('info_id',$id);
            $this->display();
            return true;
        }  
        try{  
            $id = intval($id);
            //获取info表数据
            $infoData = yield $this->getGrabLogicInstance()->getInfoDataById($id);
            if(empty($infoData)){
                throw  new \Exception('暂无数据');
            }
            $pageInfo = $this->parseAoData($aoData);
            $where = $this->getContext()->getInput()->getAllGet(); 
            //获取统计表数据
            $countData = yield  $this->getVideoCountLogicInstance()->getVideoCountList('',$pageInfo,$where);
            $count = yield  $this->getVideoCountLogicInstance()->getVideoCount($where); 
            //获取分类数据
            $cateList  = yield $this->getCateModelInstance()->getFormatCateGoryData();
            //格式化数据
            $list =  $this->getGrabLogicInstance()->pageFormatData($infoData,$countData,$cateList);
            $this->pageJson($pageInfo['sEcho'],$count,$list);
        }catch(\Exception $e){
            $this->pageJson(0,0,[]);
        } 
    }

    /**
     * 查看单页抓取 具体成功和失败的列表
     */
    public function actionPageList()
    {
        $search = $this->getContext()->getInput()->getAllGet();
        $aoData = $this->getContext()->getInput()->post('aoData');
        $type = $search['type'];
        $this->assign('id',$search['id']);
        $this->assign('execId',$search['execid']);
        $this->assign('type',$type);
        if(empty($aoData) && !empty($type)){
           $this->display();
           return true;
        }
        //如果是失败的情况，需要单独显示页面，因为没有视频ID
        if(empty($aoData) && empty($type)){
            $this->display('Grab/FailPage');
            return true;
        }
        try{
            $pageInfo = $this->parseAoData($aoData);
            $logData  = yield $this->getVideoLogLogicInstance()->getLogListData($search,$pageInfo);
            $count    = yield $this->getVideoLogLogicInstance()->getLogCount($search);
            if(empty($type)){
                $this->pageJson($pageInfo['sEcho'],$count,$logData);
                return true;
            }
            $videoIdList = array_column($logData,'video_id');
            $videoData = yield  $this->getVideoInfoLogicInstance()->getVideoData($videoIdList);
            $videoData = yield  $this->getVideoInfoLogicInstance()->formatArray($videoData,$logData);
            $this->pageJson($pageInfo['sEcho'],$count,$videoData);
        }catch(\Exception $e){
            $this->pageJson(0,0,[]);
        }
    }

    /**
     * 显示错误详情
     */
    public  function  actionFail()
    {
        $id   = $this->getContext()->getInput()->get('id');
        $info_id = $this->getContext()->getInput()->get('info_id');
        if(empty($id)){
            $data = yield $this->getVideoLogLogicInstance()->getLogDataByInfoId($info_id);
        } else{
           $data = yield $this->getVideoLogLogicInstance()->getLogDataById($id);
        }
        $this->assign('data',$data);
        $this->display();
    }

}

