<?define('CORE_ROOT_DIR',"/parts");?>
<?require_once("core/prolog.php");
require_once("core/header.php");
?>
<div class="corp_out shad_a">
<h1 class="hd1">Artikelsuche</h1><hr>
<?php
if(isset($_REQUEST['kba']))
{
$kba = ArtToNumber($_REQUEST['kba'],'FULL');
}
?>
<?
	global $TDataBase;
	$TDataBase->DBSelect("TECDOC");
	//Set UTF8
	mysql_query("set names utf8");
	$query =
		"SELECT ".
	" TYP_ID, ".
    " MOD_ID, ".
	" MFA_BRAND, ".
    " LTE_ENG_ID, ".
	" DES_TEXTS7.TEX_TEXT AS MOD_CDS_TEXT, ".
	" DES_TEXTS.TEX_TEXT AS TYP_CDS_TEXT, ".
	" TYP_PCON_START, ".
	" TYP_PCON_END, ".
	" TYP_CCM, ".
	" TYP_KW_FROM, ".
	" TYP_KW_UPTO, ".
	" TYP_HP_FROM, ".
	" TYP_HP_UPTO, ".
	" TYP_CYLINDERS, ".
    " ENGINES.ENG_CODE, ".
	" DES_TEXTS2.TEX_TEXT AS TYP_ENGINE_DES_TEXT, ".
	" DES_TEXTS3.TEX_TEXT AS TYP_FUEL_DES_TEXT, ".
	" IFNULL(DES_TEXTS4.TEX_TEXT, DES_TEXTS5.TEX_TEXT) AS TYP_BODY_DES_TEXT, ".
	" DES_TEXTS6.TEX_TEXT AS TYP_AXLE_DES_TEXT, ".
	" TYP_MAX_WEIGHT ".
" FROM ".
	          " TYPE_NUMBERS ".
	" INNER JOIN TYPES ON TYP_ID = TYN_TYP_ID ".
	" INNER JOIN COUNTRY_DESIGNATIONS ON COUNTRY_DESIGNATIONS.CDS_ID = TYP_CDS_ID ".
	" INNER JOIN DES_TEXTS ON DES_TEXTS.TEX_ID = COUNTRY_DESIGNATIONS.CDS_TEX_ID ".
	" INNER JOIN MODELS ON MOD_ID = TYP_MOD_ID ".
	" INNER JOIN MANUFACTURERS ON MFA_ID = MOD_MFA_ID ".
	" INNER JOIN COUNTRY_DESIGNATIONS AS COUNTRY_DESIGNATIONS2 ON COUNTRY_DESIGNATIONS2.CDS_ID = MOD_CDS_ID ".
	" INNER JOIN DES_TEXTS AS DES_TEXTS7 ON DES_TEXTS7.TEX_ID = COUNTRY_DESIGNATIONS2.CDS_TEX_ID ".
	" LEFT JOIN DESIGNATIONS ON DESIGNATIONS.DES_ID = TYP_KV_ENGINE_DES_ID ".
	" LEFT JOIN DES_TEXTS AS DES_TEXTS2 ON DES_TEXTS2.TEX_ID = DESIGNATIONS.DES_TEX_ID ".
	" LEFT JOIN DESIGNATIONS AS DESIGNATIONS2 ON DESIGNATIONS2.DES_ID = TYP_KV_FUEL_DES_ID ".
	" LEFT JOIN DES_TEXTS AS DES_TEXTS3 ON DES_TEXTS3.TEX_ID = DESIGNATIONS2.DES_TEX_ID ".
	" LEFT JOIN LINK_TYP_ENG ON LTE_TYP_ID = TYP_ID ".
    " LEFT JOIN ENGINES ON ENG_ID = LTE_ENG_ID ".
	" LEFT JOIN DESIGNATIONS AS DESIGNATIONS3 ON DESIGNATIONS3.DES_ID = TYP_KV_BODY_DES_ID ".
	" LEFT JOIN DES_TEXTS AS DES_TEXTS4 ON DES_TEXTS4.TEX_ID = DESIGNATIONS3.DES_TEX_ID ".
	" LEFT JOIN DESIGNATIONS AS DESIGNATIONS4 ON DESIGNATIONS4.DES_ID = TYP_KV_MODEL_DES_ID ".
	" LEFT JOIN DES_TEXTS AS DES_TEXTS5 ON DES_TEXTS5.TEX_ID = DESIGNATIONS4.DES_TEX_ID ".
	" LEFT JOIN DESIGNATIONS AS DESIGNATIONS5 ON DESIGNATIONS5.DES_ID = TYP_KV_AXLE_DES_ID ".
	" LEFT JOIN DES_TEXTS AS DES_TEXTS6 ON DES_TEXTS6.TEX_ID = DESIGNATIONS5.DES_TEX_ID ".
" WHERE ".
	" TYN_SEARCH_TEXT = '".$kba."' AND ".
	" TYN_KIND = 1 AND ".
	" COUNTRY_DESIGNATIONS.CDS_LNG_ID = 1 AND ".
	" COUNTRY_DESIGNATIONS2.CDS_LNG_ID = 1 AND ".
	" (DESIGNATIONS.DES_LNG_ID IS NULL OR DESIGNATIONS.DES_LNG_ID = 1) AND ".
	" (DESIGNATIONS2.DES_LNG_ID IS NULL OR DESIGNATIONS2.DES_LNG_ID = 1) AND ".
	" (DESIGNATIONS3.DES_LNG_ID IS NULL OR DESIGNATIONS3.DES_LNG_ID = 1) AND ".
	" (DESIGNATIONS4.DES_LNG_ID IS NULL OR DESIGNATIONS4.DES_LNG_ID = 1) AND ".
	" (DESIGNATIONS5.DES_LNG_ID IS NULL OR DESIGNATIONS5.DES_LNG_ID = 1) ".
" ORDER BY ".
	" MFA_BRAND, ".
	" MOD_CDS_TEXT, ".
	" TYP_CDS_TEXT, ".
	" TYP_PCON_START, ".
	" TYP_CCM " ;
	//sql_query
	$result = mysql_query($query);
	$num_rows = mysql_num_rows($result);
	if($num_rows>0){?>
	<div class="subtit">KBA Nummern: 
<a href="<?php echo CORE_ROOT_DIR; ?>/kba/<?php echo $kba; ?>/"><?php
echo $kba;
?></a>		
</div>
<hr class="marbot18">
	<table class="corp_table" id="cortab"><tr><td class="head"><?=TMes('Model')?></td><td class="head"><?=TMes('Year of construction')?></td><td class="head"><?=TMes('Type')?></td><td class="head"><?=TMes('Power')?></td><td class="head"><?=TMes('Capacity')?></td><td class="head"><?=TMes('Cylinder')?></td><td class="head"><?=TMes('Fuel')?></td><td class="head"><?=TMes('Body')?></td><?/*<td class="head"><?=TMes('Axis')?></td>*/?></tr>
	<?php
	//Results of a SQL query
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
    echo '<tr class="gtr">';
	echo '<td class="pads"><a href="'.CORE_ROOT_DIR.'/'.$row['MFA_BRAND'].'/m'.$row['MOD_ID'].'/t'.$row['TYP_ID'].'/" class="amod">'.$row['MFA_BRAND'].' '.$row['MOD_CDS_TEXT']. ' '.$row['TYP_CDS_TEXT'].'</a></td>';
	$DateFr = TDDateFormat($row['TYP_PCON_START'],TMes('to p.t.'));
			$DateTo = TDDateFormat($row['TYP_PCON_END'],TMes('to p.t.'));
			echo '<td class="pads">'.$DateFr.' - '.$DateTo.'</td>';
    echo '<td class="pads">'.$row['ENG_CODE'].'</td>';
    echo '<td class="pads">'.$row['TYP_KW_FROM'].' <span>'.TMes('Kv').'</span> - '.$row['TYP_HP_FROM'].' <span>'.TMes('Hp').'</span></td>';
    echo '<td class="pads">'.$row['TYP_CCM'].' <span>'.TMes('sm').'<sup>3</sup></span></td>';
    echo '<td class="pads">'.$row['TYP_CYLINDERS'].'</td>';
	echo '<td class="pads">'.$row['TYP_FUEL_DES_TEXT'].'</td>';
	echo '<td class="pads">'.$row['TYP_BODY_DES_TEXT'].'</td>';
    echo "</tr>";
    }
	echo "</table>";
	}
	else
	{
	echo TMes('Has no parts in this section for nomer KBA').' ...';
	}
	
	mysql_free_result($result);
	mysql_close($db);
?>
</div>
<?require_once("core/footer.php");?>