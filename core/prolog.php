<?
define('CORE_PROLOG_INCLUDED',true); //For safe scripts mode
require_once("classes_tecdoc.php");
require_once("classes_core.php");
require_once("functions.php");
require_once("cms_functions.php");


error_reporting(E_ERROR);
ini_set("error_reporting", E_ERROR);
global $CmsTemlpateFooter;

//Settings
define('CORE_ROOT_DIR',"/parts"); //in (/CORE_ROOT_DIR/urlrewrite.php) too...
require_once($_SERVER["DOCUMENT_ROOT"].CORE_ROOT_DIR.'/components/brands_cou.php');
global $TCore;
$TCore = new TCore();
$TCore->GetSettings();

session_start();

//Lang. select
global $arTDLengs;
if($_POST['tdmodlng']!=''){$_SESSION['language']=$_POST['tdmodlng'];}
if($_SESSION['language']!='' AND $_SESSION['language']!=$_SESSION['TECDOC_LNG_CODE']){
	if(in_array($_SESSION['language'],$arTDLengs)){
		$LengID = array_search($_SESSION['language'],$arTDLengs);
		if($LengID>0){ $_SESSION['TECDOC_LNG_CODE']=$_SESSION['language']; $_SESSION['TECDOC_LNG_ID']=$LengID; }
	}
}
if($_SESSION['TECDOC_LNG_ID']>0){$TCore->arSettings['MAIN']['TECDOC_LNG_ID'] = $_SESSION['TECDOC_LNG_ID'];}


//Is admin
if(isset($_SESSION['CORE_IS_ADMIN']) AND $_SESSION['CORE_IS_ADMIN']=="Y"){define('CORE_IS_ADMIN',true);}else{define('CORE_IS_ADMIN',false);}

//[MAIN]
define('CORE_ADMIN_KEY',$TCore->arSettings['MAIN']['CORE_ADMIN_KEY']);
define('TECDOC_LNG_ID',$TCore->arSettings['MAIN']['TECDOC_LNG_ID']);
define('TECDOC_FILES_PREFIX',$TCore->arSettings['MAIN']['TECDOC_FILES_PREFIX']);
define('MODELS_START_YEAR_LIMIT',$TCore->arSettings['MAIN']['MODELS_START_YEAR_LIMIT']);
define('TECDOC_DEFAULT_CUR',$TCore->arSettings['MAIN']['TECDOC_DEFAULT_CUR']);
define('TECDOC_GETPARTS_LIMIT',$TCore->arSettings['MAIN']['TECDOC_GETPARTS_LIMIT']);
define('CORE_SITE_CHARSET',$TCore->arSettings['MAIN']['CORE_SITE_CHARSET']);
define('INCLUDE_SEO_BLOCKS',$TCore->arSettings['MAIN']['INCLUDE_SEO_BLOCKS']);
define('CORE_USE_DBSELECT',$TCore->arSettings['MAIN']['CORE_USE_DBSELECT']);
define('USE_STATISTIC',$TCore->arSettings['MAIN']['USE_STATISTIC']);
define('ADDTOCART_NOPRICE',$TCore->arSettings['MAIN']['ADDTOCART_NOPRICE']);
define('HIDE_PARTS_NOPRICE',$TCore->arSettings['MAIN']['HIDE_PARTS_NOPRICE']);
define('HIDE_PARTS_NOPRICE',$TCore->arSettings['MAIN']['HIDE_PARTS_NOPRICE']);
define('SEARCH_LINK_DEEP_CROSSES',$TCore->arSettings['MAIN']['SEARCH_LINK_DEEP_CROSSES']);


//[TECDOC_DB]
define('TECDOC_DB_SERVER',$TCore->arSettings['TECDOC_DB']['TECDOC_DB_SERVER']);
define('TECDOC_DB_LOGIN',$TCore->arSettings['TECDOC_DB']['TECDOC_DB_LOGIN']);
define('TECDOC_DB_PASS',$TCore->arSettings['TECDOC_DB']['TECDOC_DB_PASS']);
define('TECDOC_DB_NAME',$TCore->arSettings['TECDOC_DB']['TECDOC_DB_NAME']);
define('TECDOC_DB_CHARSET',$TCore->arSettings['TECDOC_DB']['TECDOC_DB_CHARSET']);
define('TECDOC_DB_PCON',$TCore->arSettings['TECDOC_DB']['TECDOC_DB_PCON']);
//[CORE_DB]
define('CORE_DB_SERVER',$TCore->arSettings['CORE_DB']['CORE_DB_SERVER']);
define('CORE_DB_LOGIN',$TCore->arSettings['CORE_DB']['CORE_DB_LOGIN']);
define('CORE_DB_PASS',$TCore->arSettings['CORE_DB']['CORE_DB_PASS']);
define('CORE_DB_NAME',$TCore->arSettings['CORE_DB']['CORE_DB_NAME']);
define('CORE_DB_CHARSET',$TCore->arSettings['CORE_DB']['CORE_DB_CHARSET']);
define('CORE_DB_PCON',$TCore->arSettings['CORE_DB']['CORE_DB_PCON']);
define('CORE_DB_ISCHEMA',$TCore->arSettings['CORE_DB']['CORE_DB_ISCHEMA']);
define('CORE_DB_PRICES_TABLE','PRICES');
define('CORE_DB_LINKS_TABLE','LINKS');

//Currency select
if(count($_SESSION['TECDOC_CUR_SYMBS'])<=0){
	$arCurList = explode('/',$TCore->arSettings['MAIN']['TECDOC_CUR_LIST']); //  RUB=# руб/USD=#$/EUR=#И/UAH=# грн
	foreach($arCurList as $LCur){
		$arLCur = explode('=',$LCur);	// RUB=# руб
		$_SESSION['TECDOC_CUR_RATES'][$arLCur[0]] = 0; // Default rate
		$_SESSION['TECDOC_CUR_SYMBS'][$arLCur[0]] = $arLCur[1];
	}
}
if($_SESSION['TECDOC_SELECTED_CUR']==''){$_SESSION['TECDOC_SELECTED_CUR'] = TECDOC_DEFAULT_CUR; $doSYM="Y";}
if($_SESSION['currency']!=$_SESSION['TECDOC_SELECTED_CUR']){
	foreach($_SESSION['TECDOC_CUR_SYMBS'] as $cCur=>$cSym){ if($_SESSION['currency']==$cCur){$_SESSION['TECDOC_SELECTED_CUR']=$cCur; $doSYM="Y";} }
}
if($doSYM=="Y"){
	$SelCurSym = $_SESSION['TECDOC_CUR_SYMBS'][$_SESSION['TECDOC_SELECTED_CUR']];
	$_SESSION['TECDOC_SELECTED_SYM'] = trim(str_replace('#','',$SelCurSym));
	define('TECDOC_DEFINE_CURRENCY',true);
}



//Connect to DB
global $TDataBase;
$TDataBase = new TDataBase();
if(!defined("TDM_ADMIN_SIDE")){
	$TDataBase->DBConnect("CORE");
	$TCore->SetMetaData();
	if($_SESSION['TECDOC_CUR_MODULE']==""){
		$_SESSION['TECDOC_CUR_MODULE']="TDM";
		$rsCurs = TDCurrency::GetList(Array("ID"=>"ASC"),Array());
		while($arCur = $rsCurs->GetNext()){$_SESSION['TECDOC_CUR_RATES'][$arCur['CODE']] = $arCur['RATE'];}
	}
	$TDataBase->DBSelect("TECDOC");
}
//echo mysql_error();

//UTF-8
if(CORE_SITE_CHARSET=="utf8"){
	mb_internal_encoding('UTF-8');
	mb_regex_encoding('UTF-8');
	setlocale(LC_ALL, 'ru_RU.UTF-8');
	define('CORE_CHARSET_PREG','u');
}else{
	define('CORE_CHARSET_PREG','');
}

global $arStat;
if(USE_STATISTIC=="Y"){$arStat[] = Array("Init",microtime(true));}


//Order with no price
if(ADDTOCART_NOPRICE=="Y" AND $_POST['OrderPartToCart']=="Y"){
	$NUMBER = ArtToNumber($_POST['OrderPartArticle']);
	if($_POST['OrderPartURL']==''){$_POST['OrderPartURL']=CORE_ROOT_DIR.'/search/'.$NUMBER;}
	$arBFields = Array(
		"ART_NUM" => $NUMBER,
		"ART_ARTICLE_NR" => $_POST['OrderPartArticle'],
		"PART_NAME" => $_POST['OrderPartName'],
		"SUP_BRAND" => $_POST['OrderPartBrand'],
		"PRICE" => 0,
		"AVAILABLE" => 1,
		"IMG" => $_POST['OrderPartImg'],
		"DETAIL_URL" => $_POST['OrderPartURL'],
	);
	AddPartToBasket($arBFields,$_POST['count']);
}
?>