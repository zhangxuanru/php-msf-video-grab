<?php  $this->layout('Public/MainHeader',$staticOption ) ?>  
</head>
<body>

<script type="text/javascript">
function metreturn(url){
	if(url){
		location.href=url;
	}else if($.browser.msie){
		history.go(-1);
	}else{
		history.go(-1);
	}
}
</script>
	
	
	<div class="metinfotop">
	<div class="position">简体中文 > 内容管理 > <a href="content.html">内容管理</a> > <a href='recycle.html'>内容回收站</a></div>
	<div class="return"><a href="javascript:;" onClick="location.href='javascript:history.go(-1)'">&lt;&lt;返回</a></div>
	</div>
	<div class="clear"></div>
	</div>
<div class="v52fmbx_tbmax">
<div class="v52fmbx_tbbox">
<div class="clear"></div>
	<table cellpadding="2" cellspacing="1" class="table">
		<tr>
			<td class="centle" colspan="4" style=" height:20px; line-height:30px; font-weight:normal;">	
				<span style=" float:left;font-weight:normal; color:#999; padding-left:10px;">仅支持（新闻、产品、下载、图片）模块的内容。</span>
				<div class="formright">
				<select name="module" id="module" onChange="changes($(this));" style="position:relative; top:2px;">
					<option value="index.php/admin/recycle/index">所有栏目</option>
					<option  value="index.php/admin/recycle/index?cid=44">聚贤简述</option>
<option  value="index.php/admin/recycle/index?cid=45">艺术品展示</option>
<option  value="index.php/admin/recycle/index?cid=55">　├ 企业形象网站</option>
<option  value="index.php/admin/recycle/index?cid=74">　├ 品牌网站设计</option>
<option  value="index.php/admin/recycle/index?cid=96">　├ 互动专题设计</option>
<option  value="index.php/admin/recycle/index?cid=97">　├ 大型门户网站</option>
<option  value="index.php/admin/recycle/index?cid=101">　├ cccc</option>
<option  value="index.php/admin/recycle/index?cid=68">招聘事宜</option>
<option  value="index.php/admin/recycle/index?cid=98">　├ 网站地图</option>
<option  value="index.php/admin/recycle/index?cid=100">在线留言</option>
				</select>
				<form method="POST" name="search" action="index.php/admin/recycle/index">
					<input name="keyword" type="text" class="text100" id="searchtext" value="" />				
					<input type="submit" name="searchsubmit" value="搜索" class="bnt_pinyin"/>
				</form>
				</div>
			</td>
		</tr>
       <tr id="list-top">
            <td class="list" width="30">选择</td>
            <td class="list list_left" width="350">标题</td>
            <td class="list" width="100" style="padding:0px; text-align:center;">所属栏目</td>
			<td class="list" width="100" style="padding:0px; text-align:center;">操作</td>
      </tr>
		<form name="myform" method="post" id="myform">
		  
        <tr class="mouse click">
            <td class="list-text" width="30"><input name="id[]" type="checkbox" id="id" value="30" /></td>
            <td class="list-text alignleft" width="350">测试</td>
            <td class="list-text color999" width="100" style="padding:0px; text-align:center;" title="艺术品展示">艺术品展示</td>
		    <td class="list-text " width="100" style="padding:0px; text-align:center;">
				<a href="javascript:;" onclick="{if(confirm('确定还原吗?')){window.location='index.php/admin/recycle/recoveryone?id=30&&page=1';return true;}return false;}">还原</a>&nbsp;&nbsp;
				<a href="javascript:;" onclick="{if(confirm('确定删除吗?')){window.location='index.php/admin/recycle/del?id=30&&page=1';return true;}return false;}">删除</a>
			</td>
        </tr>
		  
		<tr>
	      <td class="list" width="30"><input name="chkAll" type="checkbox" id="chkAll" onclick=CheckAll(this.form) value="checkbox"></td>
			<td class="all-submit" colspan="3" style="padding:5px;">
			      <div class="page_list">
				  <input type='submit' value='删除' class="submit" onclick="{if(confirm('确定删除吗?')){document.myform.action='index.php/admin/recycle/delsome?&page=1';return true;}return false;}"/>
				  <input type='submit' value='还原' class="submit" onclick="{if(confirm('确定还原吗?')){document.myform.action='index.php/admin/recycle/recoverysome?&page=1';return true;}return false;}"/> 
				  
				  <input type='submit' value='还原所有' class="submit" onclick="{if(confirm('确定还原所有吗?')){document.myform.action='index.php/admin/recycle/recovery';return true;}return false;}" /> 
				  <input type='submit' value='删除所有' class="submit" onclick="{if(confirm('确定删除所有吗?')){document.myform.action='index.php/admin/recycle/clear';return true;}return false;}" /> 
				  </div>
			</td>
        </tr>      
        </form>
		<tr>
			<td colspan="4" class="page_list" style="padding:5px 0px;">
			<form method='POST' action='index.php/admin/recycle/index?&page=1'>
			<style>.digg4 a{ border:1px solid #ccdbe4; padding:2px 8px 2px 8px; background:#fff; background-position:50%; margin:2px; color:#666; text-decoration:none;}
			.digg4 a:hover { border:1px solid #999; color:#fff; background-color:#999;}
			.digg4 a:active {border:1px solid #000099; color:#000000;}
			.digg4 span.current { padding:2px 8px 2px 8px; margin:2px; text-decoration:none;}
			.digg4 span.disabled { border:1px solid #ccc; background:#fff; padding:1px 8px 1px 8px; margin:2px; color:#999;}
		    </style>
			<div class="digg4">
			<span class="disabled" style="font-family: Tahoma, Verdana;"><b>«</b></span><span class="disabled" style="font-family: Tahoma, Verdana;">‹</span><span class="current">1</span><span class="disabled" style="font-family: Tahoma, Verdana;">›</span><span class="disabled" style="font-family: Tahoma, Verdana;"><b>»</b></span>  共1条 			转到<input name='page' class='page_input' value="1" />页 
			<input type="submit" value=" go " class="bnt_pinyin"/>
			</form>
			</div>
			</td>
		</tr>
</table>
</div>
</div>
<div class="clear"></div>
<?php $this->insert('Public/Footer',$staticOption ) ?>
</body>
</html>
