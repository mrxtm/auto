<?require_once("core/prolog.php");
//Components

$GetComponent = new TComponent();
$GetComponent->IncludeComponent('parts.by.number','default',Array(
	"SEARCH_NUMBER"=>$_REQUEST['artnum'],
	"SEARCH_BRAND"=>$_REQUEST['brand']
));

$ViewComponent = new TComponent();
if($GetComponent->arResult['LIST_VIEW_TEMPLATE']==''){$LVTemp='default';}else{$LVTemp=$GetComponent->arResult['LIST_VIEW_TEMPLATE'];}
$ViewComponent->IncludeComponent('parts.list.view',$LVTemp,Array(
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