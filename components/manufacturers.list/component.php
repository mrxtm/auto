<?if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die();?>
<?
$BCout=0;
$rsManuf = TDSQL::GetManufs();
while($arManuf = $rsManuf->GetNext()){
	if($arManuf['MFA_BRAND']=="JEEP VIASA"){continue;}
	global $TCore;
	$arSets = $TCore->arSettings['CATALOG']['MANUFACTURERS'];
	if(in_array($arManuf['MFA_MFC_CODE'],$arSets)){
		$BCout++;
		if($arManuf['MFA_BRAND']=="CITRO?N"){$arManuf['MFA_BRAND']="CITROEN";}
		$arMan['LINK'] = CORE_ROOT_DIR.'/'.str_replace(' ','_',mb_strtolower($arManuf['MFA_BRAND'])).'/';
		$arMan['LOGO_CODE'] = str_replace(' ','_',$arManuf['MFA_MFC_CODE']);
		$arMan['NAME'] = $arManuf['MFA_BRAND'];
		$arMan['CODE'] = $arManuf['MFA_MFC_CODE'];
		$arResult[] = $arMan;
	}
}
if($BCout>0){
	$this->ViewTemplate = true;
}
?>