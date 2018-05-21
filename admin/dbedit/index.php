<?define("TDM_ADMIN_SIDE","Y");
require_once("../../core/prolog.php"); 
if($_SESSION['CORE_IS_ADMIN']!="Y"){header('Location: '.CORE_ROOT_DIR.'/admin/'); die();}
if(CORE_SITE_CHARSET=="utf8"){header('Content-type: text/html; charset=utf-8');}

global $TCore;
global $TDataBase;
$TDataBase->DBConnect("CORE");
//Defaults
if($_SESSION['TDM_DBEDIT_TABLE']==""){$_SESSION['TDM_DBEDIT_TABLE']="PRICES";}
if($_SESSION['TDM_DBEDIT_ONPAGE']<=0){$_SESSION['TDM_DBEDIT_ONPAGE']=50;}
if($_SESSION['TDM_DBEDIT_SORT']==""){$_SESSION['TDM_DBEDIT_SORT']="ID";}
if($_SESSION['TDM_DBEDIT_ORDER']==""){$_SESSION['TDM_DBEDIT_ORDER']="ASC";}

// Process form
if($_POST['filtersets']=="Y" AND $TDataBase->isDBCon){
	if($_POST['table']!=$_SESSION['TDM_DBEDIT_TABLE']){
		$_SESSION['TDM_DBEDIT_SORT']="ID"; $_SESSION['TDM_DBEDIT_ORDER']="ASC"; $_SESSION['TDM_DBEDIT_VALUE1']=""; $_SESSION['TDM_DBEDIT_VALUE2']=""; $_SESSION['TDM_DBEDIT_FILTER1']=""; $_SESSION['TDM_DBEDIT_FILTER2']=""; 
		$_SESSION['TDM_DBEDIT_TABLE']=$_POST['table']; 
		header('Location: '.CORE_ROOT_DIR.'/admin/dbedit/'); 
		die();
	}
	$_SESSION['TDM_DBEDIT_ONPAGE']=$_POST['onpage'];
	$_SESSION['TDM_DBEDIT_SORT']=$_POST['sort'];
	$_SESSION['TDM_DBEDIT_ORDER']=$_POST['order'];
	$_SESSION['TDM_DBEDIT_FILTER1']=$_POST['filter_1'];
	$_SESSION['TDM_DBEDIT_FILTER2']=$_POST['filter_2'];
	$_SESSION['TDM_DBEDIT_VALUE1']=$_POST['filter_value_1'];
	$_SESSION['TDM_DBEDIT_VALUE2']=$_POST['filter_value_2'];
}
if(intval($_REQUEST['p'])>0){$PAGE_NUM=intval($_REQUEST['p']);}else{$PAGE_NUM=1;}
if($_REQUEST['mess_deleted']>0){$MESSAGE.='Deleted records count: '.$_REQUEST['mess_deleted'].'<br>';}
if($_REQUEST['mess_delrow']>0){$MESSAGE.='Deleted record ID: '.$_REQUEST['mess_delrow'].'<br>';}


//Filter setups
$arOrder = Array($_SESSION['TDM_DBEDIT_SORT']=>$_SESSION['TDM_DBEDIT_ORDER']);
if($_SESSION['TDM_DBEDIT_VALUE1']!=''){$arFilter[$_SESSION['TDM_DBEDIT_FILTER1'].' LIKE']='%'.$_SESSION['TDM_DBEDIT_VALUE1'].'%';}
if($_SESSION['TDM_DBEDIT_VALUE2']!=''){$arFilter[$_SESSION['TDM_DBEDIT_FILTER2']]=$_SESSION['TDM_DBEDIT_VALUE2'];}
if($arFilter['IMPORT_DATE LIKE']!=""){
	list($day, $month, $year) = explode('.', str_replace('%','',$arFilter['IMPORT_DATE LIKE']));
	$arFilter['IMPORT_DATE LIKE'] = mktime(0, 0, 0, $month, $day, $year);	
}
if($arFilter['IMPORT_DATE']!=""){
	list($day, $month, $year) = explode('.',$arFilter['IMPORT_DATE']);
	$arFilter['IMPORT_DATE'] = mktime(0, 0, 0, $month, $day, $year);	
}
$arParams = Array("ITEMS_COUNT"=>$_SESSION['TDM_DBEDIT_ONPAGE'], "PAGE_NUM"=>$PAGE_NUM);
if($_REQUEST['action']=="delete_records"){
	$resDB = new CShopDBResult;
	$resDB->QuerySelect($_SESSION['TDM_DBEDIT_TABLE'],Array(),$arFilter,Array("DELETE"=>"Y"));
	header('Location: '.CORE_ROOT_DIR.'/admin/dbedit/?mess_deleted='.$resDB->NumRows); die();
}

//Table setups
if($_SESSION['TDM_DBEDIT_TABLE']=="PRICES"){
	$arColumns = Array(
		"ART_NUM"=>Array("VALUE"=>TMes('Number'),"TITLE"=>TMes('Number')),
		"SUP_BRAND"=>Array("VALUE"=>"Brand","TITLE"=>"Brand"),
		"PRICE"=>Array("VALUE"=>TMes('Price'),"TITLE"=>TMes('Price')),
		"CURRENCY"=>Array("VALUE"=>"<img src='".CORE_ROOT_DIR."/media/images/currency.png' width='16' height='16' alt=''>","TITLE"=>TMes('Currency')),
		"AVAILABLE"=>Array("VALUE"=>"<img src='".CORE_ROOT_DIR."/media/images/avail.png' width='16' height='16' alt=''>","TITLE"=>TMes('Count available')),
		"PART_NAME"=>Array("VALUE"=>TMes('Name'),"TITLE"=>TMes('Name')),
		"SUPPLIER"=>Array("VALUE"=>"<img src='".CORE_ROOT_DIR."/media/images/truck.png' width='16' height='16' alt=''>","TITLE"=>TMes('Supplier')),
		"STOCK"=>Array("VALUE"=>"<img src='".CORE_ROOT_DIR."/media/images/stock.png' width='16' height='16' alt=''>","TITLE"=>TMes('Stock')),
		"DAY"=>Array("VALUE"=>"<img src='".CORE_ROOT_DIR."/media/images/clock.png' width='16' height='16' alt=''>","TITLE"=>TMes('Days')),
		"IMPORT_CODE"=>Array("VALUE"=>"<img src='".CORE_ROOT_DIR."/media/images/key.png' width='16' height='16' alt=''>","TITLE"=>TMes('Group key')),
		"IMPORT_DATE"=>Array("VALUE"=>TMes('Date'),"TITLE"=>TMes('Date')),
	);
	$rsSRows = CShopPrice::GetListDB($arOrder,$arFilter,$arParams);
	while($arRow = $rsSRows->GetNext()){
		$arRow["ART_NUM_S"] = $arRow["ART_NUM"];
		$arRow["ART_NUM_F"] = ArtToNumber($arRow["ART_NUM"],'FULL');
		$arRow["ART_NUM"] = '<a href="/parts/search/'.$arRow["ART_NUM_F"].'/'.BrandNameDecode($arRow['SUP_BRAND']).'">'.$arRow["ART_NUM"].'</a>';
		$arRow["IMPORT_DATE"] = date("d.m.y",$arRow["IMPORT_DATE"]);
		if($_REQUEST['del']==$arRow["ID"]){
			CShopPrice::Delete($arRow["ID"]); 
			header('Location: '.CORE_ROOT_DIR.'/admin/dbedit/?mess_delrow='.$arRow["ID"]); die();
		}
		$arRows[] = $arRow;
	}
	
	$arDelFCodes = Array("ART_NUM_S","SUP_BRAND");
}elseif($_SESSION['TDM_DBEDIT_TABLE']=="LINKS"){
	$arColumns = Array(
		"CROSS_NUMS"=>Array("VALUE"=>TMes('Cross num'),"TITLE"=>TMes('Cross num')),
		"CROSS_BRAND"=>Array("VALUE"=>"Cross Brand","TITLE"=>"Cr.Brand"),
		"ORIGINAL_NUMS"=>Array("VALUE"=>TMes('Original num'),"TITLE"=>TMes('Original num')),
		"ORIGINAL_BRAND"=>Array("VALUE"=>"Original Brand","TITLE"=>"Or.Brand"),
		"IMPORT_CODE"=>Array("VALUE"=>"<img src='".CORE_ROOT_DIR."/media/images/key.png' width='16' height='16' alt=''>","TITLE"=>TMes('Key')),
	);
	$rsSRows = CShopCross::GetList($arOrder,$arFilter,$arParams);
	while($arRow = $rsSRows->GetNext()){
		if($_REQUEST['del']==$arRow["ID"]){
			CShopCross::Delete($arRow["ID"]); 
			header('Location: '.CORE_ROOT_DIR.'/admin/dbedit/?mess_delrow='.$arRow["ID"]); die();
		}
		$arRows[] = $arRow;
	}
	$arDelFCodes = Array("CROSS_NUMS","CROSS_BRAND");
}elseif($_SESSION['TDM_DBEDIT_TABLE']=="CONVERT_RULES"){
	$arColumns = Array(
		"R_FIELD"=>Array("VALUE"=>TMes('Field'),"TITLE"=>TMes('Field')),
		"R_FROM"=>Array("VALUE"=>TMes('From this'),"TITLE"=>TMes('From this')),
		"R_TO"=>Array("VALUE"=>TMes('To this'),"TITLE"=>TMes('To this')),
	);
	$rsSRows = TDConvRule::GetList($arOrder,$arFilter,$arParams);
	while($arRow = $rsSRows->GetNext()){
		if($_REQUEST['del']==$arRow["ID"]){
			TDConvRule::Delete($arRow["ID"]); 
			header('Location: '.CORE_ROOT_DIR.'/admin/dbedit/?mess_delrow='.$arRow["ID"]); die();
		}
		$arRows[] = $arRow;
	}
	$arDelFCodes = Array("R_FIELD","R_FROM");
}
?>
<head><title><?=TMes('Data Base editor')?> :: TecDoc Module</title></head>
<div class="displayblock">
	<?require_once("../admin_panel.php");?>
	<link rel="stylesheet" href="<?=CORE_ROOT_DIR?>/media/js/colorbox.css" />
	<script type="text/javascript" language="javascript" src="<?=CORE_ROOT_DIR?>/media/js/jquery-1.9.1.js"></script>
	<script type="text/javascript" language="javascript" src="<?=CORE_ROOT_DIR?>/media/js/jquery.colorbox.js"></script>
	<script>
		$(document).ready(function(){
			$(".popup").colorbox({rel:false, current:'', preloading:false, arrowKey:false, scrolling:false, overlayClose:false});
		});
		$("#cboxPrevious").hide();
		$("#cboxNext").hide();
	</script>
	
		
	<div class="acorp_out ashad_a">
		<h1 class="hd1"><?=TMes('Data Base editor')?></h1>
		<hr class="marbot18">
		<p>
			<?$TCore->ShowErrors()?>
			<?if($MESSAGE!=''){?><div class="fnote fnbox"><?=$MESSAGE?></div><?}?>
		</p>
		
		<form method="post" action="" name="eform">
			<input type="hidden" name="filtersets" value="Y"/>
			<table class="sfiltab">
				<tr>
					<td class="tarig"><?=TMes('Table')?>:</td>
					<td>
						<select name="table" class="swL" OnChange="this.form.submit()">
							<option value="PRICES"><?=TMes('Prices')?></option>
							<option value="LINKS" <?if($_SESSION['TDM_DBEDIT_TABLE']=="LINKS"){echo 'selected';}?> ><?=TMes('Crosses')?></option>
							<option value="CONVERT_RULES" <?if($_SESSION['TDM_DBEDIT_TABLE']=="CONVERT_RULES"){echo 'selected';}?> ><?=TMes('Rules')?></option>
						</select>
					</td>
					<td class="tarig"><?=TMes('Sort')?>:</td>
					<td>
						<select name="sort" class="swM" OnChange="this.form.submit()">
							<option value="ID"><?=TMes('By default')?></option>
							<?foreach($arColumns as $Code=>$arValues){
								if($_SESSION['TDM_DBEDIT_SORT']==$Code){$SortSel='selected';}else{$SortSel='';}
								echo '<option value="'.$Code.'" '.$SortSel.'>'.$arValues['TITLE'].'</option>';
							}?>
						</select>
					</td>
					<td class="tarig"><?=TMes('Filter')?> A:</td>
					<td>
						<select name="filter_1" class="swM">
							<?foreach($arColumns as $Code=>$arValues){
								if($_SESSION['TDM_DBEDIT_FILTER1']==$Code){$Filt1Sel='selected';}else{$Filt1Sel='';}
								echo '<option value="'.$Code.'" '.$Filt1Sel.'>'.$arValues['TITLE'].'</option>';
							}?>
						</select>
					</td>
					<td class="tarig"><?=TMes('Filter')?> B:</td>
					<td>
						<select name="filter_2" class="swM">
							<?foreach($arColumns as $Code=>$arValues){
								if($_SESSION['TDM_DBEDIT_FILTER2']==$Code){$Filt2Sel='selected';}else{$Filt2Sel='';}
								echo '<option value="'.$Code.'" '.$Filt2Sel.'>'.$arValues['TITLE'].'</option>';
							}?>
						</select>
					</td>
					<td rowspan="2">
						<input type="submit" value="Filter" class="abutton" title="<?=TMes('Apply')?> <?=TMes('Filter')?>"/> 
						<input type="button" value="+" class="abutton greedbut popup" title="<?=TMes('Add')?> <?=TMes('record')?>" href="<?=CORE_ROOT_DIR?>/admin/dbedit/edit.php?ID=NEW"/> 
					</td>
				</tr>
				<tr>
					<td class="tarig"><?=TMes('List by')?>:</td>
					<td><?$arOnPages=Array(20,30,50,100,200,500,1000);?>
						<select name="onpage" class="swL" OnChange="this.form.submit()">
							<?foreach($arOnPages as $OnPage){
								if($_SESSION['TDM_DBEDIT_ONPAGE']==$OnPage){$OnPSel='selected';}else{$OnPSel='';}
								echo '<option value="'.$OnPage.'" '.$OnPSel.'>'.$OnPage.'</option>';
							}?>
						</select>
					</td>
					<td class="tarig"><?=TMes('Direction')?>:</td>
					<td>
						<select name="order" class="swM" OnChange="this.form.submit()">
							<option value="ASC"><?=TMes('Increase')?></option>
							<option value="DESC" <?if($_SESSION['TDM_DBEDIT_ORDER']=="DESC"){echo 'selected';}?>><?=TMes('Decrease')?></option>
						</select>
					</td>
					<td class="tarig"><?=TMes('Contains')?>:</td>
					<td><input type="text" name="filter_value_1" value="<?=$_SESSION['TDM_DBEDIT_VALUE1']?>" class="swM"/></td>
					<td class="tarig"><?=TMes('Equally')?>:</td>
					<td><input type="text" name="filter_value_2" value="<?=$_SESSION['TDM_DBEDIT_VALUE2']?>" class="swM"/></td>
				</tr>
			</table>
		</form>
		<hr class="marbot18">
		<?if($rsSRows->NavPageCount>1){?>
			<div class="navpages"><div><?=TMes('Page')?>: </div>
			<?for($p=1; $p<=$rsSRows->NavPageCount; $p++){
				if($p>20){echo '...'; break;}
				if($rsSRows->NavPageNomer==$p){$pAct="active";}else{$pAct="";}
				echo '<a href="?p='.$p.'" class="'.$pAct.'">'.$p.'</a>';
			}?>
			
			</div>
		<?}?>
		<?if($rsSRows->DBCount>0){?>
			<div class="tfrig grey_color">
				<?=TMes('All records')?>: <?=$rsSRows->DBCount?>, 
				<a href="javascript:void(0);" onclick="if(confirm('<?=TMes('Really delete?')?>')) window.location='?action=delete_records';"><?=TMes('Delete this records')?></a>
			</div>
		<?}?>
		<div class="cler"></div>
		
		<table class="simtab" <?if($_SESSION['TDM_DBEDIT_TABLE']=="PRICES"){?>width="100%"<?}?> ><tr class="head">
		<?
		foreach($arColumns as $Code=>$arValues){
			echo '<td title="'.$arValues['TITLE'].'">'.$arValues['VALUE'].'</td>';
		}
		echo '<td>'.TMes('Actions').'</td></tr>';
		if($rsSRows->DBCount<=0){
			echo '<tr><td colspan="100"><br><center>'.TMes('No search result').'<br><br></center></td></tr>';
		}
		foreach($arRows as $arRow){
			echo '<tr class="rows">';
			foreach($arColumns as $Code=>$arValues){
				echo '<td>'.$arRow[$Code].'</td>';
			}
			?><td><a href="<?=CORE_ROOT_DIR?>/admin/dbedit/edit.php?ID=<?=$arRow["ID"]?>" class="popup"><img src="<?=CORE_ROOT_DIR?>/media/images/edit.gif" width="16" height="16" title="<?=TMes('Edit')?>"></a>
				<a href="javascript:void(0);" onclick="if(confirm('<?=TMes('Really delete?')?> <?foreach($arDelFCodes as $DelFCode){echo $arRow[$DelFCode].' - ';}?> ...')) window.location='<?=$FORM_ACTION?>?del=<?=$arRow['ID']?>';" ><img src="<?=CORE_ROOT_DIR?>/media/images/trash.gif" width="16" height="16" title="<?=TMes('Delete')?>"></a></td>
			<?echo '</tr>';
		}
		?>
		</table>
	</div>
</div>