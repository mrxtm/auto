<?if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die();?>
<?
function GetWSPrices($arPrices, $arNUMBERs, $arArtBrands=Array()){
	//Work with array type only
	if(!is_array($arNUMBERs) AND $arNUMBERs!=''){$arNUMBERs=Array($arNUMBERs);}
	
	//Extra price ranges
	$arExRanges = Array(
		0 => 1.4,
		1000 => 1.35,
		2000 => 1.3,
		5000 => 1.25,
		10000 => 1.2
	);
	
	///////////////////////////////////////////////////////
	//// ezoko.ru
	///////////////////////////////////////////////////////
	/*$key = 'SOAPEB1F6-631DE92B8-9A21028B7-AD8632***';
	$client = new SoapClient("http://api.ezoko.ru/wsdl/v1",array('trace'=>1, 'exceptions'=>0, 'encoding'=>'UTF-8'));
	$result = $client->DetailSearch($key, $arNUMBERs, 'No'); // ключ, массив номеров, показывать ли замены
	foreach($result as $obRes){
		$arAPrice = Array();
		$arAPrice['SUP_BRAND'] =  StrToUp((string)$obRes->Vendor);
		$arAPrice['ART_ARTICLE_NR'] = (string)$obRes->PartNumber;
		if(count($arArtBrands)>0 AND !in_array($arAPrice['SUP_BRAND'],$arArtBrands[$arAPrice['ART_ARTICLE_NR']])){continue;}
		$arAPrice['ART_NUM'] = ArtToNumber($arAPrice['ART_ARTICLE_NR']);
		$arAPrice['PART_NAME'] = (string)$obRes->DescriptionRu;
		if(trim($arAPrice['PART_NAME'])==''){$arAPrice['PART_NAME'] = (string)$obRes->DescriptionEn;}
		foreach($obRes->Prices as $obPrice){
			$arAPrice['PRICE'] = (string)$obPrice->Price;
			$arAPrice['CURRENCY'] = "RUB";
			if($arAPrice['PRICE']>0){
				$arAPrice['PRICE'] = round($arAPrice['PRICE']+(($arAPrice['PRICE']/100)*30),2);
				$arAPrice['PRICE'] = $arAPrice['PRICE'] * 32; 
 			}
			$arAPrice['DAY'] = (string)$obPrice->DeliveryTime;
			$arAPrice['AVAILABLE'] = (string)$obPrice->Quantity;
			if($arAPrice['AVAILABLE']>1000){$arAPrice['AVAILABLE']='999+';}
			$arAPrice['SUPPLIER'] = (string)$obPrice->SupplierId.' - ezoko.ru';
			$arAPrice['ID'] = md5($arAPrice['ART_NUM'].$arAPrice['SUP_BRAND'].$arAPrice['PRICE'].$arAPrice['DAY'].$arAPrice['SUPPLIER']);
			$arPrices[] = $arAPrice;
		}
	}
	///////////////////////////////////
	*/
	
	
	///////////////////////////////////////////////////////
	//// mikado-parts.ru
	//// http://www.mikado-parts.ru/ws/service.asmx
	///////////////////////////////////////////////////////
	/*$client = new SoapClient("http://www.mikado-parts.ru/ws/service.asmx?WSDL");
	foreach($arNUMBERs as $Number){
		$result = $client->Code_Search(Array("Search_Code"=>$Number, "ClientID"=>000, "Password"=>"xxxxxx"));
		foreach($result->Code_SearchResult->List->Code_List_Row as $obRes){
			$arAPrice = Array();
			$arAPrice['SUP_BRAND'] =  StrToUp((string)$obRes->ProducerBrand);
			$arAPrice['ART_ARTICLE_NR'] = StrToUp((string)$obRes->ProducerCode);
			if(count($arArtBrands)>0 AND !in_array($arAPrice['SUP_BRAND'],$arArtBrands[$arAPrice['ART_ARTICLE_NR']])){continue;}
			$arAPrice['ART_NUM'] = ArtToNumber($arAPrice['ART_ARTICLE_NR']);
			$arAPrice['PART_NAME'] = (string)$obRes->Name;
			$arAPrice['PRICE'] = (string)$obRes->PriceRUR;
			foreach($arExRanges as $Range=>$Extra){ if($arAPrice['PRICE']<$Range){break;}else{$arAPrice['PriceExtra'] = $Extra;} }
			$arAPrice['PRICE'] = round(($arAPrice['PRICE']*$arAPrice['PriceExtra']),2);
			$arAPrice['CURRENCY'] = "RUB";
			$arAPrice['DAY'] = 1;
			$arAPrice['AVAILABLE'] = (string)$obRes->OnStock;
			$arAPrice['SUPPLIER'] = (string)$obRes->Supplier.' - mikado';
			$arAPrice['ID'] = md5($arAPrice['ART_NUM'].$arAPrice['SUP_BRAND'].$arAPrice['PRICE'].$arAPrice['DAY'].$arAPrice['SUPPLIER']);
			if(!in_array($arAPrice['ID'],$UnIDs)){
				$arPrices[] = $arAPrice;
				$UnIDs[] = $arAPrice['ID'];
			}
		}
	}
	///////////////////////////////////
	*/
	
	
	///////////////////////////////////////////////////////
	//// avtoto.ru
	//// http://www.avtoto.ru/services/search/docs/
	//// Сервис поиска предложений будет работать в случае выполнения условия: сумма заказов / количество запросов > 20 после некоторого порога проценок.
	///////////////////////////////////////////////////////
	/*$client = new SoapClient("http://www.avtoto.ru/services/search/soap.wsdl",array('soap_version' => SOAP_1_1));
	foreach($arNUMBERs as $Number){
		$result = $client->SearchParts($params = array(
			'user_id' => 00000,
			'user_login' => 'xxxxxx',
			'user_password' => 'xxxxxx',	
			'search_code' => $Number,
			'search_cross' => 'off'
		));
		foreach($result['Parts'] as $arRes){
			$arAPrice = Array();
			$arAPrice['SUP_BRAND'] =  StrToUp($arRes['Manuf']);
			$arAPrice['ART_ARTICLE_NR'] = StrToUp($arRes['Code']);
			if(count($arArtBrands)>0 AND !in_array($arAPrice['SUP_BRAND'],$arArtBrands[$arAPrice['ART_ARTICLE_NR']])){continue;}
			$arAPrice['ART_NUM'] = ArtToNumber($arAPrice['ART_ARTICLE_NR']);
			$arAPrice['PART_NAME'] = $arRes['Name'];
			foreach($arExRanges as $Range=>$Extra){ if($arRes['Price']<$Range){break;}else{$arRes['PriceExtra'] = $Extra;} }
			$arAPrice['PRICE'] = round(($arRes['Price']*$arRes['PriceExtra']),2);
			$arAPrice['CURRENCY'] = "RUB";
			$arAPrice['DAY'] = $arRes['Delivery'];
			$arAPrice['AVAILABLE'] = $arRes['MaxCount'];
			$arAPrice['SUPPLIER'] = $arRes['Storage'].' - avtoto';
			$arAPrice['ID'] = md5($arAPrice['ART_NUM'].$arAPrice['SUP_BRAND'].$arAPrice['PRICE'].$arAPrice['DAY'].$arAPrice['SUPPLIER']);
			$arPrices[] = $arAPrice;
		}
	}*/
	///////////////////////////////////
	
	
	//echo '<pre>';print_r($result);echo '</pre>';
		
	
	return $arPrices;
}


?>