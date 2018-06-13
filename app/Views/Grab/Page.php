<?php  $this->insert('Public/Head',$staticOption); ?>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> <a href="/">首页</a> <span class="c-gray en">&gt;</span> <a href="/">抓取管理</a> <span class="c-gray en">&gt;</span> 抓取列表  <span class="c-gray en">&gt;</span> 抓取详情  <a class=" btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont btn-refresh">&#xe68f;</i></a></nav>
<div class="page-container">
    <div class="text-c"> 执行日期范围：
        <input type="text" onfocus="WdatePicker({ maxDate:'#F{$dp.$D(\'datemax\')||\'%y-%M-%d\'}' })" id="datemin" class="input-text Wdate" style="width:120px;">
        -
        <input type="text" onfocus="WdatePicker({ minDate:'#F{$dp.$D(\'datemin\')}',maxDate:'%y-%M-%d' })" id="datemax" class="input-text Wdate" style="width:120px;">
         <button type="submit" class="btn btn-success radius btn-search" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 搜索</button>
    </div>
    <div class="mt-20">
        <table class="table table-border table-bordered table-hover table-bg table-sort">
            <thead>
            <tr class="text-c">
                <th width="25"><input type="checkbox" name="select-all" value="0" id="select-all"></th>
                <th width="100">抓取说明</th>
                <th width="60">视频类别</th>
                <th width="50">分类</th>
                <th width="90">视频总数量</th>
                <th width="210">成功数</th>
                <th width="60">失败数</th>
                <th width="50">重复数</th>
                <th width="50">执行时间</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
<!--_footer 作为公共模版分离出去-->
<?php $this->insert('Public/Footer',$staticOption); ?>
</body>
</html>

<script type="text/javascript">
    $(function() {
         refreshDataTable();
        /**
         * 搜索事件
         */
        $(".btn-search").click(function () {
            $(".table-sort").dataTable().fnDestroy();
                 refreshDataTable();
        });
    })

    /**
     * dataselect 列表函数
     */
    function refreshDataTable() {
        var url = '/grab/page/?info_id=<?php echo $info_id; ?>';
        var datemin = $("#datemin").val();
        var datemax = $("#datemax").val();
        url += "&datemin="+datemin+"&datemax="+datemax;
        var table = $('.table-sort').DataTable({
            "aaSorting": [[ 0, "desc" ]],//默认第几个排序
            "sPaginationType": "full_numbers",
            "bPaginite": true,
            "bInfo": true,
            "bSort": true,
            "processing": false,
            "serverSide": true,
            "searching" : false, //去掉搜索框方法一
            "sAjaxSource": url,//这个是请求的地址
            "fnServerData": retrieveData,
            "aoColumns" : [//初始化要显示的列
                {
                    "mDataProp" : "id",//获取列数据，跟服务器返回字段一致
                    "sClass" : "center",//显示样式
                    "mRender" : function(data, type, full) {
                        return "<label><input type='checkbox' class='ace checkbox_select' name='chkId' value='"+data+"' /><span class='lbl'></span></label>"
                    }
                },
                {
                    "mDataProp" : "grab_title"
                },
                {
                    "mDataProp" : "videoTypeName"
                },
                {
                    "mDataProp" : "category"
                },
                {
                    "mDataProp" : "total"
                },
                {
                    "mDataProp" : "success_number",
                    "mRender" : function(data, type, full) {
                        return "<a href='"+full.successUrl+"' target='_blank' />"+data+"</a>"
                    }
                },
                {
                    "mDataProp" : "fail_number",
                    "mRender" : function(data, type, full) {
                        return "<a href='"+full.failUrl+"' target='_blank' />"+data+"</a>"
                    }
                },
                {
                    "mDataProp" : "rep_number",
                    "mRender" : function(data, type, full) {
                        return "<a href='"+full.repUrl+"' target='_blank' />"+data+"</a>"
                    }
                },
                {
                    "mDataProp" : "execDate"
                }
            ],
            "aoColumnDefs": [
//        //{"bVisible": false, "aTargets": [ 3 ]} //控制列的隐藏显示
                {"orderable":false,"aTargets":[0,1,2,3]}// 制定列不参与排序
            ]
        });
    };

</script>
</body>
</html>