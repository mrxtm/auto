<?if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die();?>
<?
global $arStat;
global $TCore;
if($TCore->arSettings['MAIN']['PRICES_ART_TYPE']=="SHORT"){$ART_TYPE="SHORT";}else{$ART_TYPE="FULL";}
if(USE_STATISTIC=="Y"){$arStat[] = Array("Component start",microtime(true));}
$Brand = substr($arParams['BRAND'],0,25);
$Brand = trim($Brand);
$Brand = mysql_real_escape_string($Brand);
$UBrand = strtoupper($Brand);
$MOD_ID = intval($arParams['MODEL_ID']);
$TYP_ID = intval($arParams['TYPE_ID']);
$arParams['SECTION_ID'] = intval($arParams['SECTION_ID']);
if($arParams['SECTION_NAME']!=''){
	$Section = urldecode(substr($arParams['SECTION_NAME'],0,255));
	$Section = str_replace('|','/',$Section);
	$WSection = str_replace('_',' ',$Section);
	$Section = str_replace('_','%',$Section);
}
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
					$arResult['MODEL'] = $arModel['MOD_CDS_TEXT'].' ('.TMes('from').' '.$DateFr.' '.TMes('to').' '.$DateTo.')';
					//Type
					$rsType = TDSQL::GetTypeByID($arModel['MOD_ID'],$TYP_ID,TECDOC_LNG_ID);
					if($arType = $rsType->GetNext()){
						$DateFr = TDDateFormat($arType['TYP_PCON_START'],TMes('to p.t.'));
						$DateTo = TDDateFormat($arType['TYP_PCON_END'],TMes('to p.t.'));
						$arResult['BRAND'] = $Brand;
						$arResult['UBRAND'] = $UBrand;
						$arResult['MOD_ID'] = $MOD_ID;
						$arResult['TYPE_ID'] = $TYP_ID;
						$arResult['SECTION_ID'] = $arParams['SECTION_ID'];
						$arResult['TYPE'] = $arType['TYP_CDS_TEXT'].' '.$arType['TYP_KW_FROM'].' <span>'.TMes('Kv').'</span> - '.$arType['TYP_HP_FROM'].' <span>'.TMes('Hp').'</span> '.$arType['TYP_FUEL_DES_TEXT'].' '.$arType['TYP_BODY_DES_TEXT'].' ('.TMes('from').' '.$DateFr.' '.TMes('to').' '.$DateTo.')';
						$arResult['BACK_LINK'] = CORE_ROOT_DIR.'/'.$arResult['BRAND'].'/m'.$arResult['MOD_ID'].'/t'.$arResult['TYPE_ID'].'/';
						$arResult['BACK_NAME'] = TMes('Back to the category selection');
						$arResult['COMPONENT'] = 'SECTION';
						if($WSection!=''){
							$arResult['SECTION_NAME'] = $WSection;
						}else{
							$arRes = TDSQL::GetSectionName($arParams['SECTION_ID'],TECDOC_LNG_ID); 	
							$arResult['SECTION_NAME'] = $arRes['TEX_TEXT'];
						}
						$ModelPicSrc = CORE_ROOT_DIR.'/media/models/'.$Brand.'/'.$MOD_ID.'.png';
						if(file_exists($_SERVER["DOCUMENT_ROOT"].$ModelPicSrc)){$arResult['MODEL_PIC'] = $ModelPicSrc;}
						
						//Meta
						global $TDataBase;
						$TDataBase->DBSelect("CORE");
						$TCore->ComponentMetaData("parts.by.section", Array(
								"BRAND"=>$arResult['UBRAND'],
								"MODEL"=>$arModel['MOD_CDS_TEXT'],
								"TYPE"=>$arType['TYP_CDS_TEXT'],
								"SECTION"=>$arResult['SECTION_NAME'],
							)
						);
						$TDataBase->DBSelect("TECDOC");
												
						
						if(USE_STATISTIC=="Y"){$arStat[] = Array("Get M/M/T",microtime(true));}
						
						//Get parts
						///////////////////////////////////////////////////////////////////////////////////////////////////////
						$arNUMBERs = Array(); $arARTIDs = Array(); $arBRANDs = Array(); $arORIGINALs = Array(); $arLowestPrices = Array(); 
						if($arParams['SECTION_ID']>0){
							$rsPRes = TDSQL::GetParts2($arType['TYP_ID'],$arParams['SECTION_ID'],TECDOC_GETPARTS_LIMIT);
							while($arPRes = $rsPRes->GetNext()){
								if($arPRes['ART_ARTICLE_NR']!='' AND $arPRes['LA_ART_ID']>0){
									$NumF = ArtToNumber($arPRes['ART_ARTICLE_NR'],$ART_TYPE);
									if($ART_TYPE=='SHORT'){$arART_FNUMs[$NumF]=ArtToNumber($arPRes['ART_ARTICLE_NR'],'FULL');} //save full number links
									if(!in_array($NumF,$arNUMBERs)){$arNUMBERs[]=$NumF; $arARTIDs[] = $arPRes['LA_ART_ID'];}
								}
							}
						}
						
						if(USE_STATISTIC=="Y"){$arStat[] = Array("Get parts",microtime(true));}
						//Get original numbers
						/*foreach($arARTIDs as $cARTID){ //Узнать оригиналы (если в выборке только их аналоги - нужны для линковки кроссов)
							$rsOrig = TDSQL::LookupAnalog($cARTID,3);
							while($arOrig = $rsOrig->GetNext()){
								echo '<pre>';print_r($arOrig);echo '</pre>'; // !!!!!!!!!
								$NumF = ArtToNumber($arOrig['ARL_DISPLAY_NR']);
								if(!in_array($NumF,$arNUMBERs)){
									$arNUMBERs[] = $NumF;
									$rsLookNum = TDSQL::LookupByNumber(Array($NumF),TECDOC_LNG_ID);
									if($arLookNum = $rsLookNum->GetNext()){
										if(!in_array($arLookNum['ART_ID'],$arARTIDs)){$arARTIDs[] = $arLookNum['ART_ID']; $arORIGINALs[$arLookNum['ART_ID']]=$NumF;}
									}
								}
							}
						}*/
						//Get Added TecDoc crosses
						/*if(count($arNUMBERs)>0){
							$TDataBase->DBSelect("CORE");
							$rsCross = CShopCross::GetLinks("CROSS_NUMS",$arNUMBERs);
							while($arCross = $rsCross->GetNext()){
								$arECross = explode(';',$arCross['ORIGINAL_NUMS']);
								foreach($arECross as $ECross){
									if($ECross!='' AND !in_array($ECross,$arNUMBERs)){$arCrNumbers[] = Array("NUMBER"=>$ECross,"BRAND"=>$arCross['ORIGINAL_BRAND']);}
								}
							}
							echo '<pre>';print_r($arCrNumbers);echo '</pre>';
							$rsCross = CShopCross::GetLinks("ORIGINAL_NUMS",$arNUMBERs);
							while($arCross = $rsCross->GetNext()){
								$arECross = explode(';',$arCross['CROSS_NUMS']);
								foreach($arECross as $ECross){
									if($ECross!='' AND !in_array($ECross,$arNUMBERs)){$arCrNumbers[] = Array("NUMBER"=>$ECross,"BRAND"=>$arCross['CROSS_BRAND']);}
								}
							}
							if(USE_STATISTIC=="Y"){$arStat[] = Array("Get crosses",microtime(true));}
							$TDataBase->DBSelect("TECDOC");
							if(count($arCrNumbers)>0){
								foreach($arCrNumbers as $arCrNumber){
									$rsLookNum = TDSQL::GetIDByNumber($arCrNumber['NUMBER'],TECDOC_LNG_ID);
									while($arLookNum = $rsLookNum->GetNext()){
										if(($arLookNum['BRAND']==$arCrNumber['BRAND'] OR $arCrNumber['BRAND']=="") AND !in_array($arLookNum['ART_ID'],$arARTIDs)){ //Если Бренд не указан в БД кроссов то возможно, выведутся запчасти не из этой категории, совпавшие по полю NUMBER
											$arNUMBERs[] = $arCrNumber['NUMBER']; 
											$arARTIDs[] = $arLookNum['ART_ID'];
										}
									}
								}
								if(USE_STATISTIC=="Y"){$arStat[] = Array("Make TD crosses",microtime(true));}
							}
						}*/
								
						
						//Make PARTS
						$arPARTS = Array(); $arInfoAllParts = Array();
						foreach($arARTIDs as $ARTID){
							$PartsCnt++;
							$arPart = TDSQL::GetPartInfo($ARTID,TECDOC_LNG_ID); 					//Detail info: ART_ID, ART_ARTICLE_NR, SUP_BRAND, ART_COMPLETE_DES_TEXT, STR_DES_TEXT
							$arPart = PartTexts($arPart);											//PART_NAME, SUP_BRAND_F, NUMBER, NUMBER_SHORT
							$arPart = FirstPic($arPart);											//IMG
							if($arPart['SUP_BRAND_F']!=''){
								$arPart['DETAIL_URL'] = CORE_ROOT_DIR.'/info/'.$arPart['SUP_BRAND_F'].'/'.$arPart['NUMBER_SHORT'];
							}else{
								$arPart['DETAIL_URL'] = CORE_ROOT_DIR.'/info/'.$arPart['ART_ID'];
							}
							//$arInfoAllParts[] = $arPart;
							$CurNumber = $arPart["NUMBER"];
							$arPARTS[$CurNumber]["ART_ARTICLE_NR"] = $arPart["ART_ARTICLE_NR"]; 				//Save one ARTICLE for all
							$arPARTS[$CurNumber]["NUMBER"] = $CurNumber; 										//Save one NUMBER for all
							$arPARTS[$CurNumber]['BRANDS'][$arPart["SUP_BRAND"]] = $arPart;						//Main parts Array
							if(!in_array($arPart["SUP_BRAND"],$arBRANDs)){$arBRANDs[]=$arPart["SUP_BRAND"];}	//For filter by brands
							$arLowest_Prices[$CurNumber] = 999999;												//Default large value for SORTING by price
						}
						if(USE_STATISTIC=="Y"){$arStat[] = Array("Make parts",microtime(true));}
						
						/*foreach($arPARTS as $NumFull=>$ar){
							$rsCross = CShopCross::GetLinksDouble($arResult['SEARCH_NUMBER_'.$ART_TYPE],$arResult['SEARCH_BRAND']);
							while($arCross = $rsCross->GetNext()){
								foreach(Array($arCross['CROSS_NUMS'],$arCross['ORIGINAL_NUMS']) as $CrNum){
									if(!in_array($CrNum,$arLookShNums)){$arLookShNums[] = trim($CrNum);}
									//Make default cross record (for non tecdoc parts)
									$arPARTS[$CrNum]["ART_ARTICLE_NR"]=$CrNum;
									$arPARTS[$CrNum]["NUMBER"]=$CrNum;
									$arLowest_Prices[$CrNum] = 99998;
									$arNUMBERs[]=$CrNum;
									$arPARTS[$CrNum]["BRANDS"]=Array($arCross['CROSS_BRAND']=>Array());
								}
								if(!in_array($arCross['CROSS_BRAND'],$arBRANDs)){$arBRANDs[] = trim($arCross['CROSS_BRAND']);}
								if(!in_array($arCross['ORIGINAL_BRAND'],$arBRANDs)){$arBRANDs[] = trim($arCross['ORIGINAL_BRAND']);}			
							}
							if(!in_array($arResult['SEARCH_NUMBER_SHORT'],$arLookShNums)){$arLookShNums[] = $arResult['SEARCH_NUMBER_SHORT'];}
						}*/
						
						if($PartsCnt>0){
							//Get prices
							$TDataBase->DBSelect("CORE");
							$arPrices = CShopPrice::GetList(Array(),Array("ART_NUM"=>$arNUMBERs, "WS"=>"Y", "PARTS"=>$arPARTS));
							$arInfoByParts = Array(); $arInfoNumbers = Array(); 
							foreach($arPrices as $arPrice){
								if(!in_array($arPrice['SUP_BRAND'],$arBRANDs)){continue;} //Hide new PRICES brands
								$PricesCnt++;
								$arPrice = TDCurrencyConvert($arPrice);
								if($ART_TYPE=='SHORT' AND $arART_FNUMs[$arPrice['ART_NUM']]!=''){$arPrice['ART_NUM']=$arART_FNUMs[$arPrice['ART_NUM']];}
								if(trim($arPrice['PART_NAME'])==''){ $arPrice['PART_NAME'] = $arPARTS[$arPrice['ART_NUM']]['BRANDS'][$arPrice['SUP_BRAND']]['PART_NAME']; }
								$arPrice['ADDED_TO_CART'] = ProcessAddPartToBasket($arPrice,$arPARTS);
								
								//Make
								$arPARTS[$arPrice['ART_NUM']]['BRANDS'][$arPrice['SUP_BRAND']]['PRICES'][] = $arPrice;
								$arPARTS[$arPrice['ART_NUM']]['PRICES_COUNT']++;
								//For info
								$arCurPart = $arPARTS[$arPrice['ART_NUM']]['BRANDS'][$arPrice['SUP_BRAND']];
								if($arCurPart['NUMBER']!='' AND !in_array($arCurPart['NUMBER'],$arInfoNumbers)){$arInfoByParts[] = $arCurPart; $arInfoNumbers[] = $arCurPart['NUMBER'];}
								
								if($arPrice['PRICE']<$arLowest_Prices[$arPrice['ART_NUM']]){
									$arLowest_Prices[$arPrice['ART_NUM']]=$arPrice['PRICE'];
								}
								if(!in_array($arPrice["SUP_BRAND"],$arBRANDs)){$arBRANDs[]=$arPrice["SUP_BRAND"];}	//For filter by brands
							}
							if(USE_STATISTIC=="Y"){$arStat[] = Array("Get prices",microtime(true));}
							
							
							//Sort by lowest price
							if($PricesCnt>0){
								asort($arLowest_Prices);
								$arToSort_Parts = $arPARTS; $arPARTS = Array();
								foreach($arLowest_Prices as $sNUMBER=>$LowP){
									$arPARTS[$sNUMBER] = $arToSort_Parts[$sNUMBER];
								}
							}
							//echo '<pre>';print_r($arPARTS);echo '</pre>';
							
							sort($arBRANDs);
							sort($arNUMBERs);
							$arResult['PARTS'] = $arPARTS;
							$arResult['PARTS_BRANDS'] = $arBRANDs;
							$arResult['PARTS_NUMBERS'] = $arNUMBERs;
							//Connect to TecDoc BD
						}
						
						
					}else{$arResult['ERROR'].='<div class="psys_error">'.TMes('Error').' '.TMes('There is no types of brand').' "'.$UBrand.'" [model:'.$MOD_ID.', type:'.$TYP_ID.']</div>';}
				}else{$arResult['ERROR'].='<div class="psys_error">'.TMes('Error').' '.TMes('There is no model').' "'.$UBrand.'" [model:'.$MOD_ID.']</div>';}
			}else{$arResult['ERROR'].='<div class="psys_error">'.TMes('Error').' '.TMes('There is no brand').' "'.$UBrand.'"</div>';}
		}else{$arResult['ERROR'].='<div class="psys_error">'.TMes('Error').' '.TMes('Type ID not specified').'</div>';}
	}else{$arResult['ERROR'].='<div class="psys_error">'.TMes('Error').' '.TMes('Model ID not specified').'</div>';}
}else{$arResult['ERROR'].='<div class="psys_error">'.TMes('Error').' '.TMes('Brand not specified').'</div>';}
$this->ViewTemplate = true;

?>