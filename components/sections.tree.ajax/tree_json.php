<?require_once("../../core/prolog.php");
if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die();

$Brand = mysql_real_escape_string(substr($_REQUEST['brand'],0,25));
$MOD_ID = intval(substr($_REQUEST['model'],0,12));
$TYP_ID = intval(substr($_REQUEST['type'],0,12));
$Ubrand = strtoupper($Brand);
if(CORE_SITE_CHARSET=="utf8"){
	header('Content-type: text/html; charset=utf-8');
}else{
	header('Content-type: text/html; charset='.CORE_SITE_CHARSET);
}

if(strlen($Brand)>0){
  	//Бренд
	$rsManuf = TDSQL::GetManufByID($Ubrand);
	if($arManuf = $rsManuf->GetNext()){
		//Модель
		$rsModel = TDSQL::GetModelByID($arManuf['MFA_ID'],$MOD_ID,TECDOC_LNG_ID);
		if($arModel = $rsModel->GetNext()){
			//Тип авто
			$rsType = TDSQL::GetTypeByID($arModel['MOD_ID'],$TYP_ID,TECDOC_LNG_ID);
			if($arType = $rsType->GetNext()){

				echo '[';
				if($_REQUEST['id']>0){$Node=intval($_REQUEST['id']);}else{$Node=10001;}
				$rsSecR = TDSQL::GetSections($arType['TYP_ID'],$Node,TECDOC_LNG_ID);
				while($arSecR = $rsSecR->GetNext()){
					$arSecR['NAME'] = TDSecGetName($arSecR['STR_DES_TEXT']);
					$Children='';
					if($arSecR['DESCENDANTS']>0){
						//Selected
						if($_REQUEST['sec']==$arSecR['STR_ID']){
							$State = ', "state": "open"';
							$Children = ', "children":[';
							$rsSecSub = TDSQL::GetSections($arType['TYP_ID'],$arSecR['STR_ID'],TECDOC_LNG_ID);
							while($arSecSub= $rsSecSub->GetNext()){
								$arSecSub['NAME'] = TDSecGetName($arSecSub['STR_DES_TEXT']);
								if($arSecSub['DESCENDANTS']>0){
									$State2 = ', "state": "closed"';
									$Icon2 = ', "rel":"folder"';
								}else{
									//$SerName2 = SafeSecName($arSecSub['STR_DES_TEXT']);
									//$Href2 = ', "href": "'.CORE_ROOT_DIR.'/model/'.$Brand.'/m'.$MOD_ID.'/t'.$TYP_ID.'/search/'.$SerName2.'/"';
									$Href2 = ', "href": "'.CORE_ROOT_DIR.'/model/'.$Brand.'/m'.$MOD_ID.'/t'.$TYP_ID.'/s'.$arSecSub['STR_ID'].'/"';
									$State2 = ', "state": ""';
									$Icon2 = ', "rel":"file"';
								}
								$Children .= $Comma2.'{"data":"'.$arSecSub['NAME'].'", "attr": {"id":"'.$arSecSub['STR_ID'].'"'.$Icon2.$Href2.'}'.$State2.'}';
								$Comma2=', ';
							}
							$Children .= ']';
						}else{
							$State = ', "state": "closed"';
						}
						$Icon = ', "rel":"folder"';
					}else{
						//$SerName = SafeSecName($arSecR['STR_DES_TEXT']);
						//$Href = ', "href": "'.CORE_ROOT_DIR.'/'.$Brand.'/m'.$MOD_ID.'/t'.$TYP_ID.'/search/'.$SerName.'/"';
						$Href = ', "href": "'.CORE_ROOT_DIR.'/'.$Brand.'/m'.$MOD_ID.'/t'.$TYP_ID.'/s'.$arSecR['STR_ID'].'/"';
						$State = ', "state": ""';
						$Icon = ', "rel":"file"';
					}
					//JSON
					echo $Comma.'{"data":"'.$arSecR['NAME'].'", "attr": {"id":"'.$arSecR['STR_ID'].'"'.$Icon.$Href.'}'.$State.$Children.'}';
					$Comma=', ';
				}
				echo ']';
					
			}else{echo '["'.TMes('Error').' '.TMes('There is no types of brand').'"]';}
		}else{echo '["'.TMes('Error').' '.TMes('There is no model').'"]';}
	}else{echo '["'.TMes('Error').' '.TMes('There is no brand').'"]';}
}else{echo '["'.TMes('Error').' '.TMes('Brand not specified').'"]';}

// parts/components/sections.tree/tree_json.php?brand=audi&model=10&type=1157
?>