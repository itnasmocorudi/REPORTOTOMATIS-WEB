<?php

ini_set("magic_quotes_gpc","On");
ini_set("magic_quotes_runtime","On");
ini_set("magic_quotes_sybase","On");
ini_set("allow_url_include","On");


date_default_timezone_set('Asia/Jakarta');

	// define("HTTP_API_SERVER","http://10.10.10.71/apivintracker/");
	// define("URN_API_SERVER","http://10.10.10.71/apivintracker/"); // 
	define("HTTP_API_SERVER","http://10.10.10.52/apireportotomatis/");
	define("URN_API_SERVER","http://10.10.10.52/apireportotomatis/"); // 
					 
define("URL_SERVICE2","http://10.10.26.200/apivintracker/");
//define("URL_SERVICE2","http://10.10.10.71/apivintracker/");
//define("URL_SERVICE,"http://10.10.26.200/apivintracker/vin.php");
//define("URL_SERVICE2","https://cygnus.nasmoco.net/oss/");
define("APPNAME","Vehicle infoRmation Zone");

/*
define('NAMEEMAIL',"CRC Nasmoco Group");
define('HOSTEMAIL',"mail.nasmoco.co.id");
define('PORTEMAIL',"993");
define('USEREMAIL',"crc@nasmoco.co.id");
define('PASSEMAIL',"nasmococrc#semarang");
define('PORTSMTPEMAIL',"465");


define('HOSTEMAIL',"mail.nasmoco.co.id");
define('PORTEMAIL',"993");
define('USEREMAIL',"crc@nasmoco.co.id");
define('PASSEMAIL',"nasmococrc#semarang");
*/

function enkripsi($data)
{
  $enkrip1=base64_encode($data);
  $enkrip2=$enkrip1."_05s";
  $enkrip3=base64_encode($enkrip2);
	
  return $enkrip3;
}

function dekripsi($data)
{
  $dekrip1=base64_decode($data);
  $dekrip2=explode("_05s",$dekrip1);
  $dekrip3=base64_decode($dekrip2[0]);
  
  return $dekrip3;
}

//anti sql injeksi/ hack
function antisqlinjection($value){
	// Stripslashes
	if 	(get_magic_quotes_gpc()){
		$value = stripslashes($value);
	}
	/*
	if (!is_numeric($value)){
		$value = mysql_real_escape_string($value);
	}*/
	return $value;
}

// anti cross site scripting
function anti_xss($data){ 
	$xss=stripslashes(strip_tags(htmlspecialchars($data,ENT_QUOTES))); 
	return $xss; 
} 




function nama_bulan($bulan,&$bln) {
	# Ganti Nama Bulan
	switch ($bulan)  {
		case "01" :
			$bln = "Januari";
			break;
		case "02" :
			$bln = "Februari";
			break;
		case "03" :
			$bln = "Maret";
			break;
		case "04" :
			$bln = "April";
			break;
		case "05" :
			$bln = "Mei";
			break;
		case "06" :
			$bln = "Juni";
			break;
		case "07" :
			$bln = "Juli";
			break;
		case "08" :
			$bln = "Agustus";
			break;
		case "09" :
			$bln = "September";
			break;
		case "10" :
			$bln = "Oktober";
			break;
		case "11" :
			$bln = "November";
			break;
		case "12" :
			$bln = "Desember";
			break;
	}
	
	return $bln;	
}



	function tgl_header() {
		$hari = date("l");
		$bulan= date("F");
		$tanggal= date("d");
		$tahun= date("Y");
			
		# ganti nama hari
		switch ($hari) {
			case "Sunday" :
				print ("Minggu, ");
				break;
			case "Monday" :
				print ("Senin, ");
				break;
			case "Tuesday" :
				print ("Selasa, ");
				break;
			case "Wednesday" :
				print ("Rabu, ");
				break;
			case "Thursday" :
				print ("Kamis, ");
				break;
			case "Friday" :
				print ("Jum`at, ");
				break;
			case "Saturday" :
				print ("Sabtu, ");
				break;
		}
		# Print Tanggal			
		echo $tanggal;		
		# Ganti Nama Bulan
		switch ($bulan)  {
			case "January" :
				print (" Januari ");
				break;
			case "February" :
				print (" Februari ");
				break;
			case "March" :
				print (" Maret ");
				break;
			case "April" :
				print (" April ");
				break;
			case "May" :
				print (" Mei ");
				break;
			case "June" :
				print (" Juni ");
				break;
			case "July" :
				print (" Juli ");
				break;
			case "August" :
				print (" Agustus ");
				break;
			case "September" :
				print (" September ");
				break;
			case "October" :
				print (" Oktober ");
				break;
			case "November" :
				print (" November ");
				break;
			case "December" :
				print (" Desember ");
				break;
		}
		 echo "$tahun";
	}
	

?>
