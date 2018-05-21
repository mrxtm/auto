<?if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die(); ?>
<?if(CORE_IS_ADMIN){?>
	<link rel="stylesheet" href="<?=CORE_ROOT_DIR?>/admin/styles.css" type="text/css">
	<div class="admin_panel">
		<div class="adplef"></div>
		<a href="<?=CORE_ROOT_DIR?>/admin/" class="adphome"></a>
		<?if(!defined("TDM_ADMIN_SIDE")){
			$META_EDIT="Y";?>
			<a href="javascript:void(0);" onclick="$('#csetlay').hide(); $('#metalay').slideToggle();" class="apiclink <?if($TCore->Meta_isRecord=="Y"){echo 'radfon';}?>" ><img src="<?=CORE_ROOT_DIR?>/admin/images/seo.png" title="SEO" width="16px" height="16px"/></a>
			<div class="adpsep"></div>
		<?}?>
		<?if(defined("TDM_ADMIN_SIDE")){?>
			<a href="<?=CORE_ROOT_DIR?>/"><?=TMes('Catalog')?></a><div class="adpsep"></div>
		<?}?>
		<?if($TCore->Included_Component!=''){?>
			<a href="javascript:void(0);" onclick="$('#metalay').hide(); $('#csetlay').slideToggle('fast');" class="apiclink"><img src="<?=CORE_ROOT_DIR?>/admin/images/setedit.png" title="<?=TMes('Component settings')?>" width="16px" height="16px"/></a>
			<div class="adpsep"></div>
		<?}?>
		<a href="<?=CORE_ROOT_DIR?>/admin/import/"><?=TMes('Import master')?></a>
		<div class="adpsep"></div>
		
		
		<a href="<?=CORE_ROOT_DIR?>/admin/dbedit/" class="apiclink"><img src="<?=CORE_ROOT_DIR?>/admin/images/dbedit.png" title="<?=TMes('Data Base editor')?>" width="16px" height="16px"/></a>
		<div class="adpsep"></div>
		
		<a href="<?=CORE_ROOT_DIR?>/admin/currencies.php" class="apiclink"><img src="<?=CORE_ROOT_DIR?>/admin/images/curs.png" title="<?=TMes('Exchange Rates')?>" width="16px" height="16px"/></a>
		<div class="adpsep"></div>
		
		<a href="<?=CORE_ROOT_DIR?>/admin/db_service.php" class="apiclink"><img src="<?=CORE_ROOT_DIR?>/admin/images/database.png" title="<?=TMes('Data Base service')?>" width="16px" height="16px"/></a>
		<div class="adpsep"></div>
		
		<div class="adprig"></div>
		<a href="<?=CORE_ROOT_DIR?>/admin/?logout=Y" class="adplogout"><?=TMes('Logout')?></a>
		<div class="adpsep adplogout"></div>
		
		<a href="javascript:void(0)" class="adplogout" title="<?=TMes('Currently selected currency')?> (<?=$_SESSION['TECDOC_CUR_MODULE']?>)"><?=$_SESSION['TECDOC_SELECTED_CUR']?></a>
		<div class="adpsep adplogout"></div>
	</div>
	
	<?if($META_EDIT=="Y"){?>
		<div class="sublay" id="metalay">
			<form action="" method="post">
			<input type="hidden" name="set_meta" value="Y">
			<table class="sublaytab">
				<tr><td>Title: </td><td><input type="text" name="set_title" value="<?if(defined("Real_Title")){echo Real_Title;}?>" class="subinput"></td></tr>
				<tr><td>Keywords: </td><td><input type="text" name="set_keywords" value="<?if(defined("Real_Keywords")){echo Real_Keywords;}?>" class="subinput"></td></tr>
				<tr><td>Description: </td><td><input type="text" name="set_description" value="<?if(defined("Real_Description")){echo Real_Description;}?>" class="subinput"></td></tr>
				<tr><td><?=TMes('Title')?> H1: </td><td><input type="text" name="set_title_h1" value="<?if(defined("Real_Title_H1")){echo Real_Title_H1;}?>" class="subinput"></td></tr>
				<tr><td><?=TMes('Top SEO text')?>: </td><td><textarea name="set_toptext" class="subinput sbinp"><?if(defined("SEO_TOPTEXT")){echo SEO_TOPTEXT;}?></textarea>
				<tr><td><?=TMes('Bottom SEO text')?>: </td><td><textarea name="set_bottext" class="subinput sbinp"><?if(defined("SEO_BOTTEXT")){echo SEO_BOTTEXT;}?></textarea></td></tr>
				<tr><td></td><td>
					<input type="submit" value="<?=TMes('Save')?>" class="abutton smbut"/> 
					<?if($TCore->Meta_isRecord=="Y"){?>
						<input type="submit" name="set_delete" value="<?=TMes('Delete this Meta record')?>" class="abutton smbut smgrey tfrig"/>
					<?}?>
				</td></tr>
			</table>
			</form>
		</div>
		<?if($_POST['set_meta']=="Y"){?><script>$('#metalay').show();</script><?}?>
	<?}?>
	<?if(!defined("TDM_ADMIN_SIDE")){?>
		<div class="sublay" id="csetlay">
			<form action="" method="post">
				<input type="hidden" name="set_com_sets" value="Y">
				<?include($_SERVER["DOCUMENT_ROOT"].CORE_ROOT_DIR."/components/".$TCore->Included_Component."/settings.php");?>
			</form>
		</div>
	<?}?>
<?}?>