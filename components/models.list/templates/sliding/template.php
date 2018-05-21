<?if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die();?>
<script> $(function() {$( "#accordion1" ).accordion({heightStyle: "30px"});}); </script>

<hr>
<?if($arResult['ERROR']!=''){?>
	<?=$arResult['ERROR']?>
<?}else{?>
	<div class="autopic" style="background:url(<?=CORE_ROOT_DIR?>/media/brands/<?=$arResult['MFA_MFC_CODE']?>.png); width:40px"></div>
	<div class="subtit"><?=TMes('Brand')?>: <a href="<?=CORE_ROOT_DIR?>/"><?=$arResult['UBRAND']?></a></div>
	<hr class="marbot18">
	<?if(defined("SEO_TOPTEXT")){?><?=SEO_TOPTEXT?><hr class="marbot18"><?}?>
	<table width="100%"><tr><td width="50%">
	<div id="accordion1">
		<?foreach($arResult['MODELS'] as $CurModel=>$arModels){
			?><div alt="<?=$arResult['UBRAND']?> <?=$CurModel?>"><?=$CurModel?></div>
			<div><table class="plistab corp_table">
			<?foreach($arModels as $arModel){?>
				<tr class="gtr">
					<td><a class="amod" href="<?=CORE_ROOT_DIR?>/<?=$arResult['BRAND']?>/m<?=$arModel['MOD_ID']?>/"><?=$arModel['MOD_CDS_TEXT']?></a></td>
					<td class="grey_color"><?=$arModel['DATE_FROM']?> - <?=$arModel['DATE_TO']?></td>
				</tr>
			<?}?>
			</table></div>
			<?
		}?>
	</div>
	</table>
<?}?>
<?if(defined("SEO_BOTTEXT")){?><hr class="marbot18"><?=SEO_BOTTEXT?><hr class="marbot18"><?}?>
<br>
<br>
<a href="<?=CORE_ROOT_DIR?>/" class="bglink">&#9668; <?=TMes('Back to the brand selection')?></a>
