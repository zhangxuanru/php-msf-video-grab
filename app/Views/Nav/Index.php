<?php  $this->insert('Public/Head',$staticOption); ?>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> <a href="/"> 首页</a>
	<span class="c-gray en">&gt;</span>
	<a href="/">系统管理</a>
	<span class="c-gray en">&gt;</span>
	栏目管理
	<a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a>
</nav>
<div class="page-container">
	<div class="cl pd-5 bg-1 bk-gray">
		<span class="l">
 		<a class="btn btn-primary radius" onclick="system_category_add('添加导航','/nav/addnav')" href="javascript:;"><i class="Hui-iconfont">&#xe600;</i> 添加栏目</a>
		</span>
		<span class="r">共有数据：<strong><?php echo count($navList); ?></strong> 条</span>
	</div>
	<div class="mt-20">
		<table class="table table-border table-bordered table-hover table-bg table-sort">
			<thead>
				<tr class="text-c">
					<th width="25"><input type="checkbox" name="" value=""></th>
					<th width="40">ID</th>
					<th width="40">排序</th>
					<th width="120">显示区域</th>
					<th width="120">栏目名称</th>
                    <th width="150">链接地址</th>
                    <th>分类数据</th>
					<th width="100">操作</th>
				</tr>
			</thead>
			<tbody>
               <?php foreach($navList as $key => $val){ ?>
				<tr class="text-c">
					<td><input type="checkbox" name="" value="<?php echo $val['id']; ?>"></td>
					<td><?php echo $val['id']; ?></td>
					<td><?php echo $val['sort']; ?></td>
                    <td><?php echo $regionList[$val['region']]; ?></td>
					<td class="text-l"><?php echo $val['pid'] > 0 ? '&nbsp;&nbsp;&nbsp;&nbsp;├&nbsp;'.$val['nav_name'] :$val['nav_name']; ?></td>
                    <td class="text-l"><?php echo $val['pid'] > 0 ? "" : $val['url']; ?></td>
                    <td class="text-l"><?php if(isset($val['catIdData']) && !empty($val['catIdData'])){ ?>
                        <a href="/video/catData/?catId=<?php echo $val['catIdData']; ?>"> <?php echo $val['catData']; ?></a>
                    <?php } ?>
                    </td>
					<td class="f-14"><a title="编辑" href="javascript:;" onclick="system_category_add('导航编辑','/nav/addnav/?id=<?php echo $val['id'] ?>')" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
				    <a title="删除" href="javascript:;" onclick="system_category_del(this,'<?php echo $val['id'] ?>')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a></td>
				</tr>
               <?php } ?>
			</tbody>
		</table>
	</div>
</div>

<!--_footer 作为公共模版分离出去-->
<?php $this->insert('Public/Footer',$staticOption); ?>

<script type="text/javascript">
/*系统-栏目-添加*/
function system_category_add(title,url,w,h){
	layer_show(title,url,w,h);
}
/*系统-栏目-删除*/
function system_category_del(obj,id){
	layer.confirm('确认要删除吗？',function(index){
		$.ajax({
			type: 'POST',
			url: '/nav/del/?id='+id,
			dataType: 'json',
			success: function(data){
				$(obj).parents("tr").remove();
				layer.msg(data.message, {icon: 1, time: 1000});
			},
			error:function(data) {
				console.log(data.msg);
			},
		});
	});
}
</script>
</body>
</html>