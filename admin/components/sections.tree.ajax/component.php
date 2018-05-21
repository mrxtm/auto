<?if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die();?>
<?
$Brand = substr($arParams['BRAND'],0,25);
$Brand = trim($Brand);
$Brand = mysql_real_escape_string($Brand);
$UBrand = strtoupper($Brand);
$MOD_ID = intval($arParams['MODEL_ID']);
$TYP_ID = intval($arParams['TYPE_ID']);
if(strlen($UBrand)>0){
	if($MOD_ID>0){
		if($TYP_ID>0){
			//Brand
			$rsManuf = TDSQL::GetManufByID($UBrand);
			if($arManuf = $rsManuf->GetNext()){
				//Model
				$rsModel = TDSQL::GetModelByID($arManuf['MFA_ID'],$MOD_ID,TECDOC_LNG_ID);
				if($arModel = $rsModel->GetNext()){
					$DateFr = TDDateFormat($arModel['MOD_PCON_START'],TMes('to p.t.'));
					$DateTo = TDDateFormat($arModel['MOD_PCON_END'],TMes('to p.t.'));
					$arResult['MODEL'] = $arModel['MOD_CDS_TEXT']; //.' ('.TMes('from').' '.$DateFr.' '.TMes('to').' '.$DateTo.')';
					//Type
					$rsType = TDSQL::GetTypeByID($arModel['MOD_ID'],$TYP_ID,TECDOC_LNG_ID);
					if($arType = $rsType->GetNext()){
						$DateFr = TDDateFormat($arType['TYP_PCON_START'],TMes('to p.t.'));
						$DateTo = TDDateFormat($arType['TYP_PCON_END'],TMes('to p.t.'));
						$arResult['BRAND'] = $Brand;
						$arResult['UBRAND'] = $UBrand;
						$arResult['MOD_ID'] = $MOD_ID;
						$arResult['SEC_ID'] = intval($arParams['SEC_ID']);
						$arResult['TYPE_ID'] = $TYP_ID;
						$arResult['TYPE'] = $arType['TYP_CDS_TEXT'].' '.$arType['TYP_KW_FROM'].' <span>'.TMes('Kv').'</span> - '.$arType['TYP_HP_FROM'].' <span>'.TMes('Hp').'</span> '.$arType['TYP_FUEL_DES_TEXT'].' '.$arType['TYP_BODY_DES_TEXT'].' ('.TMes('from').' '.$DateFr.' '.TMes('to').' '.$DateTo.')';
						$ModelPicSrc = CORE_ROOT_DIR.'/media/types/'.$Brand.'/'.$MOD_ID.'.png';
						if(file_exists($_SERVER["DOCUMENT_ROOT"].$ModelPicSrc)){$arResult['MODEL_PIC'] = $ModelPicSrc;}
						
						//Meta
						global $TDataBase;
						$TDataBase->DBSelect("CORE");
						$TCore->ComponentMetaData("sections.tree", Array(
								"BRAND"=>$arResult['UBRAND'],
								"MODEL"=>$arModel['MOD_CDS_TEXT'],
								"TYPE"=>$arType['TYP_CDS_TEXT'],
								"TYPE_FULL"=>$arType['TYP_CDS_TEXT'].' '.$arType['TYP_KW_FROM'].' '.TMes('Kv').' - '.$arType['TYP_HP_FROM'].' '.TMes('Hp').' '.$arType['TYP_FUEL_DES_TEXT'].' '.$arType['TYP_BODY_DES_TEXT'].' ',
							)
						);
						
					}else{$arResult['ERROR'].='<div class="psys_error">'.TMes('Error').' '.TMes('There is no types of brand').' "'.$UBrand.'" [model:'.$MOD_ID.', type:'.$TYP_ID.']</div>';}
				}else{$arResult['ERROR'].='<div class="psys_error">'.TMes('Error').' '.TMes('There is no model').' "'.$UBrand.'" [model:'.$MOD_ID.']</div>';}
			}else{$arResult['ERROR'].='<div class="psys_error">'.TMes('Error').' '.TMes('There is no brand').' "'.$UBrand.'"</div>';}
		}else{$arResult['ERROR'].='<div class="psys_error">'.TMes('Error').' '.TMes('Type ID not specified').'</div>';}
	}else{$arResult['ERROR'].='<div class="psys_error">'.TMes('Error').' '.TMes('Model ID not specified').'</div>';}
}else{$arResult['ERROR'].='<div class="psys_error">'.TMes('Error').' '.TMes('Brand not specified').'</div>';}
$this->ViewTemplate = true;

?>