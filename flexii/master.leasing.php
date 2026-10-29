<?php
	session_start(); 
	error_reporting(0);
	require_once "../permission.php";
	// require_once ("../inc/fungsi_all.php");
	// require_once "../lib/nusoap.php";
	
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
	$rp = isset($_POST['rp']) ? $_POST['rp'] : 20;
	$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'IDCust';
	$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'desc';
	$query = isset($_POST['query']) ? $_POST['query'] : false;
	$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

	$page = $_POST['page'];
	$rp = $_POST['rp'];
	$sortname = $_POST['sortname'];
	$sortorder = $_POST['sortorder'];
	$user = $IdUser;
    $qText = $_REQUEST['qText'];
	$qField = $_REQUEST['qField'];

	if (!$sortname) $sortname = 'IDCust';
	if (!$sortorder) $sortorder = 'asc';
	
	header("Content-type: text/xml");
	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$xml .= "<rows>";
	$xml .= "<page>$page</page>";
	$no=1;
	$params = array('user' => $user,'page' => $page,'rp'=>$rp,'sortname'=>$sortname,'sortorder'=>$sortorder,'qText'=>$qText,'qField'=>$qField); 
	$client = new nusoap_client(URL_SERVICE2."parameter12.php");
	$results = $client->call("masterLeasing",$params);
	// echo $client->response;
	foreach($results as $row_jenis) {
		$xml .= "<row id='".$row_jenis['NoSPK']."'>";
		$xml .= "<cell><![CDATA[<input type='checkbox' id='IDCust' name='id[]' value='".$row_jenis['nomor']."'>]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['IDCust'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['namaLeasing'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['areaLeasing'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['KodeDealer'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['NamaDealer'])."]]></cell>";
		$xml .= "</row>";
		$total=$row_jenis['total'];
		$no++;
	}
	$xml .= "<total>".$total."</total>";	
	$xml .= "</rows>";
	echo $xml;
?>