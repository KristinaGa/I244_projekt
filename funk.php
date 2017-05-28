<?php


function connect_db(){
	global $connection;
	$host="localhost";
	// $user="test";
	// $pass="t3st3r123";
	$user="root"; //Localhost
	$pass="root"; //Localhost
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
	global $connection;
	$user_id = mysqli_real_escape_string ($connection, $_SESSION["useird"]);
	$sql = "SELECT pulma_kuupaev, concat(nimi_1,' ja ', nimi_2, ' pulmadeni on: ') countdown_text FROM kgarmatj_seaded WHERE user_id='$user_id'";
	$result = mysqli_query($connection, $sql);
	$result_array = mysqli_fetch_assoc($result);
	$pulma_kuupaev = $result_array['pulma_kuupaev'];
	$countdown_text = $result_array['countdown_text'];

	if ($pulma_kuupaev == '0000-00-00') {
		$pulma_kuupaev = '';
		$countdown_text = '';
	}

	include_once('views/avaleht.html');
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

function saa_andmed($user_id) {
	global $connection;
	$sql = "SELECT * FROM kgarmatj_kylalised WHERE user_id='$user_id'";
	$result = mysqli_query($connection, $sql);


	$kylalised = array();

	while(($row = mysqli_fetch_assoc($result))) {
		$kylalised[] = $row;
	}
	return $kylalised;
}


function kylalised() {
	global $connection;
	$user_id = mysqli_real_escape_string($connection, $_SESSION["useird"]);
	$sql = "SELECT * FROM kgarmatj_seaded WHERE user_id='$user_id'";
	$result = mysqli_query($connection, $sql);
	$result_array = mysqli_fetch_assoc($result);

	$kylalised = saa_andmed($user_id);
	# Juhuks kui pole andmeid veel salvestatud
	if (empty($kylalised)) {
		$kylalised= array(array(
			  'kaaslase_nimi' => ''
			, 'menuu_valik' => ''
			, 'kaaslase_menuu_valik' => ''
			, 'nimi' => ''
			, 'oobimine' => ''
			, 'rea_id' => ''
			, 'rea_jarg' => ''
			, 'tulemas' => '' 
		));
	}

	$teade = '';

	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['salvesta'])) {
		for ($i = 0; $i < count($_POST['nimi']); $i++) {
			
			try {
				$rea_jarg = mysqli_real_escape_string($connection, $i);
			} catch (Exception $e) {
				$rea_jarg = '';		
			}

			try {
				error_log($_POST['nimi'][$i]);
				$nimi = mysqli_real_escape_string($connection, $_POST['nimi'][$i]);
				error_log($nimi);
			} catch  (Exception $e) {
				$nimi = '';
			}

			try {
				$kaaslase_nimi = mysqli_real_escape_string($connection, $_POST['kaaslase_nimi'][$i]);
			} catch  (Exception $e) {
				$kaaslase_nimi = '';
				
			}

			if (isset($_POST['tulemas'][$i])) {
				try {
					$tulemas = mysqli_real_escape_string($connection, $_POST['tulemas'][$i]);
				} catch  (Exception $e) {
					$tulemas = '';
				}
			}else {
				$tulemas = '';
			}

			if (isset($_POST['menuu_valik'][$i])) {
				try {
					$menuu_valik = mysqli_real_escape_string($connection, $_POST['menuu_valik'][$i]);
				} catch  (Exception $e) {
					$menuu_valik = '';
				}
			} else {
					$menuu_valik = '';
			}

			if (isset($_POST['kaaslase_menuu_valik'][$i])) {
				try {
					$kaaslase_menuu_valik = mysqli_real_escape_string($connection, $_POST['kaaslase_menuu_valik'][$i]);
				} catch  (Exception $e) {
					$kaaslase_menuu_valik = '';
				}
			} else {
					$kaaslase_menuu_valik = '';
			}
			
			if (isset($_POST['oobimine'][$i])){
				try {
					$oobimine = mysqli_real_escape_string($connection, $_POST['oobimine'][$i]);
				} catch  (Exception $e) {
					$oobimine = '';
				}
			}else {
				$oobimine = '';
			}			
									

			if (isset($_POST['rea_id'])) {
				$rea_id = mysqli_real_escape_string($connection, $_POST['rea_id'][$i]);
			} else {
				$rea_id = False;
			}

			$sql_insert = "insert into kgarmatj_kylalised
									  ( user_id
									  ,	rea_jarg
									  , nimi
									  , kaaslase_nimi
									  , tulemas
									  , menuu_valik
									  , kaaslase_menuu_valik
									  , oobimine)
							  values ( '$user_id'
							  		 , '$rea_jarg'
									 , '$nimi'
									 , '$kaaslase_nimi'
									 , '$tulemas'
									 , '$menuu_valik'
									 , '$kaaslase_menuu_valik'
									 , '$oobimine')";

			$sql_update = "update kgarmatj_kylalised
							set	rea_jarg = '$rea_jarg'
								, nimi = '$nimi'
								, kaaslase_nimi = '$kaaslase_nimi'
								, tulemas = '$tulemas'
								, menuu_valik = '$menuu_valik'
								, kaaslase_menuu_valik = '$kaaslase_menuu_valik'
								, oobimine = '$oobimine'
							where user_id = '$user_id'
							and rea_id = '$rea_id'";

			if ($rea_id != False) {
			if (mysqli_query($connection, $sql_update) === TRUE) {
				$kylalised = saa_andmed($user_id);
				$teade = 'Andmed salvestatud';
			}} else {
			if (mysqli_query($connection, $sql_insert) === TRUE) {
				$kylalised = saa_andmed($user_id);
				$teade = 'Andmed salvestatud';
			}
	}
}
	} elseif (isset($_POST['kustuta'])) {

		if (isset($_POST['del'])){
			foreach ($_POST['del'] as $kustuta) {
				 $sql = "delete from kgarmatj_kylalised where rea_id = $kustuta and user_id=$user_id";
				 mysqli_query($connection, $sql);
			}
		$kylalised = saa_andmed($user_id);
		}

	}

	include_once('views/kylalised.html');
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
			$menuu_1 = mysqli_real_escape_string ($connection, $_POST['menuu_1']);
			$menuu_2 = mysqli_real_escape_string ($connection, $_POST['menuu_2']);
			$sql_insert = "insert into kgarmatj_seaded
									  ( user_id
									  ,	pulma_kuupaev
									  , nimi_1
									  , nimi_2
									  , menuu_1
									  , menuu_2)
							  values ( '$user_id'
							  		 , '$pulma_kuupaev'
									 , '$nimi_1'
									 , '$nimi_2'
									 , '$menuu_1'
									 , '$menuu_2')";
			
			$sql_update = "
				update kgarmatj_seaded
				set   pulma_kuupaev = '$pulma_kuupaev'
					, nimi_1 = '$nimi_1'
					, nimi_2 = '$nimi_2'
					, menuu_1 = '$menuu_1'
					, menuu_2 = '$menuu_2'
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
