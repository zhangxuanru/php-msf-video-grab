<?php

session_start();

define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('FCPATH', str_replace(SELF, '', __FILE__));


function CheckAuthentication(){
	if ($_SESSION['admin_phpci'] != 'admin_phpci') return false;   //验证
	return true;
}

$config['LicenseName'] = '';
$config['LicenseKey']  = '';


$url_upload = explode('statics','http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);

$baseUrl = $url_upload[0].'data/upfile/';  //路径

$fcpath  = explode('statics',FCPATH);
$baseDir = $fcpath[0].'data/upfile/';      //物理路径


//'url' => $baseUrl . '_thumbs',
//'directory' => $baseDir . '_thumbs',
$config['Thumbnails'] = Array(
		'enabled' => true,
		'directAccess' => false,
		'maxWidth' => 100,
		'maxHeight' => 100,
		'bmpSupported' => false,
		'quality' => 80);


$config['Images'] = Array(
		'maxWidth' => 1600,
		'maxHeight' => 1200,
		'quality' => 80);


$config['RoleSessionVar'] = 'CKFinder_UserRole';


$config['AccessControl'][] = Array(
		'role' => '*',
		'resourceType' => '*',
		'folder' => '/',

		'folderView' => true,
		'folderCreate' => true,
		'folderRename' => true,
		'folderDelete' => true,

		'fileView' => true,
		'fileUpload' => true,
		'fileRename' => true,
		'fileDelete' => true);


$config['DefaultResourceTypes'] = '';

$config['ResourceType'][] = Array(
		'name' => 'upfile',				// Single quotes not allowed
		'url' => $baseUrl,
		'directory' => $baseDir ,
		'maxSize' => 0,
		'allowedExtensions' => '7z,aiff,asf,avi,bmp,csv,doc,docx,fla,flv,gif,gz,gzip,jpeg,jpg,mid,mov,mp3,mp4,mpc,mpeg,mpg,ods,odt,pdf,png,ppt,pptx,pxd,qt,ram,rar,rm,rmi,rmvb,rtf,sdc,sitd,swf,sxc,sxw,tar,tgz,tif,tiff,txt,vsd,wav,wma,wmv,xls,xlsx,zip',
		'deniedExtensions' => '');


$config['CheckDoubleExtension'] = true;


$config['DisallowUnsafeCharacters'] = false;


if (stristr(PHP_OS,"WIN")) {
	$config['FilesystemEncoding'] = 'GBK';      //中文图片乱码解决
} else {
	$config['FilesystemEncoding'] = 'UTF-8';
}

$config['SecureImageUploads'] = true;

$config['CheckSizeAfterScaling'] = true;

$config['HtmlExtensions'] = array('html', 'htm', 'xml', 'js');

$config['HideFolders'] = Array(".*", "CVS");

$config['HideFiles'] = Array(".*");

$config['ChmodFiles'] = 0777 ;

$config['ChmodFolders'] = 0755 ;

$config['ForceAscii'] = false;

$config['XSendfile'] = false;

include_once "plugins/imageresize/plugin.php";
include_once "plugins/fileeditor/plugin.php";
include_once "plugins/zip/plugin.php";

$config['plugin_imageresize']['smallThumb'] = '90x90';
$config['plugin_imageresize']['mediumThumb'] = '120x120';
$config['plugin_imageresize']['largeThumb'] = '180x180';
