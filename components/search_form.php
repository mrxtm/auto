<?if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die();?>
<?if(!isset($_REQUEST['artnum'])){$_REQUEST['artnum']="";}?>
<div class="search_box">
	<table class="tab_smpads">
		<tr>
			<td align="right">
				<?=TMes('Part number')?>:
			</td><td>
				<input type="text" id="artnum" name="artnum" value="<?=$_REQUEST['artnum']?>" size="25" maxlength="40" class="tinptxt" placeholder="<?=TMes('for example')?>: CT637"> 
				<input type="submit" value="<?=TMes('Search')?>" class="tinpbut" onclick="tdm_search_bubmit()">
			</td><td>
				<a href="<?=CORE_ROOT_DIR?>/" class="master_import_link tflef"><?=TMes('Parts catalog')?></a>
		</tr>
	</table>
</div>
<script type="text/javascript">
function tdm_search_bubmit(){
	var str=''; 
	str = $('#artnum').val();
	str = str.replace(/\s/g, '');
	url = '/parts/search/'+str+'/';
	location = url;
}
</script>