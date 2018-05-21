<?if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die();?>
<hr>
<?if($arResult['ERROR']!=''){?>
	<?=$arResult['ERROR']?>
<?}else{?>
	<div class="subtit"><?=TMes('Brand')?>: <a href="<?=CORE_ROOT_DIR?>/"><?=$arResult['UBRAND']?></a></div>
	<hr class="marbot18">
	<?if(defined("SEO_TOPTEXT")){?><?=SEO_TOPTEXT?><hr class="marbot18"><?}?>
	<table class="corp_table" id="cortab">
		<tr><td class="head"><?=TMes('Model')?></td><td class="head"><?=TMes('Year of construction')?></td></tr>
		<?$arCMods=Array();
		foreach($arResult['MODELS'] as $arModel){
			$arMd = explode(' ',$arModel['MOD_CDS_TEXT']);
			if(!in_array($arMd[0],$arCMods)){
				$arCMods[] = $arMd[0];
				$Mc++;
				echo '<tr id="tr'.$Mc.'" class="modtr">';
				echo '<td colspan="2"><a class="amodtr" href="javascript:void(0)" OnClick="ShowMods('.$Mc.')">'.$arMd[0].'</a></td></tr>';
			}
			echo '<tr class="mods'.$Mc.'" id="typtr">';
			echo '<td class="pads"><a class="amod" href="'.CORE_ROOT_DIR.'/'.$arResult['BRAND'].'/m'.$arModel['MOD_ID'].'/">'.$arModel['MOD_CDS_TEXT'].'</a></td>';
			echo '<td class="pads">'.$arModel['DATE_FROM'].' - '.$arModel['DATE_TO'].'</td></tr>';
		}?>
	</table>
<?}?>
<?if(defined("SEO_BOTTEXT")){?><hr class="marbot18"><?=SEO_BOTTEXT?><hr class="marbot18"><?}?>
<br>
<br>
<a href="<?=CORE_ROOT_DIR?>/" class="bglink">&#9668; <?=TMes('Back to the brand selection')?></a>

<script type="text/javascript">
	var manuf = document.getElementById('cortab');
	function ShowMods(Mc){
		var f1 = manuf.getElementsByTagName('tr');
		var cname = "mods"+Mc.toString();
		for(var i=0; i<f1.length; i++){
			if(f1[i].className == cname){
				f1[i].style.display = "table-row";
			}
		}
		 document.getElementById('tr'+Mc.toString()).style.display = "none";
	}
</script>
