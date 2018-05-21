<?
$ROOT_DIR = 'parts';
$arUrlRewrite = array(
	array(
		"CONDITION"	=>	"#^/".$ROOT_DIR."/import/#",
		"RULE"	=>	"",
		"ID"	=>	"",
		"PATH"	=>	"/".$ROOT_DIR."/import/index.php",
	),
	array(
		"CONDITION"	=>	"#^/".$ROOT_DIR."/brand/(.+?)/#",
		"RULE"	=>	"bid=$1&last=$2",
		"ID"	=>	"",
		"PATH"	=>	"/".$ROOT_DIR."/brands.detail.php",
	),
	array(
		"CONDITION"	=>	"#^/".$ROOT_DIR."/(.+?)/m([0-9]+)/t([0-9]+)/s([0-9]+)/#",
		"RULE"	=>	"brand=$1&model=$2&type=$3&sec_id=$4",
		"ID"	=>	"",
		"PATH"	=>	"/".$ROOT_DIR."/parts.by.section.php",
	),
	array(
		"CONDITION"	=>	"#^/".$ROOT_DIR."/(.+?)/m([0-9]+)/t([0-9]+)/search/(.+?)/#",
		"RULE"	=>	"brand=$1&model=$2&type=$3&sec_name=$4",
		"ID"	=>	"",
		"PATH"	=>	"/".$ROOT_DIR."/parts.by.section.php",
	),
	array(
		"CONDITION"	=>	"#^/".$ROOT_DIR."/(.+?)/m([0-9]+)/t([0-9]+)/c([0-9]+)/#",
		"RULE"	=>	"brand=$1&model=$2&type=$3&cat1=$4",
		"ID"	=>	"",
		"PATH"	=>	"/".$ROOT_DIR."/sections.php",
	),
	array(
		"CONDITION"	=>	"#^/".$ROOT_DIR."/(.+?)/m([0-9]+)/t([0-9]+)/#",
		"RULE"	=>	"brand=$1&model=$2&type=$3",
		"ID"	=>	"",
		"PATH"	=>	"/".$ROOT_DIR."/sections.php",
	),
	array(
		"CONDITION"	=>	"#^/".$ROOT_DIR."/(.+?)/m([0-9]+)/#",
		"RULE"	=>	"brand=$1&model=$2",
		"ID"	=>	"",
		"PATH"	=>	"/".$ROOT_DIR."/types.php",
	),
	array(
		"CONDITION"	=>	"#^/".$ROOT_DIR."/info/(.+?)/(.+?)#",
		"RULE"	=>	"sup_brand=$1&number=$2",
		"ID"	=>	"",
		"PATH"	=>	"/".$ROOT_DIR."/parts.detail.php",
	),
	array(
		"CONDITION"	=>	"#^/".$ROOT_DIR."/info/([0-9]+)#",
		"RULE"	=>	"artid=$1&last=$2",
		"ID"	=>	"",
		"PATH"	=>	"/".$ROOT_DIR."/parts.detail.php",
	),
	array(
		"CONDITION"	=>	"#^/".$ROOT_DIR."/search/(.+?)/(.+?)#",
		"RULE"	=>	"artnum=$1&brand=$2",
		"ID"	=>	"",
		"PATH"	=>	"/".$ROOT_DIR."/parts.by.number.php",
	),
	array(
		"CONDITION"	=>	"#^/".$ROOT_DIR."/search/(.+?)/#",
		"RULE"	=>	"artnum=$1",
		"ID"	=>	"",
		"PATH"	=>	"/".$ROOT_DIR."/parts.by.number.php",
	),
	array(
		"CONDITION"	=>	"#^/".$ROOT_DIR."/search/(.+?)#",
		"RULE"	=>	"artnum=$1",
		"ID"	=>	"",
		"PATH"	=>	"/".$ROOT_DIR."/parts.by.number.php",
	),
	array(
		"CONDITION"	=>	"#^/".$ROOT_DIR."/search/#",
		"RULE"	=>	"",
		"ID"	=>	"",
		"PATH"	=>	"/".$ROOT_DIR."/parts.by.number.php",
	),
	array(
		"CONDITION"	=>	"#^/".$ROOT_DIR."/kba/(.+?)#",
		"RULE"	=>	"kba=$1",
		"ID"	=>	"",
		"PATH"	=>	"/".$ROOT_DIR."/parts.by.kba.php",
	),
	array(
		"CONDITION"	=>	"#^/".$ROOT_DIR."/kba/#",
		"RULE"	=>	"",
		"ID"	=>	"",
		"PATH"	=>	"/".$ROOT_DIR."/parts.by.kba.php",
	),
	array(
		"CONDITION"	=>	"#^/".$ROOT_DIR."/(.+?)/#",
		"RULE"	=>	"brand=$1",
		"ID"	=>	"",
		"PATH"	=>	"/".$ROOT_DIR."/models.php",
	),
);

?>