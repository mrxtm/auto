<?require_once("core/prolog.php");
//Components
$Component = new TComponent();
$Component->IncludeComponent('types.list','default',Array(
	"BRAND"=>$_REQUEST['brand'],
	"MODEL_ID"=>$_REQUEST['model']
));

//CMS Header
require_once("core/header.php");
?>
<div class="corp_out shad_a">
	<h1 class="hd1"><?=$TCore->Head_Title_H1?></h1>
	<?$Component->IncludeTemplate()?>
	
</div>
<div class="cler"></div>
<?require_once("core/footer.php");?>