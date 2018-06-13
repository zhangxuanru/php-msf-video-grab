<?php
/**
 * 图片上传与下载相关
 *
 * Created by PhpStorm.
 * User: zxr  strive@965432@gmail.com
 * Date: 2017/11/6
 * Time: 14:28
 */
namespace App\Library\Tool;

use cdcchen\net\curl\HttpRequest;
use Flexihash\Exception;
use PG\MSF\Base\Core;

/**
 * Class Child
 * @package PG\MSF\Base
 */
class Picture
{
    private $img_upload_service = '';

    private  $img_upload_token;

    const CONNECT_TIMEOUT = 3;

    public function __construct()
    {
        $Core = new Core();
        $this->img_upload_service = $Core->getConfig()->get('constant.IMG_UPLOAD_SERVICE');
        $this->img_upload_token = $Core->getConfig()->get('constant.IMG_UPLOAD_TOKEN');
    }

    /**
     * 上传图片
     *
     * @param $type
     * @param $filename
     * @param string $method
     * @return bool|\cdcchen\net\curl\HttpResponse|\cdcchen\net\curl\Response
     * @throws \cdcchen\net\curl\RequestException
     */
    public  function uploadPicture($type,$filename,$fileName='',$method='post')
    {
      try{
        $request = new HttpRequest();
        $request->setUrl($this->img_upload_service);
        $request->setMethod($method);
        $request->setConnectTimeout(self::CONNECT_TIMEOUT);
        $request->setTimeout(self::CONNECT_TIMEOUT);
        $data = array('type' => $type,'token' => $this->img_upload_token,'fileName' => $fileName);
        $request->setData($data);
        $request->addFile('imgFile',$filename,'image/jpeg');
        $response = $request->send();
        $request->clearFiles();
     }catch(\Exception $e){
          $ret = ['code' => $e->getCode(),'msg' => $e->getMessage()];
          return $ret;
     }
        if(empty($response)){
             throw  new \Exception('返回数据为空，请重新上传');
        }
        if( $response->getStatus() != 200 ){
            throw  new \Exception('连接服务失败，请重新上传');
        }
        $ret = json_decode($response->getContent(),true);
        if($ret['code'] != 200 ){
            throw  new \Exception($ret['msg']);
        }
         return $ret;
        // $request->setFormat(HttpRequest::FORMAT_JSON);
       // echo $response->getContent();
    }

    /**
     * 销毁,解除引用
     */
    public function destroy()
    {

    }

}
