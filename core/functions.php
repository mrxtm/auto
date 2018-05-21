<?if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die();?>
<?
function ArtToNumber($Article,$Type='FULL'){
	if($Type=='FULL'){
		$Article = preg_replace("/[^a-zA-Z0-9_.-]/".CORE_CHARSET_PREG,"",trim($Article));
	}elseif($Type=='SHORT'){
		$Article = preg_replace("/[^a-zA-Z0-9_]/".CORE_CHARSET_PREG,"",trim($Article));
	}
	return $Article;
}

function AddPartToBasket($arBFields,$CNT){
	if(!intval($CNT)>0){$arBFields['COUNT']=1;}else{$arBFields['COUNT']=$CNT;}
	if($arBFields['CURRENCY']!=TECDOC_DEFAULT_CUR AND $_SESSION['TECDOC_CUR_RATES'][$arBFields['CURRENCY']]>0 AND $_SESSION['TECDOC_CUR_RATES'][TECDOC_DEFAULT_CUR]>0){
		$arBFields['PRICE'] = round( $arBFields['PRICE'] * ($_SESSION['TECDOC_CUR_RATES'][TECDOC_DEFAULT_CUR] / $_SESSION['TECDOC_CUR_RATES'][$arBFields['CURRENCY']]) ,2);
	}
	define('TOCART_PRICE_ID',$arBFields['ID']);
	define('TOCART_NUMBER',$arBFields['ART_NUM']);
	define('TOCART_ARTICLE',$arBFields['ART_ARTICLE_NR']);
	define('TOCART_NAME',$arBFields['PART_NAME']);
	define('TOCART_BRAND',$arBFields['SUP_BRAND']);
	define('TOCART_ART_ID',$arBFields['ART_ID']);
	define('TOCART_PRICE',$arBFields['PRICE']);
	define('TOCART_CURRENCY',$arBFields['CURRENCY']);
	define('TOCART_DAY',$arBFields['DAY']);
	define('TOCART_AVAILABLE',$arBFields['AVAILABLE']);
	define('TOCART_SUPPLIER',$arBFields['SUPPLIER']);
	define('TOCART_STOCK',$arBFields['STOCK']);
	define('TOCART_IMG',$arBFields['IMG']);
	define('TOCART_COUNT',$arBFields['COUNT']);
	define('DETAIL_URL',$arBFields['DETAIL_URL']);
	return 1;
}


function ProcessAddPartToBasket($arPrice,$arPARTS){
	if($_POST['AddPartToCart']=="Y" AND $_POST['ID']==$arPrice['ID']){
		$arBFields = $arPrice;
		$arBFields['ART_ARTICLE_NR'] = $arPARTS[$arPrice['ART_NUM']]['ART_ARTICLE_NR'];
		$arBFields['DETAIL_URL'] = $arPARTS[$arPrice['ART_NUM']]['BRANDS'][$arPrice['SUP_BRAND']]['DETAIL_URL'];
		if($arBFields['DETAIL_URL']==''){$arBFields['DETAIL_URL']='/parts/search/'.ArtToNumber($arBFields['ART_ARTICLE_NR']);}
		$arBFields['ART_ID'] = $arPARTS[$arPrice['ART_NUM']]['BRANDS'][$arPrice['SUP_BRAND']]['ART_ID'];
		$arBFields['IMG'] = $arPARTS[$arPrice['ART_NUM']]['BRANDS'][$arPrice['SUP_BRAND']]['IMG'];
		$NBID = AddPartToBasket($arBFields,$_POST['count']);
		return $NBID;
	}
}

function TDCurrencyConvert($arPrice){
	$arCurRates = $_SESSION['TECDOC_CUR_RATES'];
	$arCurSymbs = $_SESSION['TECDOC_CUR_SYMBS'];
	$SelCur = $_SESSION['TECDOC_SELECTED_CUR'];
	if($arPrice['CURRENCY']!=$SelCur){
		if($arCurRates[$SelCur]>0 AND $arCurRates[$arPrice['CURRENCY']]>0){
			$arPrice['PRICE'] = round( $arPrice['PRICE'] * ($arCurRates[$SelCur] / $arCurRates[$arPrice['CURRENCY']]) ,2);
			$arPrice['CURRENCY'] = $SelCur;
		}else{$arPrice['PRICE'] = str_replace('#',$arPrice['PRICE'],$_SESSION['TECDOC_CUR_SYMBS'][$arPrice['CURRENCY']]);}
	}
	$arPrice['PRICE'] = str_replace('.00','',$arPrice['PRICE']);
	return $arPrice;
}

function SafeSecName($Name){
	$Name = str_replace(' ','_',$Name);
	$Name = str_replace('/','|',$Name);
	return urlencode($Name);
}

function TDSelectDB($DB){
	mysql_select_db($DB);
}

function TDDateFormat($date_ym,$DoNV='',$type=''){
   if($date_ym!=0){
		$year = substr($date_ym, 0,4);
		$mount = substr($date_ym, 4,2);
		if($type=='year'){
			$dat_my= $year;
		}else{
			$dat_my= "$mount.$year";
		}
	}else{$dat_my=$DoNV;}
	return  $dat_my;
}

function mb_ucwords($str){
	$str = mb_convert_case($str, MB_CASE_TITLE, "UTF-8"); 
	return ($str); 
}

function StrToLow($str){
	if(CORE_SITE_CHARSET=="utf8"){
		$str = mb_strtolower($str);
	}else{
		$str = strtolower($str);
	}
	return trim($str); 
}
function StrToUp($str){
	if(CORE_SITE_CHARSET=="utf8"){
		$str = mb_strtoupper($str);
	}else{
		$str = strtoupper($str);
	}
	return trim($str); 
}

function TDSecGetName($SName){
	$SName = strtolower($SName);
	if(CORE_SITE_CHARSET=="utf8"){
		$SName = mb_ucwords($SName);
	}else{
		$SName = ucwords($SName);
	}
	return $SName;
}
function NotNullInArray($var){return($var != NULL);}
?>