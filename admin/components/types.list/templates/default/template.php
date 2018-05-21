<?if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die();?>
<hr>
<?if($arResult['ERROR']!=''){?>
	<?=$arResult['ERROR']?>
<?}else{?>
	<?if($arResult['MODEL_PIC']!=''){?>
		<div class="autopic" style="background:url(<?=$arResult['MODEL_PIC']?>)"></div>
	<?}?>
	<div class="subtit"><?=TMes('Brand')?>: <a href="<?=CORE_ROOT_DIR?>/"><?=$arResult['UBRAND']?></a></div>
	<div class="subtit"><?=TMes('Model')?>: <a href="<?=CORE_ROOT_DIR?>/<?=$arResult['BRAND']?>/"><?=$arResult['MODEL']?></a></div>
	<hr class="marbot18">
	<?if(defined("SEO_TOPTEXT") AND SEO_TOPTEXT!=''){?><?=SEO_TOPTEXT?><hr class="marbot18"><?}?>
	<table class="corp_table" id="cortab">
		<tr><td class="head"><?=TMes('Model')?></td><td class="head"><?=TMes('Year of construction')?></td><td class="head"><?=TMes('Type')?></td><td class="head"><?=TMes('Power')?></td><td class="head"><?=TMes('Capacity')?></td><td class="head"><?=TMes('Cylinder')?></td><td class="head"><?=TMes('Fuel')?></td><td class="head"><?=TMes('Body')?></td><?/*<td class="head"><?=TMes('Axis')?></td>*/?></tr>
		<?foreach($arResult['TYPES'] as $arType){
			echo '<tr class="gtr">';
			echo '<td class="pads"><a href="'.CORE_ROOT_DIR.'/'.$arResult['BRAND'].'/m'.$arResult['MOD_ID'].'/t'.$arType['TYP_ID'].'/" class="amod">'.$arType['TYP_CDS_TEXT'].'</a></td>';
			$DateFr = TDDateFormat($arType['TYP_PCON_START'],TMes('to p.t.'));
			$DateTo = TDDateFormat($arType['TYP_PCON_END'],TMes('to p.t.'));
			echo '<td class="pads">'.$DateFr.' - '.$DateTo.'</td>';
			echo '<td class="pads">'.$arType['ENG_CODE'].'</td>';
			echo '<td class="pads">'.$arType['TYP_KW_FROM'].' <span>'.TMes('Kv').'</span> - '.$arType['TYP_HP_FROM'].' <span>'.TMes('Hp').'</span></td>';
			echo '<td class="pads">'.$arType['TYP_CCM'].' <span>'.TMes('sm').'<sup>3</sup></span></td>';
			echo '<td class="pads">'.$arType['TYP_CYLINDERS'].'</td>';
			echo '<td class="pads">'.$arType['TYP_FUEL_DES_TEXT'].'</td>';
			echo '<td class="pads">'.$arType['TYP_BODY_DES_TEXT'].'</td>';
			//echo '<td class="pads">'.$arType['TYP_AXLE_DES_TEXT'].'</td>';
			echo '</tr>';
		}?>
	</table>
	<?if(defined("SEO_BOTTEXT") AND SEO_BOTTEXT!=''){?><hr class="marbot18"><?=SEO_BOTTEXT?><hr class="marbot18"><?}?>
	<br>
	<br>
	<a href="<?=CORE_ROOT_DIR?>/<?=$arResult['BRAND']?>/" class="bglink">&#9668; <?=TMes('Back to the model selection')?> <?=$arResult['UBRAND']?></a>
<?}?>