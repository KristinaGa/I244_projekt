<?php


function connect_db(){
	global $connection;
	$host="localhost";
	$user="test";
	$pass="t3st3r123";
	$db="test";
	$connection = mysqli_connect($host, $user, $pass, $db) or die("ei saa ühendust mootoriga- ".mysqli_error());
	mysqli_query($connection, "SET CHARACTER SET UTF8") or die("Ei saanud baasi utf-8-sse - ".mysqli_error($connection));
}

function logi(){
	global $connection;
	if(!empty($_SESSION["user"])) {
		header("Location: ?page=avaleht");
	}else{
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if($_POST["user"] == '' || $_POST["pass"] == ''){
				$errors =array();
				if(empty($_POST["user"])) {
					$errors[] = "Palun sisesta kasutajanimi!";
				}
				if(empty($_POST["pass"]))
					$errors[] = "Palun sisesta parool!";
				} else {
					$kasutaja = mysqli_real_escape_string ($connection, $_POST["user"]);
					$parool = mysqli_real_escape_string ($connection, $_POST["pass"]);
					$sql = "SELECT id,role FROM kgarmatj_kylastajad WHERE username='$kasutaja' AND passw=sha1('$parool')";
					$result = mysqli_query($connection, $sql);
					$rida = mysqli_num_rows($result);
					$role = mysqli_fetch_assoc($result);
					if($rida){
						$_SESSION["role"] = $role["role"];
						$_SESSION["user"] = $_POST["user"];
						$_SESSION["useird"] = $role["id"];
						header("Location: ?page=avaleht");
					} else {
						header("Location: ?page=login");
					}
				}
			} else {
			}
		}
include('views/login.html');
	}

function logout(){
	$_SESSION=array();
	session_destroy();
	header("Location: ?");
}

function countdown(){
	//siia tuleb countdowni asi
}


function eelarve(){
	//siia tuleb eelarve leht
	global $connection;	
	
	if(empty($_SESSION["user"])) {
		header("Location: ?page=login");
	} else {	
	$p= mysqli_query($connection, "select distinct(PUUR) as puur from kgarmatj_loomaaed order by puur asc");
	$puurid=array();
	while ($r=mysqli_fetch_assoc($p)) {
		$l=mysqli_query($connection, "SELECT * FROM kgarmatj_loomaaed WHERE  puur=".mysqli_real_escape_string($connection, $r['puur']));
		while ($row=mysqli_fetch_assoc($l)) {
			$puurid[$r['puur']][]=$row;
			
	}
	}
	}
include_once('views/puurid.html');
}	

function kylalised() {
	global $connection;
	if(empty($_SESSION["user"])) {
		header("Location: ?page=login");
	} else {
		if($_SESSION["role"] == 'admin') {
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if($_POST["nimi"] == '' || $_POST["puur"] == '' ) {
				$errors =array();
				if(empty($_POST["nimi"])) {
					$errors[] = "Palun sisesta nimi!";
				}
				if(empty($_POST["puur"])){
					$errors[] = "Palun sisesta puur!";
				}
				} else {
					upload('liik');
					$nimi = mysqli_real_escape_string ($connection, $_POST["nimi"]);
					$puur = mysqli_real_escape_string ($connection, $_POST["puur"]);
					$liik = mysqli_real_escape_string ($connection, "pildid/".$_FILES["liik"]["name"]);
					$sql = "INSERT INTO kgarmatj_loomaaed (nimi, PUUR, liik) VALUES ('$nimi','$puur','$liik')";
					$result = mysqli_query($connection, $sql);
					$id = mysqli_insert_id($connection);
					if($id) {
						header("Location: ?page=loomad");
					} else {
						header("Location: ?page=loomavorm");
					}
}
}
} else {
	header("Location: ?page=loomad");
 }
}
include_once('views/loomavorm.html');
}


function seaded(){
	global $connection;
	$teade = '';
	$user_id = mysqli_real_escape_string ($connection, $_SESSION["useird"]);
	$sql = "SELECT * FROM kgarmatj_seaded WHERE user_id='$user_id'";
	$result = mysqli_query($connection, $sql);
	$result_array = mysqli_fetch_assoc($result);

	if($_SERVER['REQUEST_METHOD'] == 'POST') {
			$user_id = mysqli_real_escape_string ($connection, $_SESSION["useird"]);
			$pulma_kuupaev = mysqli_real_escape_string ($connection, $_POST['pulma_kuupaev']);
			$nimi_1 = mysqli_real_escape_string ($connection, $_POST['nimi_1']);
			$nimi_2 = mysqli_real_escape_string ($connection, $_POST['nimi_2']);
			$countdown_text = mysqli_real_escape_string ($connection, $_POST['countdown_text']);
			$menuu_1 = mysqli_real_escape_string ($connection, $_POST['menuu_1']);
			$menuu_2 = mysqli_real_escape_string ($connection, $_POST['menuu_2']);
			$sql_insert = "insert into kgarmatj_seaded
									  ( user_id
									  ,	pulma_kuupaev
									  , nimi_1
									  , nimi_2
									  , countdown_text
									  , menuu_1
									  , menuu_2)
							  values ( '$user_id'
							  		 , '$pulma_kuupaev'
									 , '$nimi_1'
									 , '$nimi_2'
									 , '$countdown_text'
									 , '$menuu_1'
									 , '$menuu_2')";
			
			$sql_update = "
				update kgarmatj_seaded
				set   pulma_kuupaev = '$pulma_kuupaev'
					, nimi_1 = '$nimi_1'
					, nimi_2 = '$nimi_2'
					, countdown_text = '$countdown_text'
					, menuu_1 = '$menuu_1'
					, menuu_2 = '$menuu_1'
				where user_id = '$user_id'
			";

			# salvestamine ja muutmine -> kui baasis pole juba salvestatud seadeid, siis insertime 
			# muul juhul uuendame
			if (empty($result_array)){
				mysqli_query($connection, $sql_insert);	
				$teade = 'Seaded salvestatud';
			} else {
				mysqli_query($connection, $sql_update);	
				$teade = 'Seaded uuendatud';
			};

	} else {

		$pulma_kuupaev = mysqli_real_escape_string ($connection, $result_array['pulma_kuupaev']);
		$nimi_1 = mysqli_real_escape_string ($connection, $result_array['nimi_1']);
		$nimi_2 = mysqli_real_escape_string ($connection, $result_array['nimi_2']);
		$countdown_text = mysqli_real_escape_string ($connection, $result_array['countdown_text']);
		$menuu_1 = mysqli_real_escape_string ($connection, $result_array['menuu_1']);
		$menuu_2 = mysqli_real_escape_string ($connection, $result_array['menuu_2']);		
	}
include_once('views/seaded.html');
}
?>
