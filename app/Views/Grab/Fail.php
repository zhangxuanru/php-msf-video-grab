<?php  $this->insert('Public/Head',$staticOption); ?>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> <a href="/"> 首页</a> <span class="c-gray en">&gt;</span> 抓取管理 <span class="c-gray en">&gt;</span> 抓取列表  <span class="c-gray en">&gt;</span> 失败详情  <a class=" btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont btn-refresh">&#xe68f;</i></a></nav>
<div class="pd-20">
    <table class="table">
        <tbody>
        <tr>
            <th class="text-r" width="80">ID：</th>
            <td><?php echo isset($data['id']) ? $data['id'] : ''; ?></td>
        </tr>
        <tr>
            <th class="text-r">抓取地址：</th>
            <td><?php echo isset($data['grab_address']) ? $data['grab_address'] : ''; ?></td>
        </tr>
        <tr>
            <th class="text-r">失败原因：</th>
            <td><?php echo isset($data['content']) ? $data['content'] : ''; ?></td>
        </tr>
        </tbody>
    </table>
</div>
<!--_footer 作为公共模版分离出去-->
<?php $this->insert('Public/Footer',$staticOption); ?>
</body>
</html>