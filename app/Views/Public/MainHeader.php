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

<script type="text/javascript">var basepath='<?php echo $static_url; ?>/statics/base/images';</script>
<?php foreach ($static['script'] as $key => $value) {
    $jsLink = $static_url.$value;
    echo sprintf('<script type="text/javascript" src="%s"></script>',$jsLink);
} ?>   

<script type="text/javascript">
/*ajax执行*/
var basepath='<?php echo $static_url; ?>/statics/base/images';
var lang = 'cn';
var metimgurl='../templates/met/images/';
var depth='';
$(document).ready(function(){
	ifreme_methei();
});
</script>


<!--[if lte IE 9]>
<SCRIPT language=JavaScript>  
function killErrors() {
return true;
}
window.onerror = killErrors;
</SCRIPT> 
<![endif]--> 

<?php echo $this->section("content");?>
