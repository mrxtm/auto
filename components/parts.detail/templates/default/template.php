<?if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die();?>
<?if($arResult['ERROR']!=''){?>
	<hr>
	<br><?=$arResult['ERROR']?>
<?}else{?>
	<script> $(function() { $( "#detailtabs" ).tabs(); }); </script>
	<?if($arResult['BRAND_LOGO']!=''){?>
		<div class="brlogo"><img src="<?=$arResult['BRAND_LOGO']?>" alt="<?=$arResult['PART']['SUP_BRAND']?>" title="<?=$arResult['PART']['SUP_BRAND']?>"></div>
	<?}?>
	<table><tr><td style="padding-right:20px;">
		<h1 class="hd1"><?=$TCore->Head_Title_H1?></h1></td><td>
		<?/*if($arResult['SHOW_SUP_COUNTRY']=="Y"){?>
			<img src="<?=CORE_ROOT_DIR?>/media/countries/<?=$arResult['SUP_COUNTRY']['CODE']?>.png" class="coupic" title="<?=$arResult['SUP_COUNTRY']['NAME']?>" width="16" height="11">
		<?}?>
		<?=$arResult['PART']['SUP_BRAND']*/?>
		<div class="cler"></div>
	</table>
	<div class="subtit" style="padding-bottom:14px;">
		<?if(defined("SEO_TOPTEXT") AND SEO_TOPTEXT!=''){?>
			<?=SEO_TOPTEXT?>
		<?}else{?>
			<?=$arResult['PART']['PART_NAME']?><?if($arResult['SHOW_INFO']=="Y"){?>. <?foreach($arResult['INFO'] as $arInfo){echo $InCom.$arInfo; $InCom=', ';}?><?}?>
		<?}?>
	</div>
	
	<a href="<?=CORE_ROOT_DIR?>/search/<?=$arResult['PART']['NUMBER']?>" class="amod"><?=TMes('Show all analogs')?> <strong><?=$arResult['PART']['ART_ARTICLE_NR']?></strong> (<?=$arResult['PART']['SUP_BRAND']?>)</a>
	<hr>
	
	<?if($arResult['SHOW_PRICES']=="Y"){?>
		<h2 class="hd2"><?=TMes('Prices')?></h2>
		<div id="prices" class="listlays">
			<div class="head">
				<div class="brand roul"><?=TMes('Firm')?></div>
				<div class="brlef1 brrig1 name"><?=TMes('Parts name')?></div>
				<div class="brrig1 avail" title="<?=TMes('Stock available')?>"><?=TMes('Avail')?></div>
				<div class="brrig1 price"><?=TMes('Price')?> <?=$_SESSION['TECDOC_SELECTED_SYM']?></div>
				<div class="brrig1 days" title="<?=TMes('Parts delivery time')?>"><?=TMes('Days')?></div>
				<div class="brrig1 order rour"><?=TMes('Order')?></div>
			</div>
			<div class="cler"></div>
			<?foreach($arResult['PRICES'] as $BRAND_NAME=>$arBrand){?>
				<div class="tflef brbot1_img hovback1">
					<div class="brand">
						<?if($arBrand['BRAND_ID']>0){?>
							<a href="<?=CORE_ROOT_DIR?>/brand/<?=$arBrand['BRAND_ID']?>/" title="<?=TMes('More about the firm')?>"><?=$BRAND_NAME?></a>
						<?}else{?>
							<?=$BRAND_NAME?>
						<?}?>
					</div>
					<div class="tflef">
						<?foreach($arBrand['PRICES'] as $arPrice){?>
							<table class="tflef brbot1"><tr class="hovback2">
								<td class="brlef1 brrig1 name"><?=$arPrice['PART_NAME']?>&nbsp;</td>
								<td class="brrig1 avail"><?=$arPrice['AVAILABLE']?></td>
								<td class="brrig1 price"><?=$arPrice['PRICE']?></td>
								<td class="brrig1 days"><?=$arPrice['DAY']?></td>
								<td class="order ordpad">
									<form method="post" name="tocart" class="orderform">
										<input type="hidden" name="AddPartToCart" value="Y">
										<input type="hidden" name="ID" value="<?=$arPrice['ID']?>">
										<input type="number" name="count" value="1" class="count_inp" size="2" maxlength="3" min="1" max="99">
										<input type="submit" class="tcart" title="<?=TMes('Add to cart')?>" value="">
									</form>
								</td>
							</table>
							<div class="cler"></div>
						<?}?>
					</div>
					<div class="cler"></div>
				</div>
				<div class="cler"></div>
			<?}?>
		</div>
		<div class="cler"></div>
		
	<?}elseif(ADDTOCART_NOPRICE=="Y"){?>
		<form method="post" name="tocart" class="orderform">
			<input type="hidden" name="OrderPartToCart" value="Y">
			<input type="hidden" name="OrderPartArticle" value="<?=$arResult['PART']['ART_ARTICLE_NR']?>">
			<input type="hidden" name="OrderPartBrand" value="<?=$arResult['PART']['SUP_BRAND']?>">
			<input type="hidden" name="OrderPartName" value="<?=$arResult['PART']['PART_NAME']?>">
			<input type="hidden" name="OrderPartURL" value="<?=$arResult['CUR_URL']?>">
			<input type="hidden" name="OrderPartImg" value="<?=$arResult['PART']['IMG']?>">
			<input type="hidden" name="count" value="1">
			<input type="submit" class="orderbutton" title="<?=TMes('Add to cart')?>" value="<?=TMes('Make_order')?>">
		</form>
	<?}?>
	<br><br>
	
	<div id="detailtabs">
		<ul>
			<?if($arResult['SHOW_CHARS']=="Y"){?>
				<li><a href="<?=$arResult['CUR_URL']?>#chars"><?=TMes('Characteristics')?></a></li>
			<?}?>
			<?if($arResult['SHOW_ORIGINALS']=="Y"){?>
				<li><a href="<?=$arResult['CUR_URL']?>#originals" ><?=TMes('Original numbers')?></a></li>
			<?}?>
			<?if($arResult['SHOW_APPLICS']=="Y"){?>
				<li><a href="<?=$arResult['CUR_URL']?>#appics" ><?=TMes('Applicability of parts for cars')?></a></li>
			<?}?>
			<?if($arResult['SHOW_PDFS']=="Y"){?>
				<li><a href="<?=$arResult['CUR_URL']?>#pdfs" ><?=TMes('Instructions')?> / PDF</a></li>
			<?}?>
		</ul>
		<?if($arResult['SHOW_CHARS']=="Y"){?>
			<div id="chars">
				<table class="corp_table">
					<?foreach($arResult['CHARS'] as $arChar){?>
						<tr><td class="pads brbot1"><?=$arChar['NAME']?></td><td class="pads brbot1 brlef1"><?=$arChar['VALUE']?></td></tr>
					<?}?>
				</table>
			</div>
		<?}?>
		
		<?if($arResult['SHOW_ORIGINALS']=="Y"){?>
			<div id="originals">
				<table class="corp_table">
					<?foreach($arResult['ORIGINALS'] as $arOrig){?>
						<tr><td class="pads brbot1"><?=$arOrig['BRAND']?></td>
							<td class="pads brbot1 brlef1"><a href="<?=CORE_ROOT_DIR?>/search/<?=$arOrig['NUMBER']?>"><?=$arOrig['ARTICLE']?></a></td>
						</tr>
					<?}?>
				</table>
			</div>
		<?}?>
		
		<?if($arResult['SHOW_APPLICS']=="Y"){?>
			<div id="appics">
				<table class="corp_table" id="cortab">
					<tr><td class="head"><?=TMes('Model')?> / <?=TMes('Engine')?></td><td class="head"><?=TMes('Year of construction')?></td><td class="head"><?=TMes('Power')?></td><td class="head"><?=TMes('Fuel')?></td></tr>
					<?$arCMods=Array();
					foreach($arResult['APPLICS'] as $arAType){
						$arMd = explode(' ',$arAType['MOD_CDS_TEXT']);
						$AutoType = $arAType['MFA_BRAND'].' '.$arMd[0];
						if(!in_array($arMd[0],$arCMods)){
							$arCMods[] = $arMd[0];
							$Mc++;
							echo '<tr id="tr'.$Mc.'" class="modtr"><td colspan="10"><a class="amodtr" href="javascript:void(0)" OnClick="ShowMods('.$Mc.')">'.$AutoType.'</a></td></tr>';
						}
						echo '<tr class="mods'.$Mc.'" id="typtr">';
						echo '<td class="pads"><a href="'.CORE_ROOT_DIR.'/'.$arAType['MFA_LOW_BRAND'].'/m'.$arAType['MOD_ID'].'/t'.$arAType['TYP_ID'].'/" class="amod">'.$arAType['MFA_BRAND'].' '.$arAType['MOD_CDS_TEXT'].' / '.$arAType['TYP_CDS_TEXT'].'</a></td>';
						echo '<td class="pads">'.$arAType['DATE_FROM'].' - '.$arAType['DATE_TO'].'</td>';
						echo '<td class="pads">'.$arAType['TYP_KW_FROM'].' <span>'.TMes('Kv').'</span> - '.$arAType['TYP_HP_FROM'].' <span>'.TMes('Hp').'</span> / '.$arAType['ENG_CODE'].'</td>';
						echo '<td class="pads">'.$arAType['TYP_ENGINE_DES_TEXT'].'</td>';
						echo '</tr>';
					}?>
				</table>
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
			</div>
		<?}?>
		
		<?if($arResult['SHOW_PDFS']=="Y"){?>
			<div id="pdfs">
				<?foreach($arResult['PDFS'] as $PdfPath){$PDF++;?>
					<a href="<?=$PdfPath?>" class="prpdf" target="_blank" title="<?=TMes('Open')?>"><img src="<?=CORE_ROOT_DIR?>/media/images/pdf_file.png" width="64px" height="89px"><br><?=$PDF?>. <?=TMes('Download')?></a>
				<?}?>
				<div class="cler"></div>
			</div>
		<?}?>
	
	</div>
	<br><br>
		
	<?if($arResult['SHOW_IMGS']=="Y"){?>
		<link rel="stylesheet" href="<?=CORE_ROOT_DIR?>/media/js/colorbox.css" />
		<script type="text/javascript" language="javascript" src="<?=CORE_ROOT_DIR?>/media/js/jquery.colorbox.js"></script>
		<script>
			$(document).ready(function(){
				$(".group1").colorbox({rel:'group1', current:'', innerWidth:900, innerHeight:600});
			});
		</script>
		<h2 class="hd2"><?=TMes('Pictures')?></h2>
		<?foreach($arResult['IMGS'] as $ImgPath){?>
			<a href="<?=$ImgPath?>" style="background:#ffffff url(<?=$ImgPath?>);" class="group1 primg" ></a>
		<?}?>
		<div class="cler"></div>
	<?}?>
	
	<?if(defined("SEO_BOTTEXT") AND SEO_BOTTEXT!=''){?><hr class="marbot18"><?=SEO_BOTTEXT?><hr class="marbot18"><?}?>
	
<?}?>