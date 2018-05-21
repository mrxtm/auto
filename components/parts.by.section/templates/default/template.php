<?if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die();?>
<hr>
<?if($arResult['ERROR']!=''){?>
	<br><?=$arResult['ERROR']?>
<?}else{?>
	<?if($arResult['MODEL_PIC']!=''){?>
		<div class="autopic" style="background:url(<?=$arResult['MODEL_PIC']?>)"></div>
	<?}?>
	<div class="subtit"><?=TMes('Brand')?>: <a href="<?=CORE_ROOT_DIR?>/"><?=$arResult['UBRAND']?></a></div>
	<div class="subtit"><?=TMes('Model')?>: <a href="<?=CORE_ROOT_DIR?>/<?=$arResult['BRAND']?>/"><?=$arResult['MODEL']?></a></div>
	<div class="subtit"><?=TMes('Type')?>: <a href="<?=CORE_ROOT_DIR?>/<?=$arResult['BRAND']?>/m<?=$arResult['MOD_ID']?>/"><?=$arResult['TYPE']?></a></div>
	<div class="subtit"><?=TMes('Section')?>: <a href="<?=CORE_ROOT_DIR?>/<?=$arResult['BRAND']?>/m<?=$arResult['MOD_ID']?>/t<?=$arResult['TYPE_ID']?>/"><?=$arResult['SECTION_NAME']?></a></div>
	<hr class="marbot18">
<?}?>