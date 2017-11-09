<?php  $this->layout('Public/MainHeader',$staticOption ) ?> 
<script type="text/javascript">
var gettagspath="";
var upload_json="<?php echo $static_url; ?>/statics/lib/kind/php/upload_json.php";
var file_manager_json="<?php echo $static_url; ?>/statics/lib/kind/php/file_manager_json.php";
KindEditor.ready(function(K) {
	var editor = K.create('textarea[name="info"]', {
		urlType : 'domain',
		allowFileManager : true
	});
	K('#image').click(function() {
		editor.loadPlugin('image', function() {
			editor.plugin.imageDialog({
				imageUrl : K('#pic').val(),
					clickFn : function(url, title, width, height, border, align) {
						K('#pic').val(url);
						$("#img").attr("src",url);
						editor.hideDialog();
					}
			});
		});
	});
	K('#insertfile').click(function() {
		editor.loadPlugin('insertfile', function() {
			editor.plugin.fileDialog({
				fileUrl : K('#files').val(),
				clickFn : function(url, title) {
					K('#files').val(url);
						editor.hideDialog();
				}
			});
		});
	});
});

</script>
<script type="text/javascript">
$(document).ready(function(){
	ifreme_methei();
});
</script>
</head>

<body>
	<div class="metinfotop">
	<div class="position">简体中文：内容管理 > <a href="content.html">内容管理</a> > <a href="video.html">视频管理</a> > 添加内容</div>
	<div class="return"><a href="javascript:;" onClick="location.href='javascript:history.go(-1)'">&lt;&lt;返回</a></div>
	</div>
	<div class="clear"></div>
	
<form  method="post" name="myform" action="index.php/admin/video/add">
<div class="v52fmbx_tbmax">
<div class="v52fmbx_tbbox">
<div class="v52fmbx">
		<div class="v52fmbx_dlbox">
		<dl>
			<dt>所属栏目：</dt>
			<dd>
			    <select name="cid">
				<option value="0">选择分类</option>
								<option value="75">客片欣赏</option>
								</select>
				<input name="istop" type="checkbox" class="checkbox" value="1" >&nbsp;置顶&nbsp;&nbsp;&nbsp;
				<input name="isnice" type="checkbox" class="checkbox" value="1" >&nbsp;推荐&nbsp;&nbsp;&nbsp;
				<input name="status" type="checkbox" class="checkbox" value="1" checked="checked">&nbsp;审核&nbsp;&nbsp;&nbsp;
			</dd>
		</dl>
		</div>
		
		<div class="v52fmbx_dlbox">
		<dl>
			<dt>标题：</dt>
			<dd>
				<input name="title" id="title" type="text" class="text nonull" value="">
			</dd>
		</dl>
		</div>
		
		
		<div class="v52fmbx_dlbox">
		<dl>
			<dt>缩略图：</dt>
			<dd>
				<input name="pic" id="pic" type="text" class="text" value="">
				<input type="button" id="image" class="bnt_public" value="图片上传"/>
				<img id="img" width="80" height="50" src="<?php echo $static_url; ?>/statics/base/images/nopic.gif">
			</dd>
		</dl>
		</div>
		
		
		<div class="v52fmbx_dlbox">
		<dl>
			<dt>视频：</dt>
			<dd>
				<input name="fileurl" id="files" type="text" class="text" value="">
				<input type="button" id="insertfile" class="bnt_public" value="上传文件" />
				<span class="tips">不推荐上传视频文件，把视频上传到优酷，获取视频文件地址拷贝到此处</span>
			</dd>
		</dl>
		</div>
		
		<h3 class="v52fmbx_hr metsliding" sliding="3">参数设置</h3>
 
		<div class="v52fmbx_dlbox">
		<dl>
			<dt>发布人：</dt>
			<dd>
			    <input name="author" type="text" class="text mid" size="10" value="admin">
			    排序：
				<input name="ordnum" type="text" class="text mid" size="10" value="0">
				<span class="tips">数字越大越靠前</span>
			</dd>
		</dl>
		</div>

		<div class="v52fmbx_dlbox">
		<dl>
			<dt>点击次数：</dt>
			<dd>
				<input name="hits" type="text" class="text mid" size="10"  value="0">
				<span class="tips">点击次数越多，热门信息中排名越靠前</span>
				来源：
				<input name="comefrom" type="text" class="text" value="">
				<span class="tips">文章来自哪个网站？</span>
			</dd>
		</dl>
		</div>
		
		
		<div class="v52fmbx_dlbox">
		<dl>
			<dt>发布时间：</dt>
			<dd>
				<input name="addtime" type="text" class="text" value="2015-03-10 16:39:33">
				<span class="tips">当前时间为：2015-03-10 16:39:33 注意不要改变格式。</span>
			</dd>
		</dl>
		</div>
		
		<div class="v52fmbx_dlbox">
		<dl>
			<dt>详细内容：</dt>
			<dd>
				<textarea class="ckeditor" name="info" style="width:98%;"></textarea>
			</dd>
		</dl>
		</div>
		<h3 class="v52fmbx_hr metsliding" sliding="3">搜索引擎优化设置(seo)</h3>
		<div class="v52fmbx_dlbox">
		<dl>
			<dt>页面Title：</dt>
			<dd>
				<input name="seotitle" type="text" class="text" value="">
				<span class="tips">为空则使用SEO参数设置中设置的title构成方式</span>
			</dd>
		</dl>
		</div>
		
		<div class="v52fmbx_dlbox">
		<dl>
			<dt>关键词：</dt>
			<dd>
				<input name="seokey" type="text" class="text" size="40"  value="">
				<span class="tips">用于搜索引擎优化,多个关键词请用&quot;,&quot;隔开</span>
			</dd>
		</dl>
		</div>
		
		<div class="v52fmbx_dlbox">
		<dl>
			<dt>简短描述：</dt>
			<dd>
				<textarea name="seodesc" class="textarea gen" cols="60" rows="5" ></textarea>
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
	
	<div class="v52fmbx_dlbox v52fmbx_mo" style="height:200px">
		 <dl>
			<dt></dt>
			<dd></dd>	
		</dl>	
	</div>
	
</div>
</div>
</div>      
</form>
<?php $this->insert('Public/Footer',$staticOption ) ?>
</body>
</html>