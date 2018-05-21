<?if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die();?>
<?
$CurDirPath = getcwd();
define('HEADER_INCLUDED','Y');
define('TECDOC_HEADER_INC',"Y");
if(CORE_SITE_CHARSET=="utf8"){
	header('Content-type: text/html; charset=utf-8');
}

//CMS Prolog
////////////////////////////////////////////////////////////
JoomlaSetMeta();


//CMS Header
////////////////////////////////////////////////////////////
$_SERVER['REQUEST_URI']='/';
$_SERVER['SCRIPT_NAME']='/index.php';
chdir($_SERVER["DOCUMENT_ROOT"]);
require($_SERVER["DOCUMENT_ROOT"]."/index.php");
chdir($CurDirPath);


//CMS Epilog
////////////////////////////////////////////////////////////
//JoomlaJShDefineCurrencies();

?>
<link href="<?=CORE_ROOT_DIR?>/media/js/jquery-ui-1.10.0/css/smoothness/jquery-ui-1.10.0.custom.css" rel="stylesheet">
<script src="<?=CORE_ROOT_DIR?>/media/js/jquery-1.9.1.js"></script>
<script src="<?=CORE_ROOT_DIR?>/media/js/jquery-ui-1.10.0/jquery-ui-1.10.0.custom.min.js"></script>
<?
//require_once($_SERVER["DOCUMENT_ROOT"].CORE_ROOT_DIR."/components/search_form.php");
require_once($_SERVER["DOCUMENT_ROOT"].CORE_ROOT_DIR."/admin/admin_panel.php");
if(USE_STATISTIC=="Y"){$arStat[] = Array("Header inc",microtime(true));}
?>
<link rel="stylesheet" href="<?=CORE_ROOT_DIR?>/media/styles.css" type="text/css">
