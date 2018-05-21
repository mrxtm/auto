<?if(!defined("CORE_PROLOG_INCLUDED") || CORE_PROLOG_INCLUDED!==true)die();?>
<?
if(HEADER_INCLUDED=='Y'){
	if(!defined('DB_CONNECTION_P')){
		mysql_close($TDataBase->rsSQL);
	}
	
	if(USE_STATISTIC=="Y" AND CORE_IS_ADMIN){
		//Statistic
		global $arStat;
		echo '<div class="corp_out shad_a">';
		echo '<h1 class="hd1">Statistic</h1>';
		echo '<table class="tab_smpads">';
		foreach($arStat as $arS){
			if($arS[0]!='Init'){
				$sec = round(($arS[1]-$prev),2);
				$sec_sum = $sec_sum+$sec;
				echo '<tr><td>'.$arS[0].': </td><td>'.$sec.'</td></tr>';
			}
			$prev = $arS[1];
		}
		echo '<tr><td>Total: </td><td>'.$sec_sum.'</td></tr>';
		echo '</table>';
		echo '</div><div class="cler"></div>';
	}
	
	//CMS Footer
	////////////////////////////////////////////////////////////
	/* Opencart / Drupal / Joomla */
	global $CmsTemlpateFooter;
	echo $CmsTemlpateFooter;
}
?>