<?if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die();?>
<hr>
<?if($arResult['ERROR']!=''){?>
	<br><?=$arResult['ERROR']?>
<?}else{?>
	<?if($arResult['COMPONENT']=="SEARCH"){?>
		<div class="subtit"><?=TMes('Search number')?>: 
			<a href="<?=CORE_ROOT_DIR?>/search/<?=$arResult['SEARCH_NUMBER_FULL']?>"><?=$arResult['SEARCH_NUMBER_FULL']?></a><?
				if($arResult['SEARCH_BRAND']!=''){?>, <?=TMes('Firm')?>: <?=$arResult['SEARCH_BRAND']?>
			<?}?>
		</div>
		<hr class="marbot18">
	<?}elseif($arResult['COMPONENT']=="KEYWORDS"){?>
		<div class="subtit"><?=TMes('Keywords')?>: <?if(defined('KEYWORDS_FORMATED')){echo KEYWORDS_FORMATED;}?></div>
		<hr class="marbot18">
	<?}?>
<?}?>