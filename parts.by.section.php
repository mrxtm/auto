<?require_once("core/prolog.php");
//Components
$GetComponent = new TComponent();
$GetComponent->IncludeComponent('parts.by.section','default',Array(
	"BRAND"=>$_REQUEST['brand'],
	"MODEL_ID"=>$_REQUEST['model'],
	"TYPE_ID"=>$_REQUEST['type'],
	"SECTION_NAME"=>$_REQUEST['sec_name'],
	"SECTION_ID"=>$_REQUEST['sec_id'],
));

$ViewComponent = new TComponent();
$ViewComponent->IncludeComponent('parts.list.view','default',Array(
	"GET_RESULT"=>$GetComponent->arResult
));

//CMS Header
require_once("core/header.php");
?>
<div class="corp_out shad_a">
	<h1 class="hd1"><?=$TCore->Head_Title_H1?></h1>
	<?$GetComponent->IncludeTemplate()?>
	<?$ViewComponent->IncludeTemplate()?>
</div>
<div class="cler"></div>
<?require_once("core/footer.php");?>