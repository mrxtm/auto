<?if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die();?>
<?
$ART_ID = intval(substr($arParams['ART_ID'],0,16));
if($ART_ID<=0 AND $arParams['SUP_BRAND']!='' AND $arParams['NUMBER']!=''){
	$arResult['CUR_URL'] = CORE_ROOT_DIR.'/info/'.$arParams['SUP_BRAND'].'/'.$arParams['NUMBER'];
	$arParams['SUP_BRAND'] = BrandNameDecode($arParams['SUP_BRAND']);
	$arRes = TDSQL::GetArtIDByNBName($arParams['NUMBER'],$arParams['SUP_BRAND']);
	$ART_ID = intval($arRes['ART_ID']);
}else{
	$arResult['CUR_URL'] = CORE_ROOT_DIR.'/info/'.$ART_ID;
}

if($ART_ID>0){
	global $TCore;
	if($TCore->arSettings['MAIN']['PRICES_ART_TYPE']=="SHORT"){$ART_TYPE="SHORT";}else{$ART_TYPE="FULL";}
	
	global $arStat;
	$arPart = TDSQL::GetPartInfo($ART_ID,TECDOC_LNG_ID);
	if($arPart['ART_ID']>0){
		$arPart = PartTexts($arPart);
		$arResult['ART_ID'] = $arPart['ART_ID'];
		global $arBCous;
		
		$arPart['NUMBER'] = ArtToNumber($arPart['ART_ARTICLE_NR'],$ART_TYPE);
		
		//Meta
		global $TDataBase;
		$TDataBase->DBSelect("CORE");
		TMes('Search');
		$TCore->ComponentMetaData("parts.detail", Array(
				"NAME"=>$arPart['PART_NAME'],
				"BRAND"=>$arPart['SUP_BRAND'],
				"NUMBER"=>$arPart['ART_ARTICLE_NR'],
			)
		);
		$TDataBase->DBSelect("TECDOC");
		
		//Brand logo
		if($TCore->arSettings['MAIN']['TECDOC_FILES_PREFIX']!=''){
			$rsBLogo = TDSQL::GetBrandLogo($ART_ID);
			while($arBLogo = $rsBLogo->GetNext()){
				$arResult['BRAND_LOGO'] = TECDOC_FILES_PREFIX.$arBLogo['PATH'];
			}
		}
		//More Info
		$rsDopInfo = TDSQL::GetDopInfo($ART_ID,TECDOC_LNG_ID);
		while($arDopInfo = $rsDopInfo->GetNext()){
			$arResult['SHOW_INFO'] = "Y";
			$arResult['INFO'][] = $arDopInfo['AIN_TMO_TEXT'];
		}
		if(USE_STATISTIC=="Y"){$arStat[] = Array("More Info",microtime(true));}
		if($TCore->arSettings['MAIN']['TECDOC_FILES_PREFIX']!=''){
			//PHOTO
			$rsImgs = TDSQL::GetImages($arPart['ART_ID'],TECDOC_LNG_ID);
			while($arImg = $rsImgs->GetNext()){
				$arResult['SHOW_IMGS'] = "Y";
				$arResult['IMGS'][] = TECDOC_FILES_PREFIX.$arImg['PATH'];
				if($FstImg==0){$arPart['IMG'] = $arImg['PATH']; $FstImg=1;}
			}
			//PDF
			$rsPdfs = TDSQL::GetPDFs($arPart['ART_ID'],TECDOC_LNG_ID);
			while($arPdf = $rsPdfs->GetNext()){
				$arResult['SHOW_PDFS'] = "Y";
				$arResult['PDFS'][] = TECDOC_FILES_PREFIX.$arPdf['PATH'];
			}
			if(USE_STATISTIC=="Y"){$arStat[] = Array("Foto & pdf",microtime(true));}
		}
		//Characteristics
		$rsProps = TDSQL::GetPropertys($ART_ID,TECDOC_LNG_ID);
		$arCRNAMEs=Array();
		while($arProps = $rsProps->GetNext()){
			$arResult['SHOW_CHARS'] = "Y";
			if(!in_array($arProps['CRITERIA_DES_TEXT'],$arCRNAMEs)){
				$arResult['CHARS'][] = Array("NAME"=>$arProps['CRITERIA_DES_TEXT'], "VALUE"=>$arProps['CRITERIA_VALUE_TEXT']);
				$arCRNAMEs[] = $arProps['CRITERIA_DES_TEXT']; //Hide dublicate names
			}
		}
		if(USE_STATISTIC=="Y"){$arStat[] = Array("Chars.",microtime(true));}
		//Original numbers
		$rsOrig = TDSQL::LookupAnalog($arPart['ART_ID'],3);
		if($rsOrig->NumRows>0){
			$arResult['SHOW_ORIGINALS'] = "Y";
			while($arOrig = $rsOrig->GetNext()){
				$arOrig['NUMBER'] = ArtToNumber($arOrig['ARL_DISPLAY_NR']);
				$arResult['ORIGINALS'][] = Array("BRAND"=>$arOrig['BRAND'], "NUMBER"=>$arOrig['NUMBER'], "ARTICLE"=>$arOrig['ARL_DISPLAY_NR']);
			}
		}
		if(USE_STATISTIC=="Y"){$arStat[] = Array("Originals",microtime(true));}
		//Supplier Country
		if($arPart['SUP_ID']){
			$rsSupl = TDSQL::SupplierCountry($arPart['SUP_ID'],TECDOC_LNG_ID);
			if($rsSupl->NumRows>0){
				if($arSupl = $rsSupl->GetNext()){
					$arResult['SHOW_SUP_COUNTRY'] = "Y";
					$arResult['SUP_COUNTRY'] = Array("CODE"=>$arSupl['COU_ISO2'], "NAME"=>$arSupl['COU_DES_TEXT']);
				}
			}
		}
		if($arResult['SUP_COUNTRY']['CODE']==''){
			$arResult['SUP_COUNTRY']['CODE']=$arBCous[$arPart['SUP_BRAND']];
			if($arResult['SUP_COUNTRY']['CODE']!=''){$arResult['SUP_COUNTRY']['NAME']=$arResult['SUP_COUNTRY']['CODE']; $arResult['SHOW_SUP_COUNTRY'] = "Y";}
		}
		if(USE_STATISTIC=="Y"){$arStat[] = Array("Country",microtime(true));}
		//Applicability
		$rsATypes = TDSQL::GetAutoStack($ART_ID,TECDOC_LNG_ID);
		if($rsATypes->NumRows>0){
			$arResult['SHOW_APPLICS'] = "Y";
			while($arAType = $rsATypes->GetNext()){
				$arAType['DATE_FROM'] = TDDateFormat($arAType['TYP_PCON_START'],TMes('to p.t.'));
				$arAType['DATE_TO'] = TDDateFormat($arAType['TYP_PCON_END'],TMes('to p.t.'));
				$arAType['MFA_LOW_BRAND'] = strtolower($arAType['MFA_BRAND']);
				$arResult['APPLICS'][] = $arAType;
			}
		}
		if(USE_STATISTIC=="Y"){$arStat[] = Array("Applicability",microtime(true));}
		//Get prices
		$TDataBase->DBSelect("CORE");
		$arWSParts[$arPart['NUMBER']]['BRANDS'][$arPart['SUP_BRAND']] = ""; //WS prices filter (by brand)
		$arPrices = CShopPrice::GetList(Array("PRICE"=>"ASC"),Array("ART_NUM"=>$arPart['NUMBER'], "WS"=>"Y", "PARTS"=>$arWSParts, "SUP_BRAND"=>$arPart['SUP_BRAND']));
		foreach($arPrices as $arPrice){
			$arResult['SHOW_PRICES'] = "Y";
			$arPrice = TDCurrencyConvert($arPrice);
			if(trim($arPrice['PART_NAME'])==''){ $arPrice['PART_NAME'] = $arPart['PART_NAME']; }
			//Process add to cart
			if($_POST['AddPartToCart']=="Y" AND $_POST['ID']==$arPrice['ID']){
				$arBFields = $arPrice;
				$arBFields['ART_ARTICLE_NR'] = $arPart['ART_ARTICLE_NR'];
				$arBFields['ART_ID'] = $arPart['ART_ID'];
				$arBFields['IMG'] = $arPart['IMG'];
				$arPart['ADDED_TO_CART'] = AddPartToBasket($arBFields,$_POST['count']);
			}
			$arResult['PRICES'][$arPrice['SUP_BRAND']]['PRICES'][] = $arPrice;
		}
		if(USE_STATISTIC=="Y"){$arStat[] = Array("Get prices",microtime(true));}
		$TDataBase->DBSelect("TECDOC");
		
		$arResult['PART'] = $arPart;
		
	}else{$arResult['ERROR'].= '<span class="error">'.TMes('Error').'! '.TMes('Incorrect parts ID').'</span>';}
}else{$arResult['ERROR'].= '<span class="error">'.TMes('Error').'! '.TMes('Unknown parts ID').'</span>';}
$this->ViewTemplate = true;

?>