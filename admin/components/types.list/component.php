<?if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die();?>
<?
$Brand = substr($arParams['BRAND'],0,25);
$Brand = trim($Brand);
$Brand = mysql_real_escape_string($Brand);
$UBrand = strtoupper($Brand);
$MOD_ID = intval($arParams['MODEL_ID']);
if(strlen($UBrand)>0){
	if($MOD_ID>0){
		//Brand
		$rsManuf = TDSQL::GetManufByID($UBrand);
		if($arManuf = $rsManuf->GetNext()){
			//Model
			$rsModel = TDSQL::GetModelByID($arManuf['MFA_ID'],$MOD_ID,TECDOC_LNG_ID);
			if($arModel = $rsModel->GetNext()){
				//Types
				$rsTypes = TDSQL::GetTypes($arModel['MOD_ID'],TECDOC_LNG_ID);
				if($rsTypes->NumRows > 0){
					$DateFr = TDDateFormat($arModel['MOD_PCON_START'],TMes('to p.t.'));
					$DateTo = TDDateFormat($arModel['MOD_PCON_END'],TMes('to p.t.'));
					while($arType = $rsTypes->GetNext()){
						$arResult['TYPES'][] = $arType;
					}
					$arResult['BRAND'] = $Brand;
					$arResult['UBRAND'] = $UBrand;
					$arResult['MOD_ID'] = $MOD_ID;
					$arResult['MODEL'] = $arModel['MOD_CDS_TEXT'].' ('.TMes('from').' '.$DateFr.' '.TMes('to').' '.$DateTo.')';
					$ModelPicSrc = CORE_ROOT_DIR.'/media/types/'.$Brand.'/'.$MOD_ID.'.png';
					if(file_exists($_SERVER["DOCUMENT_ROOT"].$ModelPicSrc)){$arResult['MODEL_PIC'] = $ModelPicSrc;}
					
					//Meta
					global $TDataBase;
					$TDataBase->DBSelect("CORE");
					$TCore->ComponentMetaData("types.list", Array(
							"BRAND"=>$arResult['UBRAND'],
							"MODEL"=>$arModel['MOD_CDS_TEXT'],
							"MODEL_YEAR"=>$arResult['MODEL'],
						)
					);
						
				}else{$arResult['ERROR'].='<div class="psys_error">'.TMes('Error').' '.TMes('There is no types of brand').' "'.$UBrand.'" [model:'.$MOD_ID.']</div>';}
			}else{$arResult['ERROR'].='<div class="psys_error">'.TMes('Error').' '.TMes('There is no model').' "'.$UBrand.'" [model:'.$MOD_ID.']</div>';}
		}else{$arResult['ERROR'].='<div class="psys_error">'.TMes('Error').' '.TMes('There is no brand').' "'.$UBrand.'"</div>';}
	}else{$arResult['ERROR'].='<div class="psys_error">'.TMes('Error').' '.TMes('Model ID not specified').'</div>';}
}else{$arResult['ERROR'].='<div class="psys_error">'.TMes('Error').' '.TMes('Brand not specified').'</div>';}
$this->ViewTemplate = true;

?>