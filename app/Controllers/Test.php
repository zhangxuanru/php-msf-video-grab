<?php
/**
 * 欢迎
 *
 * @author camera360_server@camera360.com
 * @copyright Chengdu pinguo Technology Co.,Ltd.
 */

namespace App\Controllers;

//use PG\MSF\Controllers\Controller;

class Test extends Base
{

    public function __construct($controllerName, $methodName)
   { 
      parent::__construct($controllerName,$methodName); 
   }



    public function actionIndex()
    {
        header("Access-Control-Allow-Origin: *");
        $this->output('abcdefg1234565y7');
    }

    public function actionSub()
    {
        $fp = '/data/video/download/How the White House Killed Two Presidents.en.srt';
        $content = file_get_contents($fp);
        $this->output($content);
    }





    public function actionBb()
    {
         $this->display();
    }
 
    public function actionAa()
    {

        $session = $this->getObject(\PG\MSF\Session\Session::class);
        $data = [];
        $data['old'] = yield $session->get('msf-session');
        yield $session->set('msf-session', date('Y-m-d H:i:s'));
        $data['new'] = yield $session->get('msf-session');
        $this->outputJson($data);

        //$this->output('aaa');
    }


    /**
     * 销毁,解除引用
     */
    public function destroy()
    {

    }
 
}

