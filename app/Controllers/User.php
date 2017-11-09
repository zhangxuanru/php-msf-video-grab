<?php
/**
 * 视频模块
 *
 * @author camera360_server@camera360.com
 * @copyright Chengdu pinguo Technology Co.,Ltd.
 */

namespace App\Controllers;

class User extends Base
{
    public function actionIndex()
    {
         $this->display();
    } 

    public function actionAdd()
    {
      $this->display();
    }  

    public function actionAdmin()
    {
      $this->display();
    }  
    
    /**
     * 销毁,解除引用
     */
    public function destroy()
    {

    }

}

