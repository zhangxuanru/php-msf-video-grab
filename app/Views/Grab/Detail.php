<?php  $this->insert('Public/Head',$staticOption); ?>
<body>
<div class="pd-20">
    <table class="table">
        <tbody>
        <tr>
            <th class="text-r" width="80">ID：</th>
            <td><?php echo $data['id']; ?></td>
        </tr>
        <tr>
            <th class="text-r">抓取地址：</th>
            <td><?php echo $data['grab_address']; ?></td>
        </tr>
        <tr>
            <th class="text-r">失败原因：</th>
            <td><?php echo $data['content']; ?></td>
        </tr>
        </tbody>
    </table>
</div>
<!--_footer 作为公共模版分离出去-->
<?php $this->insert('Public/Footer',$staticOption); ?>
</body>
</html>