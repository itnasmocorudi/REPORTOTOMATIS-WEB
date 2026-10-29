<?
$host = '10.10.10.52\HO';
//$host = '10.10.10.181';
$user= 'sa';
//$user = 'nis';
$pass= 'nasmoco~123';
$db = "STARS99";

$connectionInfo = array( "Database"=> $db, "UID"=> $user, "PWD"=> $pass);
$conn = sqlsrv_connect( $host, $connectionInfo);

if( $conn ) {
     echo "Connection established.<br />";
}else{
     echo "Connection could not be established.<br />";
     die( print_r( sqlsrv_errors(), true));
}

//$conn = mssql_connect($host,$user,$pass) or die("Connection Failed");
//mssql_select_db($db,$conn);

global $conn;

?>