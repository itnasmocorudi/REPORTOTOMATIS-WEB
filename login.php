<?php
	session_start();

ini_set('soap.wsdl_cache_enabled', '0');
	require_once "./inc/fungsi_all.php"; 
require_once("soap.php");

	$id = $_POST['username'];$id=str_replace("'", "`", $id);
	$ip_address = $_SERVER['REMOTE_ADDR'];
	$password=$_POST['password'];$password=str_replace("'", "`", $password);
	$noka=$_POST['noka'];$noka=str_replace("'", "`", $noka);
	//$params = array('id' => $id,'password' => $password, 'ip_address' => $ip_address);
	$params = array( $id, $password, $ip_address);
	//echo URL_SERVICE2."parameter.php";
	//print_r($client); 
	$client = new UserClient; 
	
	try {
 
	 // $client = new nusoap_client(URL_SERVICE2."parameter.php");
 
	    	$tambah = "";
		//$result = $client->call("loginx",$params); 
		 
		$result = $client->LoginUser($params);
 		//print_r($result);
		//var_dump($result);
		//echo HTTP_API_SERVER;
	    $obj = json_decode($result,true); 
	    if($obj[0] == 1){ 
		    if($noka != ""){
		    	$tambah = "?frameno=".$noka;
		    }
			$_SESSION['UserId']=$id;
			$_SESSION['PassUser']=$password;
			$_SESSION['NamaUser']=$obj['data'][1];
			$_SESSION['masteremail']=$obj['data']['masteremail'];
			$_SESSION['Uconfig']=$obj['data']['Uconfig'];
			$_SESSION['masterperiode']=$obj['data']['masterperiode'];
			$_SESSION['generatemanual']=$obj['data']['generatemanual'];
			$_SESSION['timeout']=time();
			//print_r($_SESSION);
			echo "<script language=javascript>document.location.href='home.php'</script> ";
		}
		else
		{
			/**/
			if($tambah != ""){
				echo "<script>alert('Username / password tidak cocok.');document.location.href='signin.php$tambah'</script>";
			}else{
				echo "<script>alert('Username / password tidak cocok.');document.location.href='logout.php'</script>";
			}
			
		}
	} catch (Exception $e) {
	  echo "<pre>Exception: ".print_r($e, true)."</pre>\n";
	}
	
	/*$r=explode("-", $result);
	if ($r[0]>0){
		$_SESSION['IdUser']=$id;
		$_SESSION['passWord']=$r[2];
		$_SESSION['namaUser']=$r[3];
		$_SESSION['tipe']=$r[4];
		$_SESSION['timeout']=time();
		echo "<script language=javascript>document.location.href='home.php'</script> ";
	}
	else
	{
		echo "<script>document.location.href='logout.php'</script>";
	}*/
?>