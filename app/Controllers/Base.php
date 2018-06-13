<?php
/**
 * Base
 *
 * @author strive965432@gmail.com
 * @copyright Chengdu pinguo Technology Co.,Ltd.
 */ 

namespace App\Controllers;

use PG\MSF\Controllers\Controller; 
use App\Library\Options\StaticOption;


use App\Models\Logic\GrabLogic;
use App\Models\Logic\CateGoryLogic;
use App\Models\Logic\VideoCountLogic;
use App\Models\Logic\VideoLogLogic;
use App\Models\Logic\VideoInfoLogic;
use App\Models\Logic\GrabNavLogic;
use App\Models\Logic\GrabAdminLogic;
use App\Models\Logic\LoginLogic;

class Base extends Controller
{
   protected $assignData = [];

   private   $controllerName;

   private   $methodName;

   public function __construct($controllerName, $methodName)
   {
      $this->controllerName = $controllerName;
      $this->methodName     = $methodName;
      parent::__construct($controllerName,$methodName);
      yield $this->__init(); 
   }


  /**
   * [__init 所有ACTION执行之前都要先执行这个方法]
   * @return [type] [description]
   */
  public function __init()
  { 
      yield  $this->__checkLogin();
      yield $this->assignData();
      $this->__staticInit();
  }

    /**
     * 页面发送静态资源
     *
     * @param null $action
     */
    protected function __staticInit($action = null)
    {
      if (empty($action)) {
          $method_prefix =  $this->getConfig()->get('http.method_prefix','action');      
          $method = str_replace($method_prefix,'',$this->methodName);
          $action = strtolower($this->controllerName.'.'.$method);
        }  
        $static = StaticOption::options($action);      
        $static_url = $this->getConfig()->get('constant.STATIC_URL');
        $assign = [
            'static_url' => $static_url,
            'static'     => $static
        ];
        $this->assign($assign);
        $this->assign('staticOption',$assign);
  }

    /**
     * 检查是否登录
     * @return \Generator
     */
    public function __checkLogin()
    {
        $data = yield $this->getLoginLogicInstance()->getLoginUser();
        if($this->controllerName == 'Login' && !empty($data)){
            $this->render('/');
        }
        if($this->controllerName == 'Login'){
            return true;
        }
        try{
             yield $this->getLoginLogicInstance()->checkLoginUser();
        }catch(\Exception $e){
            $scriptStr = "<script>alert('".$e->getMessage()."');top.location.href='/login';</script>";
            $this->output($scriptStr);
        }
    }

    /**
     * 默认发送页面的数据
     */
    public function assignData()
    {
        $data = [];
        $userInfo = yield $this->getLoginLogicInstance()->getLoginUser();
        $data['userInfo'] = $userInfo;
        $this->assign('assignData',$data);
    }

    /**
     * 七牛域名
     */
    public function getQiniuDomain()
    {
        $assign = [
            'imagesDomain' => $this->getConfig()->get('constant.QINIU_IMAGES_DOMAIN'),
            'videoDomain'  => $this->getConfig()->get('constant.QINIU_VIDEO_DOMAIN')
        ];
        $this->assign($assign);
    }
 
    /**
     * @param $key
     * @param null $value
     */
    protected function assign($key,$value=null)
    {
        if(is_array($key)){
              $this->assignData = array_merge($this->assignData,$key);
        }else{
            $this->assignData[$key] = $value;
        }
    }

    /**
     * @param $view
     * @param null $data
     */
    protected function display($view = null ,$data=array())
    {
        if($this->assignData){
            $data = array_merge($this->assignData,$data);
        }
        $this->outputView($data,$view);
   }

    /**
     * @return mixed|\stdClass
     */
    public function getGrabLogicInstance()
    {
       return $this->getObject(GrabLogic::class);
    }

    /**
     * 实例化分类模型
     * @return mixed
     */
   protected function getCateModelInstance()
   {
       $cateModel =   $this->getObject(CateGoryLogic::class);
       return $cateModel;
   }

 /**
     * @return mixed|\stdClass
     */
    public function getVideoCountLogicInstance()
    {
       return $this->getObject(VideoCountLogic::class);
    }

    /**
     * @return mixed|\stdClass
     */
    public function getVideoLogLogicInstance()
    {
        return $this->getObject(VideoLogLogic::class);
    }

    /**
     * @return mixed|\stdClass
     */
    public function getVideoInfoLogicInstance()
    {
        return $this->getObject(VideoInfoLogic::class);
    }

    /**
     * @return mixed|\stdClass
     */
    public function getNavLogicInstance()
    {
        return $this->getObject(GrabNavLogic::class);
    }

    /**
     * @return mixed|\stdClass
     */
    public function getAdminLogicInstance()
    {
        return $this->getObject(GrabAdminLogic::class);
    }

    /**
     * @return mixed|\stdClass
     */
    public function getLoginLogicInstance()
    {
        return $this->getObject(LoginLogic::class);
    }


    /**
     * 分共分页返回的JSON数据
     * @param int $sEcho
     * @param int $count
     * @param array $data
     */
    public function pageJson($sEcho=0,$count=0,$data=[])
    {
        $json_data = array(
            'sEcho' => $sEcho,
            'iTotalRecords' => $count,
            'iTotalDisplayRecords' => $count,
            'aaData' => $data
        );
        $this->outputJson($json_data);
    }


    /**
     * jquery dataselect 获取参数解析
     */
    public function parseAoData($aoData)
    {
        $ret = [];
        if(empty($aoData)){
            return $ret;
        }
         $aoDataArr = json_decode($aoData,true) ;
         $aoColDataArr = array_column($aoDataArr,'value','name');
         $ret['sEcho']  = $aoColDataArr['sEcho'];
         $ret['offset']   = $aoColDataArr['iDisplayStart'];
         $ret['limit'] = $aoColDataArr['iDisplayLength'];
         $ret['sortIndex'] = $aoColDataArr['iSortCol_0'];
         $ret['sort']  = $aoColDataArr['sSortDir_0'];
         $fieldKey  = 'mDataProp_'. $ret['sortIndex'];
         $ret['field'] = $aoColDataArr[$fieldKey] ;
         return $ret;
    }

    /**
     * [render 跳转]
     * @return [type] [description]
     */
    public function render($url)
    {
      $this->output("<script>top.location.href='".$url."'</script>" );
      return true;
    }


    /**
     * 销毁,解除引用
     */
    public function destroy()
    {
        $this->assignData = [];;
        parent::destroy();
    }

}

