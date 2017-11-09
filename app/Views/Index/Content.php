<?php  $this->layout('Public/MainHeader',$staticOption ) ?> 
<style>
img{ behavior: url('<?php echo $static_url; ?>/statics/base/images/iepngfix.htc'); }
</style>

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
	<div class="position">简体中文 > 内容管理 > <a href="content.html">内容管理</a></div>
	<div class="return"></div>
	</div>
	<div class="clear"></div>
	<script type="text/javascript">
	$("html",parent.document).find('.returnover').remove();
	</script>
</div>

<div class="v52fmbx_tbmax v52fmbx_tbmaxmt">
<div class="v52fmbx_tbbox">
 
<div class="metv5box">
	<ul class="columnlist">
        
				<li class="contlist">
			<div class="box">
				<a href='news.html'>
					<img src="<?php echo $static_url; ?>/statics/base/images/metv5/tubiao_2.png" width='64' height='64' />
					<h2>文章模块</h2>
				</a>
			</div>
		</li>
        		<li class="contlist">
			<div class="box">
				<a href='prod.html'>
					<img src="<?php echo $static_url; ?>/statics/base/images/metv5/tubiao_3.png" width='64' height='64' />
					<h2>产品模块</h2>
				</a>
			</div>
		</li>
         <li class="contlist">
			<div class="box">
				<a href='/video' class="NavCate" data-id="51">
					<img src="<?php echo $static_url; ?>/statics/base/images/metv5/tubiao_4.png" width='64' height='64' />
					<h2>视频模块</h2>
				</a>
			</div>
		</li>
        		<li class="contlist">
			<div class="box">
				<a href='case.html'>
					<img src="<?php echo $static_url; ?>/statics/base/images/metv5/tubiao_5.png" width='64' height='64' />
					<h2>案例模块</h2>
				</a>
			</div>
		</li>
        		<li class="contlist">
			<div class="box">
				<a href='expand_book.html'>
					<img src="<?php echo $static_url; ?>/statics/base/images/metv5/tubiao_7.png" width='64' height='64' />
					<h2>查看留言</h2>
				</a>
			</div>
		</li>

		 <li class="contlist">
			<div class="box">
				<a href='/grab'>
					<img src="<?php echo $static_url; ?>/statics/base/images/metv5/tubiao_4.png" width='64' height='64' />
					<h2>抓取模块</h2>
				</a>
			</div>
		</li>


		 <li class="contlist">
			<div class="box">
				<a href='/User' class="NavCate" data-id="52">
					<img src="<?php echo $static_url; ?>/statics/base/images/metv5/tubiao_6.png" width='64' height='64' />
					<h2>用户模块</h2>
				</a>
			</div>
		</li>

		 <li class="contlist">
			<div class="box">
				<a href='/comment' class="NavCate" data-id="53">
					<img src="<?php echo $static_url; ?>/statics/base/images/metv5/tubiao_6.png" width='64' height='64' />
					<h2>评论模块</h2>
				</a>
			</div>
		</li>



         
</ul>
</div>
<div class="clear"></div>
</div>
</div>
<script type="text/javascript">
$(".NavCate").click(function(){
    var data_id = $(this).attr('data-id');
    parent.showLeftNav(data_id);
})

$('.contmorehver').hover(
	function () {
		$(this).find('div.contmore').show();
	},
	function () {
		$(this).find('div.contmore').hide();
	}
);
function metHeight(group,type) {
	tallest = 0;
	group.each(function() {
		thisHeight = $(this).height();
		if(thisHeight > tallest) {
			tallest = thisHeight;
		}
	});
	if(type==1){
		group.each(function(){
			if($(this).outerHeight(true)<tallest){
				var ht = (tallest - $(this).outerHeight(true))/2;
				$(this).css('padding-top',ht+'px');
				$(this).css('padding-bottom',ht+'px');
			}
		});
	}else{
		group.height(tallest);
		group.each(function(){
			var h = tallest - $(this).find('.img').outerHeight(true);
			var x = h - $(this).find('.title').outerHeight(true);
			if(x>0){
				var ht = (x/2)+3;
				$(this).find('.title').css('padding-top',ht+'px');
				$(this).find('.title').css('padding-bottom',ht+'px');
			}
		});
	}
}
metHeight($('.box'));
metHeight($('.contlist .text'),1);
</script>
<div class="clear"></div>
<?php $this->insert('Public/Footer',$staticOption ) ?>
</body>
</html>