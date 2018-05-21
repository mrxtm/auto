<?
define("TDM_ADMIN_SIDE","Y");
require_once("../core/prolog.php"); 
if(CORE_SITE_CHARSET=="utf8"){header('Content-type: text/html; charset=utf-8');}
if($_REQUEST['logout']=="Y"){$_SESSION['CORE_IS_ADMIN']="N"; header('Location: '.CORE_ROOT_DIR.'/admin/'); die();}
if($_POST['authme']=="Y" AND $_SESSION['CORE_IS_ADMIN']!="Y" AND strlen($_POST['kpass'])>0){
	if($_POST['kpass']==CORE_ADMIN_KEY){
		$_SESSION['CORE_IS_ADMIN'] = "Y";
		header('Location: '.CORE_ROOT_DIR.'/admin/'); die();
	}else{
		$ERROR = "Wrong keycode...";
	}
}
?>
<head><title>Admin panel :: TecDoc module</title></head>
<div class="displayblock">
<?
if($_SESSION['CORE_IS_ADMIN']!="Y"){?>
	<link rel="stylesheet" href="<?=CORE_ROOT_DIR?>/admin/styles.css" type="text/css">
	<div class="keydiv acorp_out ashad_a">
		<?if($ERROR!=''){?><div class="keyerror"><?echo $ERROR?></div><?}?>
		<form name="aform" id="aform" action="" method="post">
			<input type="hidden" name="authme" value="Y"/>
			<input type="password" name="kpass" value="" size="20" class="keyinp" maxlength="30"/>
			<div class="goinp"><input type="submit" value="Go" class="abutton"/></div>
		</form>
	</div>
	<a href="<?=CORE_ROOT_DIR?>" class="kcatlink"><?=TMes('Catalog')?> &#9658;</a>
<?}else{?>
	<?require_once("admin_panel.php");?>
	<div class="acorp_out ashad_a">
		<h1 class="hd1">Описание модуля</h1>
		<p><b>Структура:</b></p>
		<p>
			Модуль состоит из группы компонент (список брендов, разделы авто, список запчастей...) размещенных в папке <span class="grays"><?=CORE_ROOT_DIR?>/components/..</span><br>
			Компоненты и названия их шаблонов подключаются в корневых управляющих файлах в папке <span class="grays"><?=CORE_ROOT_DIR?>/..</span><br>
			<br>
			<table class="corp_table smpads imptable" width="100%">
				<tr><td class="head">Логика</td><td class="head">Расположение</td></tr>
				<tr><td class="brbot1 brrig1">Стили модуля</td><td class="brbot1 grays"><?=CORE_ROOT_DIR?>/media/styles.css</td></tr>
				<tr><td class="brbot1 brrig1">Шаблоны компонент</td><td class="brbot1 grays"><?=CORE_ROOT_DIR?>/components/имя_компонента/templates/имя_шаблона/template.php</td></tr>
				<tr><td class="brbot1 brrig1">Шапка CMS</td><td class="brbot1 grays"><?=CORE_ROOT_DIR?>/core/header.php</td></tr>
				<tr><td class="brbot1 brrig1">Подвал CMS</td><td class="brbot1 grays"><?=CORE_ROOT_DIR?>/core/footer.php</td></tr>
				<tr><td class="brbot1 brrig1">Функции CMS</td><td class="brbot1 grays"><?=CORE_ROOT_DIR?>/core/cms_functions.php</td></tr>
				<tr><td class="brbot1 brrig1">Вебсервисы поставщиков</td><td class="brbot1 grays"><?=CORE_ROOT_DIR?>/core/webservices.php</td></tr>
				<tr><td class="brbot1 brrig1">Правила ЧПУ ссылок</td><td class="brbot1 grays"><?=CORE_ROOT_DIR?>/urlrewrite.php</td></tr>
				<tr><td class="brbot1 brrig1">Локализация, языки</td><td class="brbot1 grays"><?=CORE_ROOT_DIR?>/core/lang/..</td></tr>
				<tr><td class="brbot1 brrig1">Настройки модуля</td><td class="brbot1 grays"><?=CORE_ROOT_DIR?>/core/settings.ini</td></tr>
			</table>
			
		</p>
		
		<p><b>База данных TecDoc:</b></p>
		<p>
			Модуль использует следующие таблицы (27 шт.) из базы TecDoc:<br><span class="grays">
			ARTICLES, 
			ARTICLE_CRITERIA, 
			ARTICLE_INFO, 
			ART_LOOKUP, 
			BRANDS, 
			COUNTRIES, 
			COUNTRY_DESIGNATIONS, 
			CRITERIA, 
			DES_TEXTS, 
			DESIGNATIONS, 
			DOC_TYPES, 
			ENGINES, 
			GRAPHICS, 
			LINK_TYP_ENG, 
			LINK_GRA_ART, 
			LINK_GA_STR, 
			LINK_LA_TYP, 
			LINK_ART, 
			MANUFACTURERS, 
			MODELS, 
			SEARCH_TREE, 
			SUPPLIERS, 
			SUPPLIER_ADDRESSES, 
			SUPPLIER_LOGOS, 
			TYPES, 
			TEXT_MODULES, 
			TEXT_MODULE_TEXTS
			</span>
		</p>
	</div>
<?}?>
</div>