<?if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die();?>
<hr>
<?if($arResult['ERROR']!=''){?>
	<?=$arResult['ERROR']?>
<?}else{?>
	<div class="autopic" style="background:url(<?=CORE_ROOT_DIR?>/media/brands/<?=$arResult['MFA_MFC_CODE']?>.png); width:40px"></div>
	<?/*<div class="subtit"><?=TMes('Brand')?>: <a href="<?=CORE_ROOT_DIR?>/"><?=$arResult['UBRAND']?></a></div><hr class="marbot18">*/?>
	<div style="padding:3px;"></div>
	<?if(defined("SEO_TOPTEXT") AND SEO_TOPTEXT!=''){?><?=SEO_TOPTEXT?><hr class="marbot18"><?}?>
	<?foreach($arResult['MODELS'] as $CurModel=>$arModels){?>
		<div class="modelsdiv" style="background-image:url(<?=$arResult['MODEL_PICS'][$CurModel]?>);">
			<div class="modelname"><?=$CurModel?></div>
			<?if(count($arModels)<=1){
				$arModel = $arModels[0];
				$arModel['MOD_CDS_TEXT'] = str_replace(trim($CurModel),'',$arModel['MOD_CDS_TEXT']);
				$arModel['MOD_CDS_TEXT'] = str_replace('[USA]','(US)',$arModel['MOD_CDS_TEXT']);?>
				<a href="<?=CORE_ROOT_DIR?>/<?=$arResult['BRAND']?>/m<?=$arModel['MOD_ID']?>/" class="ampick" title="<?=$arModel['MOD_CDS_TEXT']?> <?=$arModel['DATE_FROM']?> - <?=$arModel['DATE_TO']?>"><?=$arModel['MOD_CDS_TEXT']?> <?=$arModel['DATE_FROM']?> - <?=$arModel['DATE_TO']?></a>
			<?}else{?>
				<ul class="ddmodel">
					<li class="ddpick"><a href="javascript:void(0)" >- <?=TMes('select the model')?> -</a>
						<ul>
						<?foreach($arModels as $arModel){?>
							<li><a href="<?=CORE_ROOT_DIR?>/<?=$arResult['BRAND']?>/m<?=$arModel['MOD_ID']?>/"><?=$arModel['MOD_CDS_TEXT']?> <?=$arModel['DATE_FROM']?> - <?=$arModel['DATE_TO']?></a></li>
						<?}?>
						</ul>
					</li>
				</ul>
			<?}?>
		</div>
		<?
	}?>
	<div class="cler"></div>
<?}?>
<?if(defined("SEO_BOTTEXT") AND SEO_BOTTEXT!=''){?><hr class="marbot18"><?=SEO_BOTTEXT?><hr class="marbot18"><?}?>
<br>
<br>
<a href="<?=CORE_ROOT_DIR?>/" class="bglink">&#9668; <?=TMes('Back to the brand selection')?></a>

<script type="text/javascript">
	$(document).ready(function() {
		$('.ddpick').bind('mousedown', openSubMenu);
		$('.ddpick').bind('mouseleave', closeSubMenu);
		function openSubMenu() {
			$(this).find('ul').css('display', 'block');	
		};
		function closeSubMenu() {
			$(this).find('ul').css('display', 'none');	
		};	   
	});
</script>
