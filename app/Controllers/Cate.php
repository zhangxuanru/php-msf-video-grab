<?php
/**
 * 视频分类管理
 *
 * @author strive965432@gmail.com
 * @copyright zxr Technology Co.,Ltd.
 */

namespace App\Controllers;

use App\Library\Tool\Picture;

class Cate extends Base{
    public function actionIndex()
    {
        $this->display();
    }

    /**
     * [actionUpload 上传logo图片]
     * @return [type] [description]
     */
    public function actionUpload()
    {
        //print_r($this->getContext()->getInput()->getAllPostGet());
        //print_r($this->getContext()->getInput()->getFile('imgFile'));
        $imgFile = $this->getContext()->getInput()->getFile('imgFile');
        if(!empty($imgFile)){
            try{
                $fileName = 'logo'.strrchr($imgFile['name'],'.');
                $pictureObj = new Picture();
                $ret =  $pictureObj->uploadPicture('logo',$imgFile['tmp_name'],$fileName);
                if($ret['code'] != 200 ){
                    throw new \Exception($ret['msg'], 1);
                }
                $ret['error'] = 0;
                $this->outputJson($ret);
            }catch(\Exception $e){
                $ret = ['error' => $e->getCode(),'message' => $e->getMessage(),'url'=>''];
                $this->outputJson($ret);
            }
        }
    }

    /**
     * 保存网站基本设置
     */
    public function actionSave()
    {
        $postData = $this->getContext()->getInput()->getAllPostGet();
        $error = ['code' => '500','msg' => '数据为空'];
        if(empty($postData) || empty($postData['site_webname'])){
            $this->outputJson($error);
            return false;
        }
        $configPath =  $this->configPath;
        if(file_exists($configPath)){
            file_put_contents($configPath,'<?php $siteInfo ='. var_export($postData,true).';');
        }else{
            $error['msg'] = '配置文件不存在';
            $this->outputJson($error);
            return false;
        }
        $error['code'] = 200;
        $error['msg'] = '保存成功';
        $this->outputJson($error);
    }

    /**
     * 销毁,解除引用
     */
    public function destroy()
    {

    }

}

