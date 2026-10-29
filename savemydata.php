<?php
	// echo "string";
	session_start();
	require_once "./inc/fungsi_all.php";
	//require_once "./lib/nusoap.php";
	
	require_once("soap.php");
	$client = new UserClient; 

	include 'permission.php';
	global $conn;
	$user=$_SESSION['UserId'];
	$ip_address = $_SERVER['REMOTE_ADDR'];
	function prepareImageDBString($filepath) {
		$out='null';
		$handle = @fopen($filepath, 'rb');
		if ($handle) {
			$content = @fread($handle, filesize($filepath));
			$content = bin2hex($content);
			@fclose($handle);
			$out=$content;
		}
		return $out;
	}


	$post = isset($_POST['post']) ? $_POST['post'] : null;
	if ($post=='followup') {
 
		$norangka=str_replace("'", "`", $_POST['norangka']);
		$namacustomer=str_replace("'", "`", $_POST['namacustomer']);
		$terhubung=str_replace("'", "`", $_POST['terhubung']);
		$alamat=str_replace("'", "`", $_POST['alamat']);
		$booking=str_replace("'", "`", $_POST['booking']);
		$nopolisi=str_replace("'", "`", $_POST['nopolisi']);
		$nohp=str_replace("'", "`", $_POST['nohp']);
		$idCust=str_replace("'", "`", $_POST['idCust']);
		$model=str_replace("'", "`", $_POST['model']);
		$varian=str_replace("'", "`", $_POST['varian']);
		$alasan=str_replace("'", "`", $_POST['alasan']);  
        $params = array('user'=>$user,'norangka' => $norangka,'nopolisi'=>$nopolisi,'idcust'=>$idCust,'namacust'=>$namacustomer,'alamatcust'=>$alamat,'hpcust'=>$nohp,'mdl'=>$model,'descmdl'=>$model . " " . $varian,'terhubung'=>$terhubung,'booking'=>$booking,'alasan'=>$alasan);
		//echo "<pre>";
		//print_r($params);
		$client = new nusoap_client(URL_SERVICE2."parameter.php");
		$result = $client->call("followup",$params);/**/
		//echo $client->response;
		echo "<script type='text/javascript'> onload =function(){ alert('".$result."');document.location.href='home.php?page=reminder';}</script>";
	} else if ($post=='lead') {
		echo "<pre>";

		$norangka=str_replace("'", "`", $_POST['norangka']);   
		$namacustomer=str_replace("'", "`", $_POST['namacustomer']);
		$notelepon=str_replace("'", "`", $_POST['notelepon']);
		$model=str_replace("'", "`", $_POST['model']);
		$nopolisi=str_replace("'", "`", $_POST['nopolisi']);
		$tahun=str_replace("'", "`", $_POST['tahun']);  
        $r=explode("#", $_POST['kodedealer']); 
        $kodedb=$r[0];
        $kodedealer=$r[1];

        $params = array('norangka' =>$norangka,'namacustomer' =>$namacustomer,'notelepon' =>$notelepon,'model' =>$model,'nopolisi' =>$nopolisi,'tahun' =>$tahun,'kodedb' =>$kodedb,'kodedealer' =>$kodedealer,'nik'=>$user,'nama'=>$_SESSION['NamaUser']);
		/*print_r($params);*/

		
		$client = new nusoap_client(URL_SERVICE2."parameter.php");
		$result = $client->call("leads",$params);/**/
		//echo $client->response;
		echo "<script type='text/javascript'> onload =function(){ alert('".$result."');document.location.href='home.php?page=lead';}</script>";
	} else if ($post=='add-user') {
		$KodeUser=strtoupper(str_replace("'", "`", $_POST['nik']));
        $NamaUser=strtoupper(str_replace("'", "`", $_POST['nama']));
        $password=strtoupper(str_replace("'", "`", $_POST['password'])); 
        $Uconfig=isset($_POST['Uconfig']) ? $_POST['Uconfig'] : 0;
        $masteremail=isset($_POST['masteremail']) ? $_POST['masteremail'] : 0;
        $masterperiode=isset($_POST['masterperiode']) ? $_POST['masterperiode'] : 0;
        $generatemanual=isset($_POST['generatemanual']) ? $_POST['generatemanual'] : 0; 

        $params = array('KodeUser'=>$KodeUser,'NamaUser'=>$NamaUser,'password'=>$password,'Uconfig'=>$Uconfig,'masteremail'=>$masteremail,'masterperiode'=>$masterperiode,'generatemanual'=>$generatemanual,'UserEntry'=>$user);
		//$client = new nusoap_client(URL_SERVICE2."parameter.php");
		//$result = $client->call("addUserx",$params);
		$result = $client->AddUser($params);
		echo "<script type='text/javascript'> onload =function(){ alert('".$result."');document.location.href='home.php?page=user';}</script>";
	} else if ($post=='edit-user') {
		$KodeUser=strtoupper(str_replace("'", "`", $_POST['nik']));
        $NamaUser=strtoupper(str_replace("'", "`", $_POST['nama'])); 
        $Uconfig=isset($_POST['Uconfig']) ? $_POST['Uconfig'] : 0;
        $masteremail=isset($_POST['masteremail']) ? $_POST['masteremail'] : 0;
        $masterperiode=isset($_POST['masterperiode']) ? $_POST['masterperiode'] : 0;
        $generatemanual=isset($_POST['generatemanual']) ? $_POST['generatemanual'] : 0;  
		
        $params = array('KodeUser'=>$KodeUser,'NamaUser'=>$NamaUser,'Uconfig'=>$Uconfig,'masteremail'=>$masteremail,'masterperiode'=>$masterperiode,'generatemanual'=>$generatemanual,'UserEntry'=>$user);
        /*print_r($params);*/
        
		//$client = new nusoap_client(URL_SERVICE2."parameter.php");
		//$result = $client->call("simpanEditUserx",$params);
		// echo $client->response;
		$result = $client->EditUser($params);

		echo "<script type='text/javascript'> onload =function(){ alert('".$result."');document.location.href='home.php?page=user';}</script>";
		
	} else if ($post=='delete-user') {
		$KodeUser=str_replace("'", "`", $_POST['KodeUser']);
        $params = array('KodeUser'=>$KodeUser,'UserEntry'=>$user);
        /*print_r($params);*/
        
		$result = $client->DeleteUser($params);

		//$client = new nusoap_client(URL_SERVICE2."parameter.php");
		//$result = $client->call("deleteUserx",$params);
		//echo $result;
		echo "<script type='text/javascript'> onload =function(){ alert('".$result."');document.location.href='home.php?page=user';}</script>";
		
	} else if ($post=='add-email') {
		$email=strtoupper(str_replace("'", "`", $_POST['email']));
        $NamaUser=strtoupper(str_replace("'", "`", $_POST['nama']));
        $tipe=strtoupper(str_replace("'", "`", $_POST['tipe']));  

        $params = array('email'=>$email,'NamaUser'=>$NamaUser,'tipe'=>$tipe,'UserEntry'=>$user);
		//$client = new nusoap_client(URL_SERVICE2."parameter.php");
		//$result = $client->call("addUserx",$params);
		$result = $client->AddEmail($params);
		echo "<script type='text/javascript'> onload =function(){ alert('".$result."');document.location.href='home.php?page=email';}</script>";
	} else if ($post=='edit-email') {
		$email=strtoupper(str_replace("'", "`", $_POST['email']));
        $NamaUser=strtoupper(str_replace("'", "`", $_POST['nama']));
        $tipe=strtoupper(str_replace("'", "`", $_POST['tipe']));  
		
        $params = array('email'=>$email,'NamaUser'=>$NamaUser,'tipe'=>$tipe,'UserEntry'=>$user);
        /*print_r($params);*/
        
		//$client = new nusoap_client(URL_SERVICE2."parameter.php");
		//$result = $client->call("simpanEditUserx",$params);
		// echo $client->response;
		$result = $client->EditEmail($params);

		echo "<script type='text/javascript'> onload =function(){ alert('".$result."');document.location.href='home.php?page=email';}</script>";
		
	} else if ($post=='delete-email') {
		$email=str_replace("'", "`", $_POST['email']);
        $params = array('email'=>$email,'UserEntry'=>$user);
        /*print_r($params);*/
        
		$result = $client->DeleteEmail($params);

		//$client = new nusoap_client(URL_SERVICE2."parameter.php");
		//$result = $client->call("deleteUserx",$params);
		//echo $result;
		echo "<script type='text/javascript'> onload =function(){ alert('".$result."');document.location.href='home.php?page=email';}</script>";
		
	} else if ($post=='add-periode') {
		$tglAwal=strtoupper(str_replace("'", "`", $_POST['tglAwal']));
        $tglAkhir=strtoupper(str_replace("'", "`", $_POST['tglAkhir']));
        $tglKirim=strtoupper(str_replace("'", "`", $_POST['tglKirim']));  
        $tipe=strtoupper(str_replace("'", "`", $_POST['tipe']));  

        $params = array('tipe'=>$tipe,'tglAwal'=>$tglAwal,'tglAkhir'=>$tglAkhir,'tglKirim'=>$tglKirim,'UserEntry'=>$user);
		//$client = new nusoap_client(URL_SERVICE2."parameter.php");
		//$result = $client->call("addUserx",$params);
		$result = $client->AddPeriode($params);
		echo "<script type='text/javascript'> onload =function(){ alert('".$result."');document.location.href='home.php?page=periode';}</script>";
	} else if ($post=='edit-periode') {
		$tglAwal=strtoupper(str_replace("'", "`", $_POST['tglAwal']));
        $tglAkhir=strtoupper(str_replace("'", "`", $_POST['tglAkhir']));
        $tglKirim=strtoupper(str_replace("'", "`", $_POST['tglKirim']));  
        $tipe=strtoupper(str_replace("'", "`", $_POST['tipe']));    
        $idperiode=strtoupper(str_replace("'", "`", $_POST['idperiode']));  

		$params = array('idperiode'=>$idperiode, 'tipe'=>$tipe,'tglAwal'=>$tglAwal,'tglAkhir'=>$tglAkhir,'tglKirim'=>$tglKirim,'UserEntry'=>$user);
        /*print_r($params);*/
        
		//$client = new nusoap_client(URL_SERVICE2."parameter.php");
		//$result = $client->call("simpanEditUserx",$params);
		// echo $client->response;
		$result = $client->EditPeriode($params);

		echo "<script type='text/javascript'> onload =function(){ alert('".$result."');document.location.href='home.php?page=periode';}</script>";
		
	} else if ($post=='delete-periode') {
		$idperiode=str_replace("'", "`", $_POST['idperiode']);
        $params = array('idperiode'=>$idperiode,'UserEntry'=>$user);
        /*print_r($params);*/
        
		$result = $client->DeletePeriode($params);

		//$client = new nusoap_client(URL_SERVICE2."parameter.php");
		//$result = $client->call("deleteUserx",$params);
		//echo $result;
		echo "<script type='text/javascript'> onload =function(){ alert('".$result."');document.location.href='home.php?page=periode';}</script>";
		
	} else if ($post=='add-manual') {
		$tahun=strtoupper(str_replace("'", "`", $_POST['tahun']));
        $bulan=strtoupper(str_replace("'", "`", $_POST['bulan']));
        $tglKirim=strtoupper(str_replace("'", "`", $_POST['tglKirim']));  
        $tipe=strtoupper(str_replace("'", "`", $_POST['tipe'])); 
        $dealer=strtoupper(str_replace("'", "`", $_POST['dealer']));   
        $tglAkhir=strtoupper(str_replace("'", "`", $_POST['tglAkhir']));  
        $tglAwal=strtoupper(str_replace("'", "`", $_POST['tglAwal']));  

        $params = array('tipe'=>$tipe,'dealer'=>$dealer,'tahun'=>$tahun,'bulan'=>$bulan,'tglKirim'=>$tglKirim,'UserEntry'=>$user,'tglAwal'=>$tglAwal,'tglAkhir'=>$tglAkhir);
		//$client = new nusoap_client(URL_SERVICE2."parameter.php");
		//$result = $client->call("addUserx",$params);
		//print_r($params);
		$result = $client->AddManual($params);
		//echo $result;
		echo "<script type='text/javascript'> onload =function(){ alert('".$result."');document.location.href='home.php?page=manual';}</script>";
	}


	function npwp($num){
        $num = $num."000000000000000";
        $char1 = substr($num, 0,2);
        $char2 = substr($num, 2,3);
        $char3 = substr($num, 5,3);
        $char4 = substr($num, 8,1);
        $char5 = substr($num, 9,3);
        $char6 = substr($num, 12,3);
        $dt = $char1.".".$char2.".".$char3.".".$char4."-".$char5.".".$char6;
        return $dt;
    }
?>