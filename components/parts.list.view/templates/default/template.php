<?if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die();?>
<?$PART_NAME_LENGTH=175;?>
<?global $arBCous;?>
<link rel="stylesheet" href="<?=CORE_ROOT_DIR?>/media/js/colorbox.css" />
<script type="text/javascript" language="javascript" src="<?=CORE_ROOT_DIR?>/media/js/jquery.colorbox.js"></script>
<?/*if(defined("CORE_IS_ADMIN") AND CORE_IS_ADMIN==true){?>
	<script>$(function() { $( document ).tooltip(); });</script>
<?}*/?>
<?if(defined("SEO_TOPTEXT") AND SEO_TOPTEXT!=''){?><?=SEO_TOPTEXT?><hr class="marbot18"><?}?>

<div class="listlays">
	<div class="head">
		<div class="brrig1 article roul"><?=TMes('Number')?></div>
		<div class="brand"><?=TMes('Firm')?></div>
		<div class="brlef1 name brrig1"><?=TMes('Parts name')?></div>
		<div class="brrig1 avail" title="<?=TMes('Stock available')?>"><?=TMes('Avail')?></div>
		<div class="brrig1 price"><?=TMes('Price')?> <?=$_SESSION['TECDOC_SELECTED_SYM']?></div>
		<div class="brrig1 days" title="<?=TMes('Parts delivery time')?>"><?=TMes('Days')?></div>
		<div class="brrig1 order rour"><?=TMes('Order')?></div>
	</div>
	<div class="cler"></div>
	<?foreach($arResult['PARTS'] as $arPart){?>
		<?//Hide NUMBERS with NO prices
		if(HIDE_PARTS_NOPRICE=="Y" AND $arPart['PRICES_COUNT']<=0){continue;}
		//Show ANALOGS separator informer
		if($arResult['COMPONENT']=="SEARCH" AND $arPart['MAIN_GROUP']!="Y" AND $SEARCH_SEPARATOR==''){ $SEARCH_SEPARATOR="OFF";?>
			<div class="brbot1_img analogs"><?=TMes('Analogs of the requested parts')?>:</div><div class="cler"></div>
		<?}?>
		<div class="brbot1_img">
			<?if($arPart['ARL_KIND']>0){$NTypeClass='num_type_'.$arPart['ARL_KIND']; $NTypeName=TMes('Number type '.$arPart['ARL_KIND']).': ';}else{$NTypeClass=''; $NTypeName='';}?>
			<div class="article tflef brrig_fon <?=$NTypeClass?>" title="<?=$NTypeName?><?=$arPart['ART_ARTICLE_NR']?>"  >
				<?=$arPart['ART_ARTICLE_NR']?>
			</div>
			<div class="tflef">
				<?foreach($arPart['BRANDS'] as $BRAND_NAME=>$arBrand){?>
					<?//Hide BRANDS with NO prices
					if(HIDE_PARTS_NOPRICE=="Y" AND count($arBrand['PRICES'])<=0){continue;}
					$PartsCount++?>
					<?if(strlen($arBrand['PART_NAME'])>$PART_NAME_LENGTH){$arBrand['PART_NAME']=substr($arBrand['PART_NAME'],0,($PART_NAME_LENGTH-3)).'...';}?>
					<div class="tflef brbot1_img hovback1">
						<div class="brand">
							<?if($arBrand['IMG']!=''){?>
								<a class="cbx_imgs rigfoto" rel="cb_<?=TMes('Number')?>" href="<?=TECDOC_FILES_PREFIX.$arBrand['IMG']?>" title="<?=$arBrand['SUP_BRAND']?> (<?=TMes('Number')?>: <?=$arPart['ART_ARTICLE_NR']?>) <?=$arBrand['PART_NAME']?>"><img src="<?=CORE_ROOT_DIR?>/media/images/fotopic.png" width="16px" height="16px"></a>
							<?}?>
							<?if($arBrand['COU_ISO2']==''){$arBrand['COU_ISO2']=$arBCous[$BRAND_NAME]; $arBrand['COU_DES_TEXT']=$arBrand['COU_ISO2'];}
							if($arBrand['COU_ISO2']!=''){?>
								<img src="<?=CORE_ROOT_DIR?>/media/countries/<?=$arBrand['COU_ISO2']?>.png" class="coupic" title="<?=$arBrand['COU_DES_TEXT']?>" width="16" height="11">&nbsp;
							<?}else{?>
								<img src="<?=CORE_ROOT_DIR?>/media/countries/Unknown.png" title="" class="coupic" width="16" height="11">&nbsp;
							<?}?>
							<?if($arBrand['BRAND_ID']>0){?>
								<a href="<?=CORE_ROOT_DIR?>/brand/<?=$arBrand['BRAND_ID']?>/" title="<?=TMes('More about the firm')?>"><?=$BRAND_NAME?></a>
							<?}else{?>
								<?=$BRAND_NAME?>
							<?}?>
						</div>
						<?
						if(count($arBrand['PRICES'])>0){?>
							<div class="tflef">
								<?foreach($arBrand['PRICES'] as $arPrice){?>
									<?if(strlen($arPrice['PART_NAME'])>$PART_NAME_LENGTH){$arPrice['PART_NAME']=substr($arPrice['PART_NAME'],0,($PART_NAME_LENGTH-3)).'...';}?>
									<?if(defined("CORE_IS_ADMIN") AND CORE_IS_ADMIN==true){
										$arPrice['ADMIN_TIPS']='title="'.$arPrice['SUPPLIER'].' ';
										if($arPrice['STOCK']!=''){$arPrice['ADMIN_TIPS'].='['.$arPrice['STOCK'].'] ';}
										if($arPrice['IMPORT_CODE']!=''){$arPrice['ADMIN_TIPS'].=' '.$arPrice['IMPORT_CODE'].' ';}
										if($arPrice['IMPORT_DATE']!=''){$arPrice['ADMIN_TIPS'].=' ('.date("d.m.y",$arPrice['IMPORT_DATE']).')';}
										$arPrice['ADMIN_TIPS'].='"';
									}?>
									<table class="pricestab"><tr class="hovback2">
										<td class="brlef1 brrig1 name infolay">
											<?if($arBrand['ART_ID']>0){?>
												<a href="<?=$arBrand['DETAIL_URL']?>" title="<?=TMes('Detail about the part of')?> <?=$BRAND_NAME?>"><?=$arPrice['PART_NAME']?></a>
												<div class="infopic">
													<a href="<?=CORE_ROOT_DIR?>/part.chars.php?artid=<?=$arBrand['ART_ID']?>" class="cbx_chars" title="<?=TMes('Information')?>"></a>
												</div>
											<?}else{?>
												<a href="#" class="nodetails" title="<?=TMes('No description of the part')?>..."><?=$arPrice['PART_NAME']?></a>
											<?}?>
											&nbsp;
										</td>
										<td class="brrig1 avail"><?=$arPrice['AVAILABLE']?></td>
										<td class="brrig1 price" <?=$arPrice['ADMIN_TIPS']?> ><?=$arPrice['PRICE']?></td>
										<td class="brrig1 days"><?=$arPrice['DAY']?></td>
										<td class="order ordpad">
											<form method="post" name="tocart" class="orderform">
												<input type="hidden" name="AddPartToCart" value="Y">
												<input type="hidden" name="artnum" value="<?=$_REQUEST['artnum']?>">
												<input type="hidden" name="ID" value="<?=$arPrice['ID']?>">
												<input type="number" name="count" value="1" class="count_inp" size="2" maxlength="3" min="1" max="99" >
												<input type="submit" class="tcart" title="<?=TMes('Add to cart')?>" value="">
											</form>
										</td>
									</table>
									<div class="cler"></div>
								<?}?>
							</div>
						<?}else{?>
							<div class="tflef">
								<div class="tflef brlef1 brrig1 name infolay">
									<?if($arBrand['ART_ID']>0){?>
										<a href="<?=$arBrand['DETAIL_URL']?>" title="<?=TMes('Detail about the part of')?> <?=$BRAND_NAME?>"><?=$arBrand['PART_NAME']?></a>
										<div class="infopic">
											<a href="<?=CORE_ROOT_DIR?>/part.chars.php?artid=<?=$arBrand['ART_ID']?>" class="cbx_chars" title="<?=TMes('Information')?>"></a>
										</div>
									<?}else{?>
										<a href="#" class="nodetails" title="<?=TMes('No description of the part')?>..."><?=$arBrand['PART_NAME']?></a>
									<?}?>
									&nbsp;
								</div>
								
								<?if(ADDTOCART_NOPRICE=="Y"){?>
									<div class="tflef avail"></div>
									<div class="tflef days"></div>
									<div class="tflef grey_color price"><?=TMes('Make_order')?>:</div>
									<div class="tflef order ordpad woprice">
										<form method="post" name="tocart" class="orderform">
											<input type="hidden" name="OrderPartToCart" value="Y">
											<input type="hidden" name="artnum" value="<?=$_REQUEST['artnum']?>">
											<input type="hidden" name="OrderPartArticle" value="<?=$arPart['ART_ARTICLE_NR']?>">
											<input type="hidden" name="OrderPartBrand" value="<?=$BRAND_NAME?>">
											<input type="hidden" name="OrderPartName" value="<?=$arBrand['PART_NAME']?>">
											<input type="hidden" name="OrderPartURL" value="<?=$arBrand['DETAIL_URL']?>">
											<input type="hidden" name="OrderPartImg" value="<?=$arBrand['IMG']?>">
											<input type="number" name="count" value="1" class="count_inp" size="2" maxlength="3" min="1" max="99" >
											<input type="submit" class="tcart" title="<?=TMes('Add to cart')?>" value="">
										</form>
									</div>
								<?}else{?>
									<div class="tflef grey_color brand">
										<?=TMes('No prices')?>...
									</div>
								<?}?>
								<div class="cler"></div>
							</div>
						<?}?>
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
		<br><br><center>
		<?if($arResult['COMPONENT']=="SEARCH"){?><?=TMes('No parts with that number (article)')?><?}else{?><?=TMes('Has no parts in this section')?><?}?>
		...</center><br><br><br>
	<?}?>
</div>
<div class="cler"></div>

<div id="nodetailinfo"><img src="<?=CORE_ROOT_DIR?>/media/images/app.png" class="tflef" style="margin-right:20px;"><br><?=TMes('No description of the part')?></div>

<script>
	$(document).ready(function(){
		$(".cbx_imgs").colorbox({rel:'nofollow', current:'', innerWidth:900, innerHeight:600});
		$(".cbx_chars").colorbox({rel:false, current:'', overlayClose:true, arrowKey:false, opacity:0.6});
	
		$(".infolay").mouseenter(function(){
			$(".infopic",this).show("fast");
		}).mouseleave(function(){
			$(".infopic",this).hide();
		});
	});
	
	$(function() { 
		$( "#nodetailinfo" ).dialog({
			autoOpen: false,
			width: 400,
			modal: true,
			buttons: [
				{
					text: "Ok",
					click: function() {
						$( this ).dialog( "close" );
					}
				}
			]
		});

		$( ".nodetails" ).click(function( event ) {
			$( "#nodetailinfo" ).dialog( "open" );
			event.preventDefault();
		});
		
	});
</script>

<?if(defined("SEO_BOTTEXT") AND SEO_BOTTEXT!=''){?><br><br><?=SEO_BOTTEXT?><hr class="marbot18"><?}?>

<?if($arResult['BACK_LINK']!=''){?>
	<br><br>
	<a href="<?=$arResult['BACK_LINK']?>" class="bglink">&#9668; <?=$arResult['BACK_NAME']?></a>
<?}?>
<?/*echo '<pre>';print_r($arResult);echo '</pre>';?>