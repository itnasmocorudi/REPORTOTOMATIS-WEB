<?php
	session_start(); 
	error_reporting(0);

	require_once "../permission.php";
	// require_once ("../inc/fungsi_all.php");
	// require_once "../lib/nusoap.php";
	
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
	$rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
	$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'a.TglEntry';
	$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'asc';
	$query = isset($_POST['query']) ? $_POST['query'] : false;
	$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

	$page = $_POST['page'];
	$rp = $_POST['rp'];
	$sortname = $_POST['sortname'];
	$sortorder = $_POST['sortorder'];

	$user = $IdUser; 
	if (!$sortname) $sortname = 'Nomor_Rangka';
	if (!$sortorder) $sortorder = 'asc';
	
	header("Content-type: text/xml");
	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$xml .= "<rows>";
	$xml .= "<page>$page</page>";
	$no=1;
	$params = array('user' => $user,'page' => $page,'rp'=>$rp,'sortname'=>$sortname,'sortorder'=>$sortorder,'query'=>$query,'qtype'=>$qtype);
	$client = new nusoap_client(URL_SERVICE2."parameter.php");
	$results = $client->call("ReportOrderOtox",$params);
	//echo $client->response;
	$total = 0;
	//echo "<pre>";
	//print_r($results);
	foreach($results as $row_jenis) {
		$id = $row_jenis['Nomor_Rangka']."#".$row_jenis['Nomor_Polisi']."#".$row_jenis['Mdl']."#".$row_jenis['namapelanggan']."#".$row_jenis['nohp']."#".$row_jenis['tahunpembuatan'];
		$xml .= "<row id='".$row_jenis['Nomor_Rangka']."'>";
		$xml .= "<cell><![CDATA[<input type='checkbox' id='IdUser' name='id[]' value='".$id."'>]]></cell>";
		$xml .= "<cell><![CDATA[".$row_jenis['Nomor_Rangka']."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['Nomor_Polisi'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['Mdl'])."]]></cell>"; 
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['namapelanggan'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['nohp'])."]]></cell>"; 
		$xml .= "</row>"; 
		$total+=1;
		$no++;
	}
	$xml .= "<total>".$total."</total>";	
	$xml .= "</rows>";
	echo $xml;
?>