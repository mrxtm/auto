<?if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die();
if(CORE_SITE_CHARSET=="utf8"){
	header('Content-type: text/html; charset=utf-8');
}else{
	header('Content-type: text/html; charset='.CORE_SITE_CHARSET);
}?>
<?if($arResult['ERROR']!=''){?>
	<?=$arResult['ERROR']?>
<?}elseif($arResult['ACTIVE']!="Y"){?>
	<div style="padding:40px;"><center><h2 class="hd2"><?=TMes('No data')?>...</h2></center></div>
<?}else{?>

	<div style="padding:20px;">
	<?if($arResult['SHOW_CHARS']=="Y"){?>
		<center><h2 class="hd2"><?=TMes('Characteristics')?></h2></center>
		<table class="corp_table" width="100%">
			<tr><td class="head"><?=TMes('Name')?></td><td class="head"><?=TMes('Value')?></td></tr>
			<?foreach($arResult['CHARS'] as $arChar){?>
				<tr><td class="pads brbot1"><?=$arChar['NAME']?></td><td class="pads brbot1 brlef1"><?=$arChar['VALUE']?></td></tr>
			<?}?>
		</table>
		<br>
		<br>
	<?}?>
	
	<?if($arResult['SHOW_ORIGINALS']=="Y"){?>
		<center><h2 class="hd2"><?=TMes('Original numbers')?></h2></center>
		<table class="corp_table" width="100%">
			<?foreach($arResult['ORIGINALS'] as $arOrig){?>
				<tr><td class="pads brbot1"><?=$arOrig['BRAND']?></td>
					<td class="pads brbot1 brlef1"><a href="<?=CORE_ROOT_DIR?>/search/<?=$arOrig['NUMBER']?>"><?=$arOrig['ARTICLE']?></a></td>
				</tr>
			<?}?>
		</table>
	<?}?>
	</div>
	
<?}?>