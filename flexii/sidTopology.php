<?php
	session_start(); 
	error_reporting(0);

	require_once ("../inc/fungsi_all.php");
	require_once "../lib/nusoap.php";
	
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
	$rp = isset($_POST['rp']) ? $_POST['rp'] : 20;
	$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'id';
	$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'asc';
	$query = isset($_POST['query']) ? $_POST['query'] : false;
	$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

	$page = $_POST['page'];
	$rp = $_POST['rp'];
	$sortname = $_POST['sortname'];
	$sortorder = $_POST['sortorder'];
	
	$pro = $_REQUEST['pro'];
	$kot = $_REQUEST['kot'];
	$kec = $_REQUEST['kec'];
	$kel = $_REQUEST['kel'];

	if (!$sortname) $sortname = 'id';
	if (!$sortorder) $sortorder = 'asc';
	
	header("Content-type: text/xml");
	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$xml .= "<rows>";
	$xml .= "<page>$page</page>";
	$no=1;
	$params = array('user' => $user,'page' => $page,'rp'=>$rp,'sortname'=>$sortname,'sortorder'=>$sortorder,'pro'=>$pro,'kot'=>$kot,'kec'=>$kec,'kel'=>$kel);
	$client = new nusoap_client(URL_SERVICE2."parameter12.php");
	$results = $client->call("sidTopology",$params);
	// echo $client->response;
	foreach($results as $row_jenis) {
		if ($no %2 == 0) {$bg="background:#eaeaea;";} else {$bg="";}
		if ($row_jenis['Propinsi']==' ' || $row_jenis['Propinsi']=='') {$Propinsi = "&nbsp;";} else {$Propinsi=$row_jenis['Propinsi'];}
		if ($row_jenis['Kota']==' ' || $row_jenis['Kota']=='') {$Kota = "&nbsp;";} else {$Kota=$row_jenis['Kota'];}
		if ($row_jenis['Kecamatan']==' ' || $row_jenis['Kecamatan']=='') {$Kecamatan = "&nbsp;";} else {$Kecamatan=$row_jenis['Kecamatan'];}
		if ($row_jenis['Kelurahan']==' ' || $row_jenis['Kelurahan']=='') {$Kelurahan = "&nbsp;";} else {$Kelurahan=$row_jenis['Kelurahan'];}
		if ($row_jenis['KodePos']==' ' || $row_jenis['KodePos']=='') {$KodePos = "&nbsp;";} else {$KodePos=$row_jenis['KodePos'];}
		$xml .= "<row id='".$row_jenis['id']."'>";
		$xml .= "<cell><![CDATA[<div style='padding: 2px 0px;$bg'><input type='checkbox' id='chk-1' name='id[]' value='".$row_jenis['Propinsi']."#".$row_jenis['Kota']."#".$row_jenis['Kecamatan']."#".$row_jenis['Kelurahan']."#".$row_jenis['KodePos']."'></div>]]></cell>";
		$xml .= "<cell><![CDATA[<div style='padding: 2px;$bg'>".utf8_encode($Propinsi)."</div>]]></cell>";
		$xml .= "<cell><![CDATA[<div style='padding: 2px;$bg'>".utf8_encode($Kota)."</div>]]></cell>";
		$xml .= "<cell><![CDATA[<div style='padding: 2px;$bg'>".utf8_encode($Kecamatan)."</div>]]></cell>";
		$xml .= "<cell><![CDATA[<div style='padding: 2px;$bg'>".utf8_encode($Kelurahan)."</div>]]></cell>";
		$xml .= "<cell><![CDATA[<div style='padding: 2px;$bg'>".utf8_encode($KodePos)."</div>]]></cell>";
		$xml .= "</row>";
		$total=$row_jenis['total'];
		$no++;
	}
	$xml .= "<total>".$total."</total>";	
	$xml .= "</rows>";
	echo $xml;
?>