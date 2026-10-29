<?php
	session_start(); 
	error_reporting(0);

	require_once ("../inc/fungsi_all.php");
	require_once "../lib/nusoap.php";
	
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
	$rp = isset($_POST['rp']) ? $_POST['rp'] : 20;
	$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'L.TglEntry';
	$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'asc';
	$query = isset($_POST['query']) ? $_POST['query'] : false;
	$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

	$page = $_POST['page'];
	$rp = $_POST['rp'];
	$sortname = $_POST['sortname'];
	$sortorder = $_POST['sortorder'];
	$aAwal=$_GET['aAwal'];
    $aAkhir=$_GET['aAkhir'];
    $bAwal=$_GET['bAwal'];
    $bAkhir=$_GET['bAkhir'];

	if (!$sortname) $sortname = 'L.TglEntry';
	if (!$sortorder) $sortorder = 'asc';
	
	header("Content-type: text/xml");
	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$xml .= "<rows>";
	$xml .= "<page>$page</page>";
	$no=1;
	// $params = array('page' => $page,'rp'=>$rp,'sortname'=>$sortname,'sortorder'=>$sortorder,'Awal'=>$Awal,'Akhir'=>$Akhir); 
	$params = array('page' => $page,'rp'=>$rp,'sortname'=>$sortname,'sortorder'=>$sortorder,'aAwal'=>$aAwal,'aAkhir'=>$aAkhir,'bAwal'=>$bAwal,'bAkhir'=>$bAkhir);
	$client = new nusoap_client(URL_SERVICE2."parameter3.php");
	$results = $client->call("HistoryKreditX",$params);
	foreach($results as $row_jenis) {
		$xml .= "<row id='".$row_jenis['NoSPK']."'>";
		$xml .= "<cell><![CDATA[<input type='checkbox' name='id' value='".$row_jenis['NoSPK']."'>]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['kodeDealer'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['NoSPK'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['statusCust'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['NamaPemesan'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['Alamat'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['Kota'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['telp_rumah'].' , '.$row_jenis['hp1'].' , '.$row_jenis['hp2'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['NamaDebitur'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['AlamatDebitur'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['kotaDebitur'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['telp_rumahDebitur'].' , '.$row_jenis['hp1Debitur'].' , '.$row_jenis['hp2Debitur'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['Kode_type'].' '.$row_jenis['NamaKategory'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['Kode_warna'].' '.$row_jenis['NamaEnglish'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['hargOTR'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['noRangka'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['noMesin'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['thnUnit'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['PddFinal'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['NamaSales'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['jenisAsuransi'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['Banjir'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['Gempa'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['SRCC'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['Teroris'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['TanggungP3'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['CelakaDiri'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['tenorAsuransi'])."]]></cell>";
		$xml .= "</row>";
		$total=$row_jenis['total'];
		$no++;
	}
	$xml .= "<total>".$total."</total>";	
	$xml .= "</rows>";
	echo $xml;
?>