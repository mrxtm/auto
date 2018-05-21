<?if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die();?>
<?
$GLOBALS['StrFrom'] = Array('script', 'javascript', 'select', 'drop', 'update', ' add ','<!--');
$GLOBALS['StrTo']   = Array('sc ript','javas cript','sel ect','dr op','up date',' ad d ','<-!-');

global $arTDLengs;
$arTDLengs = Array(1=>'de',4=>'en',6=>'fr',7=>'it',8=>'es',9=>'nl',10=>'da',11=>'sv',12=>'no',13=>'fi',14=>'hu',15=>'pt',16=>'ru',17=>'sk',18=>'cs',19=>'pl',20=>'el',21=>'ro',23=>'tr',25=>'sr',31=>'zh',32=>'bg',33=>'lv',34=>'lt',35=>'et',36=>'sl',37=>'qa',38=>'qb');

class TDataBase{
	var $rsSQL;
	var $isDBCon;
	function DBConnect($DBType){
		global $TCore;
		if($DBType=="TECDOC"){
			$S=TECDOC_DB_SERVER; $L=TECDOC_DB_LOGIN; $P=TECDOC_DB_PASS; $DB=TECDOC_DB_NAME; $Charset=TECDOC_DB_CHARSET; $Type=TECDOC_DB_PCON;
		}elseif($DBType=="CORE"){
			$S=CORE_DB_SERVER; $L=CORE_DB_LOGIN; $P=CORE_DB_PASS; $DB=CORE_DB_NAME; $Charset=CORE_DB_CHARSET; $Type=CORE_DB_PCON;
		}
		if($Type=='P'){
			$this->rsSQL = @mysql_pconnect($S,$L,$P);
			if(!defined('DB_CONNECTION_P')){define('DB_CONNECTION_P','Y');}
		}else{
			$this->rsSQL = @mysql_connect($S,$L,$P);
		}
		if($this->rsSQL){
			if(mysql_select_db($DB)){
				$this->isDBCon=true;
				mysql_set_charset($Charset);
				mysql_query("SET NAMES '".$Charset."'"); // utf8 / cp1251
				mysql_query("set character_set_connection=".$Charset.";");
				mysql_query("set character_set_database=".$Charset.";");
				mysql_query("set character_set_results=".$Charset.";");
				mysql_query("set character_set_client=".$Charset.";");
			}else{$TCore->arErrorMessages[] = 'Error connection: DB not exist "'.$DB.'" ('.$Charset.')';}
		}else{$TCore->arErrorMessages[] = 'Error! No connection to "'.$S.'" with login "'.$L.'"';}
	}
	function DBSelect($DB){
		global $TCore;
		if($DB=="TECDOC"){$DBN=TECDOC_DB_NAME;}elseif($DB=="CORE"){$DBN=CORE_DB_NAME;}
		if(trim($DBN)!=''){
			if(TECDOC_DB_SERVER==CORE_DB_SERVER AND CORE_USE_DBSELECT=="Y"){
				if(mysql_select_db($DBN)){$this->isDBCon=true; return true;}else{$TCore->arErrorMessages[] = 'Error select: DB not exist "'.$DBN.'"';}
			}else{
				global $TDataBase;
				$TDataBase->DBConnect($DB);
			}
		}else{$TCore->arErrorMessages[] = 'Error! No DB name to select';}
	}
}

class TComponent{
	var $arResult;
	var $arParams;
	var $ComponentName;
	var $TemplateName;
	var $ViewTemplate = false;
	function IncludeComponent($ComponentName,$TemplateName='',$arParams=Array()){
		global $TCore;
		if(trim($ComponentName)!=''){
			$ComPath = $_SERVER["DOCUMENT_ROOT"].CORE_ROOT_DIR."/components/".$ComponentName."/component.php";
			if(file_exists($ComPath)){
				if(trim($TemplateName)==''){$TemplateName='default';}
				$TemPath = $_SERVER["DOCUMENT_ROOT"].CORE_ROOT_DIR."/components/".$ComponentName."/templates/".$TemplateName."/template.php";
				if(file_exists($TemPath)){
					$TCore->Included_Component=$ComponentName;
					require($ComPath);
					$this->ComponentName = $ComponentName;
					$this->arResult = $arResult;
					$this->arParams = $arParams;
					$this->TemplateName = $TemplateName;
				}else{$TCore->arErrorMessages[] = 'Error! Wrong template name "'.$TemplateName.'" for component "'.$ComponentName.'"'; return;}
			}else{$TCore->arErrorMessages[] = 'Error! Wrong component name "'.$ComponentName.'"'; return;}
		}else{$TCore->arErrorMessages[] = 'Error! Component name not set'; return;}
	}
	function IncludeTemplate(){
		global $TCore;
		$TCore->ShowErrors();
		if($this->ViewTemplate){
			if($this->ComponentName!='' AND $this->TemplateName!=''){
				$arResult = $this->arResult;
				$arParams = $this->arParams;
				$StylePath = CORE_ROOT_DIR."/components/".$this->ComponentName."/templates/".$this->TemplateName."/styles.css";
				if(file_exists($_SERVER["DOCUMENT_ROOT"].$StylePath)){echo '<link rel="stylesheet" type="text/css" href="'.$StylePath.'" />';}
				$TemPath = $_SERVER["DOCUMENT_ROOT"].CORE_ROOT_DIR."/components/".$this->ComponentName."/templates/".$this->TemplateName."/template.php";
				require($TemPath);
			}else{echo '<div class="psys_error">Error! Component or template name not set</div>'; return;}
		}
	}

}

class TCore{
	var $Meta_isRecord;
	var $Head_Title;
	var $Head_Robots;
	var $Head_Keywords;
	var $Head_Description;
	var $SEO_Toptext;
	var $SEO_Bottext;
	var $arSettings;
	var $arLNG;
	var $Included_Component='';
	var $arComSets=Array();
	var $arErrorMessages=Array();
	function ShowErrors(){
		if(count($this->arErrorMessages)>0){
			$this->arErrorMessages = array_unique($this->arErrorMessages);
			echo '<div class="psys_error">'.implode('<br>',$this->arErrorMessages).'</div>';
			$this->arErrorMessages = Array();
		}else{return false;}
	}
	function GetSettings($FindParam=""){
		if(function_exists('parse_ini_file')){
			$INIFile = $_SERVER["DOCUMENT_ROOT"].CORE_ROOT_DIR."/core/settings.ini";
			if(is_readable($INIFile)){
				$this->arSettings = parse_ini_file($INIFile,true);
				if(trim($FindParam)!=''){
					foreach($this->arSettings as $Section=>$arParams){
						foreach($arParams as $Param=>$Value){
							if($Param==$FindParam){return $Value;}
						}
					}
				}else{
					return $this->arSettings;
				}
				return false;
			}else{echo '<div class="psys_error">Error! File settings.ini is not readable. Path: '.$INIFile.'</div>'; return;}
		}else{echo '<div class="psys_error">Error! Function "parse_ini_file()" does not exist</div>'; return;}
	}
	function SetMetaData(){
		if($_POST['set_meta']=="Y" AND $_SESSION['CORE_IS_ADMIN']=="Y"){
			$_POST = ClearFields($_POST);
			$Tit = $_POST['set_title']; $Key = $_POST['set_keywords']; $Des = $_POST['set_description']; $H1 = $_POST['set_title_h1']; $Stp = $_POST['set_toptext']; $Sbt = $_POST['set_bottext'];
			if($arMeta = $this->GetMetaData()){
				if($_POST['set_delete']!=''){
					$rsSQL = mysql_query("DELETE FROM SEO_META_DATA WHERE ID=".$arMeta['ID']." ");
				}else{
					$rsSQL = mysql_query("UPDATE SEO_META_DATA SET TITLE='".$Tit."',KEYWORDS='".$Key."',DESCRIPTION='".$Des."',TITLE_H1='".$H1."',SEO_TOPTEXT='".$Stp."',SEO_BOTTEXT='".$Sbt."' WHERE ID=".$arMeta['ID']." ");
				}
			}elseif($Tit!='' OR $Key!='' OR $Des!='' OR $H1!='' OR $Stp!='' OR $Sbt!=''){
				$rsSQL = mysql_query("INSERT INTO SEO_META_DATA VALUES('','".$this->GetMetaPath()."','".$Tit."','".$Key."','".$Des."','".$H1."','".$Stp."','".$Sbt."') ");
			}
		}
		if($arMeta = $this->GetMetaData()){
			$this->Meta_isRecord = "Y";
			$this->Head_Title = $arMeta['TITLE']; define("Real_Title",$arMeta['TITLE']);
			$this->Head_Keywords = $arMeta['KEYWORDS']; define("Real_Keywords",$arMeta['KEYWORDS']);
			$this->Head_Description = $arMeta['DESCRIPTION']; define("Real_Description",$arMeta['DESCRIPTION']);
			$this->Head_Title_H1 = $arMeta['TITLE_H1']; define("Real_Title_H1",$arMeta['TITLE_H1']);
			define("SEO_TOPTEXT",$arMeta['SEO_TOPTEXT']);
			define("SEO_BOTTEXT",$arMeta['SEO_BOTTEXT']);
		}
	}
	function ComponentMetaData($Component,$arMData){
		if($this->Head_Title==""){ $TempString = $this->GetComponentField($Component,"TEMPLATE_TITLE"); $TempString = $this->MetaTemplate($TempString,$arMData); $this->Head_Title = $TempString; }
		if($this->Head_Keywords==""){ $TempString = $this->GetComponentField($Component,"TEMPLATE_KEYWORDS"); $TempString = $this->MetaTemplate($TempString,$arMData); $this->Head_Keywords = $TempString; }
		if($this->Head_Description==""){ $TempString = $this->GetComponentField($Component,"TEMPLATE_DESCRIPTION"); $TempString = $this->MetaTemplate($TempString,$arMData); $this->Head_Description = $TempString; }
		if($this->Head_Title_H1==""){ $TempString = $this->GetComponentField($Component,"TEMPLATE_TITLE_H1"); $TempString = $this->MetaTemplate($TempString,$arMData); $this->Head_Title_H1 = $TempString; }
	}
	function GetMetaData(){
		$rsMeta = mysql_query("SELECT * FROM SEO_META_DATA WHERE URL_PATH = '".$this->GetMetaPath()."' LIMIT 1");
		if($rsMeta AND mysql_num_rows($rsMeta)>0){
			return mysql_fetch_assoc($rsMeta);
		}else{return false;}
	}
	function GetMetaPath(){
		$URL_PATH = str_replace(CORE_ROOT_DIR,'',$_SERVER['REQUEST_URI']);
		if(strpos($URL_PATH,"?")>0){$URL_PATH=substr($URL_PATH,0,strpos($URL_PATH,"?"));}
		return $URL_PATH;
	}
	function SetComponentField($Component,$Field,$Value){
		$resDB = new CShopDBResult;
		$SQLQuery = 'INSERT INTO COMPONENT_SETTINGS VALUES ("'.$Component.'","'.$Field.'","'.$Value.'") ON DUPLICATE KEY UPDATE VALUE="'.$Value.'";';
		$resDB->Query($SQLQuery);
		return $resDB->Result;
	}
	function GetComponentField($Component,$Field){
		$rsField = mysql_query("SELECT VALUE FROM COMPONENT_SETTINGS WHERE COMPONENT='".$Component."' AND FIELD='".$Field."' ");
		if($rsField AND mysql_num_rows($rsField)>0){
			$arField = mysql_fetch_assoc($rsField);
			return $arField['VALUE'];
		}else{return false;}
	}
	function MetaTemplate($String,$arMData){
		if($String!=''){
			$TotalChars = StrLen($String); $NewSeg=false;
			for($w=0; $w<$TotalChars; $w++){
				if($String{$w}=="#"){
					if($NewSeg==true){$NewSeg=false;}else{$NewSeg=true; $SegKey++; continue;}
				}
				if($NewSeg==true){$arSegments[$SegKey].=$String{$w};}
			}
			foreach($arSegments as $Segment){
				if($this->arLNG[$Segment]!=''){
					$String = str_replace($Segment,$this->arLNG[$Segment],$String);
				}
			}
			$String = str_replace('#','',$String);
			foreach($arMData as $CODE=>$VALUE){$String = str_replace($CODE,$VALUE,$String);}
		}
		return $String;
	}
}


function TMes($CODE){
	global $TCore;
	if(!count($TCore->arLNG)>0){
		global $arTDLengs;
		$LngPath = $_SERVER["DOCUMENT_ROOT"].CORE_ROOT_DIR."/core/lang/".$arTDLengs[TECDOC_LNG_ID].".php";
		if(file_exists($LngPath)){
			require_once($LngPath);
			$TCore->arLNG=$arLNG;
		}else{echo '<div class="psys_error">Error! No lang file specified</div>'; return;}
	}
	$Mes = $TCore->arLNG[$CODE];
	if($Mes==''){$Mes='#'.$CODE.'#';}
	return $Mes;
}

function ClearFields($arFields){
    foreach($arFields as $key=>$value){
        if(is_string($value)){
            if(strlen($value)>2){$value = str_ireplace($GLOBALS['StrFrom'],$GLOBALS['StrTo'],$value);}
            $value = mysql_real_escape_string($value);
        }
        $key = mysql_real_escape_string($key);
        $ar_New_Fields[$key] = $value;
    }
    return $ar_New_Fields;
}
function InsertInDB($DBIName,$arF){
	if(count($arF)>0){$arF = ClearFields($arF);}
	foreach($arF as $InV){$str_INSERT .= ",'".$InV."'";}
	return mysql_query("INSERT INTO ".$DBIName." VALUES (''".$str_INSERT.") ");
}
function InsertInDB2($DBIName2,$arF2){
	if(count($arF2)>0){$arF2 = ClearFields($arF2);}
	$rsCols = mysql_query("SELECT COLUMN_NAME,COLUMN_DEFAULT FROM ".CORE_DB_ISCHEMA.".COLUMNS WHERE TABLE_NAME = '".$DBIName2."' AND COLUMN_NAME != 'ID' ORDER BY ORDINAL_POSITION");
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
function UpdateInDB($DBUName,$UpID,$arUFields){
	if(intval($UpID)>0){
		$arUFields = ClearFields($arUFields);
		foreach($arUFields as $ukey=>$uvalue){
			if($uF==""){$uF="off";}else{$uComa=", ";}
			$sqlUFields .= $uComa.$ukey."='".$uvalue."' ";
		}
		return mysql_query("UPDATE ".$DBUName." SET ".$sqlUFields." WHERE ID=".$UpID." ");
	}else{return false;}
}

//////////////////////////////////////////////////////////////////////////////////////////////////
// Prices
//////////////////////////////////////////////////////////////////////////////////////////////////
class CShopPrice{
     
    static function GetList($arOrder,$arFilter,$arParams=Array()){
        $resDB = new CShopDBResult; $arPrices=Array(); $WS="";
		if($arFilter['WS']=="Y"){unset($arFilter['WS']); $WS="Y";}
		if(isset($arFilter['PARTS'])){
			foreach($arFilter['PARTS'] as $ART_NUM=>$arPart){
				foreach($arPart['BRANDS'] as $SUP_BRAND=>$arValues){
					$arArtBrands[$ART_NUM][] = $SUP_BRAND;
				}
			}
			unset($arFilter['PARTS']);
		}
		$resDB->QuerySelect(CORE_DB_PRICES_TABLE,$arOrder,$arFilter,$arParams);
		while($arPrice = $resDB->GetNext()){
			$arPrices[] = $arPrice;
		}
		if($WS=="Y"){
			require_once($_SERVER["DOCUMENT_ROOT"].CORE_ROOT_DIR."/core/webservices.php");
			$arPrices = GetWSPrices($arPrices, $arFilter['ART_NUM'], $arArtBrands);
		}
        return $arPrices;
    }
	static function GetListDB($arOrder,$arFilter,$arParams=Array()){
        $resDB = new CShopDBResult;
        $resDB->QuerySelect(CORE_DB_PRICES_TABLE,$arOrder,$arFilter,$arParams);
        return $resDB;
    }
	static function GetByID($ID){
		$resDB = new CShopDBResult;
        $resDB->QuerySelect(CORE_DB_PRICES_TABLE,Array(),Array("ID"=>$ID),Array("LIMIT"=>1));
        return $resDB->GetNext();
    }
    static function Add($f){
        if(strlen($f['ART_NUM'])>0){
			if(InsertInDB(CORE_DB_PRICES_TABLE,$f)){
				$rsDB = CShopPrice::GetListDB(Array("ID"=>"DESC"),Array("ART_NUM"=>$f['ART_NUM']),Array("LIMIT"=>1));
				return $rsDB->GetNext();
			}else{return false;}
        }else{return false;}
    }
	static function InsertUpdate($arFields){
        if(strlen($arFields['ART_NUM'])>0){
			$arFields = ClearFields($arFields);
			foreach($arFields as $Column=>$Value){$Values .= ',"'.$Value.'"';}
			$resDB = new CShopDBResult;
			$SQLQuery = 'INSERT INTO '.CORE_DB_PRICES_TABLE.' VALUES (""'.$Values.') ON DUPLICATE KEY UPDATE PRICE="'.$arFields['PRICE'].'", AVAILABLE="'.$arFields['AVAILABLE'].'", PART_NAME="'.$arFields['PART_NAME'].'", SEARCH_KEYWORDS="'.$arFields['SEARCH_KEYWORDS'].'", CURRENCY="'.$arFields['CURRENCY'].'", IMPORT_CODE="'.$arFields['IMPORT_CODE'].'", IMPORT_DATE="'.$arFields['IMPORT_DATE'].'" ;';
			$resDB->Query($SQLQuery);
			return $resDB->Result;
        }else{return false;}
    }
    static function Update($ID,$arFields){
		return UpdateInDB(CORE_DB_PRICES_TABLE,$ID,$arFields);
	}
    static function Delete($ID){
		$ID = intval($ID);
		if($ID>0){
			$rsBD = CShopPrice::GetListDB(Array(),Array("ID"=>$ID),Array("LIMIT"=>1));
			if($arBD = $rsBD->GetNext()){
				return mysql_query("DELETE FROM ".CORE_DB_PRICES_TABLE." WHERE ID='".$ID."' ");
			}else{return false;}
		}else{return false;}
    }
}

//////////////////////////////////////////////////////////////////////////////////////////////////
// Crosses
//////////////////////////////////////////////////////////////////////////////////////////////////
class CShopCross{
     
    static function GetList($arOrder,$arFilter,$arParams=Array()){
        $resDB = new CShopDBResult;
        $resDB->QuerySelect(CORE_DB_LINKS_TABLE,$arOrder,$arFilter,$arParams);
        return $resDB;
    }
	static function GetLinks($ROW,$arNumbers){
        foreach($arNumbers as $Number){ $arSelectSQL[] = 'SELECT * FROM '.CORE_DB_LINKS_TABLE.' WHERE '.$ROW.' = "'.$Number.'"'; }
		$SQLQuery = implode(' UNION ',$arSelectSQL);
		if($SQLQuery!=''){
			$resDB = new CShopDBResult;
			$resDB->Query($SQLQuery);
			return $resDB;
		}
		return false;
    }
	static function GetLinksDouble($Number,$Brand){
		$SQLQuery = 'SELECT * FROM '.CORE_DB_LINKS_TABLE.' WHERE (ORIGINAL_NUMS="'.$Number.'" ) OR (CROSS_NUMS="'.$Number.'" AND CROSS_BRAND="'.$Brand.'") '; //AND ORIGINAL_BRAND="'.$Brand.'"
		$resDB = new CShopDBResult;
		$resDB->Query($SQLQuery);
		return $resDB;
    }
	static function GetByID($ID){
		$resDB = new CShopDBResult;
        $resDB->QuerySelect(CORE_DB_LINKS_TABLE,Array(),Array("ID"=>$ID),Array("LIMIT"=>1));
        return $resDB->GetNext();
    }
    static function Add($f){
        if(strlen($f['CROSS_NUMS'])>0){
			if(InsertInDB(CORE_DB_LINKS_TABLE,$f)){
				$rsDB = CShopCross::GetList(Array("ID"=>"DESC"),Array("CROSS_NUMS"=>$f['CROSS_NUMS']),Array("LIMIT"=>1));
				return $rsDB->GetNext();
			}else{return false;}
        }else{return false;}
    }
	static function InsertUpdate($arFields){
        if($arFields['CROSS_NUMS']!=''){
			$arFields = ClearFields($arFields);
			foreach($arFields as $Column=>$Value){$Values .= ',"'.$Value.'"';}
			$resDB = new CShopDBResult;
			$SQLQuery = 'INSERT INTO '.CORE_DB_LINKS_TABLE.' VALUES (""'.$Values.') ON DUPLICATE KEY UPDATE CROSS_BRAND="'.$arFields['CROSS_BRAND'].'", ORIGINAL_BRAND="'.$arFields['ORIGINAL_BRAND'].'", IMPORT_CODE="'.$arFields['IMPORT_CODE'].'" ;';
			$resDB->Query($SQLQuery);
			return $resDB->Result;
        }else{return false;}
    }
    static function Update($ID,$arFields){
		return UpdateInDB(CORE_DB_LINKS_TABLE,$ID,$arFields);
	}
    static function Delete($ID){
        $ID = intval($ID);
		if($ID>0){
			$rsBD = CShopCross::GetList(Array(),Array("ID"=>$ID),Array("LIMIT"=>1));
			if($arBD = $rsBD->GetNext()){
				return mysql_query("DELETE FROM ".CORE_DB_LINKS_TABLE." WHERE ID='".$ID."' ");
			}else{return false;}
		}else{return false;}
    }
}


//////////////////////////////////////////////////////////////////////////////////////////////////
// Convert rules
//////////////////////////////////////////////////////////////////////////////////////////////////
class TDConvRule{
     
    static function GetList($arOrder,$arFilter,$arParams=Array()){
        $resDB = new CShopDBResult;
        $resDB->QuerySelect('CONVERT_RULES',$arOrder,$arFilter,$arParams);
        return $resDB;
    }
	static function GetByID($ID){
		$resDB = new CShopDBResult;
        $resDB->QuerySelect('CONVERT_RULES',Array(),Array("ID"=>$ID),Array("LIMIT"=>1));
        return $resDB->GetNext();
    }
    static function Add($f){
        if(strlen($f['R_FIELD'])>0 AND strlen($f['R_FROM'])>0){
			if(InsertInDB('CONVERT_RULES',$f)){
				$rsDB = TDConvRule::GetList(Array("ID"=>"DESC"),Array("R_FIELD"=>$f['R_FIELD']),Array("LIMIT"=>1));
				return $rsDB->GetNext();
			}else{return false;}
        }else{return false;}
    }
    static function Update($ID,$arFields){
		return UpdateInDB('CONVERT_RULES',$ID,$arFields);
	}
    static function Delete($ID){
        $ID = intval($ID);
		if($ID>0){
			$rsBD = TDConvRule::GetList(Array(),Array("ID"=>$ID),Array("LIMIT"=>1));
			if($arBD = $rsBD->GetNext()){
				return mysql_query("DELETE FROM CONVERT_RULES WHERE ID='".$ID."' ");
			}else{return false;}
		}else{return false;}
    }
}

//////////////////////////////////////////////////////////////////////////////////////////////////
// Currencies
//////////////////////////////////////////////////////////////////////////////////////////////////
class TDCurrency{
	
    static function GetList($arOrder,$arFilter,$arParams=Array()){
        $resDB = new CShopDBResult;
        $resDB->QuerySelect('CURRENCY',$arOrder,$arFilter,$arParams);
        return $resDB;
    }
	static function GetByID($ID){
		$resDB = new CShopDBResult;
        $resDB->QuerySelect('CURRENCY',Array(),Array("ID"=>$ID),Array("LIMIT"=>1));
        return $resDB->GetNext();
    }
    static function Add($f){
        if(strlen($f['CODE'])>0 AND $f['RATE']>0){
			if(InsertInDB('CURRENCY',$f)){
				$rsDB = TDCurrency::GetList(Array("ID"=>"DESC"),Array("CODE"=>$f['CODE']),Array("LIMIT"=>1));
				return $rsDB->GetNext();
			}else{return false;}
        }else{return false;}
    }
    static function Update($ID,$arFields){
		return UpdateInDB('CURRENCY',$ID,$arFields);
	}
    static function Delete($ID){
        $ID = intval($ID);
		if($ID>0){
			$rsBD = TDCurrency::GetList(Array(),Array("ID"=>$ID),Array("LIMIT"=>1));
			if($arBD = $rsBD->GetNext()){
				return mysql_query("DELETE FROM CURRENCY WHERE ID='".$ID."' ");
			}else{return false;}
		}else{return false;}
    }
	static function Convert($FROM,$TO,$Value){
        if($FROM!=$TO AND $Value>0){
			$rsBD = TDCurrency::GetList(Array(),Array("CODE"=>$FROM),Array("LIMIT"=>1));
			if($arFROM = $rsBD->GetNext()){
				$rsBD = TDCurrency::GetList(Array(),Array("CODE"=>$TO),Array("LIMIT"=>1));
				if($arTO = $rsBD->GetNext()){
					$Cof=$arFROM['RATE']/$arTO['RATE'];
					$Value = $Cof*$Value;
				}
			}
		}
		return round($Value,2);
    }
}

//////////////////////////////////////////////////////////////////////////////////////////////////
// SQL Data Base
//////////////////////////////////////////////////////////////////////////////////////////////////
class CShopDBResult{
    var $Error;        //Error
    var $Result;       //Result ID
    var $NumRows;      //Count of all rows
    var $NavPageNomer; //Cur. page num.
    var $NavPageCount; //All pages
    var $DBCount;      //DB result rows
    var $ItemsOnPage;  //Items on 1 page
    var $QueryString;  //SQL query string for debug/info
	
    function GetNext(){
        if($this->NumRows>0){
            $arResult = mysql_fetch_assoc($this->Result);
            return $arResult;
        }else{$this->Error="Нет записей"; return false;}
    }
    
	function Query($SQLQuery){
        $resQuery = mysql_query($SQLQuery);
        if($resQuery){
            $this->NumRows = mysql_num_rows($resQuery);
            if($this->NumRows>0){
                $this->Result = $resQuery;
            }else{$this->Error="No records with specified filter"; return false;}
        }else{$this->Error="Result ID - 0"; return false;}
    }
	
    function QuerySelect($DBTable,$arOrder,$arFilter,$arParams){
        //Filter
        foreach($arFilter as $key=>$value){
            if($F==''){$F='off';}else{$AND='AND';}
            $key = mysql_real_escape_string($key);
            if(is_array($value)){
                $ak=''; $til='';
                foreach($value as $arow){
                    $new_value .= $ak.'"'.mysql_real_escape_string($arow).'"'; $ak=', ';
					$new_cont .= $til.mysql_real_escape_string($arow); $til=' ';
                }
				if(strpos($key,' CONTAINS')){
					$arTab = explode(' ',$key);
					$sqlFilter .= $AND.' CONTAINS('.$arTab[0].', "'.$new_cont.'") ';
				}else{
					$new_value = '('.$new_value.')';
					$sqlFilter .= $AND.' '.$key.' IN '.$new_value.' ';
				}
            }else{
                if(strpos($key,' LIKE')){$Oper = ' ';}else{$Oper = '=';}
                $value = mysql_real_escape_string($value);
                $sqlFilter .= $AND.' '.$key.$Oper.'"'.$value.'" ';
            }
            
        }
        if(count($arFilter)>0){$Where = 'WHERE';}
        //Order
        foreach($arOrder as $key2=>$value2){
            if($O==''){$O='off';}else{$Com=', ';}
            $key2 = mysql_real_escape_string($key2);
            $value2 = mysql_real_escape_string($value2);
            $sqlOrder .= $Com.$key2.' '.$value2;
        }
        if(count($arOrder)>0){$OrderBy = 'ORDER BY';}
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
            $resDBC = mysql_query('SELECT COUNT(*) FROM '.$DBTable.' '.$Where.' '.$sqlFilter.' ');
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
		//Distinct
		if(is_array($arParams['DISTINCT']) AND count($arParams['DISTINCT'])>0){
			$sqlSelect = 'distinct ';
			foreach($arParams['DISTINCT'] as $DField){
				$sqlSelect.=$dComm.$DField;
				$dComm=',';
			}
		}
		
        
        //Query
		if($arParams['DELETE']=="Y"){
			$resQuery = mysql_query('DELETE FROM '.$DBTable.' '.$Where.' '.$sqlFilter.' ');
			$this->NumRows = mysql_affected_rows();
		}else{
			//echo "<br><br>SELECT ".$sqlSelect." ".$sqlDistinct." FROM ".$DBTable." ".$Where." ".$sqlFilter." ".$OrderBy." ".$sqlOrder." ".$sqlLimit." <br><br>";
			$resQuery = mysql_query('SELECT '.$sqlSelect.' '.$sqlDistinct.' FROM '.$DBTable.' '.$Where.' '.$sqlFilter.' '.$OrderBy.' '.$sqlOrder.' '.$sqlLimit.' ');
			if($resQuery){
				$this->NumRows = mysql_num_rows($resQuery);
				if($this->NumRows>0){
					$this->Result = $resQuery;
				}else{$this->Error="No records with specified filter"; return false;}
			}else{$this->Error="Result ID - 0"; return false;}
		}
		
    }
	
}
?>