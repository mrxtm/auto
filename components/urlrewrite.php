<?
//try to fix REQUEST_URI under IIS
$aProtocols = array('http', 'https');
foreach($aProtocols as $prot){
    $marker = "404;".$prot."://";
    if(($p = strpos($_SERVER["QUERY_STRING"], $marker)) !== false){
        $uri = $_SERVER["QUERY_STRING"];
        if(($p = strpos($uri, "/", $p+strlen($marker))) !== false){
            if($_SERVER["REQUEST_URI"] == '' || strpos($_SERVER["REQUEST_URI"], $marker) !== false){
                $_SERVER["REQUEST_URI"] = $REQUEST_URI = substr($uri, $p);
            }
            $_SERVER["REDIRECT_STATUS"] = '404';
            $_SERVER["QUERY_STRING"] = $QUERY_STRING = "";
            $_GET = array();
            break;
        }
    }
}

if(!defined("AUTH_404")){define("AUTH_404", "Y");}

$arUrlRewrite = array();
if(file_exists("../urlrewrite.php")){include("../urlrewrite.php");}

if(isset($_SERVER['REDIRECT_STATUS']) && $_SERVER['REDIRECT_STATUS'] == '404'){
    $url = $_SERVER["REQUEST_URI"];
    if(($pos=strpos($url, "?"))!==false){
        $params = substr($url, $pos+1);
        parse_str($params, $vars);
        $_GET += $vars;
        $_REQUEST += $vars;
        $GLOBALS += $vars;
        $_SERVER["QUERY_STRING"] = $QUERY_STRING = $params;
    }
}

foreach($arUrlRewrite as $val){
    if(preg_match($val["CONDITION"], $_SERVER["REQUEST_URI"])){
        if (strlen($val["RULE"]) > 0)
          $url = preg_replace($val["CONDITION"], (StrLen($val["PATH"]) > 0 ? $val["PATH"]."?" : "").$val["RULE"], $_SERVER["REQUEST_URI"]);
        else
          $url = $val["PATH"];

        if(($pos=strpos($url, "?"))!==false){
            $params = substr($url, $pos+1);
            parse_str($params, $vars);
            $_GET += $vars;
            $_REQUEST += $vars;
            $GLOBALS += $vars;
            $_SERVER["QUERY_STRING"] = $QUERY_STRING = $params;
            $url = substr($url, 0, $pos);
        }
        if(!file_exists($_SERVER['DOCUMENT_ROOT'].$url) || !is_file($_SERVER['DOCUMENT_ROOT'].$url))
          continue;

        //CHTTP::SetStatus("200 OK");

        $_SERVER["REAL_FILE_PATH"] = $url;
        include_once($_SERVER['DOCUMENT_ROOT'].$url);
        //echo $_SERVER['DOCUMENT_ROOT'].$url;
        die();
    }
}
if(file_exists($_SERVER['DOCUMENT_ROOT'].'/404.php')){include_once($_SERVER['DOCUMENT_ROOT'].'/404.php');}
?>