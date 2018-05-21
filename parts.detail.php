<?require_once("core/prolog.php");
//Components
$Component = new TComponent();
$Component->IncludeComponent('parts.detail','default',Array(
	"ART_ID"=>$_REQUEST['artid'],
	"SUP_BRAND"=>$_REQUEST['sup_brand'],
	"NUMBER"=>$_REQUEST['number']
));

//CMS Header
require_once("core/header.php");
?>
<div class="corp_out shad_a">
	<?$Component->IncludeTemplate()?>
</div>
<div class="cler"></div>
<?require_once("core/footer.php");?>