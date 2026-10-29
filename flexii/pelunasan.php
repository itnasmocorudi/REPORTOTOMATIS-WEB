<?php
	session_start(); 
	error_reporting(0);

	require_once "../permission.php";
	// require_once ("../inc/fungsi_all.php");
	// require_once "../lib/nusoap.php";
	
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
	$rp = isset($_POST['rp']) ? $_POST['rp'] : 20;
	$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'L.TglPO';
	$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'desc';
	$query = isset($_POST['query']) ? $_POST['query'] : false;
	$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

	$page = $_POST['page'];
	$rp = $_POST['rp'];
	$sortname = $_POST['sortname'];
	$sortorder = $_POST['sortorder'];
	
	$user = $IdUser;
	if ($_REQUEST['awal']) {
		$a=explode("/", $_REQUEST['awal']); $Awal = $a[2]."-".$a[1]."-".$a[0];
	} else {
		$Awal=date('Y-m-01');
	}
	if ($_REQUEST['akhir']) {
		$b=explode("/", $_REQUEST['akhir']); $Akhir = $b[2]."-".$b[1]."-".$b[0];
	} else {
		$Akhir=date('Y-m-t');
	}
    $qText = $_REQUEST['qText'];
	$qField = $_REQUEST['qField'];
	$Status=$_REQUEST['stat'];

	if (!$sortname) $sortname = 'L.TglPO';
	if (!$sortorder) $sortorder = 'asc';
	
	header("Content-type: text/xml");
	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$xml .= "<rows>";
	$xml .= "<page>$page</page>";
	$no=1;
	$params = array('user' => $user,'page' => $page,'rp'=>$rp,'sortname'=>$sortname,'sortorder'=>$sortorder,'Awal'=>$Awal,'Akhir'=>$Akhir, 'Status'=>$Status,'qText'=>$qText,'qField'=>$qField); 
	$client = new nusoap_client(URL_SERVICE2."parameter4.php");
	$results = $client->call("Pelunasanx",$params);
	// echo $client->response;
	foreach($results as $row_jenis) {
		$xml .= "<row id='".$row_jenis['NoSPK']."'>";
		$xml .= "<cell><![CDATA[<input type='checkbox' id='NoSPK".$no."' name='id[]' value='".$row_jenis['NoSPK']."'>]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['kodeDealer'])."]]></cell>";
		$xml .= "<cell><![CDATA[
			<a href='#modal-1' data-toggle='modal' onclick='showmeet".$no."(this);' id='klik'>".$row_jenis['NoSPK']."</a>
			<input type='hidden' id='kodeDealer".$no."' value='".$row_jenis['kodeDealer']."'>
			<input type='hidden' id='NamaPemesan".$no."' value='".$row_jenis['NamaPemesan']."'>
			<input type='hidden' id='telp_rumah".$no."' value='".$row_jenis['telp_rumah'].",".$row_jenis['hp1'].",".$row_jenis['hp2']."'>
			<input type='hidden' id='kota".$no."' value='".$row_jenis['Kota']."'>
			<input type='hidden' id='alamat".$no."' value='".$row_jenis['Alamat']."'>
			<input type='hidden' id='namaTertanggung".$no."' value='".$row_jenis['NamaDebitur']."'>
			<input type='hidden' id='AlamatTT".$no."' value='".$row_jenis['AlamatDebitur']."'>
			<input type='hidden' id='kotaTT".$no."' value='".$row_jenis['kotaDebitur']."'>
			<input type='hidden' id='telp_rumahDebitur".$no."' value='".$row_jenis['telp_rumahDebitur']."'>
			<input type='hidden' id='hp1Debitur".$no."' value='".$row_jenis['hp1Debitur']."'>
			<input type='hidden' id='hp2Debitur".$no."' value='".$row_jenis['hp2Debitur']."'>
			<input type='hidden' id='KODE_TYPE".$no."' value='".$row_jenis['Kode_type'].",".$row_jenis['NamaKategory']."'>
			<input type='hidden' id='KODE_WARNA".$no."' value='".$row_jenis['Kode_warna'].",".$row_jenis['NamaEnglish']."'>
			<input type='hidden' id='hargaOTR".$no."' value='".$row_jenis['hargOTR']."'>
			<input type='hidden' id='norangka".$no."' value='".$row_jenis['noRangka']."'>
			<input type='hidden' id='noMesin".$no."' value='".$row_jenis['noMesin']."'>
			<input type='hidden' id='NamaSales".$no."' value='".$row_jenis['NamaSales']."'>
			<script>
			    var previousCheckId;
			    function showmeet".$no."(obj) {
			        if (obj.click) {
						var a".$no." = document.getElementById('NoSPK".$no."').value; document.getElementById('NoSPK').value = a".$no.";
						var b".$no." = document.getElementById('kodeDealer".$no."').value; document.getElementById('kodeDealer').value = b".$no.";
						var c".$no." = document.getElementById('NamaPemesan".$no."').value; document.getElementById('NamaPemesan').value = c".$no.";
						var d".$no." = document.getElementById('telp_rumah".$no."').value; document.getElementById('telp_rumah').value = d".$no.";
						var f".$no." = document.getElementById('kota".$no."').value; document.getElementById('kota').value = f".$no.";
						var g".$no." = document.getElementById('alamat".$no."').value; document.getElementById('alamat').value = g".$no.";
						var h".$no." = document.getElementById('namaTertanggung".$no."').value; document.getElementById('namaTertanggung').value = h".$no.";
						var i".$no." = document.getElementById('AlamatTT".$no."').value; document.getElementById('AlamatTT').value = i".$no.";
						var j".$no." = document.getElementById('kotaTT".$no."').value; document.getElementById('kotaTT').value = j".$no.";
						var ka".$no." = document.getElementById('telp_rumahDebitur".$no."').value; document.getElementById('telp_rumahDebitur').value = ka".$no.";
						var kb".$no." = document.getElementById('hp1Debitur".$no."').value; document.getElementById('hp1Debitur').value = kb".$no.";
						var kc".$no." = document.getElementById('hp2Debitur".$no."').value; document.getElementById('hp2Debitur').value = kc".$no.";
						var l".$no." = document.getElementById('KODE_TYPE".$no."').value; document.getElementById('KODE_TYPE').value = l".$no.";
						var m".$no." = document.getElementById('KODE_WARNA".$no."').value; document.getElementById('KODE_WARNA').value = m".$no.";
						var n".$no." = document.getElementById('hargaOTR".$no."').value; document.getElementById('hargaOTR').value = n".$no.";
						var o".$no." = document.getElementById('norangka".$no."').value; document.getElementById('norangka').value = o".$no.";
						var p".$no." = document.getElementById('noMesin".$no."').value; document.getElementById('noMesin').value = p".$no.";
						var aa".$no." = document.getElementById('NamaSales".$no."').value; document.getElementById('NamaSales').value = aa".$no.";
						document.getElementById('myMeet').style.display = 'block';
					} else {
						document.getElementById('myMeet').style.display = 'none'; 
					}
				}
			</script>
		]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['NamaPemesan'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['Alamat'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['Kota'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['telp_rumah'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['hp1'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['hp2'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['NamaDebitur'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['AlamatDebitur'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['kotaDebitur'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['telp_rumahDebitur'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['hp1Debitur'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['hp2Debitur'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['Kode_type'].' '.$row_jenis['NamaKategory'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['Kode_warna'].' '.$row_jenis['NamaEnglish'])."]]></cell>";
		$xml .= "<cell><![CDATA[".number_format($row_jenis['hargOTR'],0,",",".")."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['noRangka'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['noMesin'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['NamaSales'])."]]></cell>";
		$xml .= "</row>";
		$total=$row_jenis['total'];
		$no++;
	}
	$xml .= "<total>".$total."</total>";	
	$xml .= "</rows>";
	echo $xml;
?>