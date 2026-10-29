<?php
	require_once ("../inc/fungsi_all.php");
	require_once "../lib/nusoap.php";

	$action=$_REQUEST['action'];
	if ($action=='getKota') {
		$prop=$_REQUEST['prop'];
		$params = array('prop' => $prop); 
		$client = new nusoap_client(URL_SERVICE2."parameter12.php");
		$results = $client->call("Kota",$params);
		echo "<select class='form-control' name='Kota' id='Kota'>";
		echo "<option value='' selected>-Pilih-</option>";
		foreach($results as $row) {
			echo "<option value='$row[Kota]'>$row[Kota]</option>";
		}
		echo "</select>";
	} else if ($action=='getKecamatan') {
		$prop=$_REQUEST['prop'];
		$kota=$_REQUEST['kota'];
		$params = array('prop' => $prop,'kota' => $kota); 
		$client = new nusoap_client(URL_SERVICE2."parameter12.php");
		$results = $client->call("Kecamatan",$params);
		echo "<select class='form-control' name='Kecamatan' id='Kecamatan'>";
		echo "<option value='' selected>-Pilih-</option>";
		foreach($results as $row) {
			echo "<option value='$row[Kecamatan]'>$row[Kecamatan]</option>";
		}
		echo "</select>";
	} else if ($action=='getKelurahan') {
		$prop=$_REQUEST['prop'];
		$kota=$_REQUEST['kota'];
		$kec=$_REQUEST['kec'];
		$params = array('prop' => $prop,'kota' => $kota,'kec' => $kec); 
		$client = new nusoap_client(URL_SERVICE2."parameter12.php");
		$results = $client->call("Kelurahan",$params);
		echo "<select class='form-control' name='Kelurahan' id='Kelurahan'>";
		echo "<option value='' selected>-Pilih-</option>";
		foreach($results as $row) {
			echo "<option value='$row[Kelurahan]'>$row[Kelurahan]</option>";
		}
		echo "</select>";
	} else if ($action=='getKodePos') {
		$prop=$_REQUEST['prop'];
		$kota=$_REQUEST['kota'];
		$kec=$_REQUEST['kec'];
		$kel=$_REQUEST['kel'];
		$params = array('prop' => $prop,'kota' => $kota,'kec' => $kec,'kel' => $kel); 
		$client = new nusoap_client(URL_SERVICE2."parameter12.php");
		$results = $client->call("KodePos",$params);
		foreach($results as $row) {
			echo $row['KodePos'];
		}
	}
?>