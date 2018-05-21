<?require_once("../../core/prolog.php");
if($_SESSION['CORE_IS_ADMIN']!="Y"){die();}
if(CORE_SITE_CHARSET=="utf8"){header('Content-type: text/html; charset=utf-8');}else{header('Content-type: text/html; charset='.CORE_SITE_CHARSET);}
$FORM_ACTION=CORE_ROOT_DIR.'/admin/import/';

	global $TDataBase;
	$TDataBase->DBSelect("CORE");
	require_once("core.php");
	
	//Шаблон
	$rsITemps = ImportTemplates::GetList(Array(),Array("ID"=>$_REQUEST['ID']));
	if(!$arITemp = $rsITemps->Fetch()){$ERROR .= '<div class="psys_error">'.TMes('Error').' '.TMes('Wrong ID').'</div>'; die();}
	
	//Тип полей таблицы
	if($arITemp['ITABLE']=='PRICES'){
		$arTFIELDS = Array("ART_NUM"=>TMes('Part number').'*',"SUP_BRAND"=>TMes('Firm').'*',"PRICE"=>TMes('Price').'*',"CURRENCY"=>TMes('Currency'),"DAY"=>TMes('Days of delivery'),"AVAILABLE"=>TMes('Count available'),"SUPPLIER"=>TMes('Supplier'),"STOCK"=>TMes('Stock'),"PART_NAME"=>TMes('Alt. name'),"SEARCH_KEYWORDS"=>TMes('Serach keywords'));
	}
	if($arITemp['ITABLE']=='LINKS'){
		$arTFIELDS = Array("CROSS_NUMS"=>TMes('Cross numbers').'*',"CROSS_BRAND"=>TMes('Cross brand').'*',"ORIGINAL_NUMS"=>TMes('Original numbers').'*',"ORIGINAL_BRAND"=>TMes('Original brand'));
	}
	
	//Проверка формы
	if($_POST['checkme']=="Y" AND trim($_POST['CSV_NUM_NEW'])!='' AND trim($_POST['TEMPL_FIELD_NEW'])!=''){
		$_POST['CSV_NUM_NEW'] = intval($_POST['CSV_NUM_NEW']);
		$arDoubles = Array("SEARCH_KEYWORDS","PART_NAME");
		if(!in_array($_POST['TEMPL_FIELD_NEW'],$arDoubles)){
			$rsExPRows = ImportColumns::GetList(Array(),Array("CSV_NUM"=>$_POST['CSV_NUM_NEW'],"TEMPL_ID"=>$arITemp['ID']));
			if($arExPRow = $rsExPRows->Fetch()){$ERROR .= TMes('Error').' '.TMes('Already created a field with column number').': #'.$_POST['CSV_NUM_NEW'].'<br>';}
		}
		if($ERROR==''){
			$arFields = Array(
				"TEMPL_ID" => $arITemp['ID'],
				"CSV_NUM" => $_POST['CSV_NUM_NEW'],
				"TEMPL_FIELD" => $_POST['TEMPL_FIELD_NEW'],
			);
			if($arNElem = ImportColumns::Add($arFields)){
				$NOTE .= TMes('Record was created').' #'.$_POST['CSV_NUM_NEW'].'<br>';
			}else{$ERROR .= TMes('Error').' '.TMes('Record has not been created').'<br>';}
		}
	}
	?>
	<div style="padding:30px;">
		<div class="htit"><?=TMes('Editing fields')?></div>
		<?if(strlen($ERROR)>0){?><div class="ferror"><?=$ERROR?></div><?}?>
		<?if(strlen($NOTE)>0){?><div class="fnote"><?=$NOTE?></div><?}?>
		<form name="nform" id="nform" action="<?=$FORM_ACTION?>rows.edit.php" method="post" >		
			<input type="hidden" name="checkme" value="Y"/>
			<input type="hidden" name="ID" value="<?=$arITemp['ID']?>"/>
			<input type="hidden" name="del" id="del" value=""/>
			<table class="formtab" >
				<?//Поля
				$rsPRows = ImportColumns::GetList(Array("CSV_NUM"=>"ASC"),Array("TEMPL_ID"=>$arITemp['ID']));
				while($arPRow = $rsPRows->Fetch()){
					$Rows++;
					//Удалить
					if($_POST['del']==$arPRow['ID']){
						ImportColumns::Delete($arPRow['ID']);
						$NOTE2 .= TMes('Record is deleted').' #'.$arPRow['CSV_NUM'].'<br>';
						continue;
					}
					//Обновить
					if($_POST['checkme']=="Y" AND $_POST['CSV_NUM_'.$arPRow['ID']]!=''){
						if($_POST['CSV_NUM_'.$arPRow['ID']]!=$arPRow['CSV_NUM'] OR $_POST['TEMPL_FIELD_'.$arPRow['ID']]!=$arPRow['TEMPL_FIELD']){
							ImportColumns::Update($arPRow['ID'],Array("CSV_NUM"=>$_POST['CSV_NUM_'.$arPRow['ID']], "TEMPL_FIELD"=>$_POST['TEMPL_FIELD_'.$arPRow['ID']]));
							$arPRow['CSV_NUM'] = $_POST['CSV_NUM_'.$arPRow['ID']];
							$arPRow['TEMPL_FIELD'] = $_POST['TEMPL_FIELD_'.$arPRow['ID']];
							$NOTE2 .= TMes('Record is updated').' #'.$arPRow['CSV_NUM'].'<br>';
						}
					}
					?>
					<tr><td>#<input type="text" name="CSV_NUM_<?=$arPRow['ID']?>" value="<?=$arPRow['CSV_NUM']?>" class="afield minif" maxlength="2" onkeypress="return numbersonly(this,event)"/></td>
					<td><select name="TEMPL_FIELD_<?=$arPRow['ID']?>" class="aselect">
							<?foreach($arTFIELDS as $CODE=>$NAME){
								if(strpos($NAME,'*')>1){$SColor='style="color:red"';}else{$SColor='';}?>
								<option value="<?=$CODE?>" <?=$SColor?> <?if($arPRow['TEMPL_FIELD']==$CODE){echo 'selected';}?>><?=$NAME?></option>
							<?}?>
						</select> 
						<a href="javascript:void(0)" OnClick='$("#del").val("<?=$arPRow['ID']?>"); $("#nform").submit();'><img src="<?=CORE_ROOT_DIR?>/media/images/trash.gif" width="16" height="16" title="<?=TMes('Delete')?>"></a>
					</td></tr>
				<?}?>
				<?if($Rows<=0){?>
					<tr><td colspan="10" class="grays"><?=TMes('No fields for processing CSV import')?>...</td></tr>
				<?}?>
				<tr><td colspan="10"><?if(strlen($NOTE2)>0){?><div class="fnote"><?=$NOTE2?></div><?}?><br><div class="htit"><?=TMes('Bind a new field')?>:</div></td></tr>
				<tr><td colspan="2">
					<?$arColNums=Array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16);?>
					<?=TMes('Column')?> (tag) #<select name="CSV_NUM_NEW" class="aselect">
						<option value=""></option>
						<?foreach($arColNums as $Num){?>
							<option value="<?=$Num?>"><?=$Num?></option>
						<?}?>
					</select>
					<?/*<input type="text" name="CSV_NUM_NEW" value="" class="afield minif" maxlength="2" onkeypress="return numbersonly(this,event)"/>*/?>
					<img src="<?=CORE_ROOT_DIR?>/media/images/csv_to.png" class="convert_csv_to_db" title="<?=TMes('CSV_columns_bind_desc')?>"> 
					<select name="TEMPL_FIELD_NEW" class="aselect">
						<option value=""></option>
						<?foreach($arTFIELDS as $CODE=>$NAME){
							if(strpos($NAME,'*')>1){$SColor='style="color:red"';}else{$SColor='';}?>
							<option value="<?=$CODE?>" <?=$SColor?> <?if($arPRow['TEMPL_FIELD']==$CODE){echo 'selected';}?>><?=$NAME?> <?=strpos('*',$NAME)?></option>
						<?}?>
					</select>
				</td></tr>
				<tr><td colspan="2"></td></tr>
				<tr><td></td><td><br>
					<input type="submit" value="<?=TMes('Apply')?>" class="abutton"/> 
					<input type="button" value="<?=TMes('Cancel')?>" onClick="parent.$.fn.colorbox.close()" class="abutton" style="margin-left:10px;"/><br>
					<br>
					<a href="<?=$FORM_ACTION?>index.php"><?=TMes('Reload this Page')?></a>
				</td></tr>
			</table>
		</form>
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
	<?


?>