<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $static['title']; ?></title>
<link href="favicon.ico" rel="shortcut icon" />
<?php foreach ($static['style'] as $key => $value) {
    $cssLink = $static_url.$value;
    echo sprintf('<link rel="stylesheet" href="%s"  type="text/css">',$cssLink);
} ?>  

</head>
<?php foreach ($static['script'] as $key => $value) {
    $jsLink = $static_url.$value;
    echo sprintf('<script type="text/javascript" src="%s"></script>',$jsLink);
} ?>   

<script type="text/javascript">
$(document).ready(function() {
    $("#kzqie").click(function(){
        var my = $(this);
        if(my.text()=='窄版'){
            $('#content,#top .topnbox').animate({ width: '1000px'}, 80);
            $.ajax({url : '',type: "POST"});
            my.attr('title','切换到宽版');
            my.text('宽版');
            setTimeout("topwidth(100)",100);
        }else{
            $('#content,#top .topnbox').animate({ width: '99%'}, 80);
            $.ajax({url : '',type: "POST"});
            my.attr('title','宽版');
            my.text('宽版');
            setTimeout("topwidth(100)",100);
        }
    });
});
$(function() {
    $("#cache").click( function () { 
        if(confirm('确定清除吗?')){
            $.ajax({
                type: "post",
                cache: !1,
                url: "index.php/admin/home/clear",
                data: "",
                timeout: 1e4,
                error: function() {},
                success: function(e) {
                    if (e>0) {
                        asyncbox.tips("清除成功",'success');
                    } else {
                        asyncbox.tips('清除失败','error');
                    }
                }
            })
        }
    });  
})
</script> 
<style>
#content,#top .topnbox{ width:1000px;}
#top .floatr li a span{ behavior: url(<?php echo $static_url; ?>/statics/base/images/iepngfix.htc); }
</style>

<?php echo $this->section("content");?>
