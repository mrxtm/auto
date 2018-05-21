<?$arSets = $TCore->arComSets;?>
<table width="100%" class="comstab">
	<tr><td>
		<input type="radio" name="mode" value="OFF" <?if($arSets['MODE']=="OFF" OR $arSets['MODE']==""){echo 'checked';}?> > Отключить фильтрацию моделей <b><?=$arSets['BRAND']?></b><br>
		<input type="radio" name="mode" value="IN" <?if($arSets['MODE']=="IN"){echo 'checked';}?>> Показать только выделенные  <br>
		<input type="radio" name="mode" value="NOT" <?if($arSets['MODE']=="NOT"){echo 'checked';}?>> Спрятать выделенные модели<br>
	</td><td>
		<input type="checkbox" name="addits" value="ADD" <?if($arSets['ADDIT']=="ADD"){echo 'checked';}?> > Показывать вкладку "Другие модели" <br>
		&nbsp;Показывать только модели <?=$arSets['BRAND']?> старше 
		<select name="modyear">
			<?for($Y=1960; $Y<=2013; $Y++){
				if($arSets['YEAR']==$Y){$mysel='selected';}else{$mysel='';}?>
				<option value="<?=$Y?>" <?=$mysel?> ><?=$Y?></option>
			<?}?>
		</select>
</table>
<hr>
<div class="comsdiv">Список моделей <b><?=$arSets['BRAND']?></b>:</div>
<table width="100%" class="comstab"><tr><td>
<?$Cnt=count($arSets['MODELS']);
$InCol = $Cnt/3;
foreach($arSets['MODELS'] as $arSMod){
	$arSMod['MOD_CDS_TEXT'] = $arSMod['MOD_CDS_TEXT'].' '.$arSMod['DATE_FROM'].' - '.$arSMod['DATE_TO'];
	$arSMod['FULL_NAME'] = $arSMod['MOD_CDS_TEXT'];
	if(strlen($arSMod['MOD_CDS_TEXT'])>37){$arSMod['MOD_CDS_TEXT']=substr($arSMod['MOD_CDS_TEXT'],0,35).'..';}
	if(is_array($arSets['SELECTED']) AND in_array($arSMod['MOD_ID'],$arSets['SELECTED'])){$Checked='checked';}else{$Checked='';}
	$Num++?>
	<input type="checkbox" name="models[<?=$arSMod['MOD_ID']?>]" value="<?=$arSMod['MOD_ID']?>" <?=$Checked?> > <a href="javascript:void(0);" title="<?=$arSMod['FULL_NAME']?>"><?=$arSMod['MOD_CDS_TEXT']?></a><br>
	<?if($Num>$InCol AND $SmCol<2){echo '</td><td>';$Num=0; $SmCol++;}?>
<?}?>
</table>
<hr>
<input type="submit" value="<?=TMes('Save')?>" class="abutton smbut"/> 
