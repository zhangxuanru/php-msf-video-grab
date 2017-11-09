<?php  $this->layout('Public/MainHeader',$staticOption ) ?>
</head>
<body>
<div class="metinfotop">
        <div class="position">内容管理 > <a href="content.html">视频模块</a> > <a href='news.html'>分类管理 </a></div>
</div>
<div class="clear"></div>
    <div class="v52fmbx_tbmax">
        <div class="v52fmbx_tbbox">
            <div class="clear"></div>
            <table cellpadding="2" cellspacing="1" class="table">
                <tr>
                    <td colspan="8" class="centle" style=" height:20px; line-height:30px; font-weight:normal; padding-left:10px;">
                        <div style="float:left;">
                            <a href="news_add.html">+新增</a>
                            <span style="font-weight:normal; color:#999; padding-left:10px;">排序数值越大越靠前</span>
                        </div>
                    </td>
                </tr>
                <tr id="list-top">
                    <td width="30" class="list">选择</td>
                    <td width="250" class="list list_left">分类名称</td>
                    <td width="80" class="list">添加时间</td>
                    <td width="70" class="list" style="padding:0px; text-align:center;">操作</td>
                </tr>
                <tr class="mouse click">
                    <td class="list-text">
                        <input name='id[]' type='checkbox' id="id" value='30' />
                        <input name="data[id][]" type="hidden" value="30" />
                    </td>
                    <td class="list-text alignleft">&nbsp;&nbsp;<a href="show-30" title='预览：测试' target="_blank">测试</a></td>
                      <td class="list-text color999">2014-11-27 15:40:25</td>
                     <td class="list-text">
                        <a href="news_edit.html">编辑</a>&nbsp;&nbsp;
                        <a href="javascript:;" onclick="{if(confirm('确定删除吗?')){window.location='index.php/admin/news/del?id=30&page=1';return true;}return false;}">删除</a>
                    </td>
                </tr>
                <tr>
                    <td class="all"><input name="chkAll" type="checkbox" id="chkAll" onclick=CheckAll(this.form) value="checkbox" /></td>
                    <td class="all-submit" colspan="7" style="padding:5px;">
                       <input type='submit' value='删除' class="submit li-submit" onclick="{if(confirm('确定删除吗?')){document.myform.action='index.php/admin/news/delsome?&page=1';return true;}return false;}" />
                    </td>
                </tr>
            </table>
        </div>
    </div>
	 <?php $this->insert('Public/Footer',$staticOption ) ?>
</body>
</html>