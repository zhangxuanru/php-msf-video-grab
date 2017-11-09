<?php
/**
 * 欢迎
 *
 * @author camera360_server@camera360.com
 * @copyright Chengdu pinguo Technology Co.,Ltd.
 */

namespace App\Controllers;

class Index extends Base
{
    public function actionIndex()
    {
         $this->display();
    }

   public function actionMain()
   {
      $this->display();
   }

   public function actionContent()
   {
       $this->display();
   }
 
   public function actionNav()
   {
      $this->display();
   }
   public function actionRecycle()
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

