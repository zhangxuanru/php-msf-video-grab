<?php
/**
 * 栏目管理
 *
 * @author strive965432@gmail.com
 * @copyright zxr Technology Co.,Ltd.
 */

namespace App\Controllers;
use App\Library\Tool\UrlEncrypt;

class Nav extends Base{

    /**
     * 栏目 列表
     */
    public function actionIndex()
    {
        //获取顶级导航
         $navList = yield  $this->getNavLogicInstance()->getAllData();
         foreach($navList as $key => $val){
            $cat_id  = $val['cat_id'];
            if(empty($cat_id)){
                continue;
            }
            $catIdArr = explode(',',$cat_id);
            $catData  = yield $this->getCateModelInstance()->getCateGoryDataById($catIdArr);
            $catList  = array_column($catData,'category_name');
            $catIdData = array_column($catData,'id');
            $navList[$key]['catData'] = implode(', ',$catList);
            $navList[$key]['catIdData'] =  implode(',',$catIdData);
        }
        $data = [];
        foreach($navList as $key => $val){
             if(empty($val['pid'])){
                 $data[] = $val;
                   foreach($navList as $k => $v){
                       if($v['pid'] == $val['id']){
                           $data[] = $v;
                           unset($navList[$k]);
                       }
                   }
                 unset($navList[$key]);
             }
        }
        unset($navList);
        $regionList = array(
            1 => '导航栏',
            2 => '左侧',
            3 => '主区域',
            4 => '右侧'
        );
        $this->assign('navList',$data);
        $this->assign('regionList',$regionList);
        $this->display();
    }


    /**
     * 添加导航
     */
    public function actionAddNav()
    {
        $id = $this->getContext()->getInput()->get('id');
        $row = [];
        if(!empty($id)){
             $row = yield $this->getNavLogicInstance()->getNavDataByIdData($id);
        }
        //获取顶级导航
        $navList = yield  $this->getNavLogicInstance()->getNavDataByPidData(0);
        //获取分类
        $data  = yield $this->getCateModelInstance()->getCateDataByPid();
        foreach($data as $key => $val){
            $subCatList = yield $this->getCateModelInstance()->getCateDataByPidData($val['id']);
            if(empty($subCatList)){
                unset($data[$key]);
                continue;
            }
            $data[$key]['subCatList'] = $subCatList;
        }
        $this->assign('row',$row);
        $this->assign('data',$data);
        $this->assign('navList',$navList);
        $this->display();
    }

    /**
     * 保存导航数据
     */
    public function actionNavsave()
    {
        $postData = $this->getContext()->getInput()->getAllPost();
        $urlObj = $this->getObject(UrlEncrypt::class);
        $msgData = ['code'=> '-1', 'message' => 'error'];
        try{
            $id = isset($postData['id']) ? $postData['id'] : 0;
            $postData['cat_id'] = implode(',',$postData['cat_id']);
            if( $id > 0 ){
                if(strpos($postData['url'],'/?c=') === false){
                    $postData['url'] .= '/?c='.$urlObj->encrypt_url($id);
                }
                unset($postData['id']);
                yield $this->getNavLogicInstance()->updateById($id,$postData);
                $message = '编辑成功!';
            }else{
                $postData['addDate'] = time();
                $insertId = yield $this->getNavLogicInstance()->saveData($postData);
                $upDate['url'] = $postData['url'].'/?c='.$urlObj->encrypt_url($insertId);
                yield $this->getNavLogicInstance()->updateById($insertId,$upDate);
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
            $ret =  yield $this->getNavLogicInstance()->batchDelByIdList($idArr);
            if($ret == false){
                throw new \Exception("删除失败", 1);
            }
            $msgData = ['code'=> '1','message' => '删除成功'];
        }catch(\Exception $e){
            $msgData['message'] = $e->getMessage();
        }
        $this->outputJson($msgData);
    }


}


