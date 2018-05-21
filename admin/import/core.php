<?

function ImportCSV($arFields,$num,$arRes,$ITABLE){
	if($ITABLE=='PRICES'){
		if($_REQUEST['min_prices']=="Y"){
			$arMFilter = Array("ART_NUM"=>$arFields['ART_NUM'], "SUP_BRAND"=>$arFields['SUP_BRAND'], "CURRENCY"=>$arFields['CURRENCY']); // !! конвертировать в валюте
			if($_REQUEST['min_supfilter']>=1){$arMFilter["SUPPLIER"]=$arFields['SUPPLIER'];}
			if($_REQUEST['min_supfilter']>=2){$arMFilter["STOCK"]=$arFields['STOCK'];}
			$arPrices = CShopPrice::GetList(Array(),$arMFilter,Array("SELECT"=>Array("PRICE","CURRENCY")));
			if(count($arPrices)>0){
				foreach($arPrices as $arPrice){
					if($arPrice['PRICE']>0 AND $arFields['PRICE']>$arPrice['PRICE']){
						$arRes['CLASS']='minipriced'; return $arRes;
					}
				}
			}
		}
		CShopPrice::InsertUpdate($arFields);
	}elseif($ITABLE=='LINKS'){
		CShopCross::InsertUpdate($arFields);
	}
	$SQLErr = mysql_errno();
	if($SQLErr>0){$arRes['WRONG'][$num] = $arFields['ART_NUM']; echo mysql_error().'<br>';}else{$arRes['OK']++;}
	return $arRes;
}
	


class ImportBDF{
	static function ClearFields($arFields){
		$arStrFrom = Array('script', 'javascript', 'select', 'drop', 'update', ' add ','<!--');
		$arStrTo   = Array('sc ript','javas cript','sel ect','dr op','up date',' ad d ','<-!-');
		foreach($arFields as $key=>$value){
			if(is_string($value)){
				if(strlen($value)>2){$value = str_ireplace($arStrFrom,$arStrTo,$value);}
				$value = mysql_real_escape_string($value);
			}
			$key = mysql_real_escape_string($key);
			$ar_New_Fields[$key] = $value;
		}
		return $ar_New_Fields;
	}
	static function InsertInDB($DBIName,$arF){
		if(count($arF)>0){$arF = ImportBDF::ClearFields($arF);}
		foreach($arF as $InV){$str_INSERT .= ",'".$InV."'";}
		$res = mysql_query("INSERT INTO ".$DBIName." VALUES (''".$str_INSERT.") ");
		if($res){return "Y";}else{return mysql_error();}
	}
	static function InsertInDB2($DBIName2,$arF2){
		if(count($arF2)>0){$arF2 = ImportBDF::ClearFields($arF2);}
		$rsCols = mysql_query("SELECT COLUMN_NAME,COLUMN_DEFAULT FROM ".INFORMATION_SCHEMA.".COLUMNS WHERE TABLE_NAME = '".$DBIName2."' AND COLUMN_NAME != 'ID' ORDER BY ORDINAL_POSITION");
		while($arCol = mysql_fetch_assoc($rsCols)){
			if(isset($arF2[$arCol['COLUMN_NAME']])){
				$arNF[$arCol['COLUMN_NAME']]=$arF2[$arCol['COLUMN_NAME']];
			}else{
				$arNF[$arCol['COLUMN_NAME']]=$arCol['COLUMN_DEFAULT'];
			}
		}
		foreach($arNF as $InNV){$str_nINSERT .= ",'".$InNV."'";}
		return mysql_query("INSERT INTO ".$DBIName2." VALUES (''".$str_nINSERT.") ");
	}
	static function UpdateInDB($DBUName,$UpID,$arUFields){
		if(intval($UpID)>0){
			$arUFields = ImportBDF::ClearFields($arUFields);
			foreach($arUFields as $ukey=>$uvalue){
				if($uF==''){$uF='off';}else{$uComa=', ';}
				$sqlUFields .= $uComa.$ukey.'="'.$uvalue.'"';
			}
			$res = mysql_query('UPDATE '.$DBUName.' SET '.$sqlUFields.' WHERE ID='.$UpID.' ');
			if($res){return "Y";}else{return mysql_error();} 
		}else{return false;}
	}
}

//////////////////////////////////////////////////////////////////////////////////////////////////
// Шаблоны импорта цен
//////////////////////////////////////////////////////////////////////////////////////////////////
class ImportTemplates{
    static function GetList($arOrder,$arFilter,$arParams=Array()){
        $resDB = new ITempResult;
        $resDB->QuerySelect('IMPORT_TEMPLATES',$arOrder,$arFilter,$arParams);
        return $resDB;
    }
	static function GetByID($ID){
		$resDB = new ITempResult;
        $resDB->QuerySelect('IMPORT_TEMPLATES',Array(),Array("ID"=>$ID),Array("LIMIT"=>1));
        return $resDB->Fetch();
    }
    static function Add($f){
        if($f['NAME']!=''){
			return ImportBDF::InsertInDB('IMPORT_TEMPLATES',$f);
        }else{return false;}
    }
    static function Update($ID,$arFields){
		return ImportBDF::UpdateInDB('IMPORT_TEMPLATES',$ID,$arFields);
	}
    static function Delete($ID){
        $ID = intval($ID);
		if($ID>0){
			$rsDB = ImportTemplates::GetList(Array(),Array("ID"=>$ID),Array("LIMIT"=>1));
			if($arDB = $rsDB->Fetch()){
				$rsPrRows = ImportColumns::GetList(Array(),Array("TEMPL_ID"=>$ID),Array());
				while($arPrRow = $rsPrRows->Fetch()){ImportColumns::Delete($arPrRow['ID']);}
				return mysql_query("DELETE FROM IMPORT_TEMPLATES WHERE ID='".$ID."' ");
			}else{return false;}
		}else{return false;}
    }
}

//////////////////////////////////////////////////////////////////////////////////////////////////
// Колонки импорта цен
//////////////////////////////////////////////////////////////////////////////////////////////////
class ImportColumns{
    static function GetList($arOrder,$arFilter,$arParams=Array()){
        $resDB = new ITempResult;
        $resDB->QuerySelect('IMPORT_COLUMNS',$arOrder,$arFilter,$arParams);
        return $resDB;
    }
	static function GetByID($ID){
		$resDB = new ITempResult;
        $resDB->QuerySelect('IMPORT_COLUMNS',Array(),Array("ID"=>$ID),Array("LIMIT"=>1));
        return $resDB->Fetch();
    }
    static function Add($f){
        if($f['TEMPL_ID']>0){
			if(ImportBDF::InsertInDB('IMPORT_COLUMNS',$f)){
				$rsDB = ImportColumns::GetList(Array("ID"=>"DESC"),Array("TEMPL_ID"=>$f['TEMPL_ID']),Array("LIMIT"=>1));
				return $rsDB->Fetch();
			}else{return false;}
        }else{return false;}
    }
    static function Update($ID,$arFields){
		return ImportBDF::UpdateInDB('IMPORT_COLUMNS',$ID,$arFields);
	}
    static function Delete($ID){
        $ID = intval($ID);
		if($ID>0){
			$rsDB = ImportColumns::GetList(Array(),Array("ID"=>$ID),Array("LIMIT"=>1));
			if($arDB = $rsDB->Fetch()){
				return mysql_query("DELETE FROM IMPORT_COLUMNS WHERE ID='".$ID."' ");
			}else{return false;}
		}else{return false;}
    }
}

//////////////////////////////////////////////////////////////////////////////////////////////////
// Группы наценок
//////////////////////////////////////////////////////////////////////////////////////////////////
class ImportExGroups{
    static function GetList($arOrder,$arFilter,$arParams=Array()){
        $resDB = new ITempResult;
        $resDB->QuerySelect('IMPORT_EXGROUPS',$arOrder,$arFilter,$arParams);
        return $resDB;
    }
	static function GetByID($ID){
		$resDB = new ITempResult;
        $resDB->QuerySelect('IMPORT_EXGROUPS',Array(),Array("ID"=>$ID),Array("LIMIT"=>1));
        return $resDB->Fetch();
    }
    static function Add($f){
        if($f['NAME']!=''){
			if(ImportBDF::InsertInDB('IMPORT_EXGROUPS',$f)){
				$rsDB = ImportExGroups::GetList(Array("ID"=>"DESC"),Array("NAME"=>$f['NAME']),Array("LIMIT"=>1));
				return $rsDB->Fetch();
			}else{return false;}
        }else{return false;}
    }
    static function Update($ID,$arFields){
		return ImportBDF::UpdateInDB('IMPORT_EXGROUPS',$ID,$arFields);
	}
	static function InPaste($Key,$arFields=Array(),$arExGUpdate){
		if($arFields['RENGE']>0){
			$arExGUpdate['RENGE'][$Key]=$arFields['RENGE'];
			$arExGUpdate['EXTRA'][$Key]=$arFields['EXTRA'];
			$arExGUpdate['FIXED'][$Key]=$arFields['FIXED'];
		}else{
			$arExGUpdate['RENGE'][$Key]=$_POST["RENGE_".$Key];
			$arExGUpdate['EXTRA'][$Key]=$_POST["EXTRA_".$Key];
			$arExGUpdate['FIXED'][$Key]=$_POST["FIXED_".$Key];
		}
		return $arExGUpdate;
	}
    static function Delete($ID){
        $ID = intval($ID);
		if($ID>0){
			$rsDB = ImportExGroups::GetList(Array(),Array("ID"=>$ID),Array("LIMIT"=>1));
			if($arDB = $rsDB->Fetch()){
				return mysql_query("DELETE FROM IMPORT_EXGROUPS WHERE ID='".$ID."' ");
			}else{return false;}
		}else{return false;}
    }
}

//////////////////////////////////////////////////////////////////////////////////////////////////
// SQL Data Base
//////////////////////////////////////////////////////////////////////////////////////////////////
class ITempResult{
    var $Error;        //Строка ошибки
    var $Result;       //ID результата запроса
    var $NumRows;      //Колич. всех строк результата запроса
    var $NavPageNomer; //Номер текущей страницы
    var $NavPageCount; //Всего страниц
    var $DBCount;      //Всего записей в БД по фильтру
    var $ItemsOnPage;  //Элементов на 1-й странице
    
    function Fetch(){
        if($this->NumRows>0){
            $arResult = mysql_fetch_assoc($this->Result);
            return $arResult;
        }else{$this->Error="No records"; return false;}
    }
    
    function QuerySelect($DBTable,$arOrder,$arFilter,$arParams){
        //Filter
        foreach($arFilter as $key=>$value){
            if($F==""){$F="off";}else{$AND="AND";}
            $key = mysql_real_escape_string($key);
            if(is_array($value)){
                $ak="";
                foreach($value as $arow){
                    $new_value .= $ak."'".mysql_real_escape_string($arow)."'";
                    if($ak==""){$ak=", ";}
                }
                $new_value = '('.$new_value.')';
                $sqlFilter .= $AND.' '.$key.' IN '.$new_value.' ';
            }else{
                if(strpos($key," LIKE")){$Oper = ' ';}else{$Oper = '=';}
                $value = mysql_real_escape_string($value);
                $sqlFilter .= $AND.' '.$key.$Oper."'".$value."' ";
            }
            
        }
        if(count($arFilter)>0){$Where = "WHERE";}
        //Order
        foreach($arOrder as $key2=>$value2){
            if($O==""){$O="off";}else{$Com=", ";}
            $key2 = mysql_real_escape_string($key2);
            $value2 = mysql_real_escape_string($value2);
            $sqlOrder .= $Com.$key2." ".$value2;
        }
        if(count($arOrder)>0){$OrderBy = "ORDER BY";}
        //Limit
        if($arParams['LIMIT']>0){$sqlLimit = 'LIMIT '.intval($arParams['LIMIT']);}
        //Paging
        if($arParams['ITEMS_COUNT']>0){
            $arParams['PAGE_NUM'] = intval($arParams['PAGE_NUM']);
            $arParams['ITEMS_COUNT'] = intval($arParams['ITEMS_COUNT']);
            $this->ItemsOnPage = $arParams['ITEMS_COUNT'];
            if($arParams['PAGE_NUM']>1){
                $Offset = ($arParams['PAGE_NUM']-1)*$this->ItemsOnPage;
            }else{$Offset=0; $arParams['PAGE_NUM']=1;}
            $resDBC = mysql_query("SELECT COUNT(*) FROM ".$DBTable." ".$Where." ".$sqlFilter." ");
            if($resDBC){
				$arDBC = mysql_fetch_assoc($resDBC);
				$this->DBCount = $arDBC['COUNT(*)'];
				$fPages = $this->DBCount/$this->ItemsOnPage;
				if(is_float($fPages)){$this->NavPageCount = intval($fPages)+1;
				}else{$this->NavPageCount = intval($fPages);}
				if($this->DBCount <= $Offset){
					$Offset = $this->DBCount - $this->ItemsOnPage;
					$this->NavPageNomer = $this->NavPageCount;
				}else{
					$this->NavPageNomer = $arParams['PAGE_NUM'];
				}
				$sqlLimit = 'LIMIT '.$this->ItemsOnPage.' OFFSET '.$Offset;
			}
        }
		//Select only
		if(is_array($arParams['SELECT']) AND count($arParams['SELECT'])>0){
			foreach($arParams['SELECT'] as $SField){
				$sqlSelect.=$sComm.$SField;
				$sComm=',';
			}
		}else{
			$sqlSelect = '*';
		}
		//Unique
		if(is_array($arParams['DISTINCT']) AND count($arParams['DISTINCT'])>0){
			$sqlSelect = 'distinct ';
			foreach($arParams['DISTINCT'] as $DField){
				$sqlSelect.=$dComm.$DField;
				$dComm=',';
			}
		}
		
        //echo "<br>SELECT ".$sqlSelect." ".$sqlDistinct." FROM ".$DBTable." ".$Where." ".$sqlFilter." ".$OrderBy." ".$sqlOrder." ".$sqlLimit." <br>";
        //Query
        $resQuery = mysql_query("SELECT ".$sqlSelect." ".$sqlDistinct." FROM ".$DBTable." ".$Where." ".$sqlFilter." ".$OrderBy." ".$sqlOrder." ".$sqlLimit." ");
        if($resQuery){
            $this->NumRows = mysql_num_rows($resQuery);
            if($this->NumRows>0){
                $this->Result = $resQuery;
            }else{$this->Error="No records by this filter"; return false;}
        }else{$this->Error="Request ID: 0"; return false;}
    }
    

}


function GetFileSize($file){
	if(!file_exists($file)) return "Empty"; 
	$filesize = filesize($file); 
	if($filesize > 1024){
		$filesize = ($filesize/1024);
		if($filesize > 1024){
			$filesize = ($filesize/1024); 
           if($filesize > 1024){
				$filesize = ($filesize/1024); 
				$filesize = round($filesize, 1); 
				return $filesize." Gb";    
			}else{ 
				$filesize = round($filesize, 1); 
				return $filesize." Mb";    
			}
		}else{ 
			$filesize = round($filesize, 1); 
			return $filesize." Kb";    
		}
   }else{ 
		$filesize = round($filesize, 1); 
		return $filesize." byte";    
   }
}
?>
