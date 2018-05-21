<?if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die();?>
<?
$ART_ID = intval(substr($arParams['ART_ID'],0,16));
if($ART_ID>0){
	//Characteristics
	$rsProps = TDSQL::GetPropertys($ART_ID,TECDOC_LNG_ID);
	$arCRNAMEs=Array();
	while($arProps = $rsProps->GetNext()){
		$arResult['SHOW_CHARS']="Y"; $arResult['ACTIVE']="Y";
		if(!in_array($arProps['CRITERIA_DES_TEXT'],$arCRNAMEs)){
			$arResult['CHARS'][] = Array("NAME"=>$arProps['CRITERIA_DES_TEXT'], "VALUE"=>$arProps['CRITERIA_VALUE_TEXT']);
			$arCRNAMEs[] = $arProps['CRITERIA_DES_TEXT']; //Hide dublicate names
		}
	}
	
	//Original numbers
	$rsOrig = TDSQL::LookupAnalog($ART_ID,3);
	if($rsOrig->NumRows>0){
		$arResult['SHOW_ORIGINALS'] = "Y"; $arResult['ACTIVE']="Y";
		while($arOrig = $rsOrig->GetNext()){
			$arOrig['NUMBER'] = ArtToNumber($arOrig['ARL_DISPLAY_NR'],'FULL');
			$arResult['ORIGINALS'][] = Array("BRAND"=>$arOrig['BRAND'], "NUMBER"=>$arOrig['NUMBER'], "ARTICLE"=>$arOrig['ARL_DISPLAY_NR']);
		}
	}
	
		
}else{$arResult['ERROR'].= '<span class="error">'.TMes('Error').'! '.TMes('Unknown parts ID').'</span>';}
$this->ViewTemplate = true;

?>