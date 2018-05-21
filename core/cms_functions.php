<?if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die();?>
<?
//OpenCart
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function OpenCartAddToCart(){
	$OCPID = 0;
	@session_start();
	if(defined('TOCART_NUMBER')){
		$CurQNT = intval($_SESSION['cart'][TOCART_NUMBER.'_'.TOCART_PRICE_ID]['quantity']);
		if($CurQNT>0){
			$CurQNT = $CurQNT+TOCART_COUNT;
			if($CurQNT>TOCART_AVAILABLE){$CurQNT=TOCART_AVAILABLE;}
			$_SESSION['cart'][TOCART_NUMBER.'_'.TOCART_PRICE_ID]['quantity'] = $CurQNT;
		}else{
			$arOpenCart = array();
			$arOpenCart['tecdoc'] = "Y";
			$arOpenCart['product_id'] = $OCPID;
			$arOpenCart['key'] = $OCPID;
			$arOpenCart['price'] = TOCART_PRICE;
			$arOpenCart['quantity'] = TOCART_COUNT;
			$arOpenCart['name'] = TOCART_NAME.' ['.TOCART_ARTICLE.']';
			$arOpenCart['stock'] = TOCART_AVAILABLE;
			if(TECDOC_FILES_PREFIX!='' AND TOCART_IMG!=''){
				$arOpenCart['image'] = TECDOC_FILES_PREFIX.TOCART_IMG;
			}
			$arOpenCart['brand'] = TOCART_BRAND;
			$arOpenCart['product_url'] = DETAIL_URL;
			$arOpenCart['day'] = TOCART_DAY;
			$arOpenCart['supplier'] = TOCART_SUPPLIER;
			if(defined('TOCART_STOCK') AND TOCART_STOCK!=''){$arOpenCart['supplier'].=' / '.TOCART_STOCK;}
			$arOpenCart['article'] = TOCART_ARTICLE;
			$_SESSION['cart'][TOCART_NUMBER.'_'.TOCART_PRICE_ID] = $arOpenCart;
		}
		return 1;
	}
}

function OpenCartSetMeta(){
	global $TCore;
	$_POST['tecdoc_title'] = $TCore->Head_Title;
	$_POST['tecdoc_robots'] = $TCore->Head_Robots;
	$_POST['tecdoc_keywords'] = $TCore->Head_Keywords;
	$_POST['tecdoc_description'] = $TCore->Head_Description;
}

function OpenCartDefineCurrencies(){
	if(count($_SESSION['TECDOC_CUR_RATES'])>0 AND defined('TECDOC_DEFINE_CURRENCY')){
		global $registry; $obCurs = $registry->get('currency');
		foreach($_SESSION['TECDOC_CUR_RATES'] as $cCur=>$cRate){
			$_SESSION['TECDOC_CUR_RATES'][$cCur] = $obCurs->getValue($cCur);
		}
		return true;
	}
}

function OpenCartIsAdmin(){
	if(!isset($_SESSION['CORE_IS_ADMIN'])){$_SESSION['CORE_IS_ADMIN']="N";}
	if($_SESSION['CORE_IS_ADMIN']!="Y" AND isset($_SESSION['user_id'])  AND $_SESSION['user_id']>0 AND strlen($_SESSION['token'])==32){
		define('CORE_IS_ADMIN',true);
		$_SESSION['CORE_IS_ADMIN']="Y";
	}
}


//Bitrix
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function BitrixAddToCart(){
	if(defined('TOCART_NUMBER') AND CModule::IncludeModule("sale")){
		if(TOCART_ART_ID>0){$BNUM = TOCART_ART_ID;}else{$BNUM = rand(999999,9999999);}
		$PRICE=TOCART_PRICE;
		if($PRICE<=0){$PRICE=1;}
		if(defined('TOCART_STOCK') AND TOCART_STOCK!=''){$TOCART_SUP=TOCART_SUPPLIER.' / '.TOCART_STOCK;}else{$TOCART_SUP=TOCART_SUPPLIER;}
		$arFields = Array(
			"PRODUCT_ID"	=> $BNUM,
			"PRICE"	=> TOCART_PRICE,
			"CURRENCY"	=> TECDOC_DEFAULT_CUR,
			"LID"		=> "s1", //обязательно
			"FUSER_ID"	=> CSaleBasket::GetBasketUserID(),
			"QUANTITY"	=> TOCART_COUNT,
			"NAME"	=> TOCART_NAME,
			"DELAY"	=> "N",
			"CAN_BUY"	=> "Y",
			"NOTES"	=> 1, //Тип цены
			"DETAIL_PAGE_URL"	=> DETAIL_URL,
			"PROPS" => Array(
				Array("NAME"=>"Номер","CODE"=>"ART","VALUE"=>TOCART_ARTICLE,"SORT"=>1),
				Array("NAME"=>"Фирма","CODE"=>"BRAND_TITLE","VALUE"=>TOCART_BRAND,"SORT"=>2),
				Array("NAME"=>"Дней","CODE"=>"DAY","VALUE"=>TOCART_DAY,"SORT"=>3),
				Array("NAME"=>"Поставщик","CODE"=>"SUPPLIER","VALUE"=>$TOCART_SUP,"SORT"=>4),
			)
		);
		if(TECDOC_FILES_PREFIX!='' AND TOCART_IMG!=''){
			$arFields["PROPS"][] = Array("NAME"=>"Картинка","CODE"=>"IMAGE","VALUE"=>TECDOC_FILES_PREFIX.TOCART_IMG,"SORT"=>5);
		}
		$NID =  CSaleBasket::Add($arFields);
		return $NID;
	}
}

function BitrixSetMeta(){
	global $APPLICATION;
	global $TCore;
	$APPLICATION->SetPageProperty("title", $TCore->Head_Title);
	$APPLICATION->SetPageProperty("robots", $TCore->Head_Robots);
	$APPLICATION->SetPageProperty("keywords", $TCore->Head_Keywords);
	$APPLICATION->SetPageProperty("description", $TCore->Head_Description);
}

function BitrixIsAdmin(){
	if(!isset($_SESSION['CORE_IS_ADMIN'])){$_SESSION['CORE_IS_ADMIN']="N";}
	if($_SESSION['CORE_IS_ADMIN']!="Y"){
		global $USER;
		if($USER->IsAdmin()){
			define('CORE_IS_ADMIN',true);
			$_SESSION['CORE_IS_ADMIN']="Y";
		}
	}
}

function BitrixDefineCurrencies(){
	if(count($_SESSION['TECDOC_CUR_RATES'])>0 AND defined("TECDOC_DEFINE_CURRENCY") AND CModule::IncludeModule("currency")){
		$rsCurs = CCurrencyRates::GetList($by="date",$order="desc", Array());
		while($arCurs = $rsCurs->Fetch()){ 
			$arBxCurs[$arCurs['CURRENCY']] = 1/($arCurs['RATE']/$arCurs['RATE_CNT']); //Прямой курс
		}
		if(count($arBxCurs)<=0){echo '<div class="psys_error">Warning! Create Bitrix <a href="/bitrix/admin/currencies_rates.php">currency rates</a></div>';}
		foreach($_SESSION['TECDOC_CUR_RATES'] as $cCur=>$cRate){
			$_SESSION['TECDOC_CUR_RATES'][$cCur] = $arBxCurs[$cCur];
		}
		return true;
	}
}


//WordPress
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function WordPressSetMeta(){
	global $TCore;
	$_POST['tecdoc_title'] = $TCore->Head_Title;
	$_POST['tecdoc_robots'] = $TCore->Head_Robots;
	$_POST['tecdoc_keywords'] = $TCore->Head_Keywords;
	$_POST['tecdoc_description'] = $TCore->Head_Description;
}
function WordPressAddToCart(){
	$CART_ITEM_ID = 9;
	if(defined('TOCART_NUMBER')){		
		$DESC=$arShop['STR_DES_TEXT'];
		$parameters = array();
		$parameters['tecdoc'] = 1;
		$parameters['unit_price'] = TOCART_PRICE;
		//$parameters['brand'] = TOCART_BRAND;
		$parameters['tecdoc_name'] = TOCART_NAME.' ['.TOCART_ARTICLE.']';
		$parameters['quantity'] = TOCART_COUNT;
		$parameters['product_url'] = CORE_ROOT_DIR.'/search/'.TOCART_ARTICLE.'/';
		//$parameters['day'] = TOCART_DAY;
		//$parameters['supplier'] = TOCART_SUPPLIER;
		$parameters['custom_message'] = TOCART_NAME.' ['.TOCART_ARTICLE.']';
		$parameters['stock'] = TOCART_AVAILABLE;
		if(TECDOC_FILES_PREFIX!='' AND TOCART_IMG!=''){
			$parameters['tecdoc_img'] = TECDOC_FILES_PREFIX.TOCART_IMG;
		}
		global $wpsc_cart;
		$status = $wpsc_cart->set_item($CART_ITEM_ID, $parameters, false);
		
		return 1;
	}
}


//Prestashop 
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function PrestashopSetMeta(){
	global $TCore;
	$_POST['tecdoc_title'] = $TCore->Head_Title;
	$_POST['tecdoc_robots'] = $TCore->Head_Robots;
	$_POST['tecdoc_keywords'] = $TCore->Head_Keywords;
	$_POST['tecdoc_description'] = $TCore->Head_Description;
}

//Joomla (with jshopping component)
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function JoomlaJShoppingAddToCart(){
	if(defined('TOCART_NUMBER')){
		$CurQNT = intval($_SESSION['tdm_cart'][TOCART_NUMBER.'_'.TOCART_PRICE_ID]['quantity']);
		if($CurQNT>0){
			$CurQNT = $CurQNT+TOCART_COUNT;
			//if($CurQNT>TOCART_AVAILABLE){$CurQNT=TOCART_AVAILABLE;}
			$_SESSION['tdm_cart'][TOCART_NUMBER.'_'.TOCART_PRICE_ID]['quantity'] = $CurQNT;
		}else{
			$arTDCart = array();
			$arTDCart['tdm'] = "Y";
			$arTDCart['price'] = floatval(TOCART_PRICE);
			$arTDCart['quantity'] = TOCART_COUNT;
			$arTDCart['name'] = TOCART_NAME;
			$arTDCart['available'] = TOCART_AVAILABLE;
			if(TECDOC_FILES_PREFIX!='' AND TOCART_IMG!=''){ $arTDCart['image'] = TECDOC_FILES_PREFIX.TOCART_IMG; }
			$arTDCart['brand'] = TOCART_BRAND;
			$arTDCart['product_url'] = DETAIL_URL;
			$arTDCart['day'] = TOCART_DAY;
			$arTDCart['supplier'] = TOCART_SUPPLIER;
			if(defined('TOCART_STOCK') AND TOCART_STOCK!=''){$arTDCart['supplier'].=' / '.TOCART_STOCK;}
			$arTDCart['article'] = TOCART_ARTICLE;
			$_SESSION['tdm_cart'][TOCART_NUMBER.'_'.TOCART_PRICE_ID] = $arTDCart;
			//cms params
			$arTDCart['product_id'] = 0;
			$arTDCart['category_id'] = 0;
		}
		return 1;
	}
}

function JoomlaSetMeta(){
	global $TCore;
	$_POST['tecdoc_title'] = $TCore->Head_Title;
	$_POST['tecdoc_keywords'] = $TCore->Head_Keywords;
	$_POST['tecdoc_description'] = $TCore->Head_Description;
}

function JoomlaJShDefineCurrencies(){
	if(defined("TECDOC_DEFINE_CURRENCY")){
		$arCurrs = JSFactory::getAllCurrency();
		foreach($arCurrs as $cId=>$obCur){
			$cIso = (string)$obCur->currency_code_iso;
			$cRate = (float)$obCur->currency_value;
			$_SESSION['TECDOC_CUR_RATES'][$cIso] = $cRate;
		}
		return true;
	}
}
?>