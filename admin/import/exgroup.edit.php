<?require_once("../../core/prolog.php");
if($_SESSION['CORE_IS_ADMIN']!="Y"){die();}
if(CORE_SITE_CHARSET=="utf8"){header('Content-type: text/html; charset=utf-8');}else{header('Content-type: text/html; charset='.CORE_SITE_CHARSET);}
$FORM_ACTION=CORE_ROOT_DIR.'/admin/import/';
$ExID = intval($_REQUEST['ID']);
if($ExID<=0 AND $_REQUEST['add']!="new"){echo '<div class="psys_error">'.TMes('Error').' '.TMes('Wrong ID').'</div>'; die();}


	global $TDataBase;
	$TDataBase->DBSelect("CORE");
	require_once("core.php");
	
	//Add
	if($ExID<=0 AND $_REQUEST['add']=="new" AND $_POST['checkme']=="Y" AND trim($_POST["NAME"])!=""){
		$arExGroup = ImportExGroups::Add(Array("IDEF"=>0,"NAME"=>$_POST['NAME'], "RENGE"=>$_POST["RENGE_0"], "EXTRA"=>$_POST["EXTRA_0"], "FIXED"=>$_POST["FIXED_0"]));
		if($arExGroup['ID']>0){$ExID=$arExGroup['ID']; $_POST['checkme']="N";}else{echo '<div class="psys_error">'.TMes('Error').' '.TMes('Record has not been created').'</div>';}
	}
	
	//Edit
	if($ExID>0){
		$rsExGroups = ImportExGroups::GetList(Array(),Array("ID"=>$ExID));
		if(!$arExGroup = $rsExGroups->Fetch()){echo '<div class="psys_error">'.TMes('Error').' '.TMes('Wrong ID').'</div>'; die();}
		if($_POST['NAME']==''){$_POST['NAME'] = $arExGroup['NAME'];}
		
		$arRange = explode('/',$arExGroup['RENGE']); $arExtra = explode('/',$arExGroup['EXTRA']); $arFixed = explode('/',$arExGroup['FIXED']);
		$Key=0; $arExGUpdate=Array();
		foreach($arRange as $Key=>$range){
			if($_POST['checkme']=="Y"){
				//Delete
				if(trim($_POST["RENGE_".$Key])!="" AND $Key!=$_POST["del"]){
					$arExGUpdate = ImportExGroups::InPaste($Key,Array(),$arExGUpdate);
				}
			}else{
				//Put old
				$arExGUpdate = ImportExGroups::InPaste($Key,Array('RENGE'=>$arRange[$Key], 'EXTRA'=>$arExtra[$Key], 'FIXED'=>$arFixed[$Key]),$arExGUpdate);
			}
		}
		$NewKey=($Key+1);
		if($_POST['checkme']=="Y"){
			//Add new
			if(trim($_POST["RENGE_".$NewKey])!=""){
				$arExGUpdate = ImportExGroups::InPaste($NewKey,Array(),$arExGUpdate);
			}
			$stRanges = implode('/',$arExGUpdate['RENGE']); $stExtra = implode('/',$arExGUpdate['EXTRA']); $stFixed = implode('/',$arExGUpdate['FIXED']);
			ImportExGroups::Update($arExGroup['ID'],Array("NAME"=>$_POST['NAME'], "RENGE"=>$stRanges, "EXTRA"=>$stExtra, "FIXED"=>$stFixed));
			$NOTE .= TMes('Record is updated').'<br>';
		}
	}
	?>
	<div style="padding:30px;">
		<div class="htit"><?=TMes('Editing exgroups')?></div>
		<?if(strlen($ERROR)>0){?><div class="ferror"><?=$ERROR?></div><?}?>
		<?if(strlen($NOTE)>0){?><div class="fnote"><?=$NOTE?></div><?}?>
		<form name="nform" id="nform" action="<?=$FORM_ACTION?>exgroup.edit.php" method="post" >		
			<input type="hidden" name="checkme" value="Y"/>
			<input type="hidden" name="ID" value="<?=$arExGroup['ID']?>"/>
			<?if($arExGroup['ID']<=0 AND $_REQUEST['add']=="new"){?>
				<input type="hidden" name="add" value="new"/>
			<?}?>
			<input type="hidden" name="del" id="del" value="-1"/>
			<table class="formtab" >
				<tr><td><?=TMes('Name')?>:</td><td colspan="10"><input type="text" name="NAME" value="<?=$_POST['NAME']?>" class="afield" maxlength="64"/></td></tr>
				<?//Поля
				$PrevRange=0; $Key=0;
				if(count($arExGUpdate['RENGE'])<=0){$arExGUpdate['RENGE'][0]="";}
				foreach($arExGUpdate['RENGE'] as $Key=>$range){?>
					<tr><td class="tarig">от <?=$PrevRange?> до </td>
						<td class="vamid"><input type="text" name="RENGE_<?=$Key?>" value="<?=$range?>" class="afield minif" maxlength="6" onkeypress="return numbersonly(this,event)"/>&nbsp;&nbsp;<img src="<?=CORE_ROOT_DIR?>/media/images/money-plus.png" title="+<?=TMes('Percentage margin')?>" width="16" height="16" alt=""></td>
						<td class="vamid"><input type="text" name="EXTRA_<?=$Key?>" value="<?=$arExGUpdate['EXTRA'][$Key]?>" class="afield minif" maxlength="4" onkeypress="return numbersonly(this,event)"/> %, <img src="<?=CORE_ROOT_DIR?>/media/images/fixed_price.png" title="+<?=TMes('A fixed margin')?>" width="16" height="16" alt=""</td>
						<td><input type="text" name="FIXED_<?=$Key?>" value="<?=$arExGUpdate['FIXED'][$Key]?>" class="afield minif" maxlength="9" onkeypress="return numbersonly(this,event)"/></td>
						<td><a href="javascript:void(0)" OnClick='$("#del").val("<?=$Key?>"); $("#nform").submit();'><img src="<?=CORE_ROOT_DIR?>/media/images/trash.gif" width="16" height="16" title="<?=TMes('Delete')?>"></a></td>
					</tr>
					<?
					$PrevRange=$range; $Rows++;
				}
				?>
				<?if($Rows<=1 AND $PrevRange<=0){?>
					<tr><td></td><td colspan="10" class="grays"><?=TMes('No extra charges')?>...</td></tr>
				<?}else{
					$Key++;?>
					<tr><td></td><td colspan="10" class="grays amini"><?=TMes('Add')?>:</td></tr>
					<tr><td class="tarig grays">от <?=$PrevRange?> до </td>
						<td class="vamid"><input type="text" name="RENGE_<?=$Key?>" value="" class="afield minif" maxlength="6" onkeypress="return numbersonly(this,event)"/>&nbsp;&nbsp;<img src="<?=CORE_ROOT_DIR?>/media/images/money-plus.png" title="+<?=TMes('Percentage margin')?>" width="16" height="16" alt=""></td>
						<td class="vamid"><input type="text" name="EXTRA_<?=$Key?>" value="" class="afield minif" maxlength="4" onkeypress="return numbersonly(this,event)"/> %, <img src="<?=CORE_ROOT_DIR?>/media/images/fixed_price.png" title="+<?=TMes('A fixed margin')?>" width="16" height="16" alt=""</td>
						<td><input type="text" name="FIXED_<?=$Key?>" value="" class="afield minif" maxlength="9" onkeypress="return numbersonly(this,event)"/></td>
					</tr>
				<?}?>
				
				<tr><td></td><td colspan="10"><br>
					<input type="submit" value="<?=TMes('Apply')?>" class="abutton"/> 
					<input type="button" value="<?=TMes('Cancel')?>" onClick="parent.$.fn.colorbox.close()" class="abutton" style="margin-left:10px;"/><br>
					<br>
					<a href="<?=$FORM_ACTION?>index.php"><?=TMes('Reload this Page')?></a>
				</td></tr>
			</table>
		</form>
	</div>

	<script>
		$(function(){
			cbox_submit();
		});
		function cbox_submit(){
			$("#nform").submit(function() {
				$.post($(this).attr("action"), $(this).serialize(), function(data){
					$.colorbox({html:data });
				},
				'html');
				return false;
			});
		}
		
		<?if($REDIRECT=="Y"){?>
			window.setTimeout(goRedirect, 3000);
			function goRedirect(){
				window.location.href = "<?=$FORM_ACTION?>";
			}
		<?}?>		
	</script>
	<?


?>