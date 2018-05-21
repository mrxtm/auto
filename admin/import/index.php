<?
define("TDM_ADMIN_SIDE","Y");
require_once("../../core/prolog.php");
$FORM_ACTION=CORE_ROOT_DIR.'/admin/import/';
if($_SESSION['CORE_IS_ADMIN']!="Y"){header('Location: '.CORE_ROOT_DIR.'/admin/'); die();}
if(CORE_SITE_CHARSET=="utf8"){header('Content-type: text/html; charset=utf-8');}

$TCore->Head_Title = TMes('Import master');
if($_REQUEST['ajax_import']=='Y'){
	if(CORE_SITE_CHARSET=="utf8"){header('Content-type: text/html; charset=utf-8');} 
	if(intval($_REQUEST['onestep'])>0){define("IMPORT_AJAX_STEP",intval($_REQUEST['onestep']));}else{define("IMPORT_AJAX_STEP",5000);}
	$AJAX="Y";
}else{ 
	//require_once("../../core/header.php");
}

if($AJAX!="Y"){?>
	<head><title><?=TMes('Import master')?> :: TecDoc module</title></head>
	<div class="displayblock">
	<?require_once("../admin_panel.php");?>
	<div class="acorp_out ashad_a">
<?}

	global $TDataBase;
	$TDataBase->DBSelect("CORE");
	require_once("core.php");

	if($AJAX!="Y"){?>
		<h1 class="hd1"><?=TMes('Import master')?></h1>
		
		<link rel="stylesheet" href="<?=CORE_ROOT_DIR?>/media/js/colorbox.css" />
		<script type="text/javascript" language="javascript" src="<?=CORE_ROOT_DIR?>/media/js/jquery-1.9.1.js"></script>
		<script type="text/javascript" language="javascript" src="<?=CORE_ROOT_DIR?>/media/js/jquery.colorbox.js"></script>
		<script>
			$(document).ready(function(){
				$(".popup").colorbox({rel:false, current:'', preloading:false, arrowKey:false, scrolling:false, overlayClose:false});
			});
			$("#cboxPrevious").hide();
			$("#cboxNext").hide();
		</script>
		<script>
			function SetICode(icode){$("#price_code").val(icode);}
		</script>
		
		<link rel="stylesheet" href="<?=CORE_ROOT_DIR?>/media/js/formstyler/jquery.formstyler.css" />
		<script src="<?=CORE_ROOT_DIR?>/media/js/formstyler/jquery.formstyler.min.js"></script>
		<script>(function($) { $(function(){ $('input, select, checkbox, radio').styler(); }) })(jQuery)</script>
		
		<form name="iform" id="iform" action="<?=$FORM_ACTION?>" method="post" enctype="multipart/form-data">
		<input type="hidden" name="checkme" value="Y"/>
		
		<a class="popup mblink tfrig" href="<?=$FORM_ACTION?>template.edit.php" title="<?=TMes('Templates management')?>"><?=TMes('Add template')?></a><br>
		<div class="htit">1. <?=TMes('Template')?></div>
		<table class="corp_table smpads imptable" width="100%"><tr>
			<td class="head"></td>
			<td class="head" title="<?=TMes('Any text')?>" width="40%"><?=TMes('Name')?></td>
			<td class="head" title="<?=TMes('Import columns')?>"><img src="<?=CORE_ROOT_DIR?>/media/images/links.png" width="16" height="16" alt=""></td>
			<td class="head" title="<?=TMes('Separator of columns')?>"><img src="<?=CORE_ROOT_DIR?>/media/images/sep.png" width="16" height="16" alt=""></td>
			<?/*<td class="head" title="<?=TMes('Separator in column: Part No.')?>"><img src="<?=CORE_ROOT_DIR?>/media/images/sepa.png" width="16" height="16" alt=""></td>*/?>
			<?/*<td class="head" title="<?=TMes('Price table or cross')?>"><?=TMes('Table')?></td>*/?>
			<td class="head" title="<?=TMes('CSV file encoding')?>"><img src="<?=CORE_ROOT_DIR?>/media/images/char.png" width="16" height="16" alt=""></td>
			<td class="head" title="<?=TMes('Group key')?> - <?=TMes('as default')?>"><img src="<?=CORE_ROOT_DIR?>/media/images/key.png" width="16" height="16" alt=""></td>
			<td class="head" title="<?=TMes('Manufacturer')?> - <?=TMes('as default')?>"><img src="<?=CORE_ROOT_DIR?>/media/images/manuf.png" width="16" height="16" alt=""></td>
			<td class="head" title="<?=TMes('Extra prices importing')?>"><img src="<?=CORE_ROOT_DIR?>/media/images/money-plus.png" width="16" height="16" alt=""></td>
			<td class="head" title="<?=TMes('Parts delivery time')?> - <?=TMes('as default')?>"><img src="<?=CORE_ROOT_DIR?>/media/images/clock.png" width="16" height="16" alt=""></td>
			<td class="head" title="<?=TMes('Availability by default')?>"><img src="<?=CORE_ROOT_DIR?>/media/images/avail.png" width="16" height="16" alt=""></td>
			<td class="head" title="<?=TMes('Supplier by default')?>"><img src="<?=CORE_ROOT_DIR?>/media/images/truck.png" width="16" height="16" alt=""></td>
			<td class="head" title="<?=TMes('Stock')?> - <?=TMes('as default')?>"><img src="<?=CORE_ROOT_DIR?>/media/images/stock.png" width="16" height="16" alt=""></td>
			<td class="head" title="<?=TMes('Currency')?> - <?=TMes('as default')?>"><img src="<?=CORE_ROOT_DIR?>/media/images/currency.png" width="16" height="16" alt=""></td>
			<td class="head"><?=TMes('Actions')?></td>
		</tr>
	<?}?>
	<?$rsITemps = ImportTemplates::GetList(Array("ID"=>"ASC"),Array());
	while($arITemp = $rsITemps->Fetch()){
		//Удалить
		if($_REQUEST['del']==$arITemp['ID']){
			ImportTemplates::Delete($arITemp['ID']); continue;
		}
		//Сделать по умолчанию
		if($_REQUEST['default']>0){
			if($_REQUEST['default']==$arITemp['ID']){$D=1;}else{$D=0;}
			ImportTemplates::Update($arITemp['ID'],Array("IDEF"=>$D));
			$arITemp['IDEF']=$D;
		}
		//Значения
		if($arITemp['IDEF']==1){$arITemp['DEFAULT']='<img src="'.CORE_ROOT_DIR.'/media/images/star.png" width="16" height="16" title="'.TMes('The default is on').'">';}else{$arITemp['DEFAULT']='<a href="'.$FORM_ACTION.'?default='.$arITemp['ID'].'" style="margin-right:0px!important;"><img src="'.CORE_ROOT_DIR.'/media/images/default.png" width="16" height="16" title="'.TMes('Set by default').'"></a>';}
		if($arITemp['SEP']=='	'){$arITemp['SEP']='[tab]';}
		if($arITemp['SEP']==' '){$arITemp['SEP']='[spc]';}
		//Колонки
		$rsPRows = ImportColumns::GetList(Array("ID"=>"ASC"),Array("TEMPL_ID"=>$arITemp['ID']));
		while($arPRow = $rsPRows->Fetch()){
			$arITemp['ROWS_ADDED']++;
			
		}
		//Выбрать для ипорта
		if($_REQUEST['template']==$arITemp['ID']){$arCITemp=$arITemp;}
		$ITNum++;
		?>
		<?if($AJAX!="Y"){?>
			<tr>
			<td class="brbot1"><input type="radio" name="template" value="<?=$arITemp['ID']?>" id="tem<?=$arITemp['ID']?>" <?if($arITemp['IDEF']==1){echo 'checked'; $DefICode=$arITemp['DEF_ICODE'];}?> OnChange="SetICode('<?=$arITemp['DEF_ICODE']?>')"></td>
			<td class="brbot1 brrig1"><label for="tem<?=$arITemp['ID']?>" class="tem_label"><?=$arITemp['NAME']?></label></td>
			<td class="brbot1 brrig1">
				<a href="<?=$FORM_ACTION?>rows.edit.php?ID=<?=$arITemp['ID']?>" class="popup <?if($arITemp['ROWS_ADDED']>0){?>csv_added<?}else{?>csv_new<?}?>" title="<?=TMes('Edit fields links')?>">
				<?if($arITemp['ROWS_ADDED']>0){?><?=$arITemp['ROWS_ADDED']?><?}else{?><img src="<?=CORE_ROOT_DIR?>/media/images/plus.gif" width="16px" height="16px" class="vermid"><?}?></a>
			</td>
			<td class="brbot1 brrig1"><?=$arITemp['SEP']?></td>
			<?/*<td class="brbot1 brrig1"><?=$arITemp['SEP_ART']?></td>*/?>
			<?/*<td class="brbot1 brrig1"><?=$arITemp['ITABLE']?></td>*/?>
			<td class="brbot1 brrig1 font10"><?=$arITemp['ENCODE']?></td>
			<td class="brbot1 brrig1"><?=$arITemp['DEF_ICODE']?></td>
			<?if($arITemp['ITABLE']=="PRICES"){?>
				<td class="brbot1 brrig1"><?=$arITemp['DEF_BRA']?></td>
				<td class="brbot1 brrig1"><?=$arITemp['EXTRA']?><span class="grays amini">%</span></td>
				<td class="brbot1 brrig1"><?=$arITemp['DEF_DAY']?></td>
				<td class="brbot1 brrig1"><?=$arITemp['DEF_AVAIL']?></td>
				<td class="brbot1 brrig1"><?=$arITemp['DEF_SUPL']?></td>
				<td class="brbot1 brrig1"><?=$arITemp['DEF_STOCK']?></td>
				<td class="brbot1 brrig1"><?=$arITemp['DEF_CUR']?></td>
			<?}else{?>
				<td colspan="7" class="brbot1 brrig1"></td>
			<?}?>
			<td class="brbot1 abuts"><div style="width:70px;">
				<a href="<?=$FORM_ACTION?>template.edit.php?ID=<?=$arITemp['ID']?>" class="popup"><img src="<?=CORE_ROOT_DIR?>/media/images/edit.gif" width="16" height="16" title="<?=TMes('Edit')?>"></a>
				<a href="javascript:void(0);" onclick="if(confirm('<?=TMes('Really delete?')?>')) window.location='<?=$FORM_ACTION?>?del=<?=$arITemp['ID']?>';" ><img src="<?=CORE_ROOT_DIR?>/media/images/trash.gif" width="16" height="16" title="<?=TMes('Delete')?>"></a>
				<?=$arITemp['DEFAULT']?></div>
			</td>
			</tr>
		<?}?>
	<?}?>
	<?if($AJAX!="Y"){?>
		<?if($ITNum<=0){?><tr><td colspan="99"><center><?=TMes('No templates - create')?></center></td></tr><?}?>
		</table>
		<br>
		
		<div class="roundiv tfrig setsdiv" style="margin-left:20px;">
			<div class="htit">4. <?=TMes('Settings')?></div>
			<?=TMes('Import lines from')?> <input type="text" name="minrow" id="minrow" class="afield minif70" maxlength="8" value="0"> <?=TMes('to')?> <input type="text" name="maxrow" id="maxrow" value="500000" class="afield minif70" maxlength="8" ><br>
			<hr class="marbot18">
			<div class="upline"><?=TMes('Parsing mode')?></div>
			<label class="inptype_radio" ><input type="radio" name="import_type" value="1" OnChange="SwUploader(1)" checked> <?=TMes('Test parsing do not load data into DB')?></label><br>
			<label class="inptype_radio" ><input type="radio" name="import_type" value="2" OnChange="SwUploader(2)"> <?=TMes('Import in one step')?></label><br>
			<label class="inptype_radio" ><input type="radio" name="import_type" value="3" OnChange="SwUploader(3)"> <?=TMes('One step and show result table')?></label><br>
			<label class="inptype_radio" ><input type="radio" name="import_type" value="4" id="ajax_step" OnChange="SwUploader(4)"> <?=TMes('Stepwise AJAX import')?></label><br>
			<div id="axonestep" class="onestep_sett"><?=TMes('one step')?>: <input type="text" name="onestep" id="onestep" class="afield minif" maxlength="5" value="10000"> <?=TMes('records')?></div>
			<hr class="marbot18">
			<div class="upline"><?=TMes('Grouping')?></div>
			<input type="text" name="price_code" id="price_code" class="afield minif2" maxlength="12" value="<?=$_REQUEST['price_code']?>"> <?=TMes('Group key')?>  <span class="grey_color"><?=TMes('of imported records')?></span><br>
			<script>SetICode('<?=$DefICode?>');</script>
			<input type="checkbox" name="code_clear" value="Y" class="chmarg"> <?=TMes('Delete old records with this key')?>
			<hr class="marbot18">
			<div class="upline"><?=TMes('Prices')?> / <?=TMes('Currency')?></div>
			<input type="checkbox" name="min_prices" value="Y" class="" OnChange="$('#minsupf').toggle('normal');"> <?=TMes('Import only the lowest price')?>:<br>
			<div id="minsupf" class="onestep_sett">
				<label class="inptype_radio" ><input type="radio" name="min_supfilter" value="0" checked> <?=TMes('regardless of the supplier')?></label><br>
				<label class="inptype_radio" ><input type="radio" name="min_supfilter" value="1"> <?=TMes('from each supplier')?></label><br>
				<label class="inptype_radio" ><input type="radio" name="min_supfilter" value="2"> <?=TMes('among each supplier stock')?></label><br>
			</div>
			<input type="checkbox" name="currency_convert" value="Y" class="" OnChange="$('#cursupf').toggle('normal');"> <?=TMes('Convert and save currency')?>:<br>
			<div id="cursupf" class="onestep_sett">
				<?$rsCurs = TDCurrency::GetList(Array("ID"=>"ASC"),Array());
				while($arCur = $rsCurs->GetNext()){
					if($arCur['RATE']==1){$CurChecked='checked'; $cRATE=TMes('base');}else{$CurChecked=''; $cRATE=round($arCur['RATE'],5).' ~ '.round(1/$arCur['RATE'],4);}
					?>
					<label class="inptype_radio" title="<?=TMes('Forward and reverse rate')?>">
						<input type="radio" name="tocur" value="<?=$arCur['CODE']?>" <?=$CurChecked?> > <b><?=$arCur['CODE']?></b>: <?=$cRATE?>
					</label><br>
				<?}?>
			</div>
		</div>
		
		<script>
			function FormProcess(){
				var templ = $('input[name=template]:checked', '#iform').val();
				var csvfl = $('input[name=csvfile]:checked', '#iform').val();
				if(!templ && $("#ajax_step").prop("checked")){alert('<?=TMes('Required import template')?>');}
				else if(!csvfl && $("#ajax_step").prop("checked")){alert('<?=TMes('You must select or upload a CSV file')?>');}
				else{
					if( $("#ajax_step").prop("checked") ){
						var ajlink = '<?=$FORM_ACTION?>index.php?ajax_import=Y&checkme=Y'; /*$("#ajax_popup").attr("href");*/
						var minrow = $('#minrow').val();
						var maxrow = $('#maxrow').val();
						var onestep = $('#onestep').val();
						ajlink = ajlink+'&template='+templ+'&csvfile='+csvfl+'&minrow='+minrow+'&maxrow='+maxrow+'&onestep='+onestep;
						$.colorbox({href:ajlink, rel:false, current:'', preloading:false, arrowKey:false, scrolling:false, overlayClose:false});
					}else{
						$("#iform").submit();
					}
				}
			}
			var CurITypeNum = 1;
			function SwUploader(num){
				if((num==4 && CurITypeNum!=4) || (num!=4 && CurITypeNum==4)){ $('#axonestep').toggle('normal');}
				CurITypeNum=num;
			}
		</script>
	<?}?>
	
	<?if($AJAX!="Y"){?>
		<div class="htit">2. <?=TMes('Exgroups')?></div>
		<table class="corp_table smpads imptable" width="340px"><tr>
			<td class="head"></td>
			<td class="head" title="<?=TMes('Any text')?>" width="40%"><?=TMes('Name')?></td>
			<td class="head" title="<?=TMes('Price range for extra charge')?>"><?=TMes('Range')?></td>
			<td class="head" title="<?=TMes('Percentage margin')?>"><img src="<?=CORE_ROOT_DIR?>/media/images/money-plus.png" width="16" height="16" alt=""></td>
			<td class="head" title="<?=TMes('A fixed margin')?>"><img src="<?=CORE_ROOT_DIR?>/media/images/fixed_price.png" width="16" height="16" alt=""></td>
			<td class="head"><?=TMes('Actions')?></td>
		</tr>
	<?}?>
		<?
		$rsExGroups = ImportExGroups::GetList(Array("ID"=>"ASC"),Array());
		while($arExGroup = $rsExGroups->Fetch()){
			$CurRange=0;
			//Удалить
			if($_REQUEST['exdel']==$arExGroup['ID']){ ImportExGroups::Delete($arExGroup['ID']); continue; }
			//Сделать по умолчанию
			if($_REQUEST['exdefault']>0){
				if($_REQUEST['exdefault']==$arExGroup['ID']){$D=$_REQUEST['do'];}else{$D=0;}
				ImportExGroups::Update($arExGroup['ID'],Array("IDEF"=>$D));
				$arExGroup['IDEF']=$D;
			}
			//Значения
			if($arExGroup['IDEF']==1){
				$arExGroup['DEF_IMG']='star.png'; $arExGroup['DEF_ACTION']=0; $arExGroup['DEF_DESC']=TMes('The default is on');
			}else{
				$arExGroup['DEF_IMG']='default.png'; $arExGroup['DEF_ACTION']=1; $arExGroup['DEF_DESC']=TMes('Set by default');
			}
			$arExGRanges = explode('/',$arExGroup['RENGE']); $arExGExtra = explode('/',$arExGroup['EXTRA']); $arExGFixed = explode('/',$arExGroup['FIXED']);
			foreach($arExGRanges as $Key=>$range){
				if($range>0){
					if($_POST['exgroup']==$arExGroup['ID']){
						$DoExG = "Y";
						$arExG[] = Array("FROM"=>$CurRange,"TO"=>$range,"EXTRA"=>$arExGExtra[$Key],"FIXED"=>$arExGFixed[$Key]);
					}
					$arExGroup['STR_RANGE'] .= $CurRange.'-'.$range.'<br>';
					$CurRange=$range;
				}
			}
			foreach($arExGExtra as $extra){if($extra>0){$arExGroup['STR_EXTRA'] .=$extra.'<span class="grays amini">%</span><br>';}else{$arExGroup['STR_EXTRA'].='';}}
			foreach($arExGFixed as $fixed){if($fixed>0){$arExGroup['STR_FIXED'].='<span class="grays amini">+</span>'.$fixed.'<br>';}else{$arExGroup['STR_FIXED'].='';}}
			//Выбрать для ипорта;
			if($_REQUEST['exgroup']==$arExGroup['ID']){$arCIExGroup=$arExGroup;}
			$IENum++;
			?>
			<?if($AJAX!="Y"){?>
				<tr>
				<td class="brbot1"><input type="radio" name="exgroup" value="<?=$arExGroup['ID']?>" id="exg<?=$arExGroup['ID']?>" <?if($arExGroup['IDEF']==1){echo 'checked';}?> ></td>
				<td class="brbot1 brrig1"><label for="exg<?=$arExGroup['ID']?>" class="tem_label"><?=$arExGroup['NAME']?></label></td>
				<td class="brbot1 brrig1 tarig"><?=$arExGroup['STR_RANGE']?></td>
				<td class="brbot1 brrig1"><?=$arExGroup['STR_EXTRA']?></td>
				<td class="brbot1 brrig1"><?=$arExGroup['STR_FIXED']?></td>
				<td class="brbot1 abuts"><div style="width:70px;">
					<a href="<?=$FORM_ACTION?>exgroup.edit.php?ID=<?=$arExGroup['ID']?>" class="popup"><img src="<?=CORE_ROOT_DIR?>/media/images/edit.gif" width="16" height="16" title="<?=TMes('Edit')?>"></a>
					<a href="javascript:void(0);" onclick="if(confirm('<?=TMes('Really delete?')?>')) window.location='<?=$FORM_ACTION?>?exdel=<?=$arExGroup['ID']?>';" ><img src="<?=CORE_ROOT_DIR?>/media/images/trash.gif" width="16" height="16" title="<?=TMes('Delete')?>"></a>
					<a href="<?=$FORM_ACTION?>?exdefault=<?=$arExGroup['ID']?>&do=<?=$arExGroup['DEF_ACTION']?>" style="margin-right:0px!important;"><img src="<?=CORE_ROOT_DIR?>/media/images/<?=$arExGroup['DEF_IMG']?>" width="16" height="16" title="<?=$arExGroup['DEF_DESC']?>"></a>
					</div>
				</td>
				</tr>
			<?}?>
		<?}?>
		<?if($AJAX!="Y"){?>
			</table>
			<a class="popup mblink" href="<?=$FORM_ACTION?>exgroup.edit.php?add=new" title=""><?=TMes('Add exgroup')?></a>
			<br><br>
		<?}?>
		
		
	<?/*if($AJAX!="Y"){?>
		<div class="htit">3. Правила замены</div>
		<table class="corp_table smpads imptable" width="340px"><tr>
			<td class="head"></td>
			<td class="head" title="<?=TMes('Any text')?>" width="40%"><?=TMes('Name')?></td>
			<td class="head" title="<?=TMes('Price range for extra charge')?>">Правила</td>
			<td class="head"><?=TMes('Actions')?></td>
		</tr>
	<?}?>
		<?
		$rsExGroups = ImportExGroups::GetList(Array("ID"=>"ASC"),Array());
		while($arExGroup = $rsExGroups->Fetch()){
			$CurRange=0;
			//Удалить
			if($_REQUEST['exdel']==$arExGroup['ID']){ ImportExGroups::Delete($arExGroup['ID']); continue; }
			//Сделать по умолчанию
			if($_REQUEST['exdefault']>0){
				if($_REQUEST['exdefault']==$arExGroup['ID']){$D=$_REQUEST['do'];}else{$D=0;}
				ImportExGroups::Update($arExGroup['ID'],Array("IDEF"=>$D));
				$arExGroup['IDEF']=$D;
			}
			//Значения
			if($arExGroup['IDEF']==1){
				$arExGroup['DEF_IMG']='star.png'; $arExGroup['DEF_ACTION']=0; $arExGroup['DEF_DESC']=TMes('The default is on');
			}else{
				$arExGroup['DEF_IMG']='default.png'; $arExGroup['DEF_ACTION']=1; $arExGroup['DEF_DESC']=TMes('Set by default');
			}
			$arExGRanges = explode('/',$arExGroup['RENGE']); $arExGExtra = explode('/',$arExGroup['EXTRA']); $arExGFixed = explode('/',$arExGroup['FIXED']);
			foreach($arExGRanges as $Key=>$range){
				if($range>0){
					if($_POST['exgroup']==$arExGroup['ID']){
						$DoExG = "Y";
						$arExG[] = Array("FROM"=>$CurRange,"TO"=>$range,"EXTRA"=>$arExGExtra[$Key],"FIXED"=>$arExGFixed[$Key]);
					}
					$arExGroup['STR_RANGE'] .= $CurRange.'-'.$range.'<br>';
					$CurRange=$range;
				}
			}
			foreach($arExGExtra as $extra){if($extra>0){$arExGroup['STR_EXTRA'] .=$extra.'<span class="grays amini">%</span><br>';}else{$arExGroup['STR_EXTRA'].='';}}
			foreach($arExGFixed as $fixed){if($fixed>0){$arExGroup['STR_FIXED'].='<span class="grays amini">+</span>'.$fixed.'<br>';}else{$arExGroup['STR_FIXED'].='';}}
			//Выбрать для ипорта;
			if($_REQUEST['exgroup']==$arExGroup['ID']){$arCIExGroup=$arExGroup;}
			$IENum++;
			?>
			<?if($AJAX!="Y"){?>
				<tr>
				<td class="brbot1"><input type="radio" name="exgroup" value="<?=$arExGroup['ID']?>" id="exg<?=$arExGroup['ID']?>" <?if($arExGroup['IDEF']==1){echo 'checked';}?> ></td>
				<td class="brbot1 brrig1"><label for="exg<?=$arExGroup['ID']?>" class="tem_label"><?=$arExGroup['NAME']?></label></td>
				<td class="brbot1 brrig1 tarig"><?=$arExGroup['STR_RANGE']?></td>
				<td class="brbot1 abuts"><div style="width:70px;">
					<a href="<?=$FORM_ACTION?>exgroup.edit.php?ID=<?=$arExGroup['ID']?>" class="popup"><img src="<?=CORE_ROOT_DIR?>/media/images/edit.gif" width="16" height="16" title="<?=TMes('Edit')?>"></a>
					<a href="javascript:void(0);" onclick="if(confirm('<?=TMes('Really delete?')?>')) window.location='<?=$FORM_ACTION?>?exdel=<?=$arExGroup['ID']?>';" ><img src="<?=CORE_ROOT_DIR?>/media/images/trash.gif" width="16" height="16" title="<?=TMes('Delete')?>"></a>
					<a href="<?=$FORM_ACTION?>?exdefault=<?=$arExGroup['ID']?>&do=<?=$arExGroup['DEF_ACTION']?>" style="margin-right:0px!important;"><img src="<?=CORE_ROOT_DIR?>/media/images/<?=$arExGroup['DEF_IMG']?>" width="16" height="16" title="<?=$arExGroup['DEF_DESC']?>"></a>
					</div>
				</td>
				</tr>
			<?}?>
		<?}?>
		<?if($AJAX!="Y"){?>
			</table>
			<a class="popup mblink" href="<?=$FORM_ACTION?>exgroup.edit.php?add=new" title="">Добавить правило</a>
			<br><br>
		<?}*/?>
		
	<?if($AJAX!="Y"){?>
		<div class="htit">3. <?=TMes('File')?></div>
		<div class="roundiv tflef">
	<?}?>
	<?
	if($_REQUEST['checkme']=="Y"){
		if($_FILES['getfile']["size"]>0){
			if(is_uploaded_file($_FILES["getfile"]["tmp_name"]) ){
				if(preg_match('/\.(csv)/',$_FILES["getfile"]["name"]) OR preg_match('/\.(txt)/',$_FILES["getfile"]["name"]) OR preg_match('/\.(xml)/',$_FILES["getfile"]["name"]) ){
					if(!move_uploaded_file($_FILES["getfile"]["tmp_name"], $_SERVER["DOCUMENT_ROOT"].$FORM_ACTION.'files/'.$_FILES["getfile"]["name"])){ //Необходимы права на папку
						$ERROR .= TMes('Error').' '.TMes('No file was uploaded').':<br> Permission denied ("'.$_SERVER["DOCUMENT_ROOT"].$FORM_ACTION.'files/")<br>';
					}
				}else{
					$ERROR .= TMes('Error').' '.TMes('Uploaded file should be in format').': csv, txt<br>';
				}
			}else{
				$ERROR .= TMes('Error').' '.TMes('No file was uploaded').'<br>';
			}
			$_REQUEST['checkme']="N";
		}
	}
	$dir = opendir("files");
	while($FileName = readdir($dir)){
		if(preg_match('/\.(csv)/',$FileName) OR preg_match('/\.(txt)/',$FileName) OR preg_match('/\.(xml)/',$FileName)){
			$Fnum++;
			//Удалить
			if($_REQUEST['csvdel']==$Fnum){
				unlink($_SERVER["DOCUMENT_ROOT"].$FORM_ACTION.'files/'.$FileName);
				$Fnum--; $_REQUEST['csvdel']=0;
				continue;
			}
			$FSize = GetFileSize($_SERVER["DOCUMENT_ROOT"].$FORM_ACTION.'files/'.$FileName);
			//Выбрать для ипорта
			if($_REQUEST['csvfile']==$Fnum){
				if(!$FHandle = fopen($_SERVER["DOCUMENT_ROOT"].$FORM_ACTION.'files/'.$FileName, "r")){
					$ERROR .= TMes('Error').' '.TMes('File').' "'.$FileName.'" '.TMes('not readable').'<br>';
				}else{
					$ImpFilePath = $_SERVER["DOCUMENT_ROOT"].$FORM_ACTION.'files/'.$FileName; 
					$FileExt = end(explode(".", $ImpFilePath));
					fclose($FHandle);
				}
			}
			?>
			<?if($AJAX!="Y"){?>
				<input type="radio" name="csvfile" id="file<?=$Fnum?>" value="<?=$Fnum?>">
				<label for="file<?=$Fnum?>" class="file_label"><?=$FileName?></label> &nbsp;&nbsp;<span class="grey_color"><?=$FSize?></span>&nbsp;&nbsp;
				<a href="<?=$FORM_ACTION?>files/<?=$FileName?>" target="_blank" title="<?=TMes('Download')?>"><img src="<?=CORE_ROOT_DIR?>/media/images/download.png" width="16" height="16"></a> &nbsp;&nbsp;
				<a href="javascript:void(0);" onclick="if(confirm('<?=TMes('Really delete?')?>')) window.location='<?=$FORM_ACTION?>?csvdel=<?=$Fnum?>';"><img src="<?=CORE_ROOT_DIR?>/media/images/trash.gif" width="16" height="16" title="<?=TMes('Delete')?>"></a>
				<br/>
			<?}
		}
	}
	if($AJAX!="Y" AND $Fnum<=0){echo TMes('No CSV files for import').'...';}
	
	if($AJAX!="Y"){?>
			<div class="downfile">
				<?=TMes('Upload file')?> (csv, txt, xml):<br> <input type="file" name="getfile" id="getfile">
				<script> $("#getfile").change(function(){ $("#iform").submit(); }); </script>
			</div>
			<div class="cler"></div>
			<div class="grey_color" style="line-height:14px; font-size:11px;">
				<?=TMes('Limits of your server for file uploads')?>:<br>
				upload_max_filesize: <?=(int)(ini_get('upload_max_filesize'))?> Mb<br>
				post_max_size: <?=(int)(ini_get('post_max_size'))?> Mb<br>
				memory_limit: <?=(int)(ini_get('memory_limit'))?> Mb<br>
			</div>
		</div>
	<?}?>
	
	
	<?if($AJAX!="Y"){?>
		
		</form>
		
		<div class="cler"></div>
		<br>
		<input type="button" value="<?=TMes('Start import')?>" class="abutton tfrig" OnClick="FormProcess()"/> 
		
		<br>
		<br><br>
	
		<a name="form"></a>
	
	<?}?>
	<?
	//ИМПОРТ
	if($_REQUEST['checkme']=="Y"){
		if(!$arCITemp['ID']>0){$ERROR .= TMes('Required import template').'<br>';}
		if($ImpFilePath==""){$ERROR .= TMes('You must select or upload import file').'<br>';}
		$rsPRows = ImportColumns::GetList(Array("CSV_NUM"=>"ASC"),Array("TEMPL_ID"=>$arCITemp['ID']));
		while($arPRow = $rsPRows->Fetch()){
			$arFtoN[$arPRow['TEMPL_FIELD']] = ($arPRow['CSV_NUM']-1);
		}
		if(count($arFtoN)<=0){$ERROR .= TMes('Error').' '.TMes('No fields in the import template specified for CSV linking').'<br>';}
		if(strlen($ERROR)==0){
			
			if($_POST['import_type']==1){$SHOWTAB="Y"; $TEST="Y";}
			if($_POST['import_type']==3){$SHOWTAB="Y";}
						
			if($SHOWTAB=="Y" OR $TEST=="Y"){
				echo '<hr class="marbot18 cler"><div class="htit">5. '.TMes('Import result').'</div>';
				echo '<table id="Importtab"><tr>';
				if($arCITemp['ITABLE']=='PRICES'){
					echo '<td class="Top">#</td>';
					echo '<td class="Top">'.TMes('Part number').'</td>';
					echo '<td class="Top">'.TMes('Manufacturer').'</td>';
					echo '<td class="Top">'.TMes('Price').'</td>';
					echo '<td class="Top">'.TMes('Currency').'</td>';
					echo '<td class="Top">'.TMes('Days').'</td>';
					echo '<td class="Top">'.TMes('Avail').'</td>';
					echo '<td class="Top">'.TMes('Supplier').'</td>';
					echo '<td class="Top">'.TMes('Stock').'</td>';
					echo '<td class="Top">'.TMes('Name').'</td>';
					//echo '<td class="Top">'.TMes('Serach keywords').'</td>';
					echo '<td class="Top">'.TMes('Group key').'</td>';
					echo '</tr>';
				}elseif($arCITemp['ITABLE']=='LINKS'){
					echo '<td class="Top">#</td>';
					echo '<td class="Top">'.TMes('Cross numbers').'</td>';
					echo '<td class="Top">'.TMes('Cross brand').'</td>';
					echo '<td class="Top">'.TMes('Original numbers').'</td>';
					echo '<td class="Top">'.TMes('Original brand').'</td>';
					echo '<td class="Top">'.TMes('Group key').'</td>';
					echo '</tr>';
				}
			}
			
			//Разделитель
			if($arCITemp['SEP']=='[tab]'){$arCITemp['SEP']='	';}
			if($arCITemp['SEP']=='[space]'){$arCITemp['SEP']=' ';}
			
			//XML parsing
			if($FileExt=="xml"){
				$arCITemp['ENCODE']='UTF-8';
				$arCITemp['SEP']='^^';
				global $arCFile; global $XMLElem; global $arXMLLine; global $XMLData;
				function startXMLElement($parser, $name, $attrs) {
					global $XMLElem;
					if($name=="ITEM"){$XMLElem="NEW";}
				}
				function characterXMLData($parser, $data) {
					global $XMLElem; global $XMLData;
					if($XMLElem=="GO"){ $XMLData .= $data;}
					if($XMLElem=="NEW"){$XMLElem="GO";}
				}
				function endXMLElement($parser, $name) {
					global $arCFile; global $XMLElem; global $arXMLLine; global $XMLData;
					if($name=="ITEM"){
						$arCFile[] = implode('^^',$arXMLLine);
						$arXMLLine=Array();
						$XMLElem="N";
					}elseif($XMLElem=="GO"){
						$arXMLLine[] = $XMLData; $XMLData='';
						$XMLElem="NEW";
					}
				}
				$FHandle = fopen($ImpFilePath, "r");
				$FContent = fread($FHandle, filesize($ImpFilePath));
				fclose($FHandle);
				$XMLparser = xml_parser_create();
				xml_set_element_handler($XMLparser, 'startXMLElement', 'endXMLElement');
				xml_set_character_data_handler($XMLparser, "characterXMLData");
				if(!xml_parse($XMLparser, $FContent)){die('Error! XML parsing');}
				xml_parser_free($XMLparser);
				//echo '<pre>';print_r($arCFile);echo '</pre>';die();
			//CSV or TXT parsing
			}else{
				$arCFile = file($ImpFilePath);
			}
			
			$CSVcount = count($arCFile);
			if($AJAX=="Y"){
				$_REQUEST['rowsdone'] = intval($_REQUEST['rowsdone']);
				$axPERCENT = intval($_REQUEST['rowsdone'] / ($CSVcount/100));?>
				<link rel="stylesheet" href="<?=$FORM_ACTION?>styles.css" type="text/css">
				<style>
					#cboxLoadingOverlay{background:none!important;}
					#cboxLoadingGraphic{background:none!important;}
				</style>
				<br><center><h1 class="hd1"><?=TMes('Processed records')?> <?=$_REQUEST['rowsdone']?> <?=TMes('of')?> <?=$CSVcount?></h1></center>
				<div class="axloader_block">
					<div class="axloader_bar" style="width:<?=$axPERCENT?>%;"></div>
				</div>
			<?}
			
			$Wrong=0; $num=0; $step=0; $RCnt=0;
			$MinRows = intval($_REQUEST['minrow']);
			$MaxRows = intval($_REQUEST['maxrow']);
			if(!$MaxRows>0){$MaxRows=1000000;}
						
			set_time_limit(600);
			$ImportedCount=0;
			$CurTimeStamp =  mktime(0, 0, 0, date("n"), date("j"), date("Y")); //time();
			$ImportCode = substr($_REQUEST['price_code'],0,12);
			if(trim($ImportCode)==''){$ImportCode=$arCITemp['DEF_ICODE'];}
			if($TCore->arSettings['MAIN']['PRICES_ART_TYPE']=="SHORT"){$ART_TYPE="SHORT";}else{$ART_TYPE="FULL";}
			
			//Удалить старые записи с этим ключем
			if($TEST!="Y" AND $_POST['code_clear']=="Y"){
				$DoCodeClear="Y";
				mysql_query('DELETE FROM '.$arCITemp['ITABLE'].' WHERE IMPORT_CODE = "'.$ImportCode.'" ');
				$MSDeletedRows = mysql_affected_rows();
			}
			
			foreach($arCFile as $strLine){
				$num++;
				if($num>=$MinRows){
					$RCnt++;
					
					if($AJAX=="Y" AND ($RCnt>=IMPORT_AJAX_STEP OR $num==$CSVcount) ){?>
						<form name="nform" id="nform" action="<?=$FORM_ACTION?>index.php" method="post" >		
							<input type="hidden" name="newajax" value="Y"/>
							<input type="hidden" name="checkme" value="Y"/>
							<input type="hidden" name="ajax_import" value="Y"/>
							<input type="hidden" name="template" value="<?=$_REQUEST['template']?>"/>
							<input type="hidden" name="csvfile" value="<?=$_REQUEST['csvfile']?>"/>
							<input type="hidden" name="minrow" value="<?=($_REQUEST['minrow']+IMPORT_AJAX_STEP)?>"/>
							<input type="hidden" name="maxrow" value="<?=$_REQUEST['maxrow']?>"/>
							<input type="hidden" name="onestep" value="<?=IMPORT_AJAX_STEP?>"/>
							<input type="hidden" name="rowsdone" value="<?=$_REQUEST['rowsdone']+$RCnt?>"/>
							<input type="hidden" name="min_prices" value="<?=$_REQUEST['min_prices']?>"/>
							<input type="hidden" name="min_supfilter" value="<?=$_REQUEST['min_supfilter']?>"/>
							<input type="hidden" name="tocur" value="<?=$_REQUEST['tocur']?>"/>
						</form>
						<script>
							$.post($("#nform").attr("action"), $("#nform").serialize(), function(data){
								$.colorbox({html:data});
							},'html');
						</script>
						<?
						die();
					}
					
					$DO = "Y";
					
					// Проверить валидность колонок csv
					$arCSVrow = explode($arCITemp['SEP'], $strLine);
					
					if($DO == "Y"){
						$arRes=Array();
						
						////////////////////////////////////////////		
						//Цены
						if($arCITemp['ITABLE']=='PRICES'){
							
							$DAY = $arCSVrow[$arFtoN['DAY']];
							if($DAY<=0){$DAY=$arCITemp['DEF_DAY']; $arCSVrow['DEF_DAY_INC']="Y"; $arDefDays++;}
							$AVAILABLE = $arCSVrow[$arFtoN['AVAILABLE']];
							if(trim($AVAILABLE)==''){$AVAILABLE=$arCITemp['DEF_AVAIL']; $arCSVrow['DEF_AVAILABLE_INC']="Y"; $arDefAvail++;}
							$SUPPLIER = $arCSVrow[$arFtoN['SUPPLIER']];
							if(trim($SUPPLIER)==''){$SUPPLIER=$arCITemp['DEF_SUPL']; $arCSVrow['DEF_SUPPLIER_INC']="Y"; $arDefSup++;}
							$STOCK = $arCSVrow[$arFtoN['STOCK']];
							if(trim($STOCK)==''){$STOCK=$arCITemp['DEF_STOCK']; $arCSVrow['DEF_STOCK_INC']="Y"; $arDefStock++;}
							$PART_NAME = $arCSVrow[$arFtoN['PART_NAME']];
							$SUP_BRAND = StrToUp($arCSVrow[$arFtoN['SUP_BRAND']]);
							$CURRENCY = StrToUp($arCSVrow[$arFtoN['CURRENCY']]);
							$SEARCH_KEYWORDS = trim($arCSVrow[$arFtoN['SEARCH_KEYWORDS']]);
							$ART_NUM = StrToUp($arCSVrow[$arFtoN['ART_NUM']]);
							if($arCITemp['ENCODE'] != 'UTF-8'){ //Modify to view in top...
								$PART_NAME = iconv($arCITemp['ENCODE'], "UTF-8//TRANSLIT", $PART_NAME);
								//$SUP_BRAND = iconv($arCITemp['ENCODE'], "UTF-8//TRANSLIT", $SUP_BRAND);
								//$CURRENCY = iconv($arCITemp['ENCODE'], "UTF-8//TRANSLIT", $CURRENCY);
								//$ART_NUM = iconv($arCITemp['ENCODE'], "UTF-8//TRANSLIT", $ART_NUM);
								if($arCSVrow['DEF_SUPPLIER_INC']!="Y"){$SUPPLIER = iconv($arCITemp['ENCODE'], "UTF-8//TRANSLIT", $SUPPLIER);}
								if($arCSVrow['DEF_STOCK_INC']!="Y"){$STOCK = iconv($arCITemp['ENCODE'], "UTF-8//TRANSLIT", $STOCK);}
								if($SEARCH_KEYWORDS!=''){$SEARCH_KEYWORDS = iconv($arCITemp['ENCODE'], "UTF-8//TRANSLIT", $SEARCH_KEYWORDS);}
							}
							$SEARCH_KEYWORDS = StrToLow($SEARCH_KEYWORDS);
							$ART_NUM = ArtToNumber($ART_NUM,$ART_TYPE);
							$ART_NUM = StrToUp($ART_NUM);
							if(trim($SUP_BRAND)==''){$SUP_BRAND=$arCITemp['DEF_BRA']; $arCSVrow['DEF_BRAND_INC']="Y"; $arDefBrand++;}
							if(trim($CURRENCY)==''){$CURRENCY=$arCITemp['DEF_CUR']; $arDefCurr++;}
							$PRICE = preg_replace('/[^0-9,.]/', '', $arCSVrow[$arFtoN['PRICE']]);
							$PRICE = preg_replace('/[,]/', '.', $PRICE);
							$PRICE = floatval($PRICE);
							if($arCITemp['EXTRA']!=0){$PRICE = $PRICE+(($PRICE/100)*intval($arCITemp['EXTRA']));}
							//ExGroup
							if($DoExG=="Y"){
								foreach($arExG as $arER){
									if($PRICE>$arER["FROM"] AND $PRICE<=$arER["TO"]){
										$PRICE = round($PRICE+(($PRICE/100)*$arER["EXTRA"])+$arER["FIXED"],2);
										break;
									}
								}
							}
							if($arCITemp['PRICE_CONVERT']==1){$PRICE=floor($PRICE);}
							elseif($arCITemp['PRICE_CONVERT']==2){$PRICE=round($PRICE,0);}
							elseif($arCITemp['PRICE_CONVERT']==3){$PRICE=ceil($PRICE);}
							if($_REQUEST['tocur']!='' AND $CURRENCY!=''){$PRICE = TDCurrency::Convert($CURRENCY,$_REQUEST['tocur'],$PRICE); $CURRENCY=$_REQUEST['tocur'];}
							$PRICE = preg_replace('/[,]/', '.', $PRICE);
							//Проверка
							if($ART_NUM!='' AND $SUP_BRAND!='' AND $PRICE>0 ){
								$arFields = Array(
									"ART_NUM" => trim($ART_NUM),    
									"SUP_BRAND" => trim($SUP_BRAND),    
									"PART_NAME" => trim($PART_NAME),									
									"PRICE" => trim($PRICE),  
									"CURRENCY" => trim($CURRENCY),									
									"DAY" => trim($DAY),
									"AVAILABLE" => trim($AVAILABLE),
									"SUPPLIER" => trim($SUPPLIER),
									"STOCK" => trim($STOCK),
									"SEARCH_KEYWORDS" => trim($SEARCH_KEYWORDS),
									"IMPORT_CODE" => $ImportCode,
									"IMPORT_DATE" => $CurTimeStamp
								);
								if($TEST!="Y"){
									$arRes = ImportCSV($arFields,$num,$arRes,$arCITemp['ITABLE']);
									$ImportedCount=$ImportedCount+$arRes['OK'];
								}else{$arRes['CLASS']="";}
								//Вывод
								if($SHOWTAB=="Y" OR $TEST=="Y"){
									echo '<tr class="'.$arRes['CLASS'].'">';
									echo '<td>'.$num.'.</td>';
									echo '<td>'.$ART_NUM.'</td>'; 
									echo '<td>'.$SUP_BRAND.'</td>';
									echo '<td>'.$PRICE.'</td>';
									echo '<td>'.$CURRENCY.'</td>';
									echo '<td>'.$DAY.'</td>'; 
									echo '<td>'.$AVAILABLE.'</td>'; 
									echo '<td>'.$SUPPLIER.'</td>'; 
									echo '<td>'.$STOCK.'</td>'; 
									echo '<td>'.$PART_NAME.'</td>';
									//echo '<td>'.$SEARCH_KEYWORDS.'</td>';
									echo '<td>'.$ImportCode.'</td>';
									echo '</tr>';
								}
							}else{
								$arWrongReq[$num] = $num;
								$arRes['CLASS'] = 'RowNoReq';
								//Вывод ошибочных
								if($SHOWTAB=="Y" OR $TEST=="Y"){
									$WTabStr .= '<tr class="'.$arRes['CLASS'].'"><td>'.$num.'.</td><td>'.$ART_NUM.'</td><td>'.$SUP_BRAND.'</td><td>'.$PRICE.'</td><td>'.$CURRENCY.'</td><td>'.$DAY.'</td><td>'.$AVAILABLE.'</td><td>'.$SUPPLIER.'</td><td>'.$STOCK.'</td><td>'.$PART_NAME.'</td><td>'.$ImportCode.'</td></tr>';
								}
							}
						
						
						////////////////////////////////////////////				
						//Кроссы
						}elseif($arCITemp['ITABLE']=='LINKS'){
							
							$CROSS_NUMS = trim($arCSVrow[$arFtoN['CROSS_NUMS']]);
							if(trim($arCITemp['SEP_ART'])!=''){
								$arNewCross = Array();
								$arArtNums = explode($arCITemp['SEP_ART'],$CROSS_NUMS);
								foreach($arArtNums as $Value){$arNewCross[] = ArtToNumber($Value,$ART_TYPE);}
								$CROSS_NUMS = implode(';',$arNewCross);
							}else{
								$CROSS_NUMS = ArtToNumber($CROSS_NUMS,$ART_TYPE);
							}
							$ORIGINAL_NUMS = trim($arCSVrow[$arFtoN['ORIGINAL_NUMS']]);
							if(trim($arCITemp['SEP_ART'])!=''){
								$arNewCross = Array();
								$arArtNums = explode($arCITemp['SEP_ART'],$ORIGINAL_NUMS);
								foreach($arArtNums as $Value){$arNewCross[] = ArtToNumber($Value,$ART_TYPE);}
								$ORIGINAL_NUMS = implode(';',$arNewCross);
							}else{
								$ORIGINAL_NUMS = ArtToNumber($ORIGINAL_NUMS,$ART_TYPE);
							}
							$CROSS_BRAND = StrToUp($arCSVrow[$arFtoN['CROSS_BRAND']]); // UTF8 function
							$ORIGINAL_BRAND = StrToUp($arCSVrow[$arFtoN['ORIGINAL_BRAND']]); // UTF8 function
							//Проверка
							if($CROSS_NUMS!='' AND $ORIGINAL_NUMS!=''){
								$arFields = Array(
									"CROSS_NUMS" => $CROSS_NUMS, 
									"CROSS_BRAND" => $CROSS_BRAND,     
									"ORIGINAL_NUMS" => $ORIGINAL_NUMS,       
									"ORIGINAL_BRAND" =>$ORIGINAL_BRAND,
									"IMPORT_CODE" => $ImportCode,
								);
								if($TEST!="Y"){
									$arRes = ImportCSV($arFields,$num,$arRes,$arCITemp['ITABLE']);
									$ImportedCount=$ImportedCount+$arRes['OK'];
								}else{$arRes['CLASS']="";}
								//Вывод
								if($SHOWTAB=="Y" OR $TEST=="Y"){
									echo '<tr class="'.$arRes['CLASS'].'">';
									echo '<td>'.$num.'.</td>';
									echo '<td>'.$CROSS_NUMS.'</td>'; 
									echo '<td>'.trim($CROSS_BRAND).'</td>';
									echo '<td>'.$ORIGINAL_NUMS.'</td>';
									echo '<td>'.trim($ORIGINAL_BRAND).'</td>';
									echo '<td>'.$ImportCode.'</td>';
									echo '</tr>';
								}
							}else{
								$arWrongReq[$num] = $num;
								$arRes['CLASS'] = 'RowNoReq';
								//Вывод ошибочных
								if($SHOWTAB=="Y" OR $TEST=="Y"){
									$WTabStr .= '<tr class="'.$arRes['CLASS'].'"><td>'.$num.'.</td><td>'.$CROSS_NUMS.'</td><td>'.$CROSS_BRAND.'</td><td>'.$ORIGINAL_NUMS.'</td><td>'.$ORIGINAL_BRAND.'</td><td>'.$ImportCode.'</td></tr>';
								}
							}
						}
										
		
						if($num>$MaxRows){break;}
					}
				}
			}
			if($SHOWTAB=="Y" OR $TEST=="Y"){echo $WTabStr; echo '</table>';}
			?>
			<?if($AJAX!="Y"){?>
				<br>
				<hr class="marbot18">
				<div class="htit">6. <?=TMes('Statistics')?></div>
				<div class="roundiv">
					<?=TMes('Preprocessed lines')?> <b><?=$RCnt?></b> (<?=TMes('from')?> <?=$MinRows?> <?=TMes('to')?> <?=$MaxRows?>) <?=TMes('have uploaded')?> <b><?=$ImportedCount?></b><br>
					<?=TMes('Lines do not contain the required fields')?>: <?=count($arWrongReq)?><br>
					<?if($DoCodeClear=="Y"){?>
						<?=TMes('Removed old records with a key')?> "<?=$ImportCode?>": <?=$MSDeletedRows?><br>
					<?}?>
					<?if($arCITemp['ITABLE']=='PRICES'){?>
						<?=TMes('Set as default')?>:<ul>
						<li><?=TMes('Days')?>: <?=intval($arDefDays)?></li>
						<li><?=TMes('Avail')?>: <?=intval($arDefAvail)?></li>
						<li><?=TMes('Supplier')?>: <?=intval($arDefSup)?></li>
						<li><?=TMes('Manufacturer')?>: <?=intval($arDefBrand)?></li>
						<li><?=TMes('Stock')?>: <?=intval($arDefStock)?></li>
						<li><?=TMes('Currency')?>: <?=intval($arDefCurr)?></li>
						</ul><br>
					<?}?>
					<?if($TEST=="Y"){?>
						<span class="RowNoReq">TEST PARSING</span>
					<?}?>
				</div>
				<br>
			<?
			}
		}
	}
	?>
	<?if($AJAX!="Y"){?>
		<br>
		<?if(strlen($ERROR)>0){?><div class="psys_error"><?=$ERROR?></div><?}?>
		<?if(strlen($NOTE)>0){?><div class="psys_error"><?=$NOTE?></div><?}?>
	<?}?>

<?if($AJAX!="Y"){?>
	</div>
	</div>
	<?//require_once("../../core/footer.php");?>
<?}?>