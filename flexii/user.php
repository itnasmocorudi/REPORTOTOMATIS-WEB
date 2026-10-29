<?php
	session_start(); 
	error_reporting(0);

	require_once ("../inc/fungsi_all.php"); 
	require_once("../soap.php");
	
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
	$rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
	$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'IdUser';
	$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'asc';
	$query = isset($_POST['query']) ? $_POST['query'] : false;
	$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

	$page = $_POST['page'];
	$rp = $_POST['rp'];
	$sortname = $_POST['sortname'];
	$sortorder = $_POST['sortorder'];

	if (!$sortname) $sortname = 'IdUser';
	if (!$sortorder) $sortorder = 'asc';
	
	header("Content-type: text/xml");
	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$xml .= "<rows>";
	$xml .= "<page>$page</page>";
	$no=0;
	$params = array('page' => $page,'rp'=>$rp,'sortname'=>$sortname,'sortorder'=>$sortorder); 
	
	$client = new UserClient; 
	//print_r($params);
	$result = $client->GetUserx($params);

	//$client = new nusoap_client(URL_SERVICE2."parameter.php");
	//$results = $client->call("userx",$params);
	//echo $client->response;
	$results = json_decode($result,true); 
	//print_r($results);
	foreach($results as $row_jenis) { 
		$xml .= "<row id='".$row_jenis['IdUser']."'>";
		$xml .= "<cell><![CDATA[<input type='checkbox' id='IdUser' name='id[]' value='".$row_jenis['IdUser']."'>]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['IdUser'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['namaUser'])."]]></cell>"; 
		if(utf8_encode($row_jenis['Uconfig']) == "1"){
			$xml .= "<cell><![CDATA[<button type='button' class='btn btn-block bg-gradient-success btn-xs'>Aktif</button>]]></cell>";
		}else{
			$xml .= "<cell><![CDATA[<button type='button' class='btn btn-block bg-gradient-danger btn-xs'>Non Aktif</button>]]></cell>";
		}
		if(utf8_encode($row_jenis['masteremail']) == "1"){
			$xml .= "<cell><![CDATA[<button type='button' class='btn btn-block bg-gradient-success btn-xs'>Aktif</button>]]></cell>";
		}else{
			$xml .= "<cell><![CDATA[<button type='button' class='btn btn-block bg-gradient-danger btn-xs'>Non Aktif</button>]]></cell>";
		}
		if(utf8_encode($row_jenis['masterperiode']) == "1"){
			$xml .= "<cell><![CDATA[<button type='button' class='btn btn-block bg-gradient-success btn-xs'>Aktif</button>]]></cell>";
		}else{
			$xml .= "<cell><![CDATA[<button type='button' class='btn btn-block bg-gradient-danger btn-xs'>Non Aktif</button>]]></cell>";
		}
		if(utf8_encode($row_jenis['generatemanual']) == "1"){
			$xml .= "<cell><![CDATA[<button type='button' class='btn btn-block bg-gradient-success btn-xs'>Aktif</button>]]></cell>";
		}else{
			$xml .= "<cell><![CDATA[<button type='button' class='btn btn-block bg-gradient-danger btn-xs'>Non Aktif</button>]]></cell>";
		}
		$xml .= "</row>"; 
		$no++;
	}
	$xml .= "<total>".$no."</total>";	
	$xml .= "</rows>";
	echo $xml;
?>