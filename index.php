<?php
session_start();
require_once('funk.php');
connect_db();

$page="pealeht";
if (isset($_GET['page']) && $_GET['page']!=""){
	$page=htmlspecialchars($_GET['page']);
}

include_once('views/head.html');

switch($page){
	case "login":
		logi();
	break;

	case "avaleht":
		countdown();
	break;

	case "eelarve":
		eelarve();
	break;

	case "kylalised":
		kylalised();
	break;

	case "seaded":
		seaded();
	break;

	case "logout":
		logout();
	break;

	default:
		include_once('views/login.html');
	break;
}

include_once('views/foot.html');

?>
