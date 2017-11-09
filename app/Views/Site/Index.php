<?php  $this->layout('Public/MainHeader',$staticOption ) ?> 

<script type="text/javascript">
/*ajax执行*/
var lang = 'cn';
var metimgurl='<?php echo $static_url; ?>/statics/base/images/';
var depth='index.php/admin/upload/ico_upload?uid=1&loginbase=46dd0ee33de60c025f3654a295455ed2';
var uid='';
var adminpwd='';
var postParams ={};
$(document).ready(function(){
	ifreme_methei();
});
var upload_json="/site/upload";
//var file_manager_json="statics/lib/kind/php/file_manager_json.php";
var file_manager_json="/site/fileList";

KindEditor.ready(function(K) {
	var editor = K.create('textarea[name="txxxx"]', {
		allowFileManager : true
	});
	var editor = K.editor({
		allowFileManager : true
	});
	K('#image').click(function() {
		editor.loadPlugin('image', function() {
			editor.plugin.imageDialog({
				imageUrl : K('#site_logo').val(),
					clickFn : function(url, title, width, height, border, align) {
						K('#site_logo').val(url);
						$("#img").attr("src",url);
						editor.hideDialog();
                        postParams = {
                            'url'    : url,
                            'title'  : title,
                            'width'  : width,
                            'height' : height,
                            'border' : border,
                            'align'  : align
                        };
                        jQuery.each(postParams,function(key,val){
                            var html = `<input name="${key}" type="hidden" class="text" value="${val}" />`;
                            $(".imglogo").append(html);
                        });
					}
			});
		});
	});
});


$(document).ready(function(){
	ifreme_methei();
});
</script> 
</head>
<body>
<script type="text/javascript" src="<?php echo $static_url; ?>/statics/base/js/uploadify/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript" src="<?php echo $static_url; ?>/statics/base/js/uploadify/swfobject.js"></script>
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
	<div class="position">快捷导航 > <a href="/site/">基本设置</a></div>
	</div>
	<div class="clear"></div>
	</div>
 
<div style="clear:both;"></div>

<form method="POST" name="myform" action="/site/save" target="_self">
<div class="v52fmbx_tbmax">
<div class="v52fmbx_tbbox">
<div class="v52fmbx">
	
	<div class="metsliding_box metsliding_box_1">
		<div class="v52fmbx_dlbox">
		<dl>
			<dt>网站名称：</dt>
			<dd>
			    <input name="site_webname" type="text" class="text" value="视界互动传媒" />
			</dd>
		</dl>
		</div>   
		
		<div class="v52fmbx_dlbox imglogo">
		<dl>
			<dt>网站LOGO：</dt>
			<dd>
			    <input name="site_logo" type="text" id="site_logo" class="text" value="<?php echo $static_url; ?>/statics/base/images/logoen.gif" />
				<input type="button" id="image" class="bnt_public" value="图片上传"/>
				<img id="img" width="80" height="50" src="<?php echo $static_url; ?>/statics/base/images/logoen.gif">
			</dd>
		</dl>
		</div>  
		
	</div>
	<h3 class="v52fmbx_hr metsliding" sliding="2">搜索引擎优化设置</h3>
	<div class="metsliding_box metsliding_box_2">
		<div class="v52fmbx_dlbox">
		<dl>
			<dt>网站标题：</dt>
			<dd>
			<input name="seo_title" type="text " class="text gen" value="致力于南京品牌网站建设,网站制作,网页设计,SEO服务-视界互动传媒" /> 
			<span class="tips">多个关键词请用竖线|隔开，建议3到4个关键词。</span>
			</dd>
		</dl>
		</div>
		<div class="v52fmbx_dlbox">
		<dl>
			<dt>网站关键词：</dt>
			<dd>
			<input name="seo_key" type="text " class="text gen" value="南京网站建设,南京网站制作,南京SEO,南京网页设计,南京网络公司" />
			<span class="tips">多个关键词请用竖线|隔开，建议3到4个关键词。</span>
			</dd>
		</dl>
		</div>
		<div class="v52fmbx_dlbox">
		<dl>
			<dt>网站描述：</dt>
			<dd>
			<textarea name="seo_desc" class="textarea gen">视界互动传媒专注于高品质网站建设以及搜索引擎优化,是南京地区专业提供最权威品牌网站建设,网页设计,网站制作,SEO服务的网络公司!</textarea>
			<span class="tips">100字以内</span>
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
