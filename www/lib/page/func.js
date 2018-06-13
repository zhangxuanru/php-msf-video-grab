 function retrieveData(url, aoData, fnCallback) {
        $.ajax({
            url: url,//这个就是请求地址对应sAjaxSource
            data : {
                "aoData" : JSON.stringify(aoData)
            },
            type: 'POST',
            dataType: 'json',
            async: false,
            success: function (result) {             
                fnCallback(result);//把返回的数据传给这个方法就可以了,datatable会自动绑定数据的
            },
            error:function(XMLHttpRequest, textStatus, errorThrown) {
                alert("status:"+XMLHttpRequest.status+",readyState:"+XMLHttpRequest.readyState+",textStatus:"+textStatus);
            }
        });
    }

 /**
  * 批量删除
  */
 function datadel(type)
 {
     var idStr = '';
     $('.checkbox_select').each(function () {
         if($(this).is(":checked")){
             idStr+=$(this).val()+",";
         }
     });
     if(idStr.length == 0 ){
         alert("选中数据为空");
         return false;
     }
     var url = '';
     switch(type){
         case 'grap':
             url = "/grab/del/";
             break;
         case 'cate':
             url = "/cate/del/";
             break;
         case 'admin':
             url = "/user/del/";
             break;
         case 'video':
             url = "/video/del/";
             break;
     }
     if(url.length == 0){
         alert("类别不对");
         return false;
     }
     layer.confirm('确认要删除吗？', function () {
         $.ajax({
             type: 'GET',
             url: url+'?id='+idStr,
             dataType: 'json',
             success: function (data) {
                 layer.msg(data.message, {icon: 1, time: 1000});
                 $(".table-sort").dataTable().fnDraw(false);
             },
             error: function (data) {
                 console.log(data.msg);
             },
         });
     });
     console.log(idStr)

 }
