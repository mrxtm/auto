<?define("TDM_ADMIN_SIDE","Y");
require_once("../core/prolog.php"); 
$FORM_ACTION=CORE_ROOT_DIR.'/admin/currencies.php';
if($_SESSION['CORE_IS_ADMIN']!="Y"){header('Location: '.CORE_ROOT_DIR.'/admin/'); die();}
if(CORE_SITE_CHARSET=="utf8"){header('Content-type: text/html; charset=utf-8');}

global $TCore;
global $TDataBase;
$TDataBase->DBConnect("CORE");



if($_REQUEST['ID']!=""){
	if($_REQUEST["ID"]!="NEW"){
		$arSRow = TDCurrency::GetByID($_REQUEST["ID"]);
		if($arSRow['ID']<=0){$_REQUEST["ID"]="NEW";}
	}
	
	if($_POST['checkme']=="Y" AND ($arSRow['ID']>0 OR $_REQUEST["ID"]=="NEW")){
		$_POST['RATE'] = str_replace(',','.',$_POST['RATE']);
		if(trim($_POST['CODE'])=="" OR !floatval($_POST['RATE'])>0 OR trim($_POST['TEMPLATE'])==""){
			$ERROR .= TMes('Fill in the required fields').'<br>';
			foreach($_POST as $Key=>$Value){$arSRow[$Key]=$Value;}
		}else{
			$arNFields = Array(
				"CODE" => $_POST['CODE'],
				"RATE" => $_POST['RATE'],
				"TEMPLATE" => $_POST['TEMPLATE'],
				"SEPARATOR_TEN" => '',
				"SEPARATOR_THO" => '',
				"DECIMAL_PLACES" => '',
			);
			if($arSRow['ID']>0){
				TDCurrency::Update($arSRow['ID'],$arNFields);
				$arSRow = TDCurrency::GetByID($arSRow["ID"]);
			}else{
				$arSRow = TDCurrency::Add($arNFields);
				$_REQUEST["ID"] = $arSRow["ID"];
			}
			if($arSRow["ID"]>0){$NOTE .= TMes('Changes are saved').'. <a href="'.$FORM_ACTION.'">'.TMes('Reload this Page').'</a>';}
			else{$ERROR .= 'Error! Changes are not saved!<br>'.mysql_error();}
		}
	}
	?>
	<div style="padding:30px;">
		<div class="htit">
			<?if($arSRow['ID']>0){?><?=TMes('Edit')?> <?=TMes('record')?> #<?=$arSRow['ID']?><?}?>
			<?if($_REQUEST["ID"]=="NEW"){?><?=TMes('Add')?> <?=TMes('record')?> <?}?>
		</div>
		<?if(strlen($ERROR)>0){?><div class="ferror"><?=$ERROR?></div><?}?>
		<?if(strlen($NOTE)>0){?><div class="fnote"><?=$NOTE?></div><?}?>
		<?if($arSRow['ID']>0 OR $_REQUEST["ID"]=="NEW"){?>
			<form name="nform" id="nform" action="<?=$FORM_ACTION?>?ID=<?=$_REQUEST["ID"]?>" method="post" >		
				<input type="hidden" name="checkme" value="Y"/>
				<table class="formtab" >
					<tr><td colspan="2"></td></tr>
					<tr><td class="vertop padtop tarig required"><?=TMes('Currency')?> ISO: </td><td><input type="text" name="CODE" value="<?=$arSRow['CODE']?>" maxlength="3" class="afield minif70"></td></tr>
					<tr><td class="vertop padtop tarig required"><?=TMes('Value')?>: </td><td><input type="text" name="RATE" value="<?=$arSRow['RATE']?>" class="afield minif120"></td></tr>
					<tr><td class="vertop padtop tarig required"><?=TMes('Template')?>: </td><td><input type="text" name="TEMPLATE" value="<?=$arSRow['TEMPLATE']?>" class="afield minif70"></td></tr>
					<tr><td></td><td><br>
						<input type="submit" value="<?if($_REQUEST["ID"]=="NEW"){?><?=TMes('Add')?><?}else{?><?=TMes('Apply')?><?}?>" class="abutton"/> 
						<input type="button" value="<?=TMes('Cancel')?>" onClick="parent.$.fn.colorbox.close()" class="abutton" style="margin-left:10px;"/><br>
						<br>
						<a href="<?=$FORM_ACTION?>"><?=TMes('Reload this Page')?></a>
					</td></tr>
				</table>
			</form>
		<?}?>
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
}else{
?>
<head><title><?=TMes('Exchange Rates')?> :: TecDoc Module</title></head>
	<div class="displayblock">
		<?require_once("admin_panel.php");?>
		<div class="acorp_out ashad_a">
			<h1 class="hd1"><?=TMes('Exchange Rates')?></h1>
			<p><span class="grey_color"><?=TMes('Set base currency rate')?> 1.00000</span></p>
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
			
			<table><tr><td>
				<table class="corp_table smpads imptable">
					<tr>
					<td class="head" ><?=TMes('Currency')?> ISO</td>
					<td class="head" ><?=TMes('Value')?></td>
					<td class="head" ><?=TMes('Template')?></td>
					<td class="head" title="<?=TMes('Actions')?>"></td>
					</tr>
					<?$rsCurs = TDCurrency::GetList(Array("ID"=>"ASC"),Array());
					while($arCur = $rsCurs->GetNext()){
						$Rows++;
						if($_REQUEST['del']==$arCur['ID']){
							TDCurrency::Delete($arCur['ID']); continue;
						}
						$arRates[$arCur['CODE']] = $arCur['RATE'];
						if($arCur['RATE']==1){$arCur['RATE']='<b>'.$arCur['RATE'].'</b>'; $arCur['CODE']='<b>'.$arCur['CODE'].'</b>';}
						?>
						<tr><td class="brbot1 brrig1"><?=$arCur['CODE']?></td>
						<td class="brbot1 brrig1"><?=$arCur['RATE']?></td>
						<td class="brbot1 brrig1"><?=$arCur['TEMPLATE']?></td>
						<td class="brbot1 brrig1 abuts_mr">
							<a href="<?=$FORM_ACTION?>?ID=<?=$arCur['ID']?>" class="popup"><img src="<?=CORE_ROOT_DIR?>/media/images/edit.gif" width="16" height="16" title="<?=TMes('Edit')?>"></a>
							<a href="javascript:void(0);" onclick="if(confirm('<?=TMes('Really delete?')?> <?=$arCur['CODE']?>')) window.location='<?=$FORM_ACTION?>?del=<?=$arCur['ID']?>';" ><img src="<?=CORE_ROOT_DIR?>/media/images/trash.gif" width="16" height="16" title="<?=TMes('Delete')?>"></a>
						</td>
						</tr>
					<?}?>
					<?if($Rows<=0){?>
						<tr><td class="brbot1" colspan="10"><center><?=TMes('No records')?>...</center></td>
					<?}?>
				</table>
			</td><td style="padding-left:20px;">
				<?if(count($arRates)>0){?>
					<table class="corp_table smpads imptable">
						<tr>
						<td class="head" ></td>
						<?foreach($arRates as $CODE=>$Rate){?><td class="head" ><?=$CODE?></td><?}?>
						</tr>
						<?$Nominal = 1;
						foreach($arRates as $CODE=>$Rate){
							$arRates2 = $arRates;
							if($Rate>99){$Rate=$Rate/10000; $Nominal=10000;}else{$Nominal=1;}?>
							<tr>
								<td class="brlef1 brbot1 brrig1 grbg_lig tarig"><?=$Nominal?> <?=$CODE?> =</td>
								<?foreach($arRates2 as $CODE2=>$Rate2){
									if($CODE!=$CODE2){
										$Res=$Rate2/$Rate;
										if($Res>100){$Res = round($Res,2);}else{$Res = round($Res,5);}
										if($Nominal>1000){$Res = round($Res,3);}
									}else{$Res = '-';}
									?>
									<td class="brbot1 brrig1"><?=$Res?></td>
								<?}?>
							</tr>
						<?}?>
					</table>
				<?}?>
			</table>
			<br>
			<a class="popup mblink" href="<?=$FORM_ACTION?>?ID=NEW"><?=TMes('Add')?></a><br>
			
			<?/*if(count($arRates)>0){?>
				<hr>
				<span class="grey_color">Информеры. Курсы валют на текущую дату <?=date("d.m.y")?> полученные онлайн через веб-сервисы банков:</span>
				<div class="cursdiv">
					<table class="curstab">
						<tr><td rowspan="10" class="bankstd"><img src="<?=CORE_ROOT_DIR?>/admin/images/cbrf.png" width="64" height="64" title="ЦБРФ"><br>ЦБРФ</td>
							<td class="hdr">1 руб.</td><td class="hdr">Прямой</td><td class="hdr">Обратный</td></tr>
							<?$obCBRF = new ExchangeRatesCBRF();?>
							<?foreach($arRates as $CODE=>$Rate){
								if($CODE!='RUB'){
									$cRate = $obCBRF->GetRate($CODE);?>
									<tr><td class="hdr"><?=$CODE?></td><td><?=round($cRate,5)?></td><td><?=round(1/$cRate,5)?></td></tr>
								<?}?>
							<?}?>
					</table>
				</div>
				
				<div class="cursdiv">
					<table class="curstab">
						<tr><td rowspan="10" class="bankstd"><img src="<?=CORE_ROOT_DIR?>/admin/images/nbu.png" width="64" height="64" title="НБУ"><br>НБУ</td>
							<td class="hdr">1 грн.</td><td class="hdr">Прямой</td><td class="hdr">Обратный</td></tr>
							<?$obNBU = new ExchangeRatesNBU();?>
							<?foreach($arRates as $CODE=>$Rate){
								if($CODE!='UAH'){
									$cRate = $obNBU->GetRate($CODE);?>
									<tr><td class="hdr"><?=$CODE?></td><td><?=round($cRate,5)?></td><td><?=round(1/$cRate,5)?></td></tr>
								<?}?>
							<?}?>
					</table>
				</div>
				
				<div class="cursdiv">
					<table class="curstab">
						<tr><td rowspan="10" class="bankstd"><img src="<?=CORE_ROOT_DIR?>/admin/images/nbrb.png" width="64" height="64" title="НБРБ"><br>НБРБ</td>
							<td class="hdr">10000 р.</td><td class="hdr">Прямой</td><td class="hdr">Обратный</td></tr>
							<?$obNBRB = new ExchangeRatesNBRB();?>
							<?foreach($arRates as $CODE=>$Rate){
								if($CODE!='BYR'){
									$cRate = $obNBRB->GetRate($CODE);?>
									<tr><td class="hdr"><?=$CODE?></td><td><?=round($cRate,5)?></td><td><?=round(1/($cRate/10000),3)?></td></tr>
								<?}?>
							<?}?>
					</table>
				</div>
				
				<div class="cler"></div>
			<?}*/?>
			
		</div>
	</div>
<?}?>
<?
class ExchangeRatesCBRF{
	var $rates;
	function __construct($date = null){ 
		$client = new SoapClient("http://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx?WSDL"); 
		if (!isset($date)) $date = date("Y-m-d"); 
		$curs = $client->GetCursOnDate(array("On_date" => $date));
		$this->rates = new SimpleXMLElement($curs->GetCursOnDateResult->any);
	}
	function GetRate($code){
		$code1 = (int)$code;
		if ($code1!=0){
			$result = $this->rates->xpath('ValuteData/ValuteCursOnDate/Vcode[.='.$code.']/parent::*');
		}else{
			$result = $this->rates->xpath('ValuteData/ValuteCursOnDate/VchCode[.="'.$code.'"]/parent::*');
		}
		if (!$result){return false; 
		}else {
			$vc = (float)$result[0]->Vcurs;
			$vn = (int)$result[0]->Vnom;
			return ($vc/$vn);
		}
	}
}

class ExchangeRatesNBU{
    public $exchange_url = 'http://bank-ua.com/export/currrate.xml';
    public $xml;
    function __construct(){ return $this->xml = simplexml_load_file($this->exchange_url); }
    function GetRate($NeedCode){
		if($this->xml!==FALSE){
			foreach($this->xml->children() as $obItem){
				$CurCode = (string)$obItem->char3;
				if($CurCode==$NeedCode){
					$CurRate = (float)$obItem->rate;
					$CurSize = (float)$obItem->size;
					$CurRate = $CurRate/$CurSize;
					$result = $CurRate;
				}
			}
		}
		return $result;
	}
}

class ExchangeRatesNBRB{
    public $exchange_url = 'http://www.nbrb.by/Services/XmlExRates.aspx';
    public $xml;
    function __construct(){ return $this->xml = simplexml_load_file($this->exchange_url); }
    function GetRate($NeedCode){
		if($this->xml!==FALSE){
			foreach($this->xml->children() as $obItem){
				//echo '<pre>';print_r($obItem);echo '</pre>';
				$CurCode = (string)$obItem->CharCode;
				if($CurCode==$NeedCode){
					$CurRate = (float)$obItem->Rate;
					$CurSize = (float)$obItem->Scale;
					$CurRate = $CurRate/$CurSize;
					$result = $CurRate;
				}
			}
		}
		return $result;
	}
}
?>