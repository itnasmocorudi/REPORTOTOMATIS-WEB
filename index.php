<?php
 
  require_once "./inc/fungsi_all.php";
  $noka = "";
  if(isset($_GET['frameno'])){
    $noka = trim($_GET['frameno']); 

  }
  if(!isset($_SESSION)){
      session_start();
  }
  $tambah = "";
  if($noka != ""){
    $tambah = "?frameno=".$noka;
  }
  $denie = "<script>document.location.href='signin.php$tambah'</script>";
  echo $denie; 

?>