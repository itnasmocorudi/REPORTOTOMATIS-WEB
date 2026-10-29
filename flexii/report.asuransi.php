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
		$a = explode("/", $_REQUEST['aAwal']); $aAwal = $a[2]."-".$a[1]."-".$a[0];
	} else {
		$aAwal=date('Y-m-01');
	}
    if (isset($_REQUEST['aAkhir'])) {
    	$b = explode("/", $_REQUEST['aAkhir']); $aAkhir = $b[2]."-".$b[1]."-".$b[0];
    } else {
    	$aAkhir=date('Y-m-t');
    }
    $a = explode("/", $_REQUEST['bAwal']); $bAwal = $a[2]."-".$a[1]."-".$a[0];
    $b = explode("/", $_REQUEST['bAkhir']); $bAkhir = $b[2]."-".$b[1]."-".$b[0];

    $kodeDealer = $_REQUEST['kodeDealer'];
    $statusAsuransi = str_replace("|", "", $_REQUEST['statusAsuransi']);
    $statusCust = str_replace("|", "'", $_REQUEST['statusCust']);
    $NamaAsuransi = str_replace("|", "'", $_REQUEST['NamaAsuransi']);

	if (!$sortname) $sortname = 'a.TglEntry';
	if (!$sortorder) $sortorder = 'asc';
	
	header("Content-type: text/xml");
	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$xml .= "<rows>";
	$xml .= "<page>$page</page>";
	$no=1;
	$params = array('user' => $user,'page' => $page,'rp'=>$rp,'sortname'=>$sortname,'sortorder'=>$sortorder,'aAwal'=>$aAwal,'aAkhir'=>$aAkhir,'kodeDealer'=>$kodeDealer,'statusAsuransi'=>$statusAsuransi,'statusCust'=>$statusCust,'bAwal'=>$bAwal,'bAkhir'=>$bAkhir,'NamaAsuransi'=>$NamaAsuransi);
	$client = new nusoap_client(URL_SERVICE2."parameter2.php");
	$results = $client->call("ReportAsuransix",$params);
	// echo $client->response;
	foreach($results as $row_jenis) {
		$xml .= "<row id='".$row_jenis['NoSPK']."'>";
		$xml .= "<cell><![CDATA[".$no."]]></cell>";
		$xml .= "<cell><![CDATA[".$row_jenis['NoSPK']."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['kodeDealer'])." | ".utf8_encode($row_jenis['NamaDealer'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['NamaAsuransi'])."]]></cell>";
		$xml .= "<cell><![CDATA[".$row_jenis['noPolis']."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['statusAsuransi'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['alasanDitolak'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['NamaTertanggung'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['telp_rumahTT'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['AlamatTT'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['KotaTT'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['HP1TT'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['HP2TT'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['Kode_type'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['Kode_warna'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['NamaSales'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['noHPSales'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['noRangka'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['noMesin'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['thnUnit'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['hargaOTR'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['pddFinal'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['banjir'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['gempa'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['srcc'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['teroris'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['tanggungP3'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['celakaDiri'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['tenorAsuransi'])."]]></cell>";
		$xml .= "<cell><![CDATA[".number_format($row_jenis['jenisAsuransi'],0,",",".")."]]></cell>";
		$xml .= "<cell><![CDATA[".number_format($row_jenis['jenisAsuransi2'],0,",",".")."]]></cell>";
		$xml .= "<cell><![CDATA[".number_format($row_jenis['jenisAsuransi3'],0,",",".")."]]></cell>";
		$xml .= "<cell><![CDATA[".number_format($row_jenis['jenisAsuransi4'],0,",",".")."]]></cell>";
		$xml .= "<cell><![CDATA[".number_format($row_jenis['jenisAsuransi5'],0,",",".")."]]></cell>";
		$xml .= "<cell><![CDATA[".number_format($row_jenis['jenisAsuransi6'],0,",",".")."]]></cell>";
		$xml .= "<cell><![CDATA[".number_format($row_jenis['jenisAsuransi7'],0,",",".")."]]></cell>";
		$xml .= "</row>";
		$total=$row_jenis['total'];
		$no++;
	}
	$xml .= "<total>".$total."</total>";	
	$xml .= "</rows>";
	echo $xml;
?>