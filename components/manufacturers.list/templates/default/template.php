<?if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die();?>
<?foreach($arResult as $arElem){?>
	<a href="<?=$arElem['LINK']?>" class="butcorp">
		<div class="butlogo" style="background:url(<?=CORE_ROOT_DIR?>/media/brands/<?=$arElem['LOGO_CODE']?>.png)"></div>
		<div class="buttext"><?=$arElem['NAME']?></div>
	</a>
<?}?>