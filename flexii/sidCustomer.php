<?php
	session_start(); 
	error_reporting(0);

	require_once ("../inc/fungsi_all.php");
	require_once "../lib/nusoap.php";
	// ini_set('memory_limit', '512M');
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
	$rp = isset($_POST['rp']) ? $_POST['rp'] : 20;
	$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'IDCust';
	$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'asc';
	$query = isset($_POST['query']) ? $_POST['query'] : false;
	$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

	$page = $_POST['page'];
	$rp = $_POST['rp'];
	$sortname = $_POST['sortname'];
	$sortorder = $_POST['sortorder'];
	$noID = $_REQUEST['noID'];
	$namaCust = $_REQUEST['namaCust'];
	$TglLahir = $_REQUEST['TglLahir'];
	$IDCust = $_REQUEST['IDCust'];

	if (!function_exists('datenull')) {
		function datenull($tanggal) {
			if (is_null($tanggal) || $tanggal=='Jan 1 1900 12:00AM' || $tanggal=='Jan 1 1970 12:00AM' || $tanggal=='1900-01-01 00:00:00') { $tanggal=""; } else { $tanggal=date('d/m/Y', strtotime($tanggal)); }
			return $tanggal;
		}
	}
	
	if (!$sortname) $sortname = 'IDCust';
	if (!$sortorder) $sortorder = 'asc';
	
	header("Content-type: text/xml");
	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$xml .= "<rows>";
	$xml .= "<page>$page</page>";
	$no=1;
	$params = array('user' => $user,'page' => $page,'rp'=>$rp,'sortname'=>$sortname,'sortorder'=>$sortorder,'noID'=>$noID,'namaCust'=>$namaCust,'TglLahir'=>$TglLahir,'IDCust'=>$IDCust);
	$client = new nusoap_client(URL_SERVICE2."parameter12.php");
	$results = $client->call("sidCustomer",$params);
	// echo $client->response;
	foreach($results as $row_jenis) {
		if ($no %2 == 0) {$bg="background:#eaeaea;";} else {$bg="";}
		if ($row_jenis['IDCust']==' ' || $row_jenis['IDCust']=='') {$IDCust = "&nbsp;";} else {$IDCust=$row_jenis['IDCust'];}
		if ($row_jenis['namaCust']==' ' || $row_jenis['namaCust']=='') {$namaCust = "&nbsp;";} else {$namaCust=$row_jenis['namaCust'];}
		if ($row_jenis['Alamat']==' ' || $row_jenis['Alamat']=='') {$Alamat = "&nbsp;";} else {$Alamat=$row_jenis['Alamat'];}
		if ($row_jenis['Kota']==' ' || $row_jenis['Kota']=='') {$Kota = "&nbsp;";} else {$Kota=$row_jenis['Kota'];}
		if ($row_jenis['HP1']==' ' || $row_jenis['HP1']=='') {$HP1 = "&nbsp;";} else {$HP1=$row_jenis['HP1'];}
		if ($row_jenis['noID']==' ' || $row_jenis['noID']=='') {$noID = "&nbsp;";} else {$noID=$row_jenis['noID'];}
		$xml .= "<row id='".$row_jenis['IDCust']."'>";
		$xml .= "<cell><![CDATA[<div style='padding: 5px 0px;$bg'><input type='checkbox' id='chk-1' name='id[]' value='".$row_jenis['IDCust']."'></div>]]></cell>";
		$xml .= "<cell><![CDATA[<div style='padding: 5px;$bg'>".utf8_encode($IDCust)."</div>]]></cell>";
		$xml .= "<cell><![CDATA[<div style='padding: 5px;$bg'>".utf8_encode($namaCust)."</div>]]></cell>";
		$xml .= "<cell><![CDATA[<div style='padding: 5px;$bg'>".datenull($row_jenis['TglLahir'])."</div>]]></cell>";
		$xml .= "<cell><![CDATA[<div style='padding: 5px;$bg'>".utf8_encode($Alamat)."</div>]]></cell>";
		$xml .= "<cell><![CDATA[<div style='padding: 5px;$bg'>".utf8_encode($Kota)."</div>]]></cell>";
		$xml .= "<cell><![CDATA[<div style='padding: 5px;$bg'>".utf8_encode($HP1)."</div>]]></cell>";
		$xml .= "<cell><![CDATA[<div style='padding: 5px;$bg'>".utf8_encode($noID)."</div>]]></cell>";
		$xml .= "</row>";
		$total=$row_jenis['total'];
		$no++;
	}
	$xml .= "<total>".$total."</total>";	
	$xml .= "</rows>";
	echo $xml;
?>