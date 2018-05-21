<?define("TDM_ADMIN_SIDE","Y");
require_once("../core/prolog.php"); 
if($_SESSION['CORE_IS_ADMIN']!="Y"){header('Location: '.CORE_ROOT_DIR.'/admin/'); die();}
if(CORE_SITE_CHARSET=="utf8"){header('Content-type: text/html; charset=utf-8');}

global $TCore;
global $TDataBase;
$TDataBase->DBConnect("CORE");
		
if($_POST['create_tables']=="Y" AND $TDataBase->isDBCon){
	$FileName = $_SERVER["DOCUMENT_ROOT"].CORE_ROOT_DIR.'/admin/module_db.sql';
	if(!$FHandle = fopen($FileName, "r")){
		$TCore->arErrorMessages[] = TMes('Error').' '.TMes('File').' "'.CORE_ROOT_DIR.'/admin/module_db.sql" '.TMes('not readable');
	}else{
		//$FContents = fread($FHandle, filesize($FileName));
		//fclose($FHandle);
		//if($FContents!=''){
			
			$db = new PDO("mysql:host=".CORE_DB_SERVER.";dbname=".CORE_DB_NAME , CORE_DB_LOGIN, CORE_DB_PASS);
			$sql = file_get_contents('module_db.sql');
			$qr = $db->exec($sql);
			//print_r($db->errorInfo());
			
			//$rsSQL = mysql_query($FContents);
			//$MySQLErr = mysql_error();
			//if($MySQLErr!=''){$TCore->arErrorMessages[] = $MySQLErr.'<br>';}
			$MESSAGE='<div class="fnote fnbox">QUERY SEND (CREATE TABLES)</div>';
		//}
	}
}

if($_POST['send_query']=="Y" AND $_POST['sqlquery']!='' AND $TDataBase->isDBCon){
	$db = new PDO("mysql:host=".CORE_DB_SERVER.";dbname=".CORE_DB_NAME , CORE_DB_LOGIN, CORE_DB_PASS);
	$qr = $db->exec($_POST['sqlquery']);
	$arErr=$db->errorInfo();
	if($arErr[0]>0){
		$TCore->arErrorMessages[] .= $arErr[2];
	}
}
?>
<head><title><?=TMes('Data Base service')?> :: TecDoc Module</title></head>
<div class="displayblock">
	<?require_once("admin_panel.php");?>
	<div class="acorp_out ashad_a">
		<h1 class="hd1"><?=TMes('Data Base service')?></h1>
		<p></p>
		<table class="simtab">
		<?
		$arTables = array();
		$rsSQL = mysql_query("SHOW TABLES");
		
		if(mysql_num_rows($rsSQL)>0){
			while($arRow = mysql_fetch_array($rsSQL, MYSQL_NUM)){
				$arCols = Array();
				$Table = $arRow[0];
				if($Table==$_REQUEST['del']){
					mysql_query("DROP TABLE IF EXISTS `".$Table."`;");
					continue;
				}
				$rsCntSQL = mysql_query("SELECT COUNT(*) FROM ".$Table);
				$arCnt = mysql_fetch_row($rsCntSQL);
				echo '<tr><td>'.$arCnt[0].'</td><td><b>'.$Table.'</b><br><span class="secontext">';
				$rsFields = mysql_list_fields(CORE_DB_NAME, $Table, $TDataBase->rsSQL);
				$ColsNum = mysql_num_fields($rsFields);
				for($i=0; $i<$ColsNum; $i++){
					$arCols[] = mysql_field_name($rsFields, $i);
				}
				$strCols = implode(', ',$arCols);
				echo $strCols;
				?>
				<td>
					<a href="javascript:void(0);" onclick="if(confirm('<?=TMes('Really delete?')?> <?=$Table?>')) window.location='?del=<?=$Table?>';" ><img src="<?=CORE_ROOT_DIR?>/media/images/trash.gif" width="16" height="16" title="<?=TMes('Delete')?>"></a>
				</td>
				</td></tr>
				<?
			}
		}else{
			$TCore->arErrorMessages[] = 'No tables selected at DataBase "'.CORE_DB_NAME.'"';
		}
		?>
		</table>
		<?$TCore->ShowErrors()?>
		<?=$MESSAGE?>
		<br>
		
		<br><br>
		<?if($TDataBase->isDBCon){?>
			<form name="qform" id="qform" action="" method="post">
				<input type="hidden" name="send_query" value="Y"/>
				<textarea name="sqlquery" class="subinput" style="margin:0px 0px 20px 0px; width:940px; height:200px;"><?=$_POST['sqlquery']?></textarea>
				<br>
				<input type="submit" value="Send query" class="abutton" /> 
			</form>
		<?}?>
		<?if($TDataBase->isDBCon){?>
			<form name="dform" id="dform" action="" method="post" style="float:right; margin:-52px 20px 0px 0px;">
				<input type="hidden" name="create_tables" value="Y"/>
				<input type="submit" value="Create tables" class="abutton greedbut" /> 
			</form>
		<?}?>
	</div>
</div>