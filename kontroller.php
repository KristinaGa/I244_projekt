<?php require_once('head.html'); ?>
    <?php $pildid = array(
            "pildid/nameless1.jpg",
            "pildid/nameless2.jpg",
            "pildid/nameless3.jpg",
            "pildid/nameless4.jpg",
            "pildid/nameless5.jpg",
            "pildid/nameless6.jpg");

    if (!empty($_GET["mode"])) {
    $page = $_GET["mode"];
} else {
    $page = "avaleht";
}
	switch ($page) {
	    case "eelarve": require('eelarve.html'); break;
	    case "kylalised": require('kylalised.html'); break;
	    case "logout": require('logout.html'); break;
        case "seaded": require('seaded.html'); break;
	    default: require('avaleht.html'); break;
	}
?>