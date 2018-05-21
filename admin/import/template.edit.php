<?require_once("../../core/prolog.php");
if($_SESSION['CORE_IS_ADMIN']!="Y"){die();}
if(CORE_SITE_CHARSET=="utf8"){header('Content-type: text/html; charset=utf-8');}else{header('Content-type: text/html; charset='.CORE_SITE_CHARSET);}
$FORM_ACTION=CORE_ROOT_DIR.'/admin/import/';

	global $TDataBase;
	$TDataBase->DBSelect("CORE");
	require_once("core.php");
	
	//Редактировать
	if($_REQUEST['ID']>0){
		$rsITemps = ImportTemplates::GetList(Array(),Array("ID"=>$_REQUEST['ID']));
		if(!$arITemp = $rsITemps->Fetch()){$ERROR .= TMes('Error').' '.TMes('Wrong ID').'<br>';}
	}
	
	//Проверка формы
	if($_POST['checkme']=="Y"){
		if(trim($_POST['NAME'])==''){$ERROR .= TMes('You must specify the name of the template').'<br>';}
		if(trim($_POST['SEP'])==''){$ERROR .= TMes('You must specify the field delimiter in the CSV file').'<br>';}
		if(strlen($ERROR)==0){
			$arFields = Array(
				"NAME" => $_POST['NAME'],
				"IDEF" => intval($arITemp['IDEF']),
				"SEP" => $_POST['SEP'],
				"ENCODE" => $_POST['ENCODE'],
				"ITABLE" => $_POST['ITABLE'],
				"EXTRA" => intval($_POST['EXTRA']),
				"DEF_DAY" => $_POST['DEF_DAY'],
				"DEF_AVAIL" => $_POST['DEF_AVAIL'],
				"DEF_SUPL" => $_POST['DEF_SUPL'],
				"DEF_STOCK" => $_POST['DEF_STOCK'],
				"SEP_ART" => $_POST['SEP_ART'],
				"DEF_BRA" => $_POST['DEF_BRA'],
				"PRICE_CONVERT" => intval($_POST['PRICE_CONVERT']),
				"DEF_CUR" => $_POST['DEF_CUR'],
				"DEF_ICODE" => $_POST['DEF_ICODE'],
			);
			$obImTem = new ImportTemplates;
			if($arITemp['ID']>0){
				$res = $obImTem->Update($arITemp['ID'],$arFields);
				if($res=="Y"){$NOTE .= TMes('Changes are saved').'. <a href="'.$FORM_ACTION.'">'.TMes('Reload this Page').'</a>';
				}else{$ERROR .= TMes('Error').' '.TMes('The recording was not edited').'<br>'.$res;}
			}else{
				$res = $obImTem->Add($arFields);
				if($res=="Y"){
					$_POST['ID'] = $arNElem['ID'];
					$NOTE .= TMes('Record was created').'<br>'; $REDIRECT="Y";
				}else{$ERROR .= TMes('Error').' '.TMes('Record has not been created').'<br>'.$res;}
			}
		}
	}elseif($arITemp['ID']>0){
		foreach($arITemp as $key=>$val){$_POST[$key] = $arITemp[$key];}
	}
	?>
	<div style="padding:30px;">
		<div class="htit"><?if($arITemp['ID']>0){?><?=TMes('Edit')?><?}else{?><?=TMes('Add')?><?}?> <?=TMes('import template')?></div>
		<?if(strlen($ERROR)>0){?><div class="ferror"><?=$ERROR?></div><?}?>
		<?if(strlen($NOTE)>0){?><div class="fnote"><?=$NOTE?></div><?}?>
		<form name="nform" id="nform" action="<?=$FORM_ACTION?>template.edit.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="checkme" value="Y"/>
			<input type="hidden" name="ID" value="<?=$_REQUEST['ID']?>"/>
			<table class="formtab" >
				<tr><td class="tarig"><?=TMes('Name')?>:</td><td><input type="text" name="NAME" value="<?=$_POST['NAME']?>" class="afield" maxlength="255"/></td></tr>
				<tr><td class="tarig"><?=TMes('CSV field separator')?>:</td><td>
					<input type="text" name="SEP" id="sep" value="<?=$_POST['SEP']?><?if($_POST['SEP']==''){echo ';';}?>" class="afield minif" maxlength="9"/> 
					<span class="grays">&#9668; <a href="javascript:void(0)" OnClick="$('#sep').val('[tab]')" title="Табуляция">tab</a>, <a href="javascript:void(0)" OnClick="$('#sep').val('[space]')" title="Пробел">space</a></span>
				</td></tr>
				<?/*<tr><td class="tarig"><?=TMes('Separator ART_NUMS')?>:</td><td>
					<input type="text" name="SEP_ART" value="<?=$_POST['SEP_ART']?>" class="afield minif" maxlength="9"/> 
					<span class="grays"><?=TMes('in the Part Number field')?></span>
				</td></tr>*/?>
				<tr><td class="tarig"><?=TMes('File charset')?>:</td><td>
					<select name="ENCODE" class="aselect">
						<option value="CP1251">CP1251</option>
						<option value="UTF-8" <?if($_POST['ENCODE']=="UTF-8"){echo 'selected';}?>>UTF-8</option>
						<option value="UCS-2" <?if($_POST['ENCODE']=="UCS-2"){echo 'selected';}?>>UCS-2</option>
					</select>
				</td></tr>
				<?if($_POST['ITABLE']!="LINKS"){?>
					<tr><td class="tarig"><?=TMes('Extra charge')?>:</td><td><input type="text" name="EXTRA" value="<?=$_POST['EXTRA']?>" class="afield minif" maxlength="4" /> <span class="grays"><?=TMes('% of price')?> (+/-)</span></td></tr>
					<tr><td class="tarig"><?=TMes('Convert prices')?>:</td><td>
						<select name="PRICE_CONVERT" class="aselect">
							<option value=""><?=TMes('Price with penny')?></option>
							<option value="1" <?if($_POST['PRICE_CONVERT']=="1"){echo 'selected';}?>><?=TMes('Rounded to the lower whole')?></option>
							<option value="2" <?if($_POST['PRICE_CONVERT']=="2"){echo 'selected';}?>><?=TMes('Rounded to the nearest whole')?></option>
							<option value="3" <?if($_POST['PRICE_CONVERT']=="3"){echo 'selected';}?>><?=TMes('Rounded to the greater whole')?></option>
						</select>
					</td></tr>
				<?}?>
				<tr class="td_bpad"><td class="tarig"><?=TMes('Table')?>:</td><td>
					<select name="ITABLE" class="aselect">
						<option value="PRICES">PRICES - <?=TMes('Prices')?></option>
						<option value="LINKS" <?if($_POST['ITABLE']=="LINKS"){echo 'selected';}?>>LINKS - <?=TMes('Crosses')?></option>
					</select>
				</td></tr>
				
				<tr><td colspan="2" class="defsep"><?=TMes('Values assigned by default')?>:</td><td>
				<tr><td class="tarig"><?=TMes('Group key')?>:</td><td><input type="text" name="DEF_ICODE" value="<?=$_POST['DEF_ICODE']?>" class="afield minif" maxlength="12"/> <span class="grays"><?=TMes('of imported records')?></span></td></tr>
				<?if($_POST['ITABLE']!="LINKS"){?>
					<tr><td class="tarig"><?=TMes('Days of delivery')?>:</td><td><input type="text" name="DEF_DAY" value="<?=$_POST['DEF_DAY']?>" class="afield minif" maxlength="3" /> </td></tr>
					<tr><td class="tarig"><?=TMes('Count available')?>:</td><td><input type="text" name="DEF_AVAIL" value="<?=$_POST['DEF_AVAIL']?>" class="afield minif" maxlength="6" <?/*onkeypress="return numbersonly(this,event)"*/?> /> </td></tr>
					<tr><td class="tarig"><?=TMes('Supplier')?>:</td><td><input type="text" name="DEF_SUPL" value="<?=$_POST['DEF_SUPL']?>" class="afield middlef" maxlength="64"/></td></tr>
					<tr><td class="tarig"><?=TMes('Stock')?>:</td><td><input type="text" name="DEF_STOCK" value="<?=$_POST['DEF_STOCK']?>" class="afield middlef" maxlength="64"/></td></tr>
					<tr><td class="tarig"><?=TMes('Manufacturer')?>:</td><td><input type="text" name="DEF_BRA" value="<?=$_POST['DEF_BRA']?>" class="afield middlef" maxlength="64"/></td></tr>
					<tr><td class="tarig"><?=TMes('Currency')?>:</td><td>
						<?$arDCurs=Array("","USD","EUR","RUB","UAH","BYR");?>
						<select name="DEF_CUR" class="aselect">
							<?foreach($arDCurs as $DCur){
								if($_POST['DEF_CUR']==$DCur){$IsSel='selected';}else{$IsSel='';}?>
								<option value="<?=$DCur?>" <?=$IsSel?> ><?=$DCur?></option>
							<?}?>
						</select> 
					</td></tr>
				<?}?>
				<tr><td class="tarig"></td><td>
					<br>
					<input type="submit" value="<?=TMes('Apply')?>" class="abutton"/> 
					<input type="button" value="<?=TMes('Cancel')?>" onClick="parent.$.fn.colorbox.close()" class="abutton" style="margin-left:10px;"/>
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