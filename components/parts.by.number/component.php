<?if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die();?>
<?
global $arStat;
if(USE_STATISTIC=="Y"){$arStat[] = Array("Component start",microtime(true));}
$arResult['SEARCH_NUMBER'] = mysql_real_escape_string(substr($arParams['SEARCH_NUMBER'],0,16));
$arResult['SEARCH_NUMBER'] = StrToUp($arResult['SEARCH_NUMBER']);
$arResult['SEARCH_NUMBER_FULL'] = ArtToNumber($arResult['SEARCH_NUMBER'],'FULL');
$arResult['SEARCH_NUMBER_SHORT'] = ArtToNumber($arResult['SEARCH_NUMBER'],'SHORT');
if(trim($arParams['SEARCH_BRAND'])!=''){
	$arResult['SEARCH_BRAND'] = BrandNameDecode($arParams['SEARCH_BRAND']);
}
$arResult['LIST_VIEW_TEMPLATE']='';

global $TCore; TMes('Search');
if($TCore->arSettings['MAIN']['PRICES_ART_TYPE']=="SHORT"){$ART_TYPE="SHORT";}else{$ART_TYPE="FULL";}
// Meta
global $TDataBase;
$TDataBase->DBSelect("CORE");
$TCore->ComponentMetaData("parts.by.number", Array("NUMBER"=>$arResult['SEARCH_NUMBER_FULL']));
$TDataBase->DBSelect("TECDOC");
		
// SEARCH NUMBER
if(strlen($arResult['SEARCH_NUMBER_SHORT'])>2){
	$arResult['COMPONENT'] = 'SEARCH';
	
	$arPARTS = Array(); $arNUMBERs = Array(); $arBRANDs = Array(); $arUnARTIDs=Array(); $arPCounter = Array(); $arLowest_Prices = Array(); $SEARCH_PARTS="N";
	
	/////////////////////////////////////////
	// Lookup Brands filter
	/////////////////////////////////////////
	if($arResult['SEARCH_BRAND']==""){
		$arResult['LIST_VIEW_TEMPLATE']='brands.selection';
		$rsLookNum = TDSQL::LookupByNumber($arResult['SEARCH_NUMBER_SHORT'],TECDOC_LNG_ID);	//SUP_BRAND, ART_ARTICLE_NR, ARL_KIND, ART_COMPLETE_DES_TEXT
		while($arLookNum = $rsLookNum->GetNext()){
			$arPart = PartTexts($arLookNum);											//PART_NAME, SUP_BRAND_F, NUMBER, NUMBER_SHORT
			$arPARTS[$arPart['NUMBER']]['MAIN_GROUP']="Y";
			$arPARTS[$arPart['NUMBER']]['BRANDS'][$arPart["SUP_BRAND"]] = $arPart;
			$arPCounter[$arPart["SUP_BRAND"]]++;
		}
		$TDataBase->DBSelect("CORE");
		$arSPrices = CShopPrice::GetList(Array(),Array("ART_NUM"=>$arResult['SEARCH_NUMBER_'.$ART_TYPE]));
		foreach($arSPrices as $arSPrice){
			if($arPARTS[$arPart['NUMBER']]['MAIN_GROUP']!='Y'){
				$arPart = PartTexts($arSPrice);
				$arPARTS[$arPart['NUMBER']]['MAIN_GROUP']="Y";
				$arPARTS[$arPart['NUMBER']]['BRANDS'][$arPart["SUP_BRAND"]] = $arPart;
				$arPCounter[$arPart["SUP_BRAND"]]++;
			}
		}
		$TDataBase->DBSelect("TECDOC");
		if(USE_STATISTIC=="Y"){$arStat[] = Array("Lookup in prices",microtime(true));}
		if(count($arPCounter)<=1){$SEARCH_PARTS="Y"; $arResult['SEARCH_BRAND']=$arPart["SUP_BRAND"]; $arPARTS = Array();}
		if(USE_STATISTIC=="Y"){$arStat[] = Array("Lookup Brands",microtime(true));}
	}else{$SEARCH_PARTS="Y";}
	
	
	/////////////////////////////////////////
	// Lookup Numbers
	/////////////////////////////////////////
	if($SEARCH_PARTS=="Y"){
		$TDataBase->DBSelect("CORE");
		$arResult['LIST_VIEW_TEMPLATE']='';
		// Meta
		if($arResult['SEARCH_BRAND']!=''){$TCore->Head_Title.=', '.TMes('Firm').': '.$arResult['SEARCH_BRAND'];}
		$arBRANDs[] = $arResult['SEARCH_BRAND']; //First brand must be main brand
		$arLookShNums[] = $arResult['SEARCH_NUMBER_SHORT'];
		
		// Links
		$rsCross = CShopCross::GetLinksDouble($arResult['SEARCH_NUMBER_'.$ART_TYPE],$arResult['SEARCH_BRAND']);
		while($arCross = $rsCross->GetNext()){
			if($arCross['ORIGINAL_BRAND']==""){ //Если не указан бренд оригинала то пробовать найти его в текдоке
				$TDataBase->DBSelect("TECDOC");
				$rsOrNum = TDSQL::GetIDByNumber($arCross['ORIGINAL_NUMS'],TECDOC_LNG_ID);
				if($arOrNum = $rsOrNum->GetNext()){$arCross['ORIGINAL_BRAND']=$arOrNum['BRAND'];}
				$TDataBase->DBSelect("CORE");
			}
			foreach(Array($arCross['CROSS_NUMS'],$arCross['ORIGINAL_NUMS']) as $CrNum){
				if(!in_array($CrNum,$arLookShNums)){$arLookShNums[] = trim($CrNum);}
				//Make default cross record (for non tecdoc parts)
				$arPARTS[$CrNum]["ART_ARTICLE_NR"]=$CrNum;
				$arPARTS[$CrNum]["NUMBER"]=$CrNum;
				$arLowest_Prices[$CrNum] = 99998;
				$arNUMBERs[]=$CrNum;
				//$arPARTS[$CrNum]["BRANDS"]=Array($arCross['CROSS_BRAND']=>Array());
			}
			if(!in_array($arCross['CROSS_BRAND'],$arBRANDs)){$arBRANDs[] = trim($arCross['CROSS_BRAND']);}
			if(!in_array($arCross['ORIGINAL_BRAND'],$arBRANDs)){$arBRANDs[] = trim($arCross['ORIGINAL_BRAND']);}			
		}
		if(!in_array($arResult['SEARCH_NUMBER_SHORT'],$arLookShNums)){$arLookShNums[] = $arResult['SEARCH_NUMBER_SHORT'];}
		$TDataBase->DBSelect("TECDOC");
		
		// Lookup
		$rsLookBNum = TDSQL::LookupByBrandNumber($arLookShNums,$arBRANDs); //ART_ID, SUP_BRAND, ART_ARTICLE_NR, ARL_KIND
		while($arLookBNum = $rsLookBNum->GetNext()){
			$arPart=Array();
			if($TCore->arSettings['MAIN']['SEARCH_SHOW_ORIGINAL_NUMBERS']!="Y"){
				if($arLookBNum['ARL_KIND']==3){continue;}									//Не показывать Оригинальные номера (по ним нет данных в текдоке)
			}
			if($TCore->arSettings['MAIN']['SEARCH_SHOW_TRADE_NUMBERS']!="Y"){
				if($arLookBNum['ARL_KIND']==2){continue;}									//Не показывать Торговые номера (по ним нет данных в текдоке)
			}
			$arPart = TDSQL::GetPartInfo($arLookBNum['ART_ID'],TECDOC_LNG_ID);				//ART_ID, ART_ARTICLE_NR, SUP_BRAND, SUP_ID, ART_COMPLETE_DES_TEXT, COU_ISO2, COU_DES_TEXT
			$arPart = PartTexts($arPart);													//PART_NAME, SUP_BRAND_F, NUMBER, NUMBER_SHORT
			if(in_array($arLookBNum['ART_ID'],$arUnARTIDs)){ 								//Отключить детальную инфо. повторно привязанным запчастям (Торговые, Оригинальные)
				$arPart['NUMBER_SHORT']=ArtToNumber($arLookBNum['ART_ARTICLE_NR'],'SHORT');	//Чтобы сохранить в собственный ключ $arPARTS
				$arLowest_Prices[$arPart['NUMBER_SHORT']] = 99999;							//Сортировать
				$arPart['ART_ID']=0;														//Отключить ссылки
				if($arLookBNum['ARL_KIND']==3){$arPart['COU_ISO2']='';}
			}else{
				$arUnARTIDs[] = $arLookBNum['ART_ID'];
				$arPart = FirstPic($arPart); //IMG
				if($arPart['SUP_BRAND_F']!=''){
					$arPart['DETAIL_URL'] = CORE_ROOT_DIR.'/info/'.$arPart['SUP_BRAND_F'].'/'.$arPart['NUMBER_SHORT'];
				}else{
					$arPart['DETAIL_URL'] = CORE_ROOT_DIR.'/info/'.$arPart['ART_ID'];
				}				
			}
			
			$arPart['ART_ARTICLE_NR'] = $arLookBNum['ART_ARTICLE_NR'];
			$arPart['SUP_BRAND'] = $arLookBNum['SUP_BRAND']; // Rewrite "GetPartInfo" BRAND
			$arPart['ARL_KIND'] = $arLookBNum['ARL_KIND'];
			
			$CNum = $arPart['NUMBER_SHORT'];
			$arPARTS[$CNum]["ART_ARTICLE_NR"] = $arPart['ART_ARTICLE_NR'];
			$arPARTS[$CNum]["NUMBER"] = $CNum;
			$arPARTS[$CNum]["ARL_KIND"] = $arPart['ARL_KIND'];
			$arPARTS[$CNum]['BRANDS'][$arPart["SUP_BRAND"]] = $arPart;
			if($ART_TYPE=="SHORT"){$CTNum=$arPart['NUMBER_SHORT'];}else{$CTNum=$arPart['NUMBER'];}
			if(!in_array($CTNum,$arNUMBERs)){$arNUMBERs[]=$CTNum;}
			if(!in_array($arPart["SUP_BRAND"],$arBRANDs)){$arBRANDs[]=$arPart["SUP_BRAND"];}
			if($arPart['NUMBER_SHORT']==$arResult['SEARCH_NUMBER_SHORT']){
				$arPARTS[$CNum]['MAIN_GROUP']="Y";							//For view
				$arLowest_Prices[$CNum] = 0;								//Default minimum value for SORTING - MAIN GROUP of search
			}elseif($arLowest_Prices[$CNum]<=0){
				$arLowest_Prices[$CNum] = 999998;							//Default large value for SORTING by price
			}
		}
		if(USE_STATISTIC=="Y"){$arStat[] = Array("Lookup Numbers",microtime(true));}
		
		// Lookup in prices
		$TDataBase->DBSelect("CORE");
		$arSPrices = CShopPrice::GetList(Array("PRICE"=>"ASC"),Array("ART_NUM"=>$arResult['SEARCH_NUMBER_'.$ART_TYPE], "SUP_BRAND"=>$arResult['SEARCH_BRAND']));
		foreach($arSPrices as $arSPrice){
			$arPARTS[$arSPrice['ART_NUM']]['MAIN_GROUP']="Y";
			$arLowest_Prices[$arSPrice['ART_NUM']] = 0;						//Default minimum value for SORTING - MAIN GROUP of search
			if($arPARTS[$arSPrice['ART_NUM']]['NUMBER']==''){$arPARTS[$arSPrice['ART_NUM']]['NUMBER']=$arSPrice['ART_NUM'];}
			if($arPARTS[$arSPrice['ART_NUM']]['BRANDS'][$arSPrice["SUP_BRAND"]]['ART_ARTICLE_NR']==''){
				$arPARTS[$arSPrice['ART_NUM']]["ART_ARTICLE_NR"] = $arSPrice['ART_NUM'];
				$arPARTS[$arSPrice['ART_NUM']]['BRANDS'][$arSPrice["SUP_BRAND"]]['ART_ARTICLE_NR'] = $arSPrice['ART_NUM'];
			}
			if($arPARTS[$arSPrice['ART_NUM']]['BRANDS'][$arSPrice["SUP_BRAND"]]['PART_NAME']==''){ 
				$arPARTS[$arSPrice['ART_NUM']]['BRANDS'][$arSPrice["SUP_BRAND"]]['PART_NAME'] = $arSPrice['PART_NAME'];
			}
			if(!in_array($arSPrice['ART_NUM'],$arNUMBERs)){$arNUMBERs[]=$arSPrice['ART_NUM'];}
			if(!in_array($arSPrice["SUP_BRAND"],$arBRANDs)){$arBRANDs[]=$arSPrice["SUP_BRAND"];}
		}
		$TDataBase->DBSelect("TECDOC");
		
		// Get prices
		if(count($arNUMBERs)>0){
			$TDataBase->DBSelect("CORE");
			$arPrices = CShopPrice::GetList(Array(),Array("ART_NUM"=>$arNUMBERs, "WS"=>"Y", "PARTS"=>$arPARTS));
			foreach($arPrices as $arPrice){
				if(!$arPARTS[$arPrice['ART_NUM']]['BRANDS'][$arPrice['SUP_BRAND']]){continue;}
				//if(!in_array($arPrice['SUP_BRAND'],$arBRANDs)){continue;} //Hide new PRICES brands
				$PricesCnt++;
				$arPrice = TDCurrencyConvert($arPrice);
				if(trim($arPrice['PART_NAME'])==''){ $arPrice['PART_NAME'] = $arPARTS[$arPrice['ART_NUM']]['BRANDS'][$arPrice['SUP_BRAND']]['PART_NAME']; }
				$arPrice['ADDED_TO_CART'] = ProcessAddPartToBasket($arPrice,$arPARTS);
				//Make
				$arPARTS[$arPrice['ART_NUM']]['BRANDS'][$arPrice['SUP_BRAND']]['PRICES'][] = $arPrice;
				$arPARTS[$arPrice['ART_NUM']]['PRICES_COUNT']++;
				
				if($arPrice['PRICE']<$arLowest_Prices[$arPrice['ART_NUM']]){
					$arLowest_Prices[$arPrice['ART_NUM']]=$arPrice['PRICE'];
				}
				if(!in_array($arPrice["SUP_BRAND"],$arBRANDs)){$arBRANDs[]=$arPrice["SUP_BRAND"];}	//For filter by brands
			}
			if(USE_STATISTIC=="Y"){$arStat[] = Array("Get prices",microtime(true));}
		}
	}
	
	// Sort by lowest price
	if(count($arLowest_Prices)>1){
		asort($arLowest_Prices);
		$arToSort_Parts = $arPARTS; $arPARTS = Array();
		foreach($arLowest_Prices as $sNUMBER=>$LowP){
			$arPARTS[$sNUMBER] = $arToSort_Parts[$sNUMBER];
		}
	}
	
	sort($arBRANDs);
	sort($arNUMBERs);
	$arResult['PARTS'] = $arPARTS;
	$arResult['PARTS_BRANDS'] = $arBRANDs;
	$arResult['PARTS_NUMBERS'] = $arNUMBERs;
	
	// echo '<pre>';print_r($arLookBNum);echo '</pre>';
}

	
$this->ViewTemplate = true;

?>