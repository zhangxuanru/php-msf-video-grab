<?php  $this->layout('Public/Header',$staticOption ) ?>
<body id="indexid">
<div id="metcmsbox">
<div id="top"> 
	<div class="topnbox" style="width: 99%;">
    <div class="floatr">
		<div class="top-r-box">
		<div class="top-right-boxr">
			<div class="top-r-t">
				<ol class="rnav">
					<li class="list">您好 <a href="index.php/admin/home/pwd" id="mydata" target="main" title="编辑 admin" class='tui'>admin</a></li>
					<li class="line">|</li>
					<li class="list"><a target="_top" onclick="{if(confirm('确定退出吗?')){window.location='index.php/admin/login/loginout';return true;}return false;}" href="javascript:;" id="outhome" title="退出" class='tui'>退出</a></li>
					<li class="line">|</li>
					<li class="list"><a href="javascript:;" id="kzqie" title="切换到窄版">窄版</a></li>
                    <li id="langcig" class="list langli">
					    <a id="cache" href="#">清理缓存</a>
						<span>|</span>
						<a href="index.php/admin/api_login/qq_login" target="_blank">绑定QQ</a>						<div class="langlist" style="display:none;"></div>
					</li>
					

				</ol>
				<div class="clear"></div>
			</div>
		</div>
		<div></div>
		<div class="nav">
            <ul id="topnav">
                                <li id="metnav_1" class="list">
					<a href="javascript:;" id="nav_1" class="onnav" hidefocus="true">
					<span class="c1"></span>
					<p>快捷导航</p>
					</a>
				</li>
				                <li id="metnav_10" class="list">
					<a href="javascript:;" id="nav_10"  hidefocus="true">
					<span class="c2"></span>
					<p>内容管理</p>
					</a>
				</li>
				                <li id="metnav_37" class="list">
					<a href="javascript:;" id="nav_37"  hidefocus="true">
					<span class="c3"></span>
					<p>优化推广</p>
					</a>
				</li>    
				                <li id="metnav_20" class="list">
					<a href="javascript:;" id="nav_20"  hidefocus="true">
					<span class="c5"></span>
					<p>网站设置</p>
					</a>
				</li>
				
            </ul>
		</div>
		</div>
    </div>
    <div class="floatl">
	    <a href="" hidefocus="true" id="met_logo"><img src="<?php echo $static_url; ?>/statics/base/images/logoen.gif" alt="phpci企业网站管理系统" title="phpci企业网站管理系统" /></a>
	</div>
	</div>
</div>
<div id="content" style="width: 99%;">
    <div class="floatl" id="metleft">
		<div class="floatl_box">
	    <div class="nav_list" id="leftnav">
			<div class="fast">
				<a target="_blank" href="" id="qthome" title="网站首页">网站首页</a>
			</div>
                        <ul  id="ul_1">
										<li><a href="/index/main" id="nav_1_2" target="main" class="on" title="系统信息" hidefocus="true">系统信息</a></li>
  			           </ul>
                        <ul style="display:none;" id="ul_10">
										<li ><a href="/index/content" id="nav_10_58" target="main"  title="内容管理" hidefocus="true">内容管理</a></li>

			       					<li ><a href="/index/recycle" id="nav_10_59" target="main"  title="内容回收站" hidefocus="true">内容回收站</a></li>
			       			</ul>
                        <ul style="display:none;" id="ul_37">
									 
			       					<li ><a href="/expand/" id="nav_37_13" target="main"  title="友情链接" hidefocus="true">友情链接</a></li>
			       					<li ><a href="/expand/add" id="nav_37_63" target="main"  title="站内广告" hidefocus="true">站内广告</a></li>
			       					 
			       			</ul>
                        
                        <ul style="display:none;" id="ul_20">
 			       					<li ><a href="/user/admin" id="nav_20_5" target="main"  title="管理员管理" hidefocus="true">管理员管理</a></li>
			       					<li ><a href="/expand/backup" id="nav_20_35" target="main"  title="数据备份" hidefocus="true">数据备份</a></li>
			       			</ul>


			       			    <ul style="display:none;" id="ul_51">
										<li ><a href="/video" id="nav_51_58" target="main"  title="内容管理" hidefocus="true">视频列表</a></li>
			       					 
			       					<li ><a href="/cate" id="nav_51_59" target="main"  title="分类管理" hidefocus="true">分类管理</a></li>

			       					<li ><a href="/cate" id="nav_51_60" target="main"  title="标签管理" hidefocus="true">标签管理</a></li>

			       					<li ><a href="/comment" id="nav_51_60" target="main"  title="标签管理" hidefocus="true">评论管理</a></li>
			       			</ul>


			       				    <ul style="display:none;" id="ul_52">
										<li ><a href="/user" id="nav_52_58" target="main"  title="内容管理" hidefocus="true">会员列表</a></li>
			       					 
			       					<li ><a href="/user/" id="nav_52_59" target="main"  title="分类管理" hidefocus="true">下载需求列表</a></li> 
			       			</ul>


			       				    <ul style="display:none;" id="ul_53">
										<li ><a href="/user" id="nav_53_58" target="main"  title="内容管理" hidefocus="true">评论列表</a></li>
			       					 
			       					<li ><a href="/user/" id="nav_53_59" target="main"  title="分类管理" hidefocus="true">添加评论</a></li> 
			       			</ul>

             

	    </div>
		<div class="claer"></div>
	
		<div class="left_footer"><div class="left_footer_box"><a href="http://www.phpci.com" target="_blank">我要提建议</a></div></div>
		
		</div>
	</div>
    <div class="floatr" id="metright">
        <div class="iframe">
		    <div class="min"><iframe frameborder="0" id="main" name="main" src="/index/main" scrolling="no"></iframe></div>
		</div>
    </div>
	<div class="clear"></div>
	</div>
</div>

<script src="<?php echo $static_url; ?>/statics/base/js/metinfo.js" type="text/javascript"></script>

<script type="text/javascript">
	function showLeftNav(idName)
	{
		$("#leftnav").find('ul').hide();
		var id = "ul_"+idName;
		$("#"+id).show();
	}
</script>
<?php $this->insert('Public/Footer',$staticOption ) ?>


