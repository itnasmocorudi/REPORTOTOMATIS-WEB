<?php
 
	$uConfig = 0; 
	$masteremail = 0;
	$masterperiode = 0;
	$generatemanual = 0; 
	$denie = "<script>document.location.href='logout.php'</script>";
	if($_SESSION['UserId'] && $_SESSION['PassUser'] && $_SESSION['NamaUser'] && $_SESSION['timeout']) {
		 if(!isset($_SESSION)) 
	    { 
	        session_start(); 
	    } 
		$inactive = 1800; 
		$session_life = time() - $_SESSION['timeout'];
		if($session_life > $inactive){
			session_destroy(); 
			echo $denie;
		} else {
			$_SESSION['timeout']=time();
		}

		$IdUser=$_SESSION['UserId'];  
		$page = isset($_GET['page']) ? $_GET['page'] : null;  
		$uConfig = $_SESSION['Uconfig']; 
		$masteremail = $_SESSION['masteremail'];
		$masterperiode = $_SESSION['masterperiode'];
		$generatemanual = $_SESSION['generatemanual']; 
		 
		if ($uConfig==0)  {
			if ($page=='user' or $page=='user.mod') {
				echo $denie; 
			}  
		}
		if ($masteremail==0)  {
			if ($page=='email' or $page=='email.mod') {
				echo $denie; 
			}  
		}
		if ($masterperiode==0)  {
			if ($page=='periode' or $page=='periode.mod') {
				echo $denie; 
			}  
		}
		if ($generatemanual==0)  {
			if ($page=='manual' or $page=='manual.mod') {
				echo $denie; 
			}  
		}
	} else { 
		echo $denie;
	}
?>