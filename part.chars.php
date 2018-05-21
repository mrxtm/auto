<?require_once("core/prolog.php");
//Components
$Component = new TComponent();
$Component->IncludeComponent('part.chars','default',Array(
	"ART_ID"=>$_REQUEST['artid']
));

$Component->IncludeTemplate();
?>
