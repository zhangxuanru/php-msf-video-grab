<?php  $this->layout('Public/Header',$staticOption ) ?>
<script type="text/javascript">
$(document).ready(function(){
	ifreme_methei();
});
</script>

</head>
<body>
	<div class="metinfotop">
	<div class="position">内容管理： <a href="expand_adcat.html">视频模块</a> > 分类管理</div>
 	</div>
	<div class="clear"></div>
   <div style="clear:both;"></div>
	
<form  method="post" name="myform" action="index.php/admin/expand_adcat/add">
<div class="v52fmbx_tbmax">
<div class="v52fmbx_tbbox">
<div class="v52fmbx">
		<div class="v52fmbx_dlbox">
		<dl>
			<dt>类别名称：</dt>
			<dd>
			    <input name="title" type="text" class="text nonull" value="">
			</dd>
		</dl>
		</div>
		
		<div class="v52fmbx_dlbox">
		<dl>
			<dt>排序：</dt>
			<dd>
				<input name="ordnum" type="text" class="text mid" value="0">
			</dd>
		</dl>
		</div>
		<div class="v52fmbx_dlbox v52fmbx_mo">
			<dl>
				<dt></dt>
				<dd>
					<input type="submit"  value="保存" class="submit" onclick="return Smit($(this),'myform')" />
				</dd>
			</dl>
		</div>
	</div>
	
</div>
</div>
</div>      
</form>
<?php $this->insert('Public/Footer',$staticOption ) ?>
</body>
</html>