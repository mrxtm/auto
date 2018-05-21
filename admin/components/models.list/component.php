<?if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die();?>
<?
$Brand = substr($arParams['BRAND'],0,25);
$Brand = trim($Brand);
$Brand = mysql_real_escape_string($Brand);
$UBrand = strtoupper($Brand);

if(strlen($UBrand)>0){
	//Brand
	$rsManuf = TDSQL::GetManufByID($UBrand);
	if($arManuf = $rsManuf->GetNext()){
		global $TDataBase; 
		$arResult['MFA_MFC_CODE'] = $arManuf['MFA_MFC_CODE'];
		//Set component settings data
		if(CORE_IS_ADMIN AND $_POST['set_com_sets']=="Y"){
			$TDataBase->DBSelect("CORE");
			if($_POST['addits']==""){$_POST['addits']="NOADD";}
			$NValue = $_POST['mode'].','.$_POST['addits'].','.$_POST['modyear'].',';
			$ModIDs = implode(',',$_POST['models']);
			$NValue = $NValue.$ModIDs;
			$TCore->SetComponentField($TCore->Included_Component,"FILTER_MODELS_".$arManuf['MFA_ID'],$NValue);
			$TDataBase->DBSelect("TECDOC");
		}
		//Get models filter
		$Reverse=false; $arModIDs=Array();
		$TDataBase->DBSelect("CORE");
		$strModIDs = $TCore->GetComponentField($TCore->Included_Component,"FILTER_MODELS_".$arManuf['MFA_ID']);
		if($strModIDs!=''){
			$arModIDs = explode(',',$strModIDs);
			$Mode = array_shift($arModIDs);
			$arResult['SHOW_ADDITIONALS'] = array_shift($arModIDs);
			$YearFrom = array_shift($arModIDs);
			if($Mode=="NOT"){$Reverse=true;}elseif($Mode=="OFF"){$arModIDs=Array();}
			$TCore->arComSets['SELECTED'] = $arModIDs;
			$TCore->arComSets['MODE'] = $Mode;
			$TCore->arComSets['ADDIT'] = $arResult['SHOW_ADDITIONALS'];
			$TCore->arComSets['YEAR'] = $YearFrom ;
		}
		$TDataBase->DBSelect("TECDOC");
		//Prepare settings data
		$TCore->arComSets['BRAND'] = $UBrand;
		$rsSMods = TDSQL::GetModels($arManuf['MFA_ID'],TECDOC_LNG_ID,Array(),false,$YearFrom);
		while($arSMod = $rsSMods->GetNext()){
			$arSMod = TDMakeModelItem($arSMod);
			$TCore->arComSets['MODELS'][] = $arSMod;
		}
		//Models list
		$rsModels = TDSQL::GetModels($arManuf['MFA_ID'],TECDOC_LNG_ID,$arModIDs,$Reverse,$YearFrom);
		if($rsModels->NumRows > 0){
			require_once($_SERVER["DOCUMENT_ROOT"].CORE_ROOT_DIR."/components/models.list/model_groups.php");
			$arTMods=Array(); $TCom=''; $TitleModels='';
			while($arModel = $rsModels->GetNext()){
				$arModel = TDMakeModelItem($arModel);
				foreach($arHardGroups[$UBrand] as $GrMod){
					if(strstr($arModel['MOD_CDS_TEXT'],$GrMod)){
						$arModel['GROUPED']="Y";
						if(!in_array($GrMod,$arTMods)){$arTMods[] = $GrMod;}
						$CurModel = $GrMod;
					}
				}
				if($arModel['GROUPED']!="Y"){
					$arMd = explode(' ',$arModel['MOD_CDS_TEXT']);
					if(!in_array($arMd[0],$arTMods)){
						$arTMods[] = $arMd[0];
						$CurModel = $arMd[0];
					}
				}
				if($arRenamesRegroup[$UBrand][$CurModel]!=''){$CurModel=$arRenamesRegroup[$UBrand][$CurModel];}
				if((string)intval($CurModel)==$CurModel AND !strpos($CurModel,' ')>0){$CurModel=$CurModel.' ';}
				$PicModelName=str_replace(' ','_',$CurModel); 
				$PicModelName=str_replace('/','_',$PicModelName); 
				$ModelPicSrc = CORE_ROOT_DIR.'/media/models/'.str_replace('ë','e',$Brand).'/'.$PicModelName.'.jpg';
				if(file_exists($_SERVER["DOCUMENT_ROOT"].$ModelPicSrc)){$arResult['MODEL_PICS'][$CurModel] = $ModelPicSrc;}
				else{$arResult['MODEL_PICS'][$CurModel] = CORE_ROOT_DIR.'/media/models/default.jpg';}
				$arResult['MODELS'][$CurModel][] = $arModel;
			}
			foreach($arTMods as $TMod){$TitleModels.=$TCom.$TMod; $TCom=', ';}
			$arResult['UBRAND'] = $UBrand;
			$arResult['BRAND'] = $Brand;
			
			//Sorting
			$Sorts = Array();
			foreach($arResult['MODELS'] as $GrName=>$arMod){ $Sorts[] = $GrName; }
			array_multisort($Sorts,SORT_ASC,SORT_STRING,$arResult['MODELS']);
			
			//Meta
			global $TDataBase;
			$TDataBase->DBSelect("CORE");
			if($TCore->Head_Title_H1==""){ $TCore->Head_Title_H1 = TMes('Model selection').' - '.str_replace('_',' ',$UBrand); }
			$TCore->ComponentMetaData("models.list", Array(
					"BRAND"=>$arResult['UBRAND'],
					"MODELS_LIST"=>$TitleModels
				)
			);
			
		}else{$arResult['ERROR'].='<div class="psys_error">'.TMes('Error').' '.TMes('There is no models of brand').' "'.$UBrand.'"</div>';}
	}else{$arResult['ERROR'].='<div class="psys_error">'.TMes('Error').' '.TMes('There is no brand').' "'.$UBrand.'"</div>';}
}else{$arResult['ERROR'].='<div class="psys_error">'.TMes('Error').' '.TMes('Brand not specified').' "'.$UBrand.'"</div>';}
$this->ViewTemplate = true;

function TDMakeModelItem($arModel){
	if(strpos($arModel['MOD_CDS_TEXT'],'(US)')>0){$arModel['MOD_CDS_TEXT']=str_replace('[USA]','',$arModel['MOD_CDS_TEXT']);}
	$arModel['DATE_FROM'] = TDDateFormat($arModel['MOD_PCON_START'],TMes('to p.t.'),'year');
	$arModel['DATE_TO'] = TDDateFormat($arModel['MOD_PCON_END'],TMes('to p.t.'),'year');
	return $arModel;
}
?>