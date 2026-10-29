<?php
	$host = '10.10.10.181';
	$user = "nis";
	$pass = "N3wR4tn4M0tor";
	$db  = 'oss';
	

	$conn = mssql_connect($host,$user,$pass) or die("Connection Failed");
	mssql_select_db($db,$conn);
?>