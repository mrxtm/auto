<?if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die();?>
<hr>
<?if($arResult['ERROR']!=''){?>
	<?=$arResult['ERROR']?>
<?}else{?>
	<?if($arResult['MODEL_PIC']!=''){?>
		<div class="autopic" style="background:url(<?=$arResult['MODEL_PIC']?>)"></div>
	<?}?>
	<div class="subtit"><?=TMes('Brand')?>: <a href="<?=CORE_ROOT_DIR?>/"><?=$arResult['UBRAND']?></a></div>
	<div class="subtit"><?=TMes('Model')?>: <a href="<?=CORE_ROOT_DIR?>/<?=$arResult['BRAND']?>/"><?=$arResult['MODEL']?></a></div>
	<div class="subtit"><?=TMes('Type')?>: <a href="<?=CORE_ROOT_DIR?>/<?=$arResult['BRAND']?>/m<?=$arResult['MOD_ID']?>/"><?=$arResult['TYPE']?></a></div>
	<hr class="marbot18">
	<?if(defined("SEO_TOPTEXT") AND SEO_TOPTEXT!=''){?><?=SEO_TOPTEXT?><hr class="marbot18"><?}?>
	
	<script type="text/javascript" language="javascript" src="<?=CORE_ROOT_DIR?>/media/js/jqtree/jquery.jstree.js"></script>
	<script type="text/javascript" src="<?=CORE_ROOT_DIR?>/media/js/jqtree/script.js"></script>
	
	<div id="jtree" class="jtree" style="float:left;"></div>
	<div class="cler"></div>
	
	<script type="text/javascript">
		$(function () {
			$("#jtree").jstree({
				"json_data" : {
					"ajax" : {
						"url" : "<?=CORE_ROOT_DIR?>/components/sections.tree.ajax/tree_json.php?brand=<?=$arResult['BRAND']?>&model=<?=$arResult['MOD_ID']?>&type=<?=$arResult['TYPE_ID']?><?if($arResult['SEC_ID']>0){?>&sec=<?=$arResult['SEC_ID']?><?}?>",
						"data" : function (node) {
							return { id : node.attr ? node.attr("id") : 0 }; 
						},
						"type": "POST"
					}
				},
				"types" : {
					"types" : {
						"file" : {
							"icon":{"image" : "<?=CORE_ROOT_DIR?>/media/js/jqtree/file.png"}
						}
					}
				},
				"themes" : {"theme" : "default"},
				"plugins" : [ "themes", "json_data", "types", "ui" ]
			})
			//Если задан href то перейти по ссылке
			.bind("select_node.jstree", function(e, data){
				var href = data.rslt.obj.attr("href");
				if(href){ window.location=href;}
			});
		});
		//Чтобы названия тоже раскрывали список
		$("#jtree").delegate("a","click", function (e){
			if(this.className.indexOf('icon') == -1){
				$("#jtree").jstree("toggle_node", this);
				e.preventDefault(); 
				return false;
			}
		});
	</script>
	<div class="cler"></div>
	<?if(defined("SEO_BOTTEXT") AND SEO_BOTTEXT!=''){?><hr class="marbot18"><?=SEO_BOTTEXT?><hr class="marbot18"><?}?>
	<br>
	<br>
	<a href="<?=CORE_ROOT_DIR?>/<?=$arResult['BRAND']?>/m<?=$arResult['MOD_ID']?>/" class="bglink">&#9668; <?=TMes('Back to the type selection')?> <?=$arResult['UBRAND']?></a>
<?}?>