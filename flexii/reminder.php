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
	if (isset($_REQUEST['aAwal'])) {
		$aAwal = $_REQUEST['aAwal'];
	} else {
		$aAwal=date('Y-m-01');
	}
    if (isset($_REQUEST['aAkhir'])) {
    	$aAkhir = $_REQUEST['aAkhir'];
    } else {
    	$aAkhir=date('Y-m-t');
    } 
	if (!$sortname) $sortname = 'Follow_Up_Date';
	if (!$sortorder) $sortorder = 'asc';
	
	header("Content-type: text/xml");
	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$xml .= "<rows>";
	$xml .= "<page>$page</page>";
	$no=1;
	$params = array('user' => $user,'page' => $page,'rp'=>$rp,'sortname'=>$sortname,'sortorder'=>$sortorder,'aAwal'=>$aAwal,'aAkhir'=>$aAkhir);
	$client = new nusoap_client(URL_SERVICE2."parameter.php");
	$results = $client->call("ReportReminderx",$params);
	// echo $client->response;
	$total = 0;
	//echo "<pre>";
	//print_r($results);
	foreach($results as $row_jenis) {
		$xml .= "<row id='".$row_jenis['Nomor_Rangka']."'>";
		$xml .= "<cell><![CDATA[<input type='checkbox' id='IdUser' name='id[]' value='".$row_jenis['Nomor_Rangka']."'>]]></cell>";
		$xml .= "<cell><![CDATA[".$row_jenis['Nomor_Rangka']."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['tglJual'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['Mobil_lama_yang_dijual_Brand'])."]]></cell>"; 
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['Mobil_lama_yang_dijual_Model'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['Mobil_lama_yang_dijual_Variant'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['Mobil_lama_yang_dijual_Transmisi'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['Mobil_lama_yang_dijual_Bahan_Bakar'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['NamaCust'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['Propinsi'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['kota'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['Kelurahan'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['AlamatCust'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['HpCust'])."]]></cell>"; 
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['TglFU'])."]]></cell>"; 
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['TglBook'])."]]></cell>"; 
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['TglWO'])."]]></cell>"; 
		$xml .= "</row>"; 
		$total+=1;
		$no++;
	}
	$xml .= "<total>".$total."</total>";	
	$xml .= "</rows>";
	echo $xml;
?>