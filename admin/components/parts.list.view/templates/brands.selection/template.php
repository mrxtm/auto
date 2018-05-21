<?if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die();?>
<?if(defined("SEO_TOPTEXT")){?><?=SEO_TOPTEXT?><hr class="marbot18"><?}?>
<div class="listlays">
	<div class="head">
		<div class="brand roul"><?=TMes('Firm')?></div>
		<div class="brlef1 brrig1 nameb rour"><?=TMes('Parts name')?></div>
	</div>
	<div class="cler"></div>
	<?foreach($arResult['PARTS'] as $arPart){?>
		<div class="brbot1_img">
			<div class="tflef">
				<?foreach($arPart['BRANDS'] as $BRAND_NAME=>$arBrand){?>
					<?$PartsCount++?>
					<div class="tflef brbot1_img hovback1">
						<div class="brand">
							<?if($arBrand['BRAND_ID']>0){?>
								<a href="<?=CORE_ROOT_DIR?>/brand/<?=$arBrand['BRAND_ID']?>/" title="<?=TMes('More about the firm')?>"><?=$BRAND_NAME?></a>
							<?}else{?>
								<?=$BRAND_NAME?>
							<?}?>
						</div>
						<div class="tflef">
							<div class="tflef brlef1 nameb">
								<?if($arBrand['ARL_KIND']>0){?>
									<img src="<?=CORE_ROOT_DIR?>/media/images/ntype_<?=$arBrand['ARL_KIND']?>.png" class="tflef" title="<?=TMes('Number type '.$arBrand['ARL_KIND'])?>" width="16" height="16" style="margin-right:10px!important;">
								<?}?>
								<a href="<?=CORE_ROOT_DIR?>/search/<?=$arBrand['NUMBER']?>/<?=$arBrand['SUP_BRAND_F']?>" title="<?=TMes('Search of parts')?>..."><?=$arBrand['PART_NAME']?></a>&nbsp;
							</div>
						</div>
						<div class="cler"></div>
					</div>
					<div class="cler"></div>
				<?}?>
			</div>
			<div class="cler"></div>
		</div>
		<div class="cler"></div>
	<?}?>
	<?if($PartsCount<=0){?>
		<br><br><center><?=TMes('Has no parts in this section')?>...</center><br><br><br>
	<?}?>
</div>
<div class="cler"></div>

<?if(defined("SEO_BOTTEXT") AND SEO_BOTTEXT!=''){?><br><br><?=SEO_BOTTEXT?><hr class="marbot18"><?}?>

<?//echo '<pre>';print_r($arResult);echo '</pre>';?>