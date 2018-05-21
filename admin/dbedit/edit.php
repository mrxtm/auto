<?define("TDM_ADMIN_SIDE","Y");
require_once("../../core/prolog.php");
if($_SESSION['CORE_IS_ADMIN']!="Y" OR ($_REQUEST["ID"]<=0 AND $_REQUEST["ID"]!="NEW") ){die();}
if(CORE_SITE_CHARSET=="utf8"){header('Content-type: text/html; charset=utf-8');}else{header('Content-type: text/html; charset='.CORE_SITE_CHARSET);}
$FORM_ACTION=CORE_ROOT_DIR.'/admin/dbedit/';

global $TCore;
global $TDataBase;
$TDataBase->DBConnect("CORE");


if($_SESSION['TDM_DBEDIT_TABLE']=="PRICES"){
	if($_REQUEST["ID"]=="NEW"){$Run="Y";}
	elseif($arSRow = CShopPrice::GetByID($_REQUEST["ID"])){$Run="Y";}
	else{$ERROR .= 'Error! Undefined ID.<br>';}
	if($Run=="Y"){
		$arFields = Array(
			"ART_NUM"=>Array("NAME"=>TMes('Number'),"CLASS"=>"","REQ"=>"Y"),
			"SUP_BRAND"=>Array("NAME"=>"Brand","CLASS"=>"","REQ"=>"Y"),
			"PRICE"=>Array("NAME"=>TMes('Price'),"CLASS"=>"minif70","REQ"=>"Y","COMMENT"=>TMes('Penny a point')),
			"CURRENCY"=>Array("NAME"=>TMes('Currency'),"CLASS"=>"minif70","COMMENT"=>"USD, RUB, UAH..."),
			"AVAILABLE"=>Array("NAME"=>TMes('Count available'),"CLASS"=>"minif70"),
			"PART_NAME"=>Array("NAME"=>TMes('Name'),"CLASS"=>"","TYPE"=>"TEXTAREA"),
			"SUPPLIER"=>Array("NAME"=>TMes('Supplier')),
			"STOCK"=>Array("NAME"=>TMes('Stock'),"CLASS"=>"minif70"),
			"DAY"=>Array("NAME"=>TMes('Days'),"CLASS"=>"minif70"),
			"IMPORT_CODE"=>Array("NAME"=>TMes('Group key'),"CLASS"=>"minif70"),
			//"IMPORT_DATE"=>Array("NAME"=>TMes('Date'),"TITLE"=>TMes('Date')),
		);
	}
}elseif($_SESSION['TDM_DBEDIT_TABLE']=="LINKS"){
	if($_REQUEST["ID"]=="NEW"){$Run="Y";}
	elseif($arSRow = CShopCross::GetByID($_REQUEST["ID"])){$Run="Y";}
	else{$ERROR .= 'Error! Undefined ID.<br>';}
	if($Run=="Y"){
		$arFields = Array(
			"CROSS_NUMS"=>Array("NAME"=>TMes('Cross num'),"CLASS"=>"","REQ"=>"Y"),
			"CROSS_BRAND"=>Array("NAME"=>"Cross Brand","CLASS"=>"","REQ"=>"Y"),
			"ORIGINAL_NUMS"=>Array("NAME"=>TMes('Original num'),"CLASS"=>"","REQ"=>"Y"),
			"ORIGINAL_BRAND"=>Array("NAME"=>"Original Brand","CLASS"=>""),
			"IMPORT_CODE"=>Array("NAME"=>TMes('Group key'),"CLASS"=>"minif70"),
		);
	}
}elseif($_SESSION['TDM_DBEDIT_TABLE']=="CONVERT_RULES"){
	if($_REQUEST["ID"]=="NEW"){$Run="Y";}
	elseif($arSRow = TDConvRule::GetByID($_REQUEST["ID"])){$Run="Y";}
	else{$ERROR .= 'Error! Undefined ID.<br>';}
	if($Run=="Y"){
		$arSValues = Array("ARTICLE","BRAND","NAME","DAY","AVAILABLE","SUPPLIER","STOCK");
		$arFields = Array(
			"R_FIELD"=>Array("NAME"=>TMes('Field'),"CLASS"=>"","REQ"=>"Y","TYPE"=>"SELECT","VALUES"=>$arSValues),
			"R_FROM"=>Array("NAME"=>TMes('From this'),"CLASS"=>"","REQ"=>"Y"),
			"R_TO"=>Array("NAME"=>TMes('To this'),"CLASS"=>""),
		);
	}
}

//Проверка формы
if($_POST['checkme']=="Y" AND ($arSRow['ID']>0 OR $_REQUEST["ID"]=="NEW")){
	if($_SESSION['TDM_DBEDIT_TABLE']=="PRICES"){
		if(trim($_POST['ART_NUM'])=="" OR trim($_POST['SUP_BRAND'])=="" OR trim($_POST['PRICE'])==""){
			$ERROR .= TMes('Fill in the required fields').'<br>';
		}else{
			$arNFields = Array(
				"ART_NUM" => $_POST['ART_NUM'],
				"SUP_BRAND" => $_POST['SUP_BRAND'],
				"PART_NAME" => $_POST['PART_NAME'],
				"PRICE" => $_POST['PRICE'],
				"CURRENCY" => $_POST['CURRENCY'],
				"DAY" => $_POST['DAY'],
				"AVAILABLE" => $_POST['AVAILABLE'],
				"SUPPLIER" => $_POST['SUPPLIER'],
				"STOCK" => $_POST['STOCK'],
				"SEARCH_KEYWORDS" => $arSRow['SEARCH_KEYWORDS'],
				"IMPORT_CODE" => $_POST['IMPORT_CODE'],
				"IMPORT_DATE" => $arSRow['IMPORT_DATE'],
			);
			if($arSRow['ID']>0){
				CShopPrice::Update($arSRow['ID'],$arNFields);
				$arSRow = CShopPrice::GetByID($arSRow["ID"]);
			}else{
				$arNFields["IMPORT_DATE"]=mktime(0, 0, 0, date("n"), date("j"), date("Y"));
				$arSRow = CShopPrice::Add($arNFields);
				$_REQUEST["ID"] = $arSRow["ID"];
			}
		}
	}
	
	if($_SESSION['TDM_DBEDIT_TABLE']=="LINKS"){
		if(trim($_POST['CROSS_NUMS'])=="" OR trim($_POST['CROSS_BRAND'])=="" OR trim($_POST['ORIGINAL_NUMS'])==""){
			$ERROR .= TMes('Fill in the required fields').'<br>';
		}else{
			$arNFields = Array(
				"CROSS_NUMS" => $_POST['CROSS_NUMS'],
				"CROSS_BRAND" => $_POST['CROSS_BRAND'],
				"ORIGINAL_NUMS" => $_POST['ORIGINAL_NUMS'],
				"ORIGINAL_BRAND" => $_POST['ORIGINAL_BRAND'],
				"IMPORT_CODE" => $_POST['IMPORT_CODE'],
			);
			if($arSRow['ID']>0){
				CShopCross::Update($arSRow['ID'],$arNFields);
				$arSRow = CShopCross::GetByID($arSRow["ID"]);
			}else{
				$arSRow = CShopCross::Add($arNFields);
				$_REQUEST["ID"] = $arSRow["ID"];
			}
		}
	}
	
	if($_SESSION['TDM_DBEDIT_TABLE']=="CONVERT_RULES"){
		if(trim($_POST['R_FIELD'])=="" OR trim($_POST['R_FROM'])==""){
			$ERROR .= TMes('Fill in the required fields').'<br>';
		}else{
			$arNFields = Array(
				"R_FIELD" => $_POST['R_FIELD'],
				"R_FROM" => $_POST['R_FROM'],
				"R_TO" => $_POST['R_TO'],
			);
			if($arSRow['ID']>0){
				TDConvRule::Update($arSRow['ID'],$arNFields);
				$arSRow = TDConvRule::GetByID($arSRow["ID"]);
			}else{
				$arSRow = TDConvRule::Add($arNFields);
				$_REQUEST["ID"] = $arSRow["ID"];
			}
		}
	}
	
	if($arSRow["ID"]>0){$NOTE .= TMes('Changes are saved').'. <a href="'.$FORM_ACTION.'">'.TMes('Reload this Page').'</a>';}
	else{$ERROR .= 'Error! Changes are not saved!<br>'.mysql_error();}
	
	foreach($_POST as $Key=>$Value){$arSRow[$Key]=$Value;}
}
	
?>
<div style="padding:30px;">
	<div class="htit">
		<?if($arSRow['ID']>0){?><?=TMes('Edit')?> <?=TMes('record')?> #<?=$arSRow['ID']?><?}?>
		<?if($_REQUEST["ID"]=="NEW"){?><?=TMes('Add')?> <?=TMes('record')?> <?}?>
	</div>
	<?if(strlen($ERROR)>0){?><div class="ferror"><?=$ERROR?></div><?}?>
	<?if(strlen($NOTE)>0){?><div class="fnote"><?=$NOTE?></div><?}?>
	<?if($arSRow['ID']>0 OR $_REQUEST["ID"]=="NEW"){?>
	<form name="nform" id="nform" action="<?=$FORM_ACTION?>edit.php?ID=<?=$_REQUEST["ID"]?>" method="post" >		
		<input type="hidden" name="checkme" value="Y"/>
		<table class="formtab" >
			<tr><td colspan="2"></td></tr>
			<?foreach($arFields as $CODE=>$arField){?>
				<tr>
					<td class="vertop padtop tarig <?if($arField['REQ']=="Y"){echo 'required';}?>"><?=$arField['NAME']?>: </td>
					<td>
						<?if($arField['TYPE']=="TEXTAREA"){?>
							<textarea name="<?=$CODE?>" class="atarea"><?=$arSRow[$CODE]?></textarea>
						<?}elseif($arField['TYPE']=="SELECT"){?>
							<select name="<?=$CODE?>">
								<?foreach($arField['VALUES'] as $Value){?>
									<?if($arSRow[$CODE]==$Value){$isSel="selected";}else{$isSel="";}?>
									<option value="<?=$Value?>" <?=$isSel?> ><?=$Value?></option>
								<?}?>
							</select>
						<?}else{?>
							<input type="text" name="<?=$CODE?>" value="<?=$arSRow[$CODE]?>" class="afield <?=$arField['CLASS']?>"> 
						<?}?>
						<?if($arField['COMMENT']!=""){?>
							<span class="grey_color"><?=$arField['COMMENT']?></span>
						<?}?>
					</td>
				</tr>
			<?}?>
			<tr><td></td><td><br>
				<input type="submit" value="<?if($_REQUEST["ID"]=="NEW"){?><?=TMes('Add')?><?}else{?><?=TMes('Apply')?><?}?>" class="abutton"/> 
				<input type="button" value="<?=TMes('Cancel')?>" onClick="parent.$.fn.colorbox.close()" class="abutton" style="margin-left:10px;"/><br>
				<br>
				<a href="<?=$FORM_ACTION?>index.php"><?=TMes('Reload this Page')?></a>
			</td></tr>
		</table>
	</form>
	<?}?>
</div>

	<script>
		$(function(){
			cbox_submit();
		});
		function cbox_submit(){
			$("#nform").submit(function() {
				$.post($(this).attr("action"), $(this).serialize(), function(data){
					$.colorbox({html:data });
				},
				'html');
				return false;
			});
		}
		
		<?if($REDIRECT=="Y"){?>
			window.setTimeout(goRedirect, 3000);
			function goRedirect(){
				window.location.href = "<?=$FORM_ACTION?>index.php";
			}
		<?}?>		
	</script>