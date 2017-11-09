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

	<div class="position">简体中文 > 网站设置 > <a href="expand_backup.html">数据备份</a></div>


	</div>
	<div class="clear"></div>



</div>

<div class="stat_list">
	<ul>
		<li class="now"><a href="expand_backup.html" title="备份">备份</a></li>
		<li ><a href="expand_backup_recovery.html" title="恢复">恢复</a></li>
	</ul>
</div>

<div style="clear:both;"></div>


<script type="text/javascript">
	function metdatabase(my){
		var nxt=my.next('span.tips');
		nxt.empty();
		nxt.append('<img src="statics/base/images/loadings.gif" style="position:relative; top:3px;" />正在备份，请耐心等待...');
		location.href=my.attr('url');
		nxt.empty();
		return false;
	}
</script>
<div class="v52fmbx_tbmax">
<div class="v52fmbx_tbbox">
<div class="v52fmbx">
	<h3 class="v52fmbx_hr metsliding"><span style="float:right;">建议每月备份</span>备份数据（不含上传的文件）</h3>
	<div class="v52fmbx_dlbox">
	<dl>
		<dt>数据库备份：</dt>
		<dd>
			<input type="submit" url="index.php/admin/expand_backup/backup" class="submit" value="备份" onclick="return metdatabase($(this))" />
			<span class="tips"></span>
			<a href="expand_backup_lists.html" title="自定义备份数据表" style="margin-left:10px;">自定义备份数据表</a>
		</dd>
	</dl>
	</div>
	<h3 class="v52fmbx_hr metsliding"><span style="float:right;">一般不用备份</span>备份用户上传的文件（图片、文档等）</h3>
	<div class="v52fmbx_dlbox">
	<dl>
		<dt>上传文件夹备份：</dt>
		<dd class="detabes">
			<input type="submit" url="index.php/admin/expand_backup/uploadimg" class="submit" value="备份" onclick="return metdatabase($(this))" />
			<span class="tips"></span>
		</dd>
	</dl>
	</div>
	<h3 class="v52fmbx_hr metsliding"><span style="float:right;">一般在搬家时用</span>备份数据和文件（数据库、用户文件、程序文件）</h3>
	<div class="v52fmbx_dlbox v52fmbx_mo">
	<dl>
		<dt>全部备份：</dt>
		<dd class="detabes">
			<input type="submit" url="index.php/admin/expand_backup/allfile" class="submit" value="压缩整站" onclick="return metdatabase($(this))" />
			<span class="tips"></span>
		</dd>
	</dl>
	</div>
	
	<div class="v52fmbx_dlbox v52fmbx_mo">
	<dl>
		<dt></dt>
		<dd class="detabes">
			<span class="tips">由于PHP执行时间和内存的限制，备份巨大的数据库、文件可能不太容易成功。如果你的数据库、文件非常大，你可能需要直接从命令行执行相关命令；或者如果你没有相应权限，你可能需要服务器管理员为你做这件事。</span>
			<span class="tips"></span>
		</dd>
	</dl>
	</div>
	
	
</div>
</div>
</div>

	</form>
	<?php $this->insert('Public/Footer',$staticOption ) ?>
</body>
</html>
