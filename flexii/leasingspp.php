<?php
	session_start(); 
	error_reporting(0);

	require_once ("../inc/fungsi_all.php");
	require_once "../lib/nusoap.php";
	
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
	$rp = isset($_POST['rp']) ? $_POST['rp'] : 20;
	$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'noSpp';
	$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'asc';
	$query = isset($_POST['query']) ? $_POST['query'] : false;
	$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

	$page = $_POST['page'];
	$rp = $_POST['rp'];
	$sortname = $_POST['sortname'];
	$sortorder = $_POST['sortorder'];
	$where = $_REQUEST['where'];

	if (!$sortname) $sortname = 'noSpp';
	if (!$sortorder) $sortorder = 'asc';
	
	header("Content-type: text/xml");
	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$xml .= "<rows>";
	$xml .= "<page>$page</page>";
	$no=1;
	$params = array('user' => $user,'page' => $page,'rp'=>$rp,'sortname'=>$sortname,'sortorder'=>$sortorder,'where'=>$where);
	$client = new nusoap_client(URL_SERVICE2."parameter2.php");
	$results = $client->call("leasingsppx",$params);
	// echo $client->response;
	foreach($results as $row_jenis) {
		if ($no %2 == 0) {$bg="background:#eaeaea;";} else {$bg="";}
		$xml .= "<row id='".$row_jenis['noSpp']."'>";
		$xml .= "<cell><![CDATA[<div style='padding: 2px 0px;$bg'><input type='checkbox' id='chk-1' name='id[]' value='".$row_jenis['NoSpk']."'></div>]]></cell>";
		$xml .= "<cell><![CDATA[<div style='padding: 5px;$bg'>".utf8_encode($row_jenis['NoSpk'])."</div>]]></cell>";
		$xml .= "<cell><![CDATA[<div style='padding: 5px;$bg'>".utf8_encode($row_jenis['NamaPemesan'])."</div>]]></cell>";
		$xml .= "<cell><![CDATA[<div style='padding: 5px;$bg'>".utf8_encode($row_jenis['Kota'])."</div>]]></cell>";
		$xml .= "<cell><![CDATA[<div style='padding: 5px;$bg'>".utf8_encode($row_jenis['noRangka'])."</div>]]></cell>";
		$xml .= "<cell><![CDATA[<div style='padding: 5px;$bg'>".utf8_encode($row_jenis['noMesin'])."</div>]]></cell>";
		$xml .= "</row>";
		$total=$row_jenis['total'];
		$no++;
	}
	$xml .= "<total>".$total."</total>";	
	$xml .= "</rows>";
	echo $xml;
?>