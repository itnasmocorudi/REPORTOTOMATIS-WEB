<?php
	session_start(); 
	error_reporting(0);

	require_once ("../inc/fungsi_all.php"); 
	require_once("../soap.php");
	
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
	$rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
	$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'idsend';
	$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'asc';
	$query = isset($_POST['query']) ? $_POST['query'] : false;
	$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

	$page = $_POST['page'];
	$rp = $_POST['rp'];
	$sortname = $_POST['sortname'];
	$sortorder = $_POST['sortorder'];

	if (!$sortname) $sortname = 'idsend';
	if (!$sortorder) $sortorder = 'asc';
	
	header("Content-type: text/xml");
	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$xml .= "<rows>";
	$xml .= "<page>$page</page>";
	$no=0;
	$params = array('page' => $page,'rp'=>$rp,'sortname'=>$sortname,'sortorder'=>$sortorder); 
	
	$client = new UserClient; 
	//print_r($params);
	$result = $client->GetEmailx($params);
 
	$results = json_decode($result,true);  
	//print_r($result);
	foreach($results as $row_jenis) { 
		$xml .= "<row id='".$row_jenis['idsend']."'>";
		$xml .= "<cell><![CDATA[<input type='checkbox' id='IdUser' name='id[]' value='".$row_jenis['sendemail']."'>]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['sendemail'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['namatujuan'])."]]></cell>"; 
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['tipe'])."]]></cell>"; 
		 
		$xml .= "</row>"; 
		$no++;
	}
	$xml .= "<total>".$no."</total>";	
	$xml .= "</rows>";
	echo $xml;
?>