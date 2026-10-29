<?php
	session_start(); 
	error_reporting(0);

	require_once ("../inc/fungsi_all.php");
	require_once "../lib/nusoap.php";
	
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
	$rp = isset($_POST['rp']) ? $_POST['rp'] : 20;
	$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'a.TglEntry';
	$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'desc';
	$query = isset($_POST['query']) ? $_POST['query'] : false;
	$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

	$page = $_POST['page'];
	$rp = $_POST['rp'];
	$sortname = $_POST['sortname'];
	$sortorder = $_POST['sortorder'];
	$user=$_GET['user'];
	$aAwal=$_GET['aAwal'];
    $aAkhir=$_GET['aAkhir'];
    $bAwal=$_GET['bAwal'];
    $bAkhir=$_GET['bAkhir'];

	if (!$sortname) $sortname = 'a.TglEntry';
	if (!$sortorder) $sortorder = 'asc';
	
	header("Content-type: text/xml");
	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$xml .= "<rows>";
	$xml .= "<page>$page</page>";
	$no=1;
	$params = array('user' => $user,'page' => $page,'rp'=>$rp,'sortname'=>$sortname,'sortorder'=>$sortorder,'aAwal'=>$aAwal,'aAkhir'=>$aAkhir,'bAwal'=>$bAwal,'bAkhir'=>$bAkhir);
	$client = new nusoap_client(URL_SERVICE2."parameter2.php");
	$results = $client->call("HistoryAsuransix",$params);
	// echo $client->response;
	foreach($results as $row_jenis) {
		$xml .= "<row id='".$row_jenis['NoSPK']."'>";
		$xml .= "<cell><![CDATA[".$no."]]></cell>";
		$xml .= "<cell><![CDATA[<a href='downPolis.php?id=".$row_jenis['NoSPK']."' target='blank' id='klik'>".$row_jenis['noPolis']."</a>]]></cell>";
		$xml .= "<cell><![CDATA[
			<a onclick='showmeet".$no."(this);' id='klik'>".$row_jenis['NoSPK']."</a>
			<input type='hidden' id='NoSPK".$no."' value='".$row_jenis['NoSPK']."'>
			<input type='hidden' id='statusCust".$no."' value='".$row_jenis['statusCust']."'>
			<input type='hidden' id='NamaPemesan".$no."' value='".$row_jenis['NamaPemesan']."'>
			<input type='hidden' id='telp_rumah".$no."' value='".$row_jenis['telp_rumah'].",".$row_jenis['hp1'].",".$row_jenis['hp2']."'>
			<input type='hidden' id='kota".$no."' value='".$row_jenis['KOTA']."'>
			<input type='hidden' id='alamat".$no."' value='".$row_jenis['ALAMAT']."'>
			<input type='hidden' id='namaTertanggung".$no."' value='".$row_jenis['namaTertanggung']."'>
			<input type='hidden' id='AlamatTT".$no."' value='".$row_jenis['AlamatTT']."'>
			<input type='hidden' id='kotaTT".$no."' value='".$row_jenis['kotaTT']."'>
			<input type='hidden' id='telp_rumahTT".$no."' value='".$row_jenis['telp_rumahTT']."'>
			<input type='hidden' id='hp1TT".$no."' value='".$row_jenis['hp1TT']."'>
			<input type='hidden' id='hp2TT".$no."' value='".$row_jenis['hp2TT']."'>
			<input type='hidden' id='KODE_TYPE".$no."' value='".$row_jenis['KODE_TYPE'].",".$row_jenis['NAMAKATEGORY']."'>
			<input type='hidden' id='KODE_WARNA".$no."' value='".$row_jenis['KODE_WARNA'].",".$row_jenis['namaEnglish']."'>
			<input type='hidden' id='hargaOTR".$no."' value='".number_format($row_jenis['hargaOTR'],0,",",".")."'>
			<input type='hidden' id='norangka".$no."' value='".$row_jenis['norangka']."'>
			<input type='hidden' id='noMesin".$no."' value='".$row_jenis['noMesin']."'>
			<input type='hidden' id='thnUnit".$no."' value='".$row_jenis['thnUnit']."'>
			<input type='hidden' id='pddFinal".$no."' value='".$row_jenis['pddFinal']."'>
			<input type='hidden' id='JenisAsuransi".$no."' value='".$row_jenis['JenisAsuransi']."'>
			<input type='hidden' id='tenorAsuransi".$no."' value='".$row_jenis['tenorAsuransi']."'>
			<input type='hidden' id='banjir".$no."' value='".$row_jenis['banjir']."'>
			<input type='hidden' id='gempa".$no."' value='".$row_jenis['gempa']."'>
			<input type='hidden' id='srcc".$no."' value='".$row_jenis['srcc']."'>
			<input type='hidden' id='teroris".$no."' value='".$row_jenis['teroris']."'>
			<input type='hidden' id='tanggungP3".$no."' value='".number_format($row_jenis['tanggungP3'],0,",",".")."'>
			<input type='hidden' id='celakaDiri".$no."' value='".number_format($row_jenis['celakaDiri'],0,",",".")."'>
			<script>
			    function showmeet".$no."(obj) {
			        if (obj.click) {
			        	var a".$no." = document.getElementById('NoSPK".$no."').value; document.getElementById('NoSPK').value = a".$no.";
			        	var b".$no." = document.getElementById('statusCust".$no."').value; document.getElementById('statusCust').value = b".$no.";
			        	var c".$no." = document.getElementById('NamaPemesan".$no."').value; document.getElementById('NamaPemesan').value = c".$no.";
						var d".$no." = document.getElementById('telp_rumah".$no."').value; document.getElementById('telp_rumah').value = d".$no.";
						var f".$no." = document.getElementById('kota".$no."').value; document.getElementById('kota').value = f".$no.";
						var g".$no." = document.getElementById('alamat".$no."').value; document.getElementById('alamat').value = g".$no.";
						var h".$no." = document.getElementById('namaTertanggung".$no."').value; document.getElementById('namaTertanggung').value = h".$no.";
						var i".$no." = document.getElementById('AlamatTT".$no."').value; document.getElementById('AlamatTT').value = i".$no.";
						var j".$no." = document.getElementById('kotaTT".$no."').value; document.getElementById('kotaTT').value = j".$no.";
						var ka".$no." = document.getElementById('telp_rumahTT".$no."').value; document.getElementById('telp_rumahTT').value = ka".$no.";
						var kb".$no." = document.getElementById('hp1TT".$no."').value; document.getElementById('hp1TT').value = kb".$no.";
						var kc".$no." = document.getElementById('hp2TT".$no."').value; document.getElementById('hp2TT').value = kc".$no.";
						var l".$no." = document.getElementById('KODE_TYPE".$no."').value; document.getElementById('KODE_TYPE').value = l".$no.";
						var m".$no." = document.getElementById('KODE_WARNA".$no."').value; document.getElementById('KODE_WARNA').value = m".$no.";
						var n".$no." = document.getElementById('hargaOTR".$no."').value; document.getElementById('hargaOTR').value = n".$no.";
						var o".$no." = document.getElementById('norangka".$no."').value; document.getElementById('norangka').value = o".$no.";
						var p".$no." = document.getElementById('noMesin".$no."').value; document.getElementById('noMesin').value = p".$no.";
						var q".$no." = document.getElementById('thnUnit".$no."').value; document.getElementById('thnUnit').value = q".$no.";
						var r".$no." = document.getElementById('pddFinal".$no."').value; document.getElementById('pddFinal').value = r".$no.";
						var s".$no." = document.getElementById('JenisAsuransi".$no."').value; document.getElementById('JenisAsuransi').value = s".$no.";
						var t".$no." = document.getElementById('tenorAsuransi".$no."').value; document.getElementById('tenorAsuransi').value = t".$no.";
						var u".$no." = document.getElementById('banjir".$no."').value; if (u".$no."==1) { document.getElementById('banjir').checked = true; } else { document.getElementById('banjir').checked =false; }
						var v".$no." = document.getElementById('gempa".$no."').value; if (v".$no."==1) { document.getElementById('gempa').checked = true; } else { document.getElementById('gempa').checked =false; }
						var w".$no." = document.getElementById('srcc".$no."').value; if (w".$no."==1) { document.getElementById('srcc').checked = true; } else { document.getElementById('srcc').checked =false; }
						var x".$no." = document.getElementById('teroris".$no."').value; if (x".$no."==1) { document.getElementById('teroris').checked = true; } else { document.getElementById('teroris').checked =false; }
						var y".$no." = document.getElementById('tanggungP3".$no."').value; if (y".$no."==1) { document.getElementById('tanggungP3').checked = true; } else { document.getElementById('tanggungP3').checked =false; }
						var z".$no." = document.getElementById('celakaDiri".$no."').value; if (z".$no."==1) { document.getElementById('celakaDiri').checked = true; } else { document.getElementById('celakaDiri').checked =false; }
						document.getElementById('myMeet').style.display = 'block';
					} else {
						document.getElementById('myMeet').style.display = 'none'; 
					}
				}
			</script>
		]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['statusCust'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['NamaPemesan'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['ALAMAT'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['KOTA'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['telp_rumah'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['hp1'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['hp2'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['namaTertanggung'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['AlamatTT'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['kotaTT'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['telp_rumahTT'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['hp1TT'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['hp2TT'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['KODE_TYPE'].' '.$row_jenis['NAMAKATEGORY'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['KODE_WARNA'].' '.$row_jenis['namaEnglish'])."]]></cell>";
		$xml .= "<cell><![CDATA[".number_format($row_jenis['hargaOTR'],0,",",".")."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['norangka'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['noMesin'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['thnUnit'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['pddFinal'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['JenisAsuransi'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['banjir'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['gempa'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['srcc'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['teroris'])."]]></cell>";
		$xml .= "<cell><![CDATA[".number_format($row_jenis['tanggungP3'],0,",",".")."]]></cell>";
		$xml .= "<cell><![CDATA[".number_format($row_jenis['celakaDiri'],0,",",".")."]]></cell>";
		$xml .= "<cell><![CDATA[".number_format($row_jenis['tenorAsuransi'],0,",",".")."]]></cell>";
		$xml .= "</row>";
		$total=$row_jenis['total'];
		$no++;
	}
	$xml .= "<total>".$total."</total>";	
	$xml .= "</rows>";
	echo $xml;
?>