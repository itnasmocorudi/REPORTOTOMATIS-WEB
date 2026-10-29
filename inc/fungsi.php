<?
//error_reporting(0);
session_start();
ini_set('max_execution_time', 600);
set_time_limit(600);
require_once("conn.php");

/*SbrDt : 1-Manual | 2-System | 3-Inject Spv */

define(CHIPER,		"nAsMoCoNRM");
define(DB_STARS,	"STARS");
define(DB_SPK,		"SPK");
//define(DB_BENGKEL,	"Bengkel99");
define(DB_BP,		"BP");
define(DB_PDS,		"PDS");
define(DB_PROFIL_SPK,"profilspk");
define(DB_SPK_LOG, 	"LogSPK");


define(DB_BENGKEL99,	'bengkel99');
define(DB_STARS99,		'stars99');
define(DB_SPK00,		'spk00');
define(DB_SPK99,		'spk99');
define(DB_POLREG,		'Polreg_New');
define(DB_GRADING,		'GRD_SLS');
define(DB_REPLBENGKEL,	"replbengkel99");



define(KACAB,			"BRANCH MANAGER");
define(KACAB,			"PJ BRANCH MANAGER");
define(SPV,				"MARKETING SECTION HEAD");
define(SPV,				"PJ MARKETING SECTION HEAD");
define(SALES,			"MARKETING");
define(SALES,			"MARKETING COUNTER");
define(DEALER_HO,		"NRM");


//define(DEALER,'KRA1');
//define(KD_CABANG,'18');

define(HANSA,"1");


switch($_REQUEST['tp']){
	case 'value':
		value();
		break;
	case 'kota':
		kota();
		break;
	case 'kecamatan':
		kecamatan();
		break;
	case 'kecamatan2':
		kecamatan2();
		break;
	case 'asalprospect':
		asalprospect();
		break;
	case 'grp_act':
		activity();
		break;
	/*case 'demand':
		demand();
		break;
		*/
}

function getprofil($posisi, $dealer){
	/*
		<option value="1">Sales</option>
		<option value="2">SPV</option>
		<option value="3">Kacab</option>
		<option value="4">HO</option>
	*/
	if ($dealer=="NRM") {
		$profil = "4";	
	}else {
		switch ($posisi){
			case KACAB:
				$profil = "3";
				break;	
			case SPV:
				$profil = "2";
				break;
			default:
				$profil = "1";
				break;
		}
	}
	return $profil;
}

function format_date_input($tgl) { // dd-mm-yyyy
	$tgl_arr = explode("-");
	$tgl_return = $tgl_arr[1]."/".$tgl_arr[0]."/".$tgl_arr[2];
	return $tgl_return;
}


function chiper($enkrip, $chiper, $kondisi, &$dekrip) {
	$val_chiper = 0;
	
	for ($x=0;$x<strlen($chiper);$x++) {
		$val_chiper = $val_chiper + ord(substr($chiper,$x,1)) + 13;
	}
	
	$dekrip = "";
	for ($x=0;$x<strlen($enkrip);$x++) {
		$j = $x + 1;
		if ($kondisi) {
			$val_enkrip = ord(substr($enkrip,$x,1)) + $val_chiper + ($j * $j);
			while ($val_enkrip>255) {
				$val_enkrip = $val_enkrip - 255;
			} 
		} else {
			$val_enkrip = ord(substr($enkrip,$x,1)) - $val_chiper - ($j * $j);
			while ($val_enkrip<=0) {
				$val_enkrip = $val_enkrip + 255;
			}
		}
		$dekrip .= chr($val_enkrip);
	}
	
	return $dekrip;
}


function InsertLogUser($menu){
	global $conn;
	
	/*$kd_db = getKodeDBDealer($_SESSION['stars_username']);
	$db_stars = DB_STARS.$kd_db;
	
	$kodedealer = BridgingDealer($kd_db);
	
	
	$qry_log = mssql_query("insert into ".DB_STARS99."..loguser (nik, tgl, menu, ip_address, KodeCabang, user_agent)
							values ('".$_SESSION['stars_username']."', getdate(), '".$menu."', '".$_SERVER['REMOTE_ADDR']."', '".$kodedealer."', '".$_SERVER['HTTP_USER_AGENT']."')",$conn);
	*/
	$qry_log = true;						
	return $qry_log;						
}




function getKodeDBDealer($nik){
	global $conn;
	
	if ($_SESSION['stars_profil']==4) {
		$qry_grd = mssql_query("select kodecabang from ProfilSPK..dealerbd a
						where a.kdcbg = '".$nik."'",$conn);		
	} else {
		$qry_grd = mssql_query("select kodecabang from ProfilSPK..dealerbd a
								inner join fkaryawan..datakaryawan b on a.kodehrd = b.dealer
								where b.nik = '".$nik."'",$conn);
	}
	$dt_grd = mssql_fetch_array($qry_grd);
	$kodedealer = $dt_grd['kodecabang'];
	if (strlen($kodedealer)==1) {
		$kodedealer = "0".$kodedealer;
	}
	////mssql_close	($conn);		
	return $kodedealer;
}



function BridgingDealer($kd){
	global $conn;
	
	$qry_grd = mssql_query("select kodedealer from ProfilSPK..dealerbd where kdcbg = '".$kd."'",$conn);
	$dt_grd = mssql_fetch_array($qry_grd);
	$kodedealer = $dt_grd['kodedealer'];
	////mssql_close	($conn);		
	return $kodedealer;
}

function BridgingNasa($salespr){
	global $conn;
	
	#--------------- BRDIGING NASA SALES
	$qry_grd = mssql_query("select top 1 a.nasa_id from ".DB_GRADING."..tblbridging_nasa a
							left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
							where b.nik = '".$salespr."' and isnull(tanggalkeluar,0) = 0 
							order by a.tgl_update desc",$conn);
	$dt_grd = mssql_fetch_array($qry_grd);
	$salespr = $dt_grd['nasa_id'];
	////mssql_close	($conn);		
	return $salespr;
}


function BridgingNasaSPV($kd_spv){
	global $conn;
	
	#--------------- BRDIGING NASA SALES
	$qry_grd = mssql_query("
							select top 1 a.nik ,replace(a.nama,char(13)+char(10),'') nama ,a.Dealer,[Tanggal keluar],[Tanggal Lahir] ,c.spvnasa_id kodespvvhc , tgl_update
							From fkaryawan..datakaryawan a
							left join ".DB_GRADING."..TBLMasterman b on a.NIK = b.NIK 
							left join ".DB_GRADING."..TBLBridging_spv c on b.idNama = c.spv_id  and b.KodeDealer = c.dealer 
							where  a.NIK = '".$kd_spv."' and isnull(a.[Tanggal Keluar],'') = '' 
							group by a.NIK ,replace(a.nama,char(13)+char(10),'') ,c.spvnasa_id   ,a.Dealer,[Tanggal keluar],[Tanggal Lahir], tgl_update
							order by tgl_update desc
							",$conn);
	$dt_grd = mssql_fetch_array($qry_grd);
	$salespr = $dt_grd['kodespvvhc'];
	
	return $salespr;
}

function value(){
	global $conn;
	
	$kd_db = getKodeDBDealer($_SESSION['stars_username']);
	$db_stars = DB_STARS.$kd_db;
	
	$kriteria = $_REQUEST['kriteria'];
	$qry_pil = mssql_query("select idvalue, displayvalue from ".$db_stars."..MstValue where idkriteria = '".$kriteria."'",$conn);
	while ($dt_pil = mssql_fetch_array($qry_pil)) {
		echo "<option value='".$dt_pil['idvalue']."'>".$dt_pil['displayvalue']."</option>";							
	}			
}


function kota(){
	global $conn;
	
	if ($_SESSION['stars_profil']==4) {	
		$kd_db = getKodeDBDealer(dekripsi($_REQUEST['cbg']));	
		$db_stars = DB_STARS99;
	} else {
		$kd_db = getKodeDBDealer($_SESSION['stars_username']);
		$db_stars = DB_STARS.$kd_db;
	} 
	
	$cabang = $_REQUEST['dealer'];
	$qry = "SELECT distinct a.kotamadya as kotamadya
				FROM ".DB_SPK00."..afiwilayah a With (nolock) 
				inner join ".DB_SPK00."..AFIkecamatan b With (nolock)  on a.kotamadya=b.kotamadya
				";
				
	$cabang_val = "";
	if (!empty($cabang)) {
		for ( $i=0; $i<count($cabang); $i++){
			$kodedealer = BridgingDealer($cabang[$i]);
			$cabang_val .= "'".$kodedealer."',";
		}	
		$cabang_val = substr($cabang_val,0,strlen($cabang_val)-1);
		$qry .= " WHERE a.kotamadya in (select distinct kota from ".$db_stars."..ProspectMst With (nolock) 
							where kodecabang in (".$cabang_val.")) ";
	}
	$qry .= " order by a.kotamadya ";
	
	//echo "<pre>$qry</pre>";
	$stmt = mssql_query($qry,$conn);
				
	while ($row = mssql_fetch_array($stmt)) {
		echo "<option value='".$row['kotamadya']."'>".$row['kotamadya']."</option>";
	}			
}


function kecamatan(){
	global $conn;
	
	$kota = $_REQUEST['kota'];	
	$stmt = mssql_query("SELECT kecamatan 
				FROM ".DB_SPK00."..AFIkecamatan
				WHERE Kotamadya = '$kota'",$conn);
				
	while ($row = mssql_fetch_array($stmt)) {
		echo "<option value='".$row['kecamatan']."'>".$row['kecamatan']."</option>";
	}			
}

function kecamatan2(){
	global $conn;
	
	$kota = $_REQUEST['kota'];	
	$kota_val = "";
	for ( $i=0; $i<count($kota); $i++){
		$kota_val .= "'".$kota[$i]."',";
	}
	
	$kota_val = substr($kota_val,0,strlen($kota_val)-1);
	
	$stmt = mssql_query("SELECT kecamatan, kotamadya
				FROM ".DB_SPK00."..AFIkecamatan
				WHERE Kotamadya in ($kota_val) 
				order by kotamadya, kecamatan",$conn);
	//echo $kota_val;
			
	while ($row = mssql_fetch_array($stmt)) {
		echo "<option value='".$row['kecamatan']."'>".$row['kotamadya']." - ".$row['kecamatan']."</option>";
	}			
}

//20211224 - Ari
function activity(){	
	global $conn;

	$kate = $_REQUEST['kategori'];	
	$stmt = mssql_query("SELECT DISTINCT grpact 
				FROM ".DB_STARS99."..GrpActivity ORDER BY grpact",$conn);
				
	$pilih = mssql_query("SELECT DISTINCT grpact 
				FROM ".DB_STARS99."..GrpActivity WHERE idgrpact = '".$kate."' ORDER BY grpact",$conn);
	while ($rox = mssql_fetch_array($pilih)) {
		$pilih = $rox['grpact'];
	}

	while ($row = mssql_fetch_array($stmt)) {
		if($row['grpact']!=$pilih)
		{
			$cek = 'disabled';
			$sele = '';
		} else
		{
			$cek = '';
			$sele = 'Selected';
		}
		echo "<option value='".$row['grpact']."' ".$cek." ".$sele.">".$row['grpact']."</option>";
	}			
}

function asalprospect(){
	global $conn;
	
	if ($_SESSION['stars_profil']==4) {	
		$kd_db = getKodeDBDealer(dekripsi($_REQUEST['cbg']));	
	} else {
		$kd_db = getKodeDBDealer($_SESSION['stars_username']);
	} 
	$db_stars = DB_STARS.$kd_db;
	$kodedealer = BridgingDealer($kd_db);
	
	$cabang = $_REQUEST['dealer'];
				
	$cabang_val = "";
	if (!empty($cabang)) {
		for ( $i=0; $i<count($cabang); $i++){
			$cabang_val .= "'".$cabang[$i]."',";
		}	
		$cabang_val = substr($cabang_val,0,strlen($cabang_val)-1);
	}
	$qry = "SELECT idAsal,AsalProspect 
			FROM ".$db_stars."..Ms_AsalProspect 
			WHERE aktif = 'y' and display=1
			order by AsalProspect";
	
	$stmt = mssql_query($qry,$conn);
				
	while ($row = mssql_fetch_array($stmt)) {
		echo "<option value='".$row['idAsal']."'>".$row['AsalProspect']."</option>";
	}			
}

/*
function demand(){
	global $conn;
	
	if ($_SESSION['stars_profil']==4) {	
		$db_stars = DB_STARS."99";
	} else {
		$kd_db = getKodeDBDealer($_SESSION['stars_username']);
		$db_stars = DB_STARS.$kd_db;
	} 
	$kodedealer = BridgingDealer($kd_db);
	
	$qry = "SELECT id,itemKlasifikasi 
		FROM ".$db_stars."..MstKlasifikasi 
		order by itemKlasifikasi";
	
	$stmt = mssql_query($qry,$conn);
				
	while ($row = mssql_fetch_array($stmt)) {
		echo "<option value='".$row['id']."'>".$row['itemKlasifikasi']."</option>";
	}			
}*/


function arrPenggolongan($gol){
	if ($gol!="pr" or $gol!="g" or $gol!="q" or $gol!="p" or $gol!="n" or $gol!="c") {
		$gol = "";
	} 
	
	$data = array("pr"=>"Prospecting",
				  "g"=>"Greeting",
				  "q"=>"Qualifying",
				  "p"=>"Presentation",
				  "n"=>"Negotiation",
				  "c"=>"Closing",
				  ''=>'',
				  ' '=>'',);
	return $data[$gol];
}


function notifdarisistemkesales($kodepr, $norangka, $nik_sales, $nama_cust, $model, $telp_cust, $alamat_cust){
	global $conn;
	/*	INSERT INTO NotifHansaRenkei (KodeSumber,Tipe,Nowo,NoRangka,KodeDealer,KodePesan,KodeSales,NIK,TglNotif,Proses,UserEntry,TglEntry,Keterangan) VALUES ('STARS','LEADT','NoSPK atau KodePr','No_Rangka','2_digit_kodedatabase','','','NIK_Sales',GETDATE(),0,'UserLog',GETDATE(),'NAMA_SYSTEM#NAMA_CUST#MODEL#TELP#ALAMAT')
		*/
	/*if ($_SESSION['stars_dealerhrd']=="KLG") {
		$kd_db = getKodeDBDealer($_SESSION['stars_username']);
		$db_stars = DB_STARS.$kd_db;
		
		$kodedealer = BridgingDealer($kd_db);
		
		
		$qry = mssql_query("INSERT INTO bengkel99..NotifHansaRenkei  (KodeSumber,Tipe,Nowo,NoRangka,KodeDealer,KodePesan,KodeSales,NIK,TglNotif,Proses,UserEntry,TglEntry,Keterangan)
							VALUES ('STARS','LEADT','".$kodepr."','".$norangka."','".$kd_db."','','','".$nik_sales."',
							GETDATE(),0,'".$_SESSION['stars_username']."',GETDATE(),'NAMA_SYSTEM#".$nama_cust."#".$model."#".$telp_cust."#".$alamat_cust."')",$conn);
	} else {
		$qry = true;
	}*/	
	$qry = mssql_query("INSERT INTO bengkel99..NotifHansaRenkei  (KodeSumber,Tipe,Nowo,NoRangka,KodeDealer,KodePesan,KodeSales,NIK,TglNotif,Proses,UserEntry,TglEntry,Keterangan)
						VALUES ('STARS','LEADT','".$kodepr."','".$norangka."','".$kd_db."','','','".$nik_sales."',
						GETDATE(),0,'".$_SESSION['stars_username']."',GETDATE(),'NAMA_SYSTEM#".$nama_cust."#".$model."#".$telp_cust."#".$alamat_cust."')",$conn);
	return $qry;
}


function AutoRetensi($tglplanauto,$kodecabang){
	global $conn;
	
	$kd_db = getKodeDBDealer($_SESSION['stars_username']);
	$db_stars = DB_STARS.$kd_db;
	
	$kodedealer = BridgingDealer($kd_db);
	
	
	// $_SESSION['setting'] = $this->mod_home->GetSettingsInject();
	//var_dump($_SESSION['setting']);
	
	$stmt = " SELECT top 1 penjualan, pembelian_kredit,	service,polreg,	
				leads,inquiry,ret_stnk, ret_service_1000, ret_ultah_cust, ret_ultah_istri, ret_ultah_anak, ret_hari_besar, LastUpdate, UserUpdate, ip,
				ret_service_10000, ret_service_20000, ret_service_30000, ret_service_40000,	ret_service_50000,	ret_service_60000,	ret_service_70000,
				ret_service_80000, ret_service_90000, ret_service_100000, hansa_tempo, hansa_tempo1000, hansa_tempo_cust
				from ".$db_stars."..StarsSettingsInject order by LastUpdate desc";
	$qry = mssql_query($stmt,$conn);
	
	$setting = mssql_fetch_array($qry); 
	$ret_ultah_cust = $stting['ret_ultah_cust'];
	$ret_ultah_istri = $setting['ret_ultah_istri'];
	$ret_ultah_anak = $setting['ret_ultah_anak'];
	$ret_stnk = $setting['ret_stnk'];
	
	$ret_service_1000 = $setting['ret_service_1000'];
	$ret_service_10000 = $setting['ret_service_10000'];
	$ret_service_20000 = $setting['ret_service_20000'];
	$ret_service_30000 = $setting['ret_service_30000'];
	$ret_service_40000 = $setting['ret_service_40000'];
	$ret_service_50000 = $setting['ret_service_50000'];
	$ret_service_60000 = $setting['ret_service_60000'];
	$ret_service_70000 = $setting['ret_service_70000'];
	$ret_service_80000 = $setting['ret_service_80000'];
	$ret_service_90000 = $setting['ret_service_90000'];
	$ret_service_100000 = $setting['ret_service_100000'];
	
	$ret_hansa_tempo = $setting['hansa_tempo1000'];
	$ret_hansa_tempo2 = $setting['hansa_tempo'];
	$ret_hansa_tempo_cust = $setting['hansa_tempo_cust'];
	
	/*
	$SPK_DB = SPK_DB;
	$getdate = Get_Date();
	
	$TglPlanAuto = Date_Add($_SESSION['profil']['TglPlanAuto'],"1 days");
	if($TglPlanAuto > $getdate){
		return;
	}*/
	//echo $tglplanauto;
	
	/*if (date('d-m-Y',strtotime("$tglplanauto +1 days")) > date('d-m-Y')) {
		echo date('d-m-Y',strtotime("$tglplanauto +1 days"));
		echo date('d-m-Y');
		return;
	} */
	
	//echo time()."##".strtotime("$tglplanauto +1 days");
	if (strtotime("$tglplanauto +1 days") > time()) {		
		return;
	}
	
	$stars_tglplanauto = date('m/d/Y',strtotime($tglplanauto . "+1 days"));
	
	/*RETENSI ULTAH*/
	if($ret_ultah_cust || $ret_ultah_istri || $ret_ultah_anak){
		$stmt1 = "";
		if($ret_ultah_cust){
			$stmt1 .= "SELECT 'Cust' as data, KodePr, Nama, TglLahir, SalesPr
					from ".$db_stars."..ProspectMst 
					union
					select 'Cust' as data, a.KodePr, H.nama, c.TglLahir, f.SalesPr
					from ".$db_stars."..FollowSPK a
					inner join ".$db_stars."..ProspectMst F on a.KodePr = F.KodePr 
					left join ".DB_SPK99."..DataSPK H on a.NoSpk = H.NoSPK 
					left join ".DB_SPK00."..DoDealer D on LEFT(H.nospk,4) = d.KodeDealer 
					left join SingleIDOri..Bridging E on e.KodeLgn = H.KodeLgn and e.KodeDealer = RIGHT(d.dbname,2)
					left join SingleIDOri..NewCustomer c on c.IDCust = e.IDCust ";	
					// "Data ultah customer dari SID atas data Retail"
					// wa mb tan tgl 26/08/2020 16:27
		}
		if($ret_ultah_istri){
			if($stmt1 != "") $stmt1 .= "\nUNION\n";
			$stmt1 .= "SELECT 'Istri Cust' as data, KodePr, NamaIstri Nama, TanggalLahirIstri TglLahir, SalesPr
					from ".$db_stars."..ProspectMst";
		}
		if($ret_ultah_anak){
			if($stmt1 != "") $stmt1 .= "\nUNION\n";
			$stmt1 .= "SELECT 'Anak 1 Cust' as data, KodePr, NamaAnak2 Nama, TanggalLahirAnakKe1 TglLahir, SalesPr
					from ".$db_stars."..ProspectMst
					union
					SELECT 'Anak 2 Cust' as data, KodePr, NamaAnak2 Nama, TanggalLahirAnakKe2 TglLahir, SalesPr
					from ".$db_stars."..ProspectMst
					union 
					SELECT 'Anak 3 Cust' as data, KodePr, [NamaAnak 3 ] Nama, TanggalLahirAnakKe3 TglLahir, SalesPr
					from ".$db_stars."..ProspectMst";
		}
		/*
		$stmt = "SELECT z.*, b.KodeProspect
				from ( $stmt1 ) z
				inner join ".$db_stars."..ProspectTrn b on z.KodePr=b.KodePr AND 
						b.KodeProspect = (select top 1 KodeProspect 
						from ".$db_stars."..ProspectTrn 
						where KodePr=z.KodePr and StatusAkhir = 'spk' order by KodeProspect desc) 
				where DATEPART(dy,TglLahir) BETWEEN DATEPART(dy,getdate()) AND DATEPART(dy,DATEADD(D,7,getdate())) ";
				//  z.kodepr = 'ANDRIAN20170003'";
		*/
		/*
		SELECT z.*, b.KodeProspect
				from ( SELECT 'Istri Cust' as data, KodePr, NamaIstri Nama, TanggalLahirIstri TglLahir, SalesPr
					from STARS11..ProspectMst
UNION
SELECT 'Anak 1 Cust' as data, KodePr, NamaAnak2 Nama, TanggalLahirAnakKe1 TglLahir, SalesPr
					from STARS11..ProspectMst
					union
					SELECT 'Anak 2 Cust' as data, KodePr, NamaAnak2 Nama, TanggalLahirAnakKe2 TglLahir, SalesPr
					from STARS11..ProspectMst
					union 
					SELECT 'Anak 3 Cust' as data, KodePr, [NamaAnak 3 ] Nama, TanggalLahirAnakKe3 TglLahir, SalesPr
					from STARS11..ProspectMst ) z
				inner join STARS11..ProspectTrn b on z.KodePr=b.KodePr AND 
						b.KodeProspect = (select top 1 KodeProspect 
						from STARS11..ProspectTrn 
						where KodePr=z.KodePr and StatusAkhir = 'spk' order by KodeProspect desc) 
				where DATEPART(dy,TglLahir) BETWEEN DATEPART(dy,getdate()) AND DATEPART(dy,DATEADD(D,7,getdate()))*/
		
		$stmt = "SELECT z.data, z.KodePr, z.Nama, convert(varchar(10),z.TglLahir,105) TglLahir, z.SalesPr, b.KodeProspect
				from ( $stmt1 ) z
				inner join ".$db_stars."..ProspectTrn b on z.KodePr=b.KodePr AND 
						b.KodeProspect = (select top 1 KodeProspect 
						from ".$db_stars."..ProspectTrn 
						where KodePr=z.KodePr and StatusAkhir = 'spk' order by KodeProspect desc) 
				where DATEPART(dy,TglLahir) BETWEEN DATEPART(dy,getdate()) AND DATEPART(dy,DATEADD(D,7,getdate())) ";
								
		//echo "<pre>$stmt</pre>";
		$re1 = mssql_query($stmt11,$conn);
		
		if($re1){
			while ($dt = mssql_fetch_array($re1)) {
				$kdSales = $dt['SalesPr'];
				$kodepr = $dt['KodePr'];
				$kodeprospect = $dt['KodeProspect'];
				$namaultah = $dt['Nama'];
				$ygultah = $dt['data'];
				//$tgllahir = format_date($dt['TglLahir'],'d-m-Y');
				//$tglultah = format_date(substr($tgllahir,0,-4).date("Y"));
				$tgllahir = $dt['TglLahir'];
				
				$tgl_lahir_arr = explode("-",$tgllahir);
				$tgllahir = $tgl_lahir_arr[1]."/".$tgl_lahir_arr[0]."/".$tgl_lahir_arr[2];
				//$tglultah = date("m/d/Y",substr($tgllahir,0,-4).date("Y"));
				$tglultah = substr($tgllahir,0,5)."/".date("Y");
				
				##cek Plan
				$cekPlan1 = "SELECT KodePlan from ".$db_stars."..PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 
						and [Tanggal Lahir] = '$tglultah'";
				$reCekPlan1 = mssql_query($cekPlan1,$conn);
				$jmlreCekPlan1 = mssql_num_rows($reCekPlan1);
					
				if($jmlreCekPlan1==0){
					$cekPlan1 = "SELECT KodePlan from ".$db_stars."..PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3";
					$reCekPlan1 = mssql_query($cekPlan1,$conn);
					$reCekPlan1dat = mssql_fetch_array($reCekPlan1);
					
					//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);	
					$kodeplan = empty($reCekPlan1dat['KodePlan']) ? 1 : $reCekPlan1dat['KodePlan'];
					
					##generate kodeplan
					$nKode = intval(substr($kodeplan, -4));
					$vKode = TRUE;
					while ($vKode) {
						$kodeplan = "PLRT".date('y').str_pad($nKode, 4, "0",STR_PAD_LEFT);
						/*$cek = $this->mod_plan->CekDataPlanTrn(" kodepr='$kodepr' AND kodeprospect = '$kodeprospect' AND kodeplan = '$kodeplan' ");
						$vKode = $cek[0];
						$nKode++;
						*/
						$qryKode = mssql_query("select kodepr from ".$db_stars."..plantrn 
										where kodepr='$kodepr' AND kodeprospect = '$kodeprospect' AND kodeplan = '$kodeplan'",$conn);
						//$vKode = $cek[0];
						$jmlKode = mssql_num_rows($qryKode);
						
						if($jmlKode>0){
							$vKode = TRUE;
						} else {
							$vKode = FALSE;
						}
						$nKode++;	
					}
					##ketplan
					switch ($ygultah) {
						case 'Cust':
							$idKetPlan = 31;
							$KetPlan = "Retensi ulang tahun";
							break;
						case 'Istri Cust':
							$idKetPlan = 32;
							$KetPlan = "Retensi ulang tahun istri";
							break;
						case 'Anak 1 Cust':
						case 'Anak 2 Cust':
						case 'Anak 3 Cust':
							$idKetPlan = 33;
							$KetPlan = "Retensi ulang tahun anak";
							break;
					}
					##buat plan
					/*SbrPlan 0:Manual; 1:System; 2:Inject-Spv;*/
					/*
					$plan_ultah = array("SbrPlan"=>"1",
						  "KodePr"=>$kodepr, 
						  "KodeProspect"=>$kodeprospect, 
						  "TglPlan"=>format_date($tglultah),
						  "Via"=>null,
						  "KodePlan"=>$kodeplan,
						  "TypePlan"=>3,
						  "idKetPlan"=>$idKetPlan,
						  "KetPlan"=>$KetPlan,
						  "[Yang Berulang Tahun]"=>$namaultah,
						  "[Tanggal Lahir]"=>$tglultah,
						  "KodeSales"=>$kdSales, 
						  "Kodeuser"=>"AutoPlanSystem", 
						  "TglEntry"=>$getdate, );
						  */
					//var_dump($plan_ultah);
					//$inst = $this->mod_plan->InsertPlanTrn($plan_ultah);
					
					$inst = mssql_query("insert into ".$db_stars."..plantrn (SbrPlan,KodePr,KodeProspect,TglPlan,
							KodePlan,TypePlan,idKetPlan,KetPlan,
							[Yang Berulang Tahun],[Tanggal Lahir],KodeSales,Kodeuser,TglEntry)
							values ('1', '".$kodepr."', '".$kodeprospect."', '".$tglultah."',			
							'".$kodeplan."', 3, '".$idKetPlan."', '".$KetPlan."', 
							'".$namaultah."','".$tglultah."','".$kdSales."','AutoPlanSystem',getdate())",$conn);
					
				}
			}
		}
	}
	/*END RETENSI ULTAH*/
	
	/*RETENSI SERVICE 1000KM*/
	if($ret_service_1000){
		//$re = $this->mod_plan->RetensiServiceNoRangka();
		// DATEADD(d,DATEPART(dy,DATEADD(d,-14,d.Tgl1))-1,DATEADD(year,YEAR(getdate())-1900,0)) as TglPlan, 
		
		$stmt = "SELECT DATEADD(d,-14,d.Tgl1) as TglPlan,
					convert(varchar(10),Tgl1,105) as TglTempo, c.KodePr, c.KodeProspect, c.NoSPK, d.NoRangka, f.NamaBarang as Type,
					h.SalesPr, h.nama CUSTOMER, f.NoPolisi,
					isnull(i.tdkaktif,0) tdkaktif
					from ".$db_stars."..FollowSPK c
					inner join ".DB_SPK."..T_MRS d on c.NoSpk = d.NoSPK
					left join ".$db_stars."..PlanTrn e on c.KodePr=e.KodePr and c.KodeProspect=e.KodeProspect and d.NoRangka=e.NoRangka
					inner join ".DB_SPK00."..Inmst f on d.NoRangka = f.KodeItem 
					inner join ".$db_stars."..ProspectTrn g on c.KodePr=g.KodePr and c.KodeProspect=c.KodeProspect
					inner join ".$db_stars."..Prospectmst h on g.kodepr = h.kodepr
					left join ".DB_STARS99."..Arsales i on h.salespr = i.kodesales 
					where e.KodePlan is null and i.kodecabang = '".$kodedealer."' ";
		
		$re = mssql_query($stmt,$conn);
		
		if($re){
			while ($dt = mssql_fetch_array($re)) {
				$kodepr = $dt['KodePr'];
				$kodeprospect = $dt['KodeProspect'];
				$kdSales = $dt['SalesPr'];
				$norangka = $dt['NoRangka'];
				$type = $dt['Type'];
				$tglJthTempo = $dt['TglTempo'];
				$tdkaktif = $dt['tdkaktif'];
				
				$nowo = "";
				$nopolisi = $dt['NoPolisi'];
				$nama_svc = $dt['CUSTOMER'];
				$model_svc = $dt['Type'];
				
				
				#--------------- BRDIGING NASA SALES
				$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
										left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
										where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
										order by a.tgl_update desc",$conn);
				$dt_grd = mssql_fetch_array($qry_grd);
				$nik = $dt_grd['nik'];
				$keterangan = $nopolisi."#".$model_svc."#".$nama_svc."#".$tglJthTempo."#1000";
				
				
				//$TglPlan = Date_Add($tglJthTempo,"-3 days");
				//$TglPlan = Date_Add($tglJthTempo,"-14 days");
				
				$TglPlan = date('m/d/Y',strtotime($tglJthTempo . "-$ret_hansa_tempo2 days"));
				
				##cek Plan
				$cekPlan1 = "SELECT KodePlan from ".$db_stars."..PlanTrn 
							where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
				//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
				$reCekPlan1 = mssql_query($cekPlan1,$conn);
				$jmlreCekPlan1 = mssql_num_rows($reCekPlan1);
					
				if($jmlreCekPlan1==0){
				
					$cekPlan1 = "SELECT KodePlan from ".$db_stars."..PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3";
					$reCekPlan1 = mssql_query($cekPlan1,$conn);
					$reCekPlan1dat = mssql_fetch_array($reCekPlan1);
					
					//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);	
					$kodeplan = empty($reCekPlan1dat['KodePlan']) ? 1 : $reCekPlan1dat['KodePlan'];					
					
					##generate kodeplan
					$nKode = intval(substr($kodeplan, -4));
					$vKode = TRUE;
					while ($vKode) {
						$kodeplan = "PLRT".date('y').str_pad($nKode, 4, "0",STR_PAD_LEFT);
						/*
						$cek = $this->mod_plan->CekDataPlanTrn(" kodepr='$kodepr' AND kodeprospect = '$kodeprospect' AND kodeplan = '$kodeplan' ");
						$vKode = $cek[0];
						$nKode++;
						*/
						
						$qryKode = mssql_query("select kodepr from ".$db_stars."..plantrn 
										where kodepr='$kodepr' AND kodeprospect = '$kodeprospect' AND kodeplan = '$kodeplan'",$conn);
						//$vKode = $cek[0];
						$jmlKode = mssql_num_rows($qryKode);
						
						if($jmlKode>0){
							$vKode = TRUE;
						} else {
							$vKode = FALSE;
						}
						$nKode++;	
					}
					// echo "$kodepr - $kodeprospect - $kodeplan<br>";
					##ketplan
					$idKetPlan = 36;
					$KetPlan = "Reminder service 1000 Km";
					##buat plan
					/*$plan_svc = array("SbrPlan"=>"1",
						  "KodePr"=>$kodepr, 
						  "KodeProspect"=>$kodeprospect, 
						  "TglPlan"=>format_date($TglPlan),
						  "Via"=>null,
						  "KodePlan"=>$kodeplan,
						  "TypePlan"=>3,
						  "idKetPlan"=>$idKetPlan,
						  "KetPlan"=>$KetPlan,
						  // "[Yang Berulang Tahun]"=>$namaultah,
						  // "[Tanggal Lahir]"=>$tglultah,
						  "[Tanggal Jatuh Tempo]"=>format_date($tglJthTempo),
						  "NoRangka"=>$norangka,
						  "Type"=>$type,
						  "KodeSales"=>$kdSales, 
						  "Kodeuser"=>"AutoPlanSystem", 
						  "TglEntry"=>$getdate, );
					// var_dump($plan_svc);
					$inst = $this->mod_plan->InsertPlanTrn($plan_svc);
					*/
					
					if ($tdkaktif==0) {
						$inst = mssql_query("insert into ".$db_stars."..plantrn (SbrPlan,KodePr,KodeProspect,TglPlan,
								KodePlan,TypePlan,idKetPlan,KetPlan, 
								[Tanggal Jatuh Tempo], NoRangka, Type,
								KodeSales,Kodeuser,TglEntry)
								values ('1', '".$kodepr."', '".$kodeprospect."', '".$TglPlan."',			
								'".$kodeplan."', 3, '".$idKetPlan."', '".$KetPlan."', 
								'".$tglJthTempo."', '".$norangka."', '".$type."',
								'".$kdSales."','AutoPlanSystem',getdate())",$conn);
						
						/*if ($inst and HANSA==1) {
							$kodepesan = "REMINDER";
							$inst_hansa = mssql_query("insert into ".DB_REPLBENGKEL."..NotifHansaRenkei
									 (kodesumber, Tipe, nowo, norangka, kodedealer, kodepesan,kodesales, nik, tglnotif, proses, 
									 tglentry, keterangan, userentry)
									values ('GRP', 'REMINDER', '".$nowo."', '".$norangka."', '".$kodecabang."','".$kodepesan."',  '".$kdSales."', '".$nik."', '".$TglPlan."', '0', 
									getdate(), '".$keterangan."', 'REMINDER')",$conn);
						}*/	
					}
						
				}


			}
		}
	}
	/*END RETENSI SERVICE 1000KM*/
	
	/*RETENSI STNK*/
	if($ret_stnk){
		//$re = $this->mod_plan->RetensiSTNKNoRangka();
		$stmt = "SELECT DATEADD(d,DATEPART(dy,DATEADD(d,-3,a.TglSTNK))-1,DATEADD(year,YEAR(getdate())-1900,0)) TglPlan, 
					DATEADD(d,DATEPART(dy,a.TglSTNK)-1,DATEADD(year,YEAR(getdate())-1900,0)) TglTempo, 
					a.TglSTNK, b.NoRangka, c.NamaBarang as Type, isnull(b.Kode_Sales,0) SalesPr, d.KodePr, d.KodeProspect, isnull(g.tdkaktif,0) tdkaktif
					from ".DB_STARS99."..AFI a
					inner join ".DB_SPK99."..dataSPK b on a.SPK_No=b.SPK_No
					inner join ".DB_SPK00."..Inmst c on b.NoRangka=c.KodeItem
					inner join ".$db_stars."..FollowSPK d on a.SPK_No = d.NoSpk
					left join ".$db_stars."..PlanTrn e on d.KodePr=e.KodePr and d.KodeProspect=e.KodeProspect 
							and b.NoRangka=e.NoRangka and YEAR(TglPlan)=YEAR(getdate())
					inner join ".$db_stars."..ProspectTrn f on d.KodePr=f.KodePr and d.KodeProspect=f.KodeProspect
					left join ".DB_STARS99."..ArSales g on b.Kode_Sales = g.kodesales
					where a.TglSTNK is not null and e.KodePlan is null and g.kodecabang = '".$kodedealer."'
					and DATEPART(dy,a.TglSTNK)-4 BETWEEN DATEPART(dy,getdate()) AND DATEPART(dy,getdate())+7 ";
		$re = mssql_query($stmt,$conn);
		
		if($re){
			while ($dt = mssql_fetch_array($re)) {
				$kodepr = $dt['KodePr'];
				$kodeprospect = $dt['KodeProspect'];
				$TglPlan = $dt['TglPlan'];
				$tglJthTempo = $dt['TglTempo'];
				$type = $dt['Type'];
				$norangka = $dt['NoRangka'];
				$kdSales = $dt['SalesPr'];
				$tdkaktif = $dt['tdkaktif'];
				
				##cek Plan
				$cekPlan1 = "SELECT KodePlan from ".$db_stars."..PlanTrn 
							where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
				//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
				$reCekPlan1 = mssql_query($cekPlan1,$conn);
				$jmlreCekPlan1 = mssql_num_rows($reCekPlan1);
					
				if($jmlreCekPlan1==0){
					$cekPlan1 = "SELECT KodePlan from ".$db_stars."..PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3";
					$reCekPlan1 = mssql_query($cekPlan1,$conn);
					$reCekPlan1dat = mssql_fetch_array($reCekPlan1);
					
					//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);	
					$kodeplan = empty($reCekPlan1dat['KodePlan']) ? 1 : $reCekPlan1dat['KodePlan'];					
					
					
					##generate kodeplan
					$nKode = intval(substr($kodeplan, -4));
					$vKode = TRUE;
					while ($vKode) {
						$kodeplan = "PLRT".date('y').str_pad($nKode, 4, "0",STR_PAD_LEFT);
						/*
						$cek = $this->mod_plan->CekDataPlanTrn(" kodepr='$kodepr' AND kodeprospect = '$kodeprospect' AND kodeplan = '$kodeplan' ");
						$vKode = $cek[0];
						$nKode++;
						*/
						$qryKode = mssql_query("select kodepr from ".$db_stars."..plantrn 
										where kodepr='$kodepr' AND kodeprospect = '$kodeprospect' AND kodeplan = '$kodeplan'",$conn);
						//$vKode = $cek[0];
						$jmlKode = mssql_num_rows($qryKode);
						
						if($jmlKode>0){
							$vKode = TRUE;
						} else {
							$vKode = FALSE;
						}
						$nKode++;							
					}
					##ketplan
					$idKetPlan = 34;
					$KetPlan = "Reminder perpanjang STNK";
					##buat plan
					/*
					$plan_stnk = array("SbrPlan"=>"1",
						  "KodePr"=>$kodepr, 
						  "KodeProspect"=>$kodeprospect, 
						  "TglPlan"=>format_date($TglPlan),
						  "Via"=>null,
						  "KodePlan"=>$kodeplan,
						  "TypePlan"=>3,
						  "idKetPlan"=>$idKetPlan,
						  "KetPlan"=>$KetPlan,
						  // "[Yang Berulang Tahun]"=>$namaultah,
						  // "[Tanggal Lahir]"=>$tglultah,
						  "[Tanggal Jatuh Tempo]"=>format_date($tglJthTempo),
						  "NoRangka"=>$norangka,
						  "Type"=>$type,
						  "KodeSales"=>$kdSales, 
						  "Kodeuser"=>"AutoPlanSystem", 
						  "TglEntry"=>$getdate, );
					// var_dump($plan_stnk);
					$inst = $this->mod_plan->InsertPlanTrn($plan_stnk);
					*/
					
					if ($tdkaktif==0) {	
						$inst = mssql_query("insert into ".$db_stars."..plantrn (SbrPlan,KodePr,KodeProspect,TglPlan,
							KodePlan,TypePlan,idKetPlan,KetPlan, 
							[Tanggal Jatuh Tempo], NoRangka, Type,
							KodeSales,Kodeuser,TglEntry)
							values ('1', '".$kodepr."', '".$kodeprospect."', '".$TglPlan."',			
							'".$kodeplan."', 3, '".$idKetPlan."', '".$KetPlan."', 
							'".$tglJthTempo."', '".$norangka."', '".$type."',
							'".$kdSales."','AutoPlanSystem',getdate())",$conn);
					}
							
				}					
			}

		}
	}
	/*END RETENSI STNK*/

	/*RETENSI SERVICE 10000KM*/
		if($ret_service_10000){
			$km = 10000;
			$re = RetensiServiceNoRangkaNew($km,$ret_hansa_tempo);
			if($re){
				while ($dt = mssql_fetch_array($re)) {
					$kodepr = $dt['KodePr'];
					$kodeprospect = $dt['KodeProspect'];
					$kdSales = $dt['SalesPr'];
					$norangka = $dt['NoRangka'];
					$type = $dt['Type'];
					$tglJthTempo = $dt['DateNextSvc'];
					
					$nowo = $dt['NoWo'];
					$nopolisi = $dt['NoPolisi'];
					$nama_svc = $dt['CUSTOMER'];
					$model_svc = $dt['Type'];

					#-------------------------------- inject blm ada di prospectmst
					$customer = $dt['CUSTOMER'];
					$alamat = $dt['CUST_ADRES'];
					$kota = $dt['KOTA'];
					$kecamatan = $dt['KECAMATAN'];
					$kodepos = $dt['KODEPOS'];
					$telp = $dt['TELPON'];
					$gsm = $dt['GSM'];
					$cdma = $dt['CDMA'];
					$km_akhir = $dt['KmNow'];
					$kodesupervisor = $dt['Kode_Supervisor'];
										
					if (empty($kodepr) and empty($kodeprospect)) {
						$InstMst = InsertMasterProspect($customer,$kodepr, $alamat, $kota, $kecamatan, $kodepos, $telp, $gsm, $cdma, $norangka, $km_akhir, $kdSales, $kodesupervisor);
						$kodepr = $InsMst['kodepr'];
						$kodeprospect = $InsMst['kodeprospect'];
					} 
					$tdkaktif = $dt['tdkaktif'];
					
					//echo "__XX__".$kodepr.$kodeprospect."__kd<br>";
					#--------------- BRDIGING NASA SALES
					$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
											left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
											where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
											order by a.tgl_update desc",$conn);
					$dt_grd = mssql_fetch_array($qry_grd);
					$nik = $dt_grd['nik'];
					$kodepesan = "REMINDER";
					$keterangan = $nopolisi."#".$model_svc."#".$nama_svc."#".$tglJthTempo."#10000";
					
					//$TglPlan = Date_Add($tglJthTempo,"-3 days");
					//$TglPlan = Date_Add($tglJthTempo,"-$ret_hansa_tempo days");
					##cek Plan
					
					//$cekPlan1 = "SELECT KodePlan from PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					
					$TglPlan = date('m/d/Y',strtotime($tglJthTempo . "-$ret_hansa_tempo days"));
				
					##cek Plan
					$cekPlan1 = "SELECT KodePlan from ".$db_stars."..PlanTrn 
								where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					$reCekPlan1 = mssql_query($cekPlan1,$conn);
					$jmlreCekPlan1 = mssql_num_rows($reCekPlan1);
					
					if($jmlreCekPlan1==0){
					
						$cekPlan1 = "SELECT KodePlan from ".$db_stars."..PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3";
						$reCekPlan1 = mssql_query($cekPlan1,$conn);
						$reCekPlan1dat = mssql_fetch_array($reCekPlan1);
						
						//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);	
						$kodeplan = empty($reCekPlan1dat['KodePlan']) ? 1 : $reCekPlan1dat['KodePlan'];					
						
						##generate kodeplan
						$nKode = intval(substr($kodeplan, -4));
						$vKode = TRUE;
						while ($vKode) {
							$kodeplan = "PLRT".date('y').str_pad($nKode, 4, "0",STR_PAD_LEFT);
							/*
							$cek = $this->mod_plan->CekDataPlanTrn(" kodepr='$kodepr' AND kodeprospect = '$kodeprospect' AND kodeplan = '$kodeplan' ");
							$vKode = $cek[0];
							$nKode++;
							*/
							
							$qryKode = mssql_query("select kodepr from ".$db_stars."..plantrn 
											where kodepr='$kodepr' AND kodeprospect = '$kodeprospect' AND kodeplan = '$kodeplan'",$conn);
							//$vKode = $cek[0];
							$jmlKode = mssql_num_rows($qryKode);
							
							if($jmlKode>0){
								$vKode = TRUE;
							} else {
								$vKode = FALSE;
							}
							$nKode++;	
						}
						// echo "$kodepr - $kodeprospect - $kodeplan<br>";
						##ketplan
						$idKetPlan = 39;
						$KetPlan = "Reminder service 10000 Km";
						##buat plan
						/*SbrPlan 0:Manual; 1:System; 2:Inject-Spv;*/
						/*$plan_svc = array("SbrPlan"=>"1",
							  "KodePr"=>$kodepr, 
							  "KodeProspect"=>$kodeprospect, 
							  "TglPlan"=>format_date($TglPlan),
							  "Via"=>null,
							  "KodePlan"=>$kodeplan,
							  "TypePlan"=>3,
							  "idKetPlan"=>$idKetPlan,
							  "KetPlan"=>$KetPlan,
							  // "[Yang Berulang Tahun]"=>$namaultah,
							  // "[Tanggal Lahir]"=>$tglultah,
							  "[Tanggal Jatuh Tempo]"=>format_date($tglJthTempo),
							  "NoRangka"=>$norangka,
							  "Type"=>$type,
							  "KodeSales"=>$kdSales, 
							  "Kodeuser"=>"AutoPlanSystem", 
							  "TglEntry"=>$getdate, );
						// var_dump($plan_svc);
						$inst = $this->mod_plan->InsertPlanTrn($plan_svc);
						*/
						if ($tdkaktif==0) {
					
							$jenis_service = "Service ".$km." KM";	
							$inst = mssql_query("insert into ".$db_stars."..plantrn (SbrPlan,KodePr,KodeProspect,TglPlan,
								KodePlan,TypePlan,idKetPlan,KetPlan, 
								[Tanggal Jatuh Tempo], NoRangka, Type,
								KodeSales,Kodeuser,TglEntry)
								values ('1', '".$kodepr."', '".$kodeprospect."', '".$TglPlan."',			
								'".$kodeplan."', 3, '".$idKetPlan."', '".$KetPlan."', 
								'".$tglJthTempo."', '".$norangka."', '".$type."',
								'".$kdSales."','AutoPlanSystem',getdate())",$conn);
								
							/*if ($inst and HANSA==1) {
								$kodepesan = "REMINDER";
								$inst_hansa = mssql_query("insert into ".DB_REPLBENGKEL."..NotifHansaRenkei
										 (kodesumber, Tipe, nowo, norangka, kodedealer, kodepesan,kodesales, nik, tglnotif, proses, 
										 tglentry, keterangan, userentry)
										values ('GRP', 'REMINDER', '".$nowo."', '".$norangka."', '".$kodecabang."','".$kodepesan."',  '".$kdSales."', '".$nik."', '".$TglPlan."', '0', 
										getdate(), '".$keterangan."', 'REMINDER')",$conn);
							}*/	
						}
					
					}


				}
			}
			
			#--------------------- customer
			if (HANSA==1) {
				$re_cust = RetensiServiceNoRangkaCustomer($km,$ret_hansa_tempo_cust);
				if($re_cust){
					while ($dt = mssql_fetch_array($re_cust)) {
						$kodepr = $dt['KodePr'];
						$kodeprospect = $dt['KodeProspect'];
						$kdSales = $dt['SalesPr'];
						$norangka = $dt['NoRangka'];
						$type = $dt['Type'];
						$tglJthTempo = $dt['DateNextSvc'];
						
						$nowo = $dt['NoWo'];
						$nopolisi = $dt['NoPolisi'];
						$nama_svc = $dt['CUSTOMER'];
						$model_svc = $dt['Type'];
	
						#-------------------------------- inject blm ada di prospectmst
						$customer = $dt['CUSTOMER'];
						$alamat = $dt['CUST_ADRES'];
						$kota = $dt['KOTA'];
						$kecamatan = $dt['KECAMATAN'];
						$kodepos = $dt['KODEPOS'];
						$telp = $dt['TELPON'];
						$gsm = $dt['GSM'];
						$cdma = $dt['CDMA'];
						$km_akhir = $dt['KmNow'];
						$kodesupervisor = $dt['Kode_Supervisor'];
						
						//echo "__XX__".$kodepr.$kodeprospect."__kd<br>";
						#--------------- BRDIGING NASA SALES
						$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
												left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
												where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
												order by a.tgl_update desc",$conn);
						$dt_grd = mssql_fetch_array($qry_grd);
						$nik = $dt_grd['nik'];
						$kodepesan = "REMINDER";
						$keterangan = $nopolisi."#".$model_svc."#".$nama_svc."#".$tglJthTempo."#10000";
						
						$qry_cek = mssql_query("select norangka from ".DB_REPLBENGKEL."..NotifHansaRenkei
												where norangka = '".$norangka."' and tglnotif = '".$TglPlan."'",$conn);
						
						$jml_cek = mssql_num_rows($qry_cek);
						
						if ($jml_cek==0) {
							$inst_hansa = mssql_query("insert into ".DB_REPLBENGKEL."..NotifHansaRenkei
									 (kodesumber, Tipe, nowo, norangka, kodedealer, kodepesan,kodesales, nik, tglnotif, proses, 
									 tglentry, keterangan, userentry)
									values ('GRP', 'REMINDER', '".$nowo."', '".$norangka."', '".$kodecabang."','".$kodepesan."',  '".$kdSales."', '".$nik."', '".$TglPlan."', '0', 
									getdate(), '".$keterangan."', 'REMINDER')",$conn);
						}
							
					}
				}				
			}
			
		}
		/*END RETENSI SERVICE 10000KM*/
		
		/*RETENSI SERVICE 20000KM*/
		if($ret_service_20000){
			$km = 20000;
			$re = RetensiServiceNoRangkaNew($km,$ret_hansa_tempo);
			if($re){
				while ($dt = mssql_fetch_array($re)) {
					$kodepr = $dt['KodePr'];
					$kodeprospect = $dt['KodeProspect'];
					$kdSales = $dt['SalesPr'];
					$norangka = $dt['NoRangka'];
					$type = $dt['Type'];
					$tglJthTempo = $dt['DateNextSvc'];
					
					$nowo = $dt['NoWo'];
					$nopolisi = $dt['NoPolisi'];
					$nama_svc = $dt['CUSTOMER'];
					$model_svc = $dt['Type'];
					
					
					#-------------------------------- inject blm ada di prospectmst
					$customer = $dt['CUSTOMER'];
					$alamat = $dt['CUST_ADRES'];
					$kota = $dt['KOTA'];
					$kecamatan = $dt['KECAMATAN'];
					$kodepos = $dt['KODEPOS'];
					$telp = $dt['TELPON'];
					$gsm = $dt['GSM'];
					$cdma = $dt['CDMA'];
					$km_akhir = $dt['KmNow'];
					$kodesupervisor = $dt['Kode_Supervisor'];
										
					if (empty($kodepr) and empty($kodeprospect)) {
						$InstMst = InsertMasterProspect($customer,$kodepr, $alamat, $kota, $kecamatan, $kodepos, $telp, $gsm, $cdma, $norangka, $km_akhir, $kdSales, $kodesupervisor);
						$kodepr = $InsMst['kodepr'];
						$kodeprospect = $InsMst['kodeprospect'];
					} 
					$tdkaktif = $dt['tdkaktif'];
					
					#--------------- BRDIGING NASA SALES
					$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
											left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
											where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
											order by a.tgl_update desc",$conn);
					$dt_grd = mssql_fetch_array($qry_grd);
					$nik = $dt_grd['nik'];
					$keterangan = $nopolisi."#".$model_svc."#".$nama_svc."#".$tglJthTempo."#20000";
					
					//$TglPlan = Date_Add($tglJthTempo,"-3 days");
					
					/*$TglPlan = Date_Add($tglJthTempo,"-$ret_hansa_tempo days");
					##cek Plan
					$cekPlan1 = "SELECT KodePlan from PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					*/
					
					$TglPlan = date('m/d/Y',strtotime($tglJthTempo . "-$ret_hansa_tempo days"));
				
					##cek Plan
					$cekPlan1 = "SELECT KodePlan from ".$db_stars."..PlanTrn 
								where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					$reCekPlan1 = mssql_query($cekPlan1,$conn);
					$jmlreCekPlan1 = mssql_num_rows($reCekPlan1);
					
					if($jmlreCekPlan1==0){
						$cekPlan1 = "SELECT KodePlan from ".$db_stars."..PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3";
						$reCekPlan1 = mssql_query($cekPlan1,$conn);
						$reCekPlan1dat = mssql_fetch_array($reCekPlan1);
						
						//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);	
						$kodeplan = empty($reCekPlan1dat['KodePlan']) ? 1 : $reCekPlan1dat['KodePlan'];					
						
						##generate kodeplan
						$nKode = intval(substr($kodeplan, -4));
						$vKode = TRUE;
						while ($vKode) {
							$kodeplan = "PLRT".date('y').str_pad($nKode, 4, "0",STR_PAD_LEFT);
							/*
							$cek = $this->mod_plan->CekDataPlanTrn(" kodepr='$kodepr' AND kodeprospect = '$kodeprospect' AND kodeplan = '$kodeplan' ");
							$vKode = $cek[0];
							$nKode++;
							*/
							
							$qryKode = mssql_query("select kodepr from ".$db_stars."..plantrn 
											where kodepr='$kodepr' AND kodeprospect = '$kodeprospect' AND kodeplan = '$kodeplan'",$conn);
							//$vKode = $cek[0];
							$jmlKode = mssql_num_rows($qryKode);
							
							if($jmlKode>0){
								$vKode = TRUE;
							} else {
								$vKode = FALSE;
							}
							$nKode++;	
						}

						// echo "$kodepr - $kodeprospect - $kodeplan<br>";
						##ketplan
						$idKetPlan = 40;
						$KetPlan = "Reminder service 20000 Km";
						##buat plan
						/*$plan_svc = array("SbrPlan"=>"1",
							  "KodePr"=>$kodepr, 
							  "KodeProspect"=>$kodeprospect, 
							  "TglPlan"=>format_date($TglPlan),
							  "Via"=>null,
							  "KodePlan"=>$kodeplan,
							  "TypePlan"=>3,
							  "idKetPlan"=>$idKetPlan,
							  "KetPlan"=>$KetPlan,
							  // "[Yang Berulang Tahun]"=>$namaultah,
							  // "[Tanggal Lahir]"=>$tglultah,
							  "[Tanggal Jatuh Tempo]"=>format_date($tglJthTempo),
							  "NoRangka"=>$norangka,
							  "Type"=>$type,
							  "KodeSales"=>$kdSales, 
							  "Kodeuser"=>"AutoPlanSystem", 
							  "TglEntry"=>$getdate, );
						// var_dump($plan_svc);
						$inst = $this->mod_plan->InsertPlanTrn($plan_svc);
						*/
						
						if ($tdkaktif==0) {
							$jenis_service = "Service ".$km." KM";	
							$inst = mssql_query("insert into ".$db_stars."..plantrn (SbrPlan,KodePr,KodeProspect,TglPlan,
								KodePlan,TypePlan,idKetPlan,KetPlan, 
								[Tanggal Jatuh Tempo], NoRangka, Type,
								KodeSales,Kodeuser,TglEntry)
								values ('1', '".$kodepr."', '".$kodeprospect."', '".$TglPlan."',			
								'".$kodeplan."', 3, '".$idKetPlan."', '".$KetPlan."', 
								'".$tglJthTempo."', '".$norangka."', '".$type."',
								'".$kdSales."','AutoPlanSystem',getdate())",$conn);
						
						
							/*if ($inst and HANSA==1) {
								$kodepesan = "REMINDER";
								$inst_hansa = mssql_query("insert into ".DB_REPLBENGKEL."..NotifHansaRenkei
										 (kodesumber, Tipe, nowo, norangka, kodedealer, kodepesan,kodesales, nik, tglnotif, proses, 
										 tglentry, keterangan, userentry)
										values ('GRP', 'REMINDER', '".$nowo."', '".$norangka."', '".$kodecabang."','".$kodepesan."',  '".$kdSales."', '".$nik."', '".$TglPlan."', '0', 
										getdate(), '".$keterangan."', 'REMINDER')",$conn);
							}*/	
						}
						
					}


				}
			}
			
			
			#--------------------- customer
			if (HANSA==1) {
				$re_cust = RetensiServiceNoRangkaCustomer($km,$ret_hansa_tempo_cust);
				if($re_cust){
					while ($dt = mssql_fetch_array($re_cust)) {
						$kodepr = $dt['KodePr'];
						$kodeprospect = $dt['KodeProspect'];
						$kdSales = $dt['SalesPr'];
						$norangka = $dt['NoRangka'];
						$type = $dt['Type'];
						$tglJthTempo = $dt['DateNextSvc'];
						
						$nowo = $dt['NoWo'];
						$nopolisi = $dt['NoPolisi'];
						$nama_svc = $dt['CUSTOMER'];
						$model_svc = $dt['Type'];
	
						#-------------------------------- inject blm ada di prospectmst
						$customer = $dt['CUSTOMER'];
						$alamat = $dt['CUST_ADRES'];
						$kota = $dt['KOTA'];
						$kecamatan = $dt['KECAMATAN'];
						$kodepos = $dt['KODEPOS'];
						$telp = $dt['TELPON'];
						$gsm = $dt['GSM'];
						$cdma = $dt['CDMA'];
						$km_akhir = $dt['KmNow'];
						$kodesupervisor = $dt['Kode_Supervisor'];
						
						//echo "__XX__".$kodepr.$kodeprospect."__kd<br>";
						#--------------- BRDIGING NASA SALES
						$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
												left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
												where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
												order by a.tgl_update desc",$conn);
						$dt_grd = mssql_fetch_array($qry_grd);
						$nik = $dt_grd['nik'];
						$kodepesan = "REMINDER";
						$keterangan = $nopolisi."#".$model_svc."#".$nama_svc."#".$tglJthTempo."#10000";
						
						$qry_cek = mssql_query("select norangka from ".DB_REPLBENGKEL."..NotifHansaRenkei
												where norangka = '".$norangka."' and tglnotif = '".$TglPlan."'",$conn);
						
						$jml_cek = mssql_num_rows($qry_cek);
						
						if ($jml_cek==0) {
							$inst_hansa = mssql_query("insert into ".DB_REPLBENGKEL."..NotifHansaRenkei
									 (kodesumber, Tipe, nowo, norangka, kodedealer, kodepesan,kodesales, nik, tglnotif, proses, 
									 tglentry, keterangan, userentry)
									values ('GRP', 'REMINDER', '".$nowo."', '".$norangka."', '".$kodecabang."','".$kodepesan."',  '".$kdSales."', '".$nik."', '".$TglPlan."', '0', 
									getdate(), '".$keterangan."', 'REMINDER')",$conn);
						}
							
					}
				}				
			}
			
		}
		/*END RETENSI SERVICE 20000KM*/
		
		/*RETENSI SERVICE 30000KM*/
		if($ret_service_30000){
			$km = 30000;
			$re = RetensiServiceNoRangkaNew($km,$ret_hansa_tempo);
			if($re){
				while ($dt = mssql_fetch_array($re)) {
					$kodepr = $dt['KodePr'];
					$kodeprospect = $dt['KodeProspect'];
					$kdSales = $dt['SalesPr'];
					$norangka = $dt['NoRangka'];
					$type = $dt['Type'];
					$tglJthTempo = $dt['DateNextSvc'];
					
					$nowo = $dt['NoWo'];
					$nopolisi = $dt['NoPolisi'];
					$nama_svc = $dt['CUSTOMER'];
					$model_svc = $dt['Type'];
					
					#-------------------------------- inject blm ada di prospectmst
					$customer = $dt['CUSTOMER'];
					$alamat = $dt['CUST_ADRES'];
					$kota = $dt['KOTA'];
					$kecamatan = $dt['KECAMATAN'];
					$kodepos = $dt['KODEPOS'];
					$telp = $dt['TELPON'];
					$gsm = $dt['GSM'];
					$cdma = $dt['CDMA'];
					$km_akhir = $dt['KmNow'];
					$kodesupervisor = $dt['Kode_Supervisor'];
										
					if (empty($kodepr) and empty($kodeprospect)) {
						$InstMst = InsertMasterProspect($customer,$kodepr, $alamat, $kota, $kecamatan, $kodepos, $telp, $gsm, $cdma, $norangka, $km_akhir, $kdSales, $kodesupervisor);
						$kodepr = $InsMst['kodepr'];
						$kodeprospect = $InsMst['kodeprospect'];
					} 
					$tdkaktif = $dt['tdkaktif'];
					
					#--------------- BRDIGING NASA SALES
					$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
											left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
											where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
											order by a.tgl_update desc",$conn);
					$dt_grd = mssql_fetch_array($qry_grd);
					$nik = $dt_grd['nik'];
					$keterangan = $nopolisi."#".$model_svc."#".$nama_svc."#".$tglJthTempo."#30000";
					
					//$TglPlan = Date_Add($tglJthTempo,"-3 days");
					/*$TglPlan = Date_Add($tglJthTempo,"-$ret_hansa_tempo days");
					##cek Plan
					$cekPlan1 = "SELECT KodePlan from PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					*/
					$TglPlan = date('m/d/Y',strtotime($tglJthTempo . "-$ret_hansa_tempo days"));
				
					##cek Plan
					$cekPlan1 = "SELECT KodePlan from ".$db_stars."..PlanTrn 
								where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					$reCekPlan1 = mssql_query($cekPlan1,$conn);
					$jmlreCekPlan1 = mssql_num_rows($reCekPlan1);
					
					if($jmlreCekPlan1==0){
						$cekPlan1 = "SELECT KodePlan from ".$db_stars."..PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3";
						$reCekPlan1 = mssql_query($cekPlan1,$conn);
						$reCekPlan1dat = mssql_fetch_array($reCekPlan1);
						
						//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);	
						$kodeplan = empty($reCekPlan1dat['KodePlan']) ? 1 : $reCekPlan1dat['KodePlan'];					
						
						##generate kodeplan
						$nKode = intval(substr($kodeplan, -4));
						$vKode = TRUE;
						while ($vKode) {
							$kodeplan = "PLRT".date('y').str_pad($nKode, 4, "0",STR_PAD_LEFT);
							/*
							$cek = $this->mod_plan->CekDataPlanTrn(" kodepr='$kodepr' AND kodeprospect = '$kodeprospect' AND kodeplan = '$kodeplan' ");
							$vKode = $cek[0];
							$nKode++;
							*/
							
							$qryKode = mssql_query("select kodepr from ".$db_stars."..plantrn 
											where kodepr='$kodepr' AND kodeprospect = '$kodeprospect' AND kodeplan = '$kodeplan'",$conn);
							//$vKode = $cek[0];
							$jmlKode = mssql_num_rows($qryKode);
							
							if($jmlKode>0){
								$vKode = TRUE;
							} else {
								$vKode = FALSE;
							}
							$nKode++;	
						}

						// echo "$kodepr - $kodeprospect - $kodeplan<br>";
						##ketplan
						$idKetPlan = 41;
						$KetPlan = "Reminder service 30000 Km";
						##buat plan
						/*$plan_svc = array("SbrPlan"=>"1",
							  "KodePr"=>$kodepr, 
							  "KodeProspect"=>$kodeprospect, 
							  "TglPlan"=>format_date($TglPlan),
							  "Via"=>null,
							  "KodePlan"=>$kodeplan,
							  "TypePlan"=>3,
							  "idKetPlan"=>$idKetPlan,
							  "KetPlan"=>$KetPlan,
							  // "[Yang Berulang Tahun]"=>$namaultah,
							  // "[Tanggal Lahir]"=>$tglultah,
							  "[Tanggal Jatuh Tempo]"=>format_date($tglJthTempo),
							  "NoRangka"=>$norangka,
							  "Type"=>$type,
							  "KodeSales"=>$kdSales, 
							  "Kodeuser"=>"AutoPlanSystem", 
							  "TglEntry"=>$getdate, );
						// var_dump($plan_svc);
						$inst = $this->mod_plan->InsertPlanTrn($plan_svc);
						*/
						
						if ($tdkaktif==0) {
							$jenis_service = "Service ".$km." KM";	
							$inst = mssql_query("insert into ".$db_stars."..plantrn (SbrPlan,KodePr,KodeProspect,TglPlan,
								KodePlan,TypePlan,idKetPlan,KetPlan, 
								[Tanggal Jatuh Tempo], NoRangka, Type,
								KodeSales,Kodeuser,TglEntry)
								values ('1', '".$kodepr."', '".$kodeprospect."', '".$TglPlan."',			
								'".$kodeplan."', 3, '".$idKetPlan."', '".$KetPlan."', 
								'".$tglJthTempo."', '".$norangka."', '".$type."',
								'".$kdSales."','AutoPlanSystem',getdate())",$conn);
								
							/*if ($inst and HANSA==1) {
								$kodepesan = "REMINDER";
								$inst_hansa = mssql_query("insert into ".DB_REPLBENGKEL."..NotifHansaRenkei
										 (kodesumber, Tipe, nowo, norangka, kodedealer, kodepesan,kodesales, nik, tglnotif, proses, 
										 tglentry, keterangan, userentry)
										values ('GRP', 'REMINDER', '".$nowo."', '".$norangka."', '".$kodecabang."','".$kodepesan."',  '".$kdSales."', '".$nik."', '".$TglPlan."', '0', 
										getdate(), '".$keterangan."', 'REMINDER')",$conn);
							}*/		
						}
					
					}


				}
			}
			
			#--------------------- customer
			if (HANSA==1) {
				$re_cust = RetensiServiceNoRangkaCustomer($km,$ret_hansa_tempo_cust);
				if($re_cust){
					while ($dt = mssql_fetch_array($re_cust)) {
						$kodepr = $dt['KodePr'];
						$kodeprospect = $dt['KodeProspect'];
						$kdSales = $dt['SalesPr'];
						$norangka = $dt['NoRangka'];
						$type = $dt['Type'];
						$tglJthTempo = $dt['DateNextSvc'];
						
						$nowo = $dt['NoWo'];
						$nopolisi = $dt['NoPolisi'];
						$nama_svc = $dt['CUSTOMER'];
						$model_svc = $dt['Type'];
	
						#-------------------------------- inject blm ada di prospectmst
						$customer = $dt['CUSTOMER'];
						$alamat = $dt['CUST_ADRES'];
						$kota = $dt['KOTA'];
						$kecamatan = $dt['KECAMATAN'];
						$kodepos = $dt['KODEPOS'];
						$telp = $dt['TELPON'];
						$gsm = $dt['GSM'];
						$cdma = $dt['CDMA'];
						$km_akhir = $dt['KmNow'];
						$kodesupervisor = $dt['Kode_Supervisor'];
						
						//echo "__XX__".$kodepr.$kodeprospect."__kd<br>";
						#--------------- BRDIGING NASA SALES
						$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
												left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
												where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
												order by a.tgl_update desc",$conn);
						$dt_grd = mssql_fetch_array($qry_grd);
						$nik = $dt_grd['nik'];
						$kodepesan = "REMINDER";
						$keterangan = $nopolisi."#".$model_svc."#".$nama_svc."#".$tglJthTempo."#10000";
						
						$qry_cek = mssql_query("select norangka from ".DB_REPLBENGKEL."..NotifHansaRenkei
												where norangka = '".$norangka."' and tglnotif = '".$TglPlan."'",$conn);
						
						$jml_cek = mssql_num_rows($qry_cek);
						
						if ($jml_cek==0) {
							$inst_hansa = mssql_query("insert into ".DB_REPLBENGKEL."..NotifHansaRenkei
									 (kodesumber, Tipe, nowo, norangka, kodedealer, kodepesan,kodesales, nik, tglnotif, proses, 
									 tglentry, keterangan, userentry)
									values ('GRP', 'REMINDER', '".$nowo."', '".$norangka."', '".$kodecabang."','".$kodepesan."',  '".$kdSales."', '".$nik."', '".$TglPlan."', '0', 
									getdate(), '".$keterangan."', 'REMINDER')",$conn);
						}
							
					}
				}				
			}
			
		}
		/*END RETENSI SERVICE 30000KM*/
		
		/*RETENSI SERVICE 40000KM*/
		if($ret_service_40000){
			$km = 40000;
			$re = RetensiServiceNoRangkaNew($km,$ret_hansa_tempo);
			if($re){
				while ($dt = mssql_fetch_array($re)) {
					$kodepr = $dt['KodePr'];
					$kodeprospect = $dt['KodeProspect'];
					$kdSales = $dt['SalesPr'];
					$norangka = $dt['NoRangka'];
					$type = $dt['Type'];
					$tglJthTempo = $dt['DateNextSvc'];
					
					$nowo = $dt['NoWo'];
					$nopolisi = $dt['NoPolisi'];
					$nama_svc = $dt['CUSTOMER'];
					$model_svc = $dt['Type'];
					
					#-------------------------------- inject blm ada di prospectmst
					$customer = $dt['CUSTOMER'];
					$alamat = $dt['CUST_ADRES'];
					$kota = $dt['KOTA'];
					$kecamatan = $dt['KECAMATAN'];
					$kodepos = $dt['KODEPOS'];
					$telp = $dt['TELPON'];
					$gsm = $dt['GSM'];
					$cdma = $dt['CDMA'];
					$km_akhir = $dt['KmNow'];
					$kodesupervisor = $dt['Kode_Supervisor'];
										
					if (empty($kodepr) and empty($kodeprospect)) {
						$InstMst = InsertMasterProspect($customer,$kodepr, $alamat, $kota, $kecamatan, $kodepos, $telp, $gsm, $cdma, $norangka, $km_akhir, $kdSales, $kodesupervisor);
						$kodepr = $InsMst['kodepr'];
						$kodeprospect = $InsMst['kodeprospect'];
					} 
					$tdkaktif = $dt['tdkaktif'];
					
					
					#--------------- BRDIGING NASA SALES
					$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
											left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
											where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
											order by a.tgl_update desc",$conn);
					$dt_grd = mssql_fetch_array($qry_grd);
					$nik = $dt_grd['nik'];
					$keterangan = $nopolisi."#".$model_svc."#".$nama_svc."#".$tglJthTempo."#40000";
					
					
					//$TglPlan = Date_Add($tglJthTempo,"-3 days");
					/*$TglPlan = Date_Add($tglJthTempo,"-$ret_hansa_tempo days");
					##cek Plan
					$cekPlan1 = "SELECT KodePlan from PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					*/
					
					$TglPlan = date('m/d/Y',strtotime($tglJthTempo . "-$ret_hansa_tempo days"));
				
					##cek Plan
					$cekPlan1 = "SELECT KodePlan from ".$db_stars."..PlanTrn 
								where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					$reCekPlan1 = mssql_query($cekPlan1,$conn);
					$jmlreCekPlan1 = mssql_num_rows($reCekPlan1);
					
					if($jmlreCekPlan1==0){
						$cekPlan1 = "SELECT KodePlan from ".$db_stars."..PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3";
						$reCekPlan1 = mssql_query($cekPlan1,$conn);
						$reCekPlan1dat = mssql_fetch_array($reCekPlan1);
						
						//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);	
						$kodeplan = empty($reCekPlan1dat['KodePlan']) ? 1 : $reCekPlan1dat['KodePlan'];					
						
						##generate kodeplan
						$nKode = intval(substr($kodeplan, -4));
						$vKode = TRUE;
						while ($vKode) {
							$kodeplan = "PLRT".date('y').str_pad($nKode, 4, "0",STR_PAD_LEFT);
							/*
							$cek = $this->mod_plan->CekDataPlanTrn(" kodepr='$kodepr' AND kodeprospect = '$kodeprospect' AND kodeplan = '$kodeplan' ");
							$vKode = $cek[0];
							$nKode++;
							*/
							
							$qryKode = mssql_query("select kodepr from ".$db_stars."..plantrn 
											where kodepr='$kodepr' AND kodeprospect = '$kodeprospect' AND kodeplan = '$kodeplan'",$conn);
							//$vKode = $cek[0];
							$jmlKode = mssql_num_rows($qryKode);
							
							if($jmlKode>0){
								$vKode = TRUE;
							} else {
								$vKode = FALSE;
							}
							$nKode++;	
						}

						// echo "$kodepr - $kodeprospect - $kodeplan<br>";
						##ketplan
						$idKetPlan = 42;
						$KetPlan = "Reminder service 40000 Km";
						##buat plan
						/*$plan_svc = array("SbrPlan"=>"1",
							  "KodePr"=>$kodepr, 
							  "KodeProspect"=>$kodeprospect, 
							  "TglPlan"=>format_date($TglPlan),
							  "Via"=>null,
							  "KodePlan"=>$kodeplan,
							  "TypePlan"=>3,
							  "idKetPlan"=>$idKetPlan,
							  "KetPlan"=>$KetPlan,
							  // "[Yang Berulang Tahun]"=>$namaultah,
							  // "[Tanggal Lahir]"=>$tglultah,
							  "[Tanggal Jatuh Tempo]"=>format_date($tglJthTempo),
							  "NoRangka"=>$norangka,
							  "Type"=>$type,
							  "KodeSales"=>$kdSales, 
							  "Kodeuser"=>"AutoPlanSystem", 
							  "TglEntry"=>$getdate, );
						// var_dump($plan_svc);
						$inst = $this->mod_plan->InsertPlanTrn($plan_svc);
					
						*/
						
						if ($tdkaktif==0) {
							$jenis_service = "Service ".$km." KM";	
							$inst = mssql_query("insert into ".$db_stars."..plantrn (SbrPlan,KodePr,KodeProspect,TglPlan,
								KodePlan,TypePlan,idKetPlan,KetPlan, 
								[Tanggal Jatuh Tempo], NoRangka, Type,
								KodeSales,Kodeuser,TglEntry)
								values ('1', '".$kodepr."', '".$kodeprospect."', '".$TglPlan."',			
								'".$kodeplan."', 3, '".$idKetPlan."', '".$KetPlan."', 
								'".$tglJthTempo."', '".$norangka."', '".$type."',
								'".$kdSales."','AutoPlanSystem',getdate())",$conn);
								
							/*if ($inst and HANSA==1) {
								$kodepesan = "REMINDER";
								$inst_hansa = mssql_query("insert into ".DB_REPLBENGKEL."..NotifHansaRenkei
										 (kodesumber, Tipe, nowo, norangka, kodedealer, kodepesan,kodesales, nik, tglnotif, proses, 
										 tglentry, keterangan, userentry)
										values ('GRP', 'REMINDER', '".$nowo."', '".$norangka."', '".$kodecabang."','".$kodepesan."',  '".$kdSales."', '".$nik."', '".$TglPlan."', '0', 
										getdate(), '".$keterangan."', 'REMINDER')",$conn);
							}*/	
						}						
					
					}


				}
			}
			
			#--------------------- customer
			if (HANSA==1) {
				$re_cust = RetensiServiceNoRangkaCustomer($km,$ret_hansa_tempo_cust);
				if($re_cust){
					while ($dt = mssql_fetch_array($re_cust)) {
						$kodepr = $dt['KodePr'];
						$kodeprospect = $dt['KodeProspect'];
						$kdSales = $dt['SalesPr'];
						$norangka = $dt['NoRangka'];
						$type = $dt['Type'];
						$tglJthTempo = $dt['DateNextSvc'];
						
						$nowo = $dt['NoWo'];
						$nopolisi = $dt['NoPolisi'];
						$nama_svc = $dt['CUSTOMER'];
						$model_svc = $dt['Type'];
	
						#-------------------------------- inject blm ada di prospectmst
						$customer = $dt['CUSTOMER'];
						$alamat = $dt['CUST_ADRES'];
						$kota = $dt['KOTA'];
						$kecamatan = $dt['KECAMATAN'];
						$kodepos = $dt['KODEPOS'];
						$telp = $dt['TELPON'];
						$gsm = $dt['GSM'];
						$cdma = $dt['CDMA'];
						$km_akhir = $dt['KmNow'];
						$kodesupervisor = $dt['Kode_Supervisor'];
						
						//echo "__XX__".$kodepr.$kodeprospect."__kd<br>";
						#--------------- BRDIGING NASA SALES
						$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
												left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
												where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
												order by a.tgl_update desc",$conn);
						$dt_grd = mssql_fetch_array($qry_grd);
						$nik = $dt_grd['nik'];
						$kodepesan = "REMINDER";
						$keterangan = $nopolisi."#".$model_svc."#".$nama_svc."#".$tglJthTempo."#10000";
						
						$qry_cek = mssql_query("select norangka from ".DB_REPLBENGKEL."..NotifHansaRenkei
												where norangka = '".$norangka."' and tglnotif = '".$TglPlan."'",$conn);
						
						$jml_cek = mssql_num_rows($qry_cek);
						
						if ($jml_cek==0) {
							$inst_hansa = mssql_query("insert into ".DB_REPLBENGKEL."..NotifHansaRenkei
									 (kodesumber, Tipe, nowo, norangka, kodedealer, kodepesan,kodesales, nik, tglnotif, proses, 
									 tglentry, keterangan, userentry)
									values ('GRP', 'REMINDER', '".$nowo."', '".$norangka."', '".$kodecabang."','".$kodepesan."',  '".$kdSales."', '".$nik."', '".$TglPlan."', '0', 
									getdate(), '".$keterangan."', 'REMINDER')",$conn);
						}
						
							
					}
				}				
			}
			
		}
		/*END RETENSI SERVICE 40000KM*/
		
		/*RETENSI SERVICE 50000KM*/
		if($ret_service_50000){
			$km = 50000;
			$re = RetensiServiceNoRangkaNew($km,$ret_hansa_tempo);
			if($re){
				while ($dt = mssql_fetch_array($re)) {
					$kodepr = $dt['KodePr'];
					$kodeprospect = $dt['KodeProspect'];
					$kdSales = $dt['SalesPr'];
					$norangka = $dt['NoRangka'];
					$type = $dt['Type'];
					$tglJthTempo = $dt['DateNextSvc'];
					
					$nowo = $dt['NoWo'];
					$nopolisi = $dt['NoPolisi'];
					$nama_svc = $dt['CUSTOMER'];
					$model_svc = $dt['Type'];
					
					#-------------------------------- inject blm ada di prospectmst
					$customer = $dt['CUSTOMER'];
					$alamat = $dt['CUST_ADRES'];
					$kota = $dt['KOTA'];
					$kecamatan = $dt['KECAMATAN'];
					$kodepos = $dt['KODEPOS'];
					$telp = $dt['TELPON'];
					$gsm = $dt['GSM'];
					$cdma = $dt['CDMA'];
					$km_akhir = $dt['KmNow'];
					$kodesupervisor = $dt['Kode_Supervisor'];
										
					if (empty($kodepr) and empty($kodeprospect)) {
						$InstMst = InsertMasterProspect($customer,$kodepr, $alamat, $kota, $kecamatan, $kodepos, $telp, $gsm, $cdma, $norangka, $km_akhir, $kdSales, $kodesupervisor);
						$kodepr = $InsMst['kodepr'];
						$kodeprospect = $InsMst['kodeprospect'];
					} 
					$tdkaktif = $dt['tdkaktif'];
					
					
					#--------------- BRDIGING NASA SALES
					$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
											left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
											where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
											order by a.tgl_update desc",$conn);
					$dt_grd = mssql_fetch_array($qry_grd);
					$nik = $dt_grd['nik'];
					$keterangan = $nopolisi."#".$model_svc."#".$nama_svc."#".$tglJthTempo."#50000";
					
					
					//$TglPlan = Date_Add($tglJthTempo,"-3 days");
					/*$TglPlan = Date_Add($tglJthTempo,"-$ret_hansa_tempo days");
					##cek Plan
					$cekPlan1 = "SELECT KodePlan from PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					*/
					
					$TglPlan = date('m/d/Y',strtotime($tglJthTempo . "-$ret_hansa_tempo days"));
				
					##cek Plan
					$cekPlan1 = "SELECT KodePlan from ".$db_stars."..PlanTrn 
								where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					$reCekPlan1 = mssql_query($cekPlan1,$conn);
					$jmlreCekPlan1 = mssql_num_rows($reCekPlan1);
					
					if($jmlreCekPlan1==0){
						$cekPlan1 = "SELECT KodePlan from ".$db_stars."..PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3";
						$reCekPlan1 = mssql_query($cekPlan1,$conn);
						$reCekPlan1dat = mssql_fetch_array($reCekPlan1);
						
						//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);	
						$kodeplan = empty($reCekPlan1dat['KodePlan']) ? 1 : $reCekPlan1dat['KodePlan'];					
						
						##generate kodeplan
						$nKode = intval(substr($kodeplan, -4));
						$vKode = TRUE;
						while ($vKode) {
							$kodeplan = "PLRT".date('y').str_pad($nKode, 4, "0",STR_PAD_LEFT);
							/*
							$cek = $this->mod_plan->CekDataPlanTrn(" kodepr='$kodepr' AND kodeprospect = '$kodeprospect' AND kodeplan = '$kodeplan' ");
							$vKode = $cek[0];
							$nKode++;
							*/
							
							$qryKode = mssql_query("select kodepr from ".$db_stars."..plantrn 
											where kodepr='$kodepr' AND kodeprospect = '$kodeprospect' AND kodeplan = '$kodeplan'",$conn);
							//$vKode = $cek[0];
							$jmlKode = mssql_num_rows($qryKode);
							
							if($jmlKode>0){
								$vKode = TRUE;
							} else {
								$vKode = FALSE;
							}
							$nKode++;	
						}

						// echo "$kodepr - $kodeprospect - $kodeplan<br>";
						##ketplan
						$idKetPlan = 43;
						$KetPlan = "Reminder service 50000 Km";
						##buat plan
					/*	$plan_svc = array("SbrPlan"=>"1",
							  "KodePr"=>$kodepr, 
							  "KodeProspect"=>$kodeprospect, 
							  "TglPlan"=>format_date($TglPlan),
							  "Via"=>null,
							  "KodePlan"=>$kodeplan,
							  "TypePlan"=>3,
							  "idKetPlan"=>$idKetPlan,
							  "KetPlan"=>$KetPlan,
							  // "[Yang Berulang Tahun]"=>$namaultah,
							  // "[Tanggal Lahir]"=>$tglultah,
							  "[Tanggal Jatuh Tempo]"=>format_date($tglJthTempo),
							  "NoRangka"=>$norangka,
							  "Type"=>$type,
							  "KodeSales"=>$kdSales, 
							  "Kodeuser"=>"AutoPlanSystem", 
							  "TglEntry"=>$getdate, );
						// var_dump($plan_svc);
						$inst = $this->mod_plan->InsertPlanTrn($plan_svc);
						*/
						
						if ($tdkaktif==0) {
							$jenis_service = "Service ".$km." KM";	
							$inst = mssql_query("insert into ".$db_stars."..plantrn (SbrPlan,KodePr,KodeProspect,TglPlan,
								KodePlan,TypePlan,idKetPlan,KetPlan, 
								[Tanggal Jatuh Tempo], NoRangka, Type,
								KodeSales,Kodeuser,TglEntry)
								values ('1', '".$kodepr."', '".$kodeprospect."', '".$TglPlan."',			
								'".$kodeplan."', 3, '".$idKetPlan."', '".$KetPlan."', 
								'".$tglJthTempo."', '".$norangka."', '".$type."',
								'".$kdSales."','AutoPlanSystem',getdate())",$conn);
								
							/*if ($inst and HANSA==1) {
								$kodepesan = "REMINDER";
								$inst_hansa = mssql_query("insert into ".DB_REPLBENGKEL."..NotifHansaRenkei
										 (kodesumber, Tipe, nowo, norangka, kodedealer, kodepesan,kodesales, nik, tglnotif, proses, 
										 tglentry, keterangan, userentry)
										values ('GRP', 'REMINDER', '".$nowo."', '".$norangka."', '".$kodecabang."','".$kodepesan."',  '".$kdSales."', '".$nik."', '".$TglPlan."', '0', 
										getdate(), '".$keterangan."', 'REMINDER')",$conn);
							}*/	
						}
											
					}


				}
			}
			
			#--------------------- customer
			if (HANSA==1) {
				$re_cust = RetensiServiceNoRangkaCustomer($km,$ret_hansa_tempo_cust);
				if($re_cust){
					while ($dt = mssql_fetch_array($re_cust)) {
						$kodepr = $dt['KodePr'];
						$kodeprospect = $dt['KodeProspect'];
						$kdSales = $dt['SalesPr'];
						$norangka = $dt['NoRangka'];
						$type = $dt['Type'];
						$tglJthTempo = $dt['DateNextSvc'];
						
						$nowo = $dt['NoWo'];
						$nopolisi = $dt['NoPolisi'];
						$nama_svc = $dt['CUSTOMER'];
						$model_svc = $dt['Type'];
	
						#-------------------------------- inject blm ada di prospectmst
						$customer = $dt['CUSTOMER'];
						$alamat = $dt['CUST_ADRES'];
						$kota = $dt['KOTA'];
						$kecamatan = $dt['KECAMATAN'];
						$kodepos = $dt['KODEPOS'];
						$telp = $dt['TELPON'];
						$gsm = $dt['GSM'];
						$cdma = $dt['CDMA'];
						$km_akhir = $dt['KmNow'];
						$kodesupervisor = $dt['Kode_Supervisor'];
						
						//echo "__XX__".$kodepr.$kodeprospect."__kd<br>";
						#--------------- BRDIGING NASA SALES
						$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
												left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
												where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
												order by a.tgl_update desc",$conn);
						$dt_grd = mssql_fetch_array($qry_grd);
						$nik = $dt_grd['nik'];
						$kodepesan = "REMINDER";
						$keterangan = $nopolisi."#".$model_svc."#".$nama_svc."#".$tglJthTempo."#10000";
						
						$qry_cek = mssql_query("select norangka from ".DB_REPLBENGKEL."..NotifHansaRenkei
												where norangka = '".$norangka."' and tglnotif = '".$TglPlan."'",$conn);
						
						$jml_cek = mssql_num_rows($qry_cek);
						
						if ($jml_cek==0) {
							$inst_hansa = mssql_query("insert into ".DB_REPLBENGKEL."..NotifHansaRenkei
									 (kodesumber, Tipe, nowo, norangka, kodedealer, kodepesan,kodesales, nik, tglnotif, proses, 
									 tglentry, keterangan, userentry)
									values ('GRP', 'REMINDER', '".$nowo."', '".$norangka."', '".$kodecabang."','".$kodepesan."',  '".$kdSales."', '".$nik."', '".$TglPlan."', '0', 
									getdate(), '".$keterangan."', 'REMINDER')",$conn);
						}
						
							
					}
				}				
			}
			
		}
		/*END RETENSI SERVICE 50000KM*/
		
		
		/*RETENSI SERVICE 60000KM*/
		if($ret_service_60000){
			$km = 60000;
			$re = RetensiServiceNoRangkaNew($km,$ret_hansa_tempo);
			if($re){
				while ($dt = mssql_fetch_array($re)) {
					$kodepr = $dt['KodePr'];
					$kodeprospect = $dt['KodeProspect'];
					$kdSales = $dt['SalesPr'];
					$norangka = $dt['NoRangka'];
					$type = $dt['Type'];
					$tglJthTempo = $dt['DateNextSvc'];
					
					$nowo = $dt['NoWo'];
					$nopolisi = $dt['NoPolisi'];
					$nama_svc = $dt['CUSTOMER'];
					$model_svc = $dt['Type'];
					
					
					#-------------------------------- inject blm ada di prospectmst
					$customer = $dt['CUSTOMER'];
					$alamat = $dt['CUST_ADRES'];
					$kota = $dt['KOTA'];
					$kecamatan = $dt['KECAMATAN'];
					$kodepos = $dt['KODEPOS'];
					$telp = $dt['TELPON'];
					$gsm = $dt['GSM'];
					$cdma = $dt['CDMA'];
					$km_akhir = $dt['KmNow'];
					$kodesupervisor = $dt['Kode_Supervisor'];
										
					if (empty($kodepr) and empty($kodeprospect)) {
						$InstMst = InsertMasterProspect($customer,$kodepr, $alamat, $kota, $kecamatan, $kodepos, $telp, $gsm, $cdma, $norangka, $km_akhir, $kdSales, $kodesupervisor);
						$kodepr = $InsMst['kodepr'];
						$kodeprospect = $InsMst['kodeprospect'];
					} 
					$tdkaktif = $dt['tdkaktif'];
					
					#--------------- BRDIGING NASA SALES
					$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
											left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
											where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
											order by a.tgl_update desc",$conn);
					$dt_grd = mssql_fetch_array($qry_grd);
					$nik = $dt_grd['nik'];
					$keterangan = $nopolisi."#".$model_svc."#".$nama_svc."#".$tglJthTempo."#60000";
					
					
					//$TglPlan = Date_Add($tglJthTempo,"-3 days");
					/*$TglPlan = Date_Add($tglJthTempo,"-$ret_hansa_tempo days");
					##cek Plan
					$cekPlan1 = "SELECT KodePlan from PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					*/
					
					$TglPlan = date('m/d/Y',strtotime($tglJthTempo . "-$ret_hansa_tempo days"));
				
					##cek Plan
					$cekPlan1 = "SELECT KodePlan from ".$db_stars."..PlanTrn 
								where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					$reCekPlan1 = mssql_query($cekPlan1,$conn);
					$jmlreCekPlan1 = mssql_num_rows($reCekPlan1);
					
					if($jmlreCekPlan1==0){
						$cekPlan1 = "SELECT KodePlan from ".$db_stars."..PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3";
						$reCekPlan1 = mssql_query($cekPlan1,$conn);
						$reCekPlan1dat = mssql_fetch_array($reCekPlan1);
						
						//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);	
						$kodeplan = empty($reCekPlan1dat['KodePlan']) ? 1 : $reCekPlan1dat['KodePlan'];					
						
						##generate kodeplan
						$nKode = intval(substr($kodeplan, -4));
						$vKode = TRUE;
						while ($vKode) {
							$kodeplan = "PLRT".date('y').str_pad($nKode, 4, "0",STR_PAD_LEFT);
							/*
							$cek = $this->mod_plan->CekDataPlanTrn(" kodepr='$kodepr' AND kodeprospect = '$kodeprospect' AND kodeplan = '$kodeplan' ");
							$vKode = $cek[0];
							$nKode++;
							*/
							
							$qryKode = mssql_query("select kodepr from ".$db_stars."..plantrn 
											where kodepr='$kodepr' AND kodeprospect = '$kodeprospect' AND kodeplan = '$kodeplan'",$conn);
							//$vKode = $cek[0];
							$jmlKode = mssql_num_rows($qryKode);
							
							if($jmlKode>0){
								$vKode = TRUE;
							} else {
								$vKode = FALSE;
							}
							$nKode++;	
						}
						// echo "$kodepr - $kodeprospect - $kodeplan<br>";
						##ketplan
						$idKetPlan = 44;
						$KetPlan = "Reminder service 60000 Km";
						##buat plan
						/*$plan_svc = array("SbrPlan"=>"1",
							  "KodePr"=>$kodepr, 
							  "KodeProspect"=>$kodeprospect, 
							  "TglPlan"=>format_date($TglPlan),
							  "Via"=>null,
							  "KodePlan"=>$kodeplan,
							  "TypePlan"=>3,
							  "idKetPlan"=>$idKetPlan,
							  "KetPlan"=>$KetPlan,
							  // "[Yang Berulang Tahun]"=>$namaultah,
							  // "[Tanggal Lahir]"=>$tglultah,
							  "[Tanggal Jatuh Tempo]"=>format_date($tglJthTempo),
							  "NoRangka"=>$norangka,
							  "Type"=>$type,
							  "KodeSales"=>$kdSales, 
							  "Kodeuser"=>"AutoPlanSystem", 
							  "TglEntry"=>$getdate, );
						// var_dump($plan_svc);
						$inst = $this->mod_plan->InsertPlanTrn($plan_svc);
						*/
						
						if ($tdkaktif==0) {
							$jenis_service = "Service ".$km." KM";	
							$inst = mssql_query("insert into ".$db_stars."..plantrn (SbrPlan,KodePr,KodeProspect,TglPlan,
								KodePlan,TypePlan,idKetPlan,KetPlan, 
								[Tanggal Jatuh Tempo], NoRangka, Type,
								KodeSales,Kodeuser,TglEntry)
								values ('1', '".$kodepr."', '".$kodeprospect."', '".$TglPlan."',			
								'".$kodeplan."', 3, '".$idKetPlan."', '".$KetPlan."', 
								'".$tglJthTempo."', '".$norangka."', '".$type."',
								'".$kdSales."','AutoPlanSystem',getdate())",$conn);
								
							/*if ($inst and HANSA==1) {
								$kodepesan = "REMINDER";
								$inst_hansa = mssql_query("insert into ".DB_REPLBENGKEL."..NotifHansaRenkei
										 (kodesumber, Tipe, nowo, norangka, kodedealer, kodepesan,kodesales, nik, tglnotif, proses, 
										 tglentry, keterangan, userentry)
										values ('GRP', 'REMINDER', '".$nowo."', '".$norangka."', '".$kodecabang."','".$kodepesan."',  '".$kdSales."', '".$nik."', '".$TglPlan."', '0', 
										getdate(), '".$keterangan."', 'REMINDER')",$conn);
							}*/	
						}
						
					
					}


				}
			}
			
			#--------------------- customer
			if (HANSA==1) {
				$re_cust = RetensiServiceNoRangkaCustomer($km,$ret_hansa_tempo_cust);
				if($re_cust){
					while ($dt = mssql_fetch_array($re_cust)) {
						$kodepr = $dt['KodePr'];
						$kodeprospect = $dt['KodeProspect'];
						$kdSales = $dt['SalesPr'];
						$norangka = $dt['NoRangka'];
						$type = $dt['Type'];
						$tglJthTempo = $dt['DateNextSvc'];
						
						$nowo = $dt['NoWo'];
						$nopolisi = $dt['NoPolisi'];
						$nama_svc = $dt['CUSTOMER'];
						$model_svc = $dt['Type'];
	
						#-------------------------------- inject blm ada di prospectmst
						$customer = $dt['CUSTOMER'];
						$alamat = $dt['CUST_ADRES'];
						$kota = $dt['KOTA'];
						$kecamatan = $dt['KECAMATAN'];
						$kodepos = $dt['KODEPOS'];
						$telp = $dt['TELPON'];
						$gsm = $dt['GSM'];
						$cdma = $dt['CDMA'];
						$km_akhir = $dt['KmNow'];
						$kodesupervisor = $dt['Kode_Supervisor'];
						
						//echo "__XX__".$kodepr.$kodeprospect."__kd<br>";
						#--------------- BRDIGING NASA SALES
						$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
												left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
												where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
												order by a.tgl_update desc",$conn);
						$dt_grd = mssql_fetch_array($qry_grd);
						$nik = $dt_grd['nik'];
						$kodepesan = "REMINDER";
						$keterangan = $nopolisi."#".$model_svc."#".$nama_svc."#".$tglJthTempo."#10000";
						
						$qry_cek = mssql_query("select norangka from ".DB_REPLBENGKEL."..NotifHansaRenkei
												where norangka = '".$norangka."' and tglnotif = '".$TglPlan."'",$conn);
						
						$jml_cek = mssql_num_rows($qry_cek);
						
						if ($jml_cek==0) {
							$inst_hansa = mssql_query("insert into ".DB_REPLBENGKEL."..NotifHansaRenkei
									 (kodesumber, Tipe, nowo, norangka, kodedealer, kodepesan,kodesales, nik, tglnotif, proses, 
									 tglentry, keterangan, userentry)
									values ('GRP', 'REMINDER', '".$nowo."', '".$norangka."', '".$kodecabang."','".$kodepesan."',  '".$kdSales."', '".$nik."', '".$TglPlan."', '0', 
									getdate(), '".$keterangan."', 'REMINDER')",$conn);
						}
							
					}
				}				
			}
			
		}
		/*END RETENSI SERVICE 60000KM*/
		
		/*RETENSI SERVICE 70000KM*/
		if($ret_service_70000){
			$km = 70000;
			$re = RetensiServiceNoRangkaNew($km,$ret_hansa_tempo);
			if($re){
				while ($dt = mssql_fetch_array($re)) {
					$kodepr = $dt['KodePr'];
					$kodeprospect = $dt['KodeProspect'];
					$kdSales = $dt['SalesPr'];
					$norangka = $dt['NoRangka'];
					$type = $dt['Type'];
					$tglJthTempo = $dt['DateNextSvc'];
					
					$nowo = $dt['NoWo'];
					$nopolisi = $dt['NoPolisi'];
					$nama_svc = $dt['CUSTOMER'];
					$model_svc = $dt['Type'];
					
					
					#-------------------------------- inject blm ada di prospectmst
					$customer = $dt['CUSTOMER'];
					$alamat = $dt['CUST_ADRES'];
					$kota = $dt['KOTA'];
					$kecamatan = $dt['KECAMATAN'];
					$kodepos = $dt['KODEPOS'];
					$telp = $dt['TELPON'];
					$gsm = $dt['GSM'];
					$cdma = $dt['CDMA'];
					$km_akhir = $dt['KmNow'];
					$kodesupervisor = $dt['Kode_Supervisor'];
										
					if (empty($kodepr) and empty($kodeprospect)) {
						$InstMst = InsertMasterProspect($customer,$kodepr, $alamat, $kota, $kecamatan, $kodepos, $telp, $gsm, $cdma, $norangka, $km_akhir, $kdSales, $kodesupervisor);
						$kodepr = $InsMst['kodepr'];
						$kodeprospect = $InsMst['kodeprospect'];
					} 
					$tdkaktif = $dt['tdkaktif'];
					
					#--------------- BRDIGING NASA SALES
					$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
											left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
											where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
											order by a.tgl_update desc",$conn);
					$dt_grd = mssql_fetch_array($qry_grd);
					$nik = $dt_grd['nik'];
					$keterangan = $nopolisi."#".$model_svc."#".$nama_svc."#".$tglJthTempo."#70000";
					
					
					//$TglPlan = Date_Add($tglJthTempo,"-3 days");
					/*$TglPlan = Date_Add($tglJthTempo,"-$ret_hansa_tempo days");
					##cek Plan
					$cekPlan1 = "SELECT KodePlan from PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					*/
					
					$TglPlan = date('m/d/Y',strtotime($tglJthTempo . "-$ret_hansa_tempo days"));
				
					##cek Plan
					$cekPlan1 = "SELECT KodePlan from ".$db_stars."..PlanTrn 
								where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					$reCekPlan1 = mssql_query($cekPlan1,$conn);
					$jmlreCekPlan1 = mssql_num_rows($reCekPlan1);
					
					if($jmlreCekPlan1==0){
						$cekPlan1 = "SELECT KodePlan from ".$db_stars."..PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3";
						$reCekPlan1 = mssql_query($cekPlan1,$conn);
						$reCekPlan1dat = mssql_fetch_array($reCekPlan1);
						
						//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);	
						$kodeplan = empty($reCekPlan1dat['KodePlan']) ? 1 : $reCekPlan1dat['KodePlan'];					
						
						##generate kodeplan
						$nKode = intval(substr($kodeplan, -4));
						$vKode = TRUE;
						while ($vKode) {
							$kodeplan = "PLRT".date('y').str_pad($nKode, 4, "0",STR_PAD_LEFT);
							/*
							$cek = $this->mod_plan->CekDataPlanTrn(" kodepr='$kodepr' AND kodeprospect = '$kodeprospect' AND kodeplan = '$kodeplan' ");
							$vKode = $cek[0];
							$nKode++;
							*/
							
							$qryKode = mssql_query("select kodepr from ".$db_stars."..plantrn 
											where kodepr='$kodepr' AND kodeprospect = '$kodeprospect' AND kodeplan = '$kodeplan'",$conn);
							//$vKode = $cek[0];
							$jmlKode = mssql_num_rows($qryKode);
							
							if($jmlKode>0){
								$vKode = TRUE;
							} else {
								$vKode = FALSE;
							}
							$nKode++;	
						}
						// echo "$kodepr - $kodeprospect - $kodeplan<br>";
						##ketplan
						$idKetPlan = 45;
						$KetPlan = "Reminder service 70000 Km";
						##buat plan
						/*$plan_svc = array("SbrPlan"=>"1",
							  "KodePr"=>$kodepr, 
							  "KodeProspect"=>$kodeprospect, 
							  "TglPlan"=>format_date($TglPlan),
							  "Via"=>null,
							  "KodePlan"=>$kodeplan,
							  "TypePlan"=>3,
							  "idKetPlan"=>$idKetPlan,
							  "KetPlan"=>$KetPlan,
							  // "[Yang Berulang Tahun]"=>$namaultah,
							  // "[Tanggal Lahir]"=>$tglultah,
							  "[Tanggal Jatuh Tempo]"=>format_date($tglJthTempo),
							  "NoRangka"=>$norangka,
							  "Type"=>$type,
							  "KodeSales"=>$kdSales, 
							  "Kodeuser"=>"AutoPlanSystem", 
							  "TglEntry"=>$getdate, );
						// var_dump($plan_svc);
						$inst = $this->mod_plan->InsertPlanTrn($plan_svc);
						*/
						
						if ($tdkaktif==0) {
							$jenis_service = "Service ".$km." KM";	
							$inst = mssql_query("insert into ".$db_stars."..plantrn (SbrPlan,KodePr,KodeProspect,TglPlan,
								KodePlan,TypePlan,idKetPlan,KetPlan, 
								[Tanggal Jatuh Tempo], NoRangka, Type,
								KodeSales,Kodeuser,TglEntry)
								values ('1', '".$kodepr."', '".$kodeprospect."', '".$TglPlan."',			
								'".$kodeplan."', 3, '".$idKetPlan."', '".$KetPlan."', 
								'".$tglJthTempo."', '".$norangka."', '".$type."',
								'".$kdSales."','AutoPlanSystem',getdate())",$conn);
								
							/*if ($inst and HANSA==1) {
								$kodepesan = "REMINDER";
								$inst_hansa = mssql_query("insert into ".DB_REPLBENGKEL."..NotifHansaRenkei
										 (kodesumber, Tipe, nowo, norangka, kodedealer, kodepesan,kodesales, nik, tglnotif, proses, 
										 tglentry, keterangan, userentry)
										values ('GRP', 'REMINDER', '".$nowo."', '".$norangka."', '".$kodecabang."','".$kodepesan."',  '".$kdSales."', '".$nik."', '".$TglPlan."', '0', 
										getdate(), '".$keterangan."', 'REMINDER')",$conn);
							}*/	
						}
						
						
					
					}


				}
			}
			
			#--------------------- customer
			if (HANSA==1) {
				$re_cust = RetensiServiceNoRangkaCustomer($km,$ret_hansa_tempo_cust);
				if($re_cust){
					while ($dt = mssql_fetch_array($re_cust)) {
						$kodepr = $dt['KodePr'];
						$kodeprospect = $dt['KodeProspect'];
						$kdSales = $dt['SalesPr'];
						$norangka = $dt['NoRangka'];
						$type = $dt['Type'];
						$tglJthTempo = $dt['DateNextSvc'];
						
						$nowo = $dt['NoWo'];
						$nopolisi = $dt['NoPolisi'];
						$nama_svc = $dt['CUSTOMER'];
						$model_svc = $dt['Type'];
	
						#-------------------------------- inject blm ada di prospectmst
						$customer = $dt['CUSTOMER'];
						$alamat = $dt['CUST_ADRES'];
						$kota = $dt['KOTA'];
						$kecamatan = $dt['KECAMATAN'];
						$kodepos = $dt['KODEPOS'];
						$telp = $dt['TELPON'];
						$gsm = $dt['GSM'];
						$cdma = $dt['CDMA'];
						$km_akhir = $dt['KmNow'];
						$kodesupervisor = $dt['Kode_Supervisor'];
						
						//echo "__XX__".$kodepr.$kodeprospect."__kd<br>";
						#--------------- BRDIGING NASA SALES
						$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
												left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
												where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
												order by a.tgl_update desc",$conn);
						$dt_grd = mssql_fetch_array($qry_grd);
						$nik = $dt_grd['nik'];
						$kodepesan = "REMINDER";
						$keterangan = $nopolisi."#".$model_svc."#".$nama_svc."#".$tglJthTempo."#10000";
						
						$qry_cek = mssql_query("select norangka from ".DB_REPLBENGKEL."..NotifHansaRenkei
												where norangka = '".$norangka."' and tglnotif = '".$TglPlan."'",$conn);
						
						$jml_cek = mssql_num_rows($qry_cek);
						
						if ($jml_cek==0) {
							$inst_hansa = mssql_query("insert into ".DB_REPLBENGKEL."..NotifHansaRenkei
									 (kodesumber, Tipe, nowo, norangka, kodedealer, kodepesan,kodesales, nik, tglnotif, proses, 
									 tglentry, keterangan, userentry)
									values ('GRP', 'REMINDER', '".$nowo."', '".$norangka."', '".$kodecabang."','".$kodepesan."',  '".$kdSales."', '".$nik."', '".$TglPlan."', '0', 
									getdate(), '".$keterangan."', 'REMINDER')",$conn);
						}
							
					}
				}				
			}
			
		}
		/*END RETENSI SERVICE 70000KM*/
		
		/*RETENSI SERVICE 80000KM*/
		if($ret_service_80000){
			$km = 80000;
			$re = RetensiServiceNoRangkaNew($km,$ret_hansa_tempo);
			if($re){
				while ($dt = mssql_fetch_array($re)) {
					$kodepr = $dt['KodePr'];
					$kodeprospect = $dt['KodeProspect'];
					$kdSales = $dt['SalesPr'];
					$norangka = $dt['NoRangka'];
					$type = $dt['Type'];
					$tglJthTempo = $dt['DateNextSvc'];
					
					$nowo = $dt['NoWo'];
					$nopolisi = $dt['NoPolisi'];
					$nama_svc = $dt['CUSTOMER'];
					$model_svc = $dt['Type'];
					
					#-------------------------------- inject blm ada di prospectmst
					$customer = $dt['CUSTOMER'];
					$alamat = $dt['CUST_ADRES'];
					$kota = $dt['KOTA'];
					$kecamatan = $dt['KECAMATAN'];
					$kodepos = $dt['KODEPOS'];
					$telp = $dt['TELPON'];
					$gsm = $dt['GSM'];
					$cdma = $dt['CDMA'];
					$km_akhir = $dt['KmNow'];
					$kodesupervisor = $dt['Kode_Supervisor'];
										
					if (empty($kodepr) and empty($kodeprospect)) {
						$InstMst = InsertMasterProspect($customer,$kodepr, $alamat, $kota, $kecamatan, $kodepos, $telp, $gsm, $cdma, $norangka, $km_akhir, $kdSales, $kodesupervisor);
						$kodepr = $InsMst['kodepr'];
						$kodeprospect = $InsMst['kodeprospect'];
					} 
					
					
					
					#--------------- BRDIGING NASA SALES
					$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
											left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
											where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
											order by a.tgl_update desc",$conn);
					$dt_grd = mssql_fetch_array($qry_grd);
					$nik = $dt_grd['nik'];
					$keterangan = $nopolisi."#".$model_svc."#".$nama_svc."#".$tglJthTempo."#80000";
					
					
					//$TglPlan = Date_Add($tglJthTempo,"-3 days");
					
					/*$TglPlan = Date_Add($tglJthTempo,"-$ret_hansa_tempo days");
					##cek Plan
					$cekPlan1 = "SELECT KodePlan from PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					*/
					
					$TglPlan = date('m/d/Y',strtotime($tglJthTempo . "-$ret_hansa_tempo days"));
				
					##cek Plan
					$cekPlan1 = "SELECT KodePlan from ".$db_stars."..PlanTrn 
								where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					$reCekPlan1 = mssql_query($cekPlan1,$conn);
					$jmlreCekPlan1 = mssql_num_rows($reCekPlan1);
					
					if($jmlreCekPlan1==0){
						$cekPlan1 = "SELECT KodePlan from ".$db_stars."..PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3";
						$reCekPlan1 = mssql_query($cekPlan1,$conn);
						$reCekPlan1dat = mssql_fetch_array($reCekPlan1);
						
						//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);	
						$kodeplan = empty($reCekPlan1dat['KodePlan']) ? 1 : $reCekPlan1dat['KodePlan'];					
						
						##generate kodeplan
						$nKode = intval(substr($kodeplan, -4));
						$vKode = TRUE;
						while ($vKode) {
							$kodeplan = "PLRT".date('y').str_pad($nKode, 4, "0",STR_PAD_LEFT);
							/*
							$cek = $this->mod_plan->CekDataPlanTrn(" kodepr='$kodepr' AND kodeprospect = '$kodeprospect' AND kodeplan = '$kodeplan' ");
							$vKode = $cek[0];
							$nKode++;
							*/
							
							$qryKode = mssql_query("select kodepr from ".$db_stars."..plantrn 
											where kodepr='$kodepr' AND kodeprospect = '$kodeprospect' AND kodeplan = '$kodeplan'",$conn);
							//$vKode = $cek[0];
							$jmlKode = mssql_num_rows($qryKode);
							
							if($jmlKode>0){
								$vKode = TRUE;
							} else {
								$vKode = FALSE;
							}
							$nKode++;	
						}

						// echo "$kodepr - $kodeprospect - $kodeplan<br>";
						##ketplan
						$idKetPlan = 46;
						$KetPlan = "Reminder service 80000 Km";
						##buat plan
						/*$plan_svc = array("SbrPlan"=>"1",
							  "KodePr"=>$kodepr, 
							  "KodeProspect"=>$kodeprospect, 
							  "TglPlan"=>format_date($TglPlan),
							  "Via"=>null,
							  "KodePlan"=>$kodeplan,
							  "TypePlan"=>3,
							  "idKetPlan"=>$idKetPlan,
							  "KetPlan"=>$KetPlan,
							  // "[Yang Berulang Tahun]"=>$namaultah,
							  // "[Tanggal Lahir]"=>$tglultah,
							  "[Tanggal Jatuh Tempo]"=>format_date($tglJthTempo),
							  "NoRangka"=>$norangka,
							  "Type"=>$type,
							  "KodeSales"=>$kdSales, 
							  "Kodeuser"=>"AutoPlanSystem", 
							  "TglEntry"=>$getdate, );
						// var_dump($plan_svc);
						$inst = $this->mod_plan->InsertPlanTrn($plan_svc);
						*/
						if ($tdkaktif==0) {		
							$jenis_service = "Service ".$km." KM";	
							$inst = mssql_query("insert into ".$db_stars."..plantrn (SbrPlan,KodePr,KodeProspect,TglPlan,
								KodePlan,TypePlan,idKetPlan,KetPlan, 
								[Tanggal Jatuh Tempo], NoRangka, Type,
								KodeSales,Kodeuser,TglEntry)
								values ('1', '".$kodepr."', '".$kodeprospect."', '".$TglPlan."',			
								'".$kodeplan."', 3, '".$idKetPlan."', '".$KetPlan."', 
								'".$tglJthTempo."', '".$norangka."', '".$type."',
								'".$kdSales."','AutoPlanSystem',getdate())",$conn);
						
							/*if ($inst and HANSA==1) {
								$kodepesan = "REMINDER";
								$inst_hansa = mssql_query("insert into ".DB_REPLBENGKEL."..NotifHansaRenkei
										 (kodesumber, Tipe, nowo, norangka, kodedealer, kodepesan,kodesales, nik, tglnotif, proses, 
										 tglentry, keterangan, userentry)
										values ('GRP', 'REMINDER', '".$nowo."', '".$norangka."', '".$kodecabang."','".$kodepesan."',  '".$kdSales."', '".$nik."', '".$TglPlan."', '0', 
										getdate(), '".$keterangan."', 'REMINDER')",$conn);
							}*/	
						}
						
						
					}


				}
			}
			
			#--------------------- customer
			if (HANSA==1) {
				$re_cust = RetensiServiceNoRangkaCustomer($km,$ret_hansa_tempo_cust);
				if($re_cust){
					while ($dt = mssql_fetch_array($re_cust)) {
						$kodepr = $dt['KodePr'];
						$kodeprospect = $dt['KodeProspect'];
						$kdSales = $dt['SalesPr'];
						$norangka = $dt['NoRangka'];
						$type = $dt['Type'];
						$tglJthTempo = $dt['DateNextSvc'];
						
						$nowo = $dt['NoWo'];
						$nopolisi = $dt['NoPolisi'];
						$nama_svc = $dt['CUSTOMER'];
						$model_svc = $dt['Type'];
	
						#-------------------------------- inject blm ada di prospectmst
						$customer = $dt['CUSTOMER'];
						$alamat = $dt['CUST_ADRES'];
						$kota = $dt['KOTA'];
						$kecamatan = $dt['KECAMATAN'];
						$kodepos = $dt['KODEPOS'];
						$telp = $dt['TELPON'];
						$gsm = $dt['GSM'];

						$cdma = $dt['CDMA'];
						$km_akhir = $dt['KmNow'];
						$kodesupervisor = $dt['Kode_Supervisor'];
						
						//echo "__XX__".$kodepr.$kodeprospect."__kd<br>";
						#--------------- BRDIGING NASA SALES
						$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
												left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
												where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
												order by a.tgl_update desc",$conn);
						$dt_grd = mssql_fetch_array($qry_grd);
						$nik = $dt_grd['nik'];
						$kodepesan = "REMINDER";
						$keterangan = $nopolisi."#".$model_svc."#".$nama_svc."#".$tglJthTempo."#10000";
						
						$qry_cek = mssql_query("select norangka from ".DB_REPLBENGKEL."..NotifHansaRenkei
												where norangka = '".$norangka."' and tglnotif = '".$TglPlan."'",$conn);
						
						$jml_cek = mssql_num_rows($qry_cek);
						
						if ($jml_cek==0) {
							$inst_hansa = mssql_query("insert into ".DB_REPLBENGKEL."..NotifHansaRenkei
									 (kodesumber, Tipe, nowo, norangka, kodedealer, kodepesan,kodesales, nik, tglnotif, proses, 
									 tglentry, keterangan, userentry)
									values ('GRP', 'REMINDER', '".$nowo."', '".$norangka."', '".$kodecabang."','".$kodepesan."',  '".$kdSales."', '".$nik."', '".$TglPlan."', '0', 
									getdate(), '".$keterangan."', 'REMINDER')",$conn);
						}
						
							
					}
				}				
			}
			
		}
		/*END RETENSI SERVICE 80000KM*/
		
		/*RETENSI SERVICE 90000KM*/
		if($ret_service_90000){
			$km = 90000;
			$re = RetensiServiceNoRangkaNew($km,$ret_hansa_tempo);
			if($re){
				while ($dt = mssql_fetch_array($re)) {
					$kodepr = $dt['KodePr'];
					$kodeprospect = $dt['KodeProspect'];
					$kdSales = $dt['SalesPr'];
					$norangka = $dt['NoRangka'];
					$type = $dt['Type'];
					$tglJthTempo = $dt['DateNextSvc'];
					
					$nowo = $dt['NoWo'];
					$nopolisi = $dt['NoPolisi'];
					$nama_svc = $dt['CUSTOMER'];
					$model_svc = $dt['Type'];
					
					#-------------------------------- inject blm ada di prospectmst
					$customer = $dt['CUSTOMER'];
					$alamat = $dt['CUST_ADRES'];
					$kota = $dt['KOTA'];
					$kecamatan = $dt['KECAMATAN'];
					$kodepos = $dt['KODEPOS'];
					$telp = $dt['TELPON'];
					$gsm = $dt['GSM'];
					$cdma = $dt['CDMA'];
					$km_akhir = $dt['KmNow'];
					$kodesupervisor = $dt['Kode_Supervisor'];
										
					if (empty($kodepr) and empty($kodeprospect)) {
						$InstMst = InsertMasterProspect($customer,$kodepr, $alamat, $kota, $kecamatan, $kodepos, $telp, $gsm, $cdma, $norangka, $km_akhir, $kdSales, $kodesupervisor);
						$kodepr = $InsMst['kodepr'];
						$kodeprospect = $InsMst['kodeprospect'];
					} 
					$tdkaktif = $dt['tdkaktif'];	
					
					#--------------- BRDIGING NASA SALES
					$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
											left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
											where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
											order by a.tgl_update desc",$conn);
					$dt_grd = mssql_fetch_array($qry_grd);
					$nik = $dt_grd['nik'];
					$keterangan = $nopolisi."#".$model_svc."#".$nama_svc."#".$tglJthTempo."#90000";
					
					
					//$TglPlan = Date_Add($tglJthTempo,"-3 days");
					/*$TglPlan = Date_Add($tglJthTempo,"-$ret_hansa_tempo days");
					##cek Plan
					$cekPlan1 = "SELECT KodePlan from PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					*/
					
					$TglPlan = date('m/d/Y',strtotime($tglJthTempo . "-$ret_hansa_tempo days"));
				
					##cek Plan
					$cekPlan1 = "SELECT KodePlan from ".$db_stars."..PlanTrn 
								where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					$reCekPlan1 = mssql_query($cekPlan1,$conn);
					$jmlreCekPlan1 = mssql_num_rows($reCekPlan1);
					
					if($jmlreCekPlan1==0){
						$cekPlan1 = "SELECT KodePlan from ".$db_stars."..PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3";
						$reCekPlan1 = mssql_query($cekPlan1,$conn);
						$reCekPlan1dat = mssql_fetch_array($reCekPlan1);
						
						//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);	
						$kodeplan = empty($reCekPlan1dat['KodePlan']) ? 1 : $reCekPlan1dat['KodePlan'];					
						
						##generate kodeplan
						$nKode = intval(substr($kodeplan, -4));
						$vKode = TRUE;
						while ($vKode) {
							$kodeplan = "PLRT".date('y').str_pad($nKode, 4, "0",STR_PAD_LEFT);
							/*
							$cek = $this->mod_plan->CekDataPlanTrn(" kodepr='$kodepr' AND kodeprospect = '$kodeprospect' AND kodeplan = '$kodeplan' ");
							$vKode = $cek[0];
							$nKode++;
							*/
							
							$qryKode = mssql_query("select kodepr from ".$db_stars."..plantrn 
											where kodepr='$kodepr' AND kodeprospect = '$kodeprospect' AND kodeplan = '$kodeplan'",$conn);
							//$vKode = $cek[0];
							$jmlKode = mssql_num_rows($qryKode);
							
							if($jmlKode>0){
								$vKode = TRUE;
							} else {
								$vKode = FALSE;
							}
							$nKode++;	
						}

						// echo "$kodepr - $kodeprospect - $kodeplan<br>";
						##ketplan
						$idKetPlan = 47;
						$KetPlan = "Reminder service 90000 Km";
						##buat plan
						/*$plan_svc = array("SbrPlan"=>"1",
							  "KodePr"=>$kodepr, 
							  "KodeProspect"=>$kodeprospect, 
							  "TglPlan"=>format_date($TglPlan),
							  "Via"=>null,
							  "KodePlan"=>$kodeplan,
							  "TypePlan"=>3,
							  "idKetPlan"=>$idKetPlan,
							  "KetPlan"=>$KetPlan,
							  // "[Yang Berulang Tahun]"=>$namaultah,
							  // "[Tanggal Lahir]"=>$tglultah,
							  "[Tanggal Jatuh Tempo]"=>format_date($tglJthTempo),
							  "NoRangka"=>$norangka,
							  "Type"=>$type,
							  "KodeSales"=>$kdSales, 
							  "Kodeuser"=>"AutoPlanSystem", 
							  "TglEntry"=>$getdate, );
						// var_dump($plan_svc);
						$inst = $this->mod_plan->InsertPlanTrn($plan_svc);
						*/
						if ($tdkaktif==0) {
							$jenis_service = "Service ".$km." KM";	
							$inst = mssql_query("insert into ".$db_stars."..plantrn (SbrPlan,KodePr,KodeProspect,TglPlan,
								KodePlan,TypePlan,idKetPlan,KetPlan, 
								[Tanggal Jatuh Tempo], NoRangka, Type,
								KodeSales,Kodeuser,TglEntry)
								values ('1', '".$kodepr."', '".$kodeprospect."', '".$TglPlan."',			
								'".$kodeplan."', 3, '".$idKetPlan."', '".$KetPlan."', 
								'".$tglJthTempo."', '".$norangka."', '".$type."',
								'".$kdSales."','AutoPlanSystem',getdate())",$conn);
								
							/*if ($inst and HANSA==1) {
								$kodepesan = "REMINDER";
								$inst_hansa = mssql_query("insert into ".DB_REPLBENGKEL."..NotifHansaRenkei
										 (kodesumber, Tipe, nowo, norangka, kodedealer, kodepesan,kodesales, nik, tglnotif, proses, 
										 tglentry, keterangan, userentry)
										values ('GRP', 'REMINDER', '".$nowo."', '".$norangka."', '".$kodecabang."','".$kodepesan."',  '".$kdSales."', '".$nik."', '".$TglPlan."', '0', 
										getdate(), '".$keterangan."', 'REMINDER')",$conn);
							}*/	
						}
						
					}


				}
			}
			
			#--------------------- customer
			if (HANSA==1) {
				$re_cust = RetensiServiceNoRangkaCustomer($km,$ret_hansa_tempo_cust);
				if($re_cust){
					while ($dt = mssql_fetch_array($re_cust)) {
						$kodepr = $dt['KodePr'];
						$kodeprospect = $dt['KodeProspect'];
						$kdSales = $dt['SalesPr'];
						$norangka = $dt['NoRangka'];
						$type = $dt['Type'];
						$tglJthTempo = $dt['DateNextSvc'];
						
						$nowo = $dt['NoWo'];
						$nopolisi = $dt['NoPolisi'];
						$nama_svc = $dt['CUSTOMER'];
						$model_svc = $dt['Type'];
	
						#-------------------------------- inject blm ada di prospectmst
						$customer = $dt['CUSTOMER'];
						$alamat = $dt['CUST_ADRES'];
						$kota = $dt['KOTA'];
						$kecamatan = $dt['KECAMATAN'];
						$kodepos = $dt['KODEPOS'];
						$telp = $dt['TELPON'];
						$gsm = $dt['GSM'];
						$cdma = $dt['CDMA'];
						$km_akhir = $dt['KmNow'];
						$kodesupervisor = $dt['Kode_Supervisor'];
						
						//echo "__XX__".$kodepr.$kodeprospect."__kd<br>";
						#--------------- BRDIGING NASA SALES
						$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
												left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
												where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
												order by a.tgl_update desc",$conn);
						$dt_grd = mssql_fetch_array($qry_grd);
						$nik = $dt_grd['nik'];
						$kodepesan = "REMINDER";
						$keterangan = $nopolisi."#".$model_svc."#".$nama_svc."#".$tglJthTempo."#10000";
						
						$qry_cek = mssql_query("select norangka from ".DB_REPLBENGKEL."..NotifHansaRenkei
												where norangka = '".$norangka."' and tglnotif = '".$TglPlan."'",$conn);
						
						$jml_cek = mssql_num_rows($qry_cek);
						
						if ($jml_cek==0) {
							$inst_hansa = mssql_query("insert into ".DB_REPLBENGKEL."..NotifHansaRenkei
									 (kodesumber, Tipe, nowo, norangka, kodedealer, kodepesan,kodesales, nik, tglnotif, proses, 
									 tglentry, keterangan, userentry)
									values ('GRP', 'REMINDER', '".$nowo."', '".$norangka."', '".$kodecabang."','".$kodepesan."',  '".$kdSales."', '".$nik."', '".$TglPlan."', '0', 
									getdate(), '".$keterangan."', 'REMINDER')",$conn);
						}
						
							
					}
				}				
			}
		}
		/*END RETENSI SERVICE 90000KM*/
		
		/*RETENSI SERVICE 100000KM*/
		if($ret_service_100000){
			$km = 100000;
			$re = RetensiServiceNoRangkaNew($km,$ret_hansa_tempo);
			if($re){
				while ($dt = mssql_fetch_array($re)) {
					$kodepr = $dt['KodePr'];
					$kodeprospect = $dt['KodeProspect'];
					$kdSales = $dt['SalesPr'];
					$norangka = $dt['NoRangka'];
					$type = $dt['Type'];
					$tglJthTempo = $dt['DateNextSvc'];
					
					$nowo = $dt['NoWo'];
					$nopolisi = $dt['NoPolisi'];
					$nama_svc = $dt['CUSTOMER'];
					$model_svc = $dt['Type'];
					
					#-------------------------------- inject blm ada di prospectmst
					$customer = $dt['CUSTOMER'];
					$alamat = $dt['CUST_ADRES'];
					$kota = $dt['KOTA'];
					$kecamatan = $dt['KECAMATAN'];
					$kodepos = $dt['KODEPOS'];
					$telp = $dt['TELPON'];
					$gsm = $dt['GSM'];
					$cdma = $dt['CDMA'];
					$km_akhir = $dt['KmNow'];
					$kodesupervisor = $dt['Kode_Supervisor'];
										
					if (empty($kodepr) and empty($kodeprospect)) {
						$InstMst = InsertMasterProspect($customer,$kodepr, $alamat, $kota, $kecamatan, $kodepos, $telp, $gsm, $cdma, $norangka, $km_akhir, $kdSales, $kodesupervisor);
						$kodepr = $InsMst['kodepr'];
						$kodeprospect = $InsMst['kodeprospect'];
					}
					$tdkaktif = $dt['tdkaktif']; 
					
					
					#--------------- BRDIGING NASA SALES
					$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
											left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
											where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
											order by a.tgl_update desc",$conn);
					$dt_grd = mssql_fetch_array($qry_grd);
					$nik = $dt_grd['nik'];
					$keterangan = $nopolisi."#".$model_svc."#".$nama_svc."#".$tglJthTempo."#100000";
					
					
					//$TglPlan = Date_Add($tglJthTempo,"-3 days");
					/*$TglPlan = Date_Add($tglJthTempo,"-$ret_hansa_tempo days");
					##cek Plan
					$cekPlan1 = "SELECT KodePlan from PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					*/
					$TglPlan = date('m/d/Y',strtotime($tglJthTempo . "-$ret_hansa_tempo days"));
				
					##cek Plan
					$cekPlan1 = "SELECT KodePlan from ".$db_stars."..PlanTrn 
								where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					$reCekPlan1 = mssql_query($cekPlan1,$conn);
					$jmlreCekPlan1 = mssql_num_rows($reCekPlan1);
					
					if($jmlreCekPlan1==0){
						$cekPlan1 = "SELECT KodePlan from ".$db_stars."..PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3";
						$reCekPlan1 = mssql_query($cekPlan1,$conn);
						$reCekPlan1dat = mssql_fetch_array($reCekPlan1);
						
						//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);	
						$kodeplan = empty($reCekPlan1dat['KodePlan']) ? 1 : $reCekPlan1dat['KodePlan'];					
						
						##generate kodeplan
						$nKode = intval(substr($kodeplan, -4));
						$vKode = TRUE;
						while ($vKode) {
							$kodeplan = "PLRT".date('y').str_pad($nKode, 4, "0",STR_PAD_LEFT);
							/*
							$cek = $this->mod_plan->CekDataPlanTrn(" kodepr='$kodepr' AND kodeprospect = '$kodeprospect' AND kodeplan = '$kodeplan' ");
							$vKode = $cek[0];
							$nKode++;
							*/
							
							$qryKode = mssql_query("select kodepr from ".$db_stars."..plantrn 
											where kodepr='$kodepr' AND kodeprospect = '$kodeprospect' AND kodeplan = '$kodeplan'",$conn);
							//$vKode = $cek[0];
							$jmlKode = mssql_num_rows($qryKode);
							
							if($jmlKode>0){
								$vKode = TRUE;
							} else {
								$vKode = FALSE;
							}
							$nKode++;	
						}

						// echo "$kodepr - $kodeprospect - $kodeplan<br>";
						##ketplan
						$idKetPlan = 48;
						$KetPlan = "Reminder service 100000 Km";
						##buat plan
						/*$plan_svc = array("SbrPlan"=>"1",
							  "KodePr"=>$kodepr, 
							  "KodeProspect"=>$kodeprospect, 
							  "TglPlan"=>format_date($TglPlan),
							  "Via"=>null,
							  "KodePlan"=>$kodeplan,
							  "TypePlan"=>3,
							  "idKetPlan"=>$idKetPlan,
							  "KetPlan"=>$KetPlan,
							  // "[Yang Berulang Tahun]"=>$namaultah,
							  // "[Tanggal Lahir]"=>$tglultah,
							  "[Tanggal Jatuh Tempo]"=>format_date($tglJthTempo),
							  "NoRangka"=>$norangka,
							  "Type"=>$type,
							  "KodeSales"=>$kdSales, 
							  "Kodeuser"=>"AutoPlanSystem", 
							  "TglEntry"=>$getdate, );
						// var_dump($plan_svc);
						$inst = $this->mod_plan->InsertPlanTrn($plan_svc);
						*/
						
						if ($tdkaktif==0) {
							$jenis_service = "Service ".$km." KM";	
							$inst = mssql_query("insert into ".$db_stars."..plantrn (SbrPlan,KodePr,KodeProspect,TglPlan,
								KodePlan,TypePlan,idKetPlan,KetPlan, 
								[Tanggal Jatuh Tempo], NoRangka, Type,
								KodeSales,Kodeuser,TglEntry)
								values ('1', '".$kodepr."', '".$kodeprospect."', '".$TglPlan."',			
								'".$kodeplan."', 3, '".$idKetPlan."', '".$KetPlan."', 
								'".$tglJthTempo."', '".$norangka."', '".$type."',
								'".$kdSales."','AutoPlanSystem',getdate())",$conn);
							
							/*if ($inst and HANSA==1) {
								$kodepesan = "REMINDER";
								$inst_hansa = mssql_query("insert into ".DB_REPLBENGKEL."..NotifHansaRenkei
										 (kodesumber, Tipe, nowo, norangka, kodedealer, kodepesan,kodesales, nik, tglnotif, proses, 
										 tglentry, keterangan, userentry)
										values ('GRP', 'REMINDER', '".$nowo."', '".$norangka."', '".$kodecabang."','".$kodepesan."',  '".$kdSales."', '".$nik."', '".$TglPlan."', '0', 
										getdate(), '".$keterangan."', 'REMINDER')",$conn);
							}*/	
						}
						
					
					}


				}
			}
			
			#--------------------- customer
			if (HANSA==1) {
				$re_cust = RetensiServiceNoRangkaCustomer($km,$ret_hansa_tempo_cust);
				if($re_cust){
					while ($dt = mssql_fetch_array($re_cust)) {
						$kodepr = $dt['KodePr'];
						$kodeprospect = $dt['KodeProspect'];
						$kdSales = $dt['SalesPr'];
						$norangka = $dt['NoRangka'];
						$type = $dt['Type'];
						$tglJthTempo = $dt['DateNextSvc'];
						
						$nowo = $dt['NoWo'];
						$nopolisi = $dt['NoPolisi'];
						$nama_svc = $dt['CUSTOMER'];
						$model_svc = $dt['Type'];
	
						#-------------------------------- inject blm ada di prospectmst
						$customer = $dt['CUSTOMER'];
						$alamat = $dt['CUST_ADRES'];
						$kota = $dt['KOTA'];
						$kecamatan = $dt['KECAMATAN'];
						$kodepos = $dt['KODEPOS'];
						$telp = $dt['TELPON'];
						$gsm = $dt['GSM'];
						$cdma = $dt['CDMA'];
						$km_akhir = $dt['KmNow'];
						$kodesupervisor = $dt['Kode_Supervisor'];
						
						//echo "__XX__".$kodepr.$kodeprospect."__kd<br>";
						#--------------- BRDIGING NASA SALES
						$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
												left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
												where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
												order by a.tgl_update desc",$conn);
						$dt_grd = mssql_fetch_array($qry_grd);
						$nik = $dt_grd['nik'];
						$kodepesan = "REMINDER";
						$keterangan = $nopolisi."#".$model_svc."#".$nama_svc."#".$tglJthTempo."#10000";
						
						$qry_cek = mssql_query("select norangka from ".DB_REPLBENGKEL."..NotifHansaRenkei
												where norangka = '".$norangka."' and tglnotif = '".$TglPlan."'",$conn);
						
						$jml_cek = mssql_num_rows($qry_cek);
						
						if ($jml_cek==0) {
							$inst_hansa = mssql_query("insert into ".DB_REPLBENGKEL."..NotifHansaRenkei
									 (kodesumber, Tipe, nowo, norangka, kodedealer, kodepesan,kodesales, nik, tglnotif, proses, 
									 tglentry, keterangan, userentry)
									values ('GRP', 'REMINDER', '".$nowo."', '".$norangka."', '".$kodecabang."','".$kodepesan."',  '".$kdSales."', '".$nik."', '".$TglPlan."', '0', 
									getdate(), '".$keterangan."', 'REMINDER')",$conn);
						}
						
							
					}
				}				
			}
		}
		/*END RETENSI SERVICE 100000KM*/
		

		/*RETENSI bpkb*/
		$stmt = "SELECT isnull(a.TglPlanBPKB,CONVERT(varchar(20),getdate(),101))  TglPlan,  
					DATEADD(day,1,isnull(a.TglPlanBPKB,CONVERT(varchar(20),getdate(),101))) TglTempo, 
					a.TglSTNK, b.NoRangka, c.NamaBarang as Type, isnull(b.Kode_Sales,'0') SalesPr, d.KodePr, d.KodeProspect, isnull(g.tdkaktif,0) tdkaktif
					from  ".DB_STARS99."..AFI a
					inner join  ".DB_SPK99."..dataSPK b on a.SPK_No=b.SPK_No
					left join  ".DB_STARS99."..ArSales g on b.Kode_Sales = g.KodeSales 
					inner join  ".DB_SPK00."..Inmst c on b.NoRangka=c.KodeItem
					inner join ".$db_stars."..FollowSPK d on a.SPK_No = d.NoSpk
					left join  ".$db_stars."..PlanTrn e on d.KodePr=e.KodePr and d.KodeProspect=e.KodeProspect and b.NoRangka=e.NoRangka 
						and YEAR(TglPlan)=YEAR(GETDATE()) and e.idKetPlan = 49
					inner join  ".$db_stars."..ProspectTrn f on d.KodePr=f.KodePr and d.KodeProspect=f.KodeProspect
					where isnull(a.afi_ho,'')<>'' and ISNULL(a.TglJadiBPKB,'') <> '' and ISNULL(a.tglambilbpkb,'') = ''
					and ISNULL(a.batal,0) = 0 and e.KodePlan is null and isnull(b.syarat_bayar,0) = 0  and g.kodecabang = '".$kodedealer."' ";
		$qry = mssql_query($stmt,$conn);
		
			if($qry){
				while ($dt = mssql_fetch_array($qry)) {
					$kodepr = $dt['KodePr'];
					$kodeprospect = $dt['KodeProspect'];
					$TglPlan = $dt['TglPlan'];
					$tglJthTempo = $dt['TglTempo'];
					$type = $dt['Type'];
					$norangka = $dt['NoRangka'];
					$kdSales = $dt['SalesPr'];
					$tdkaktif = $dt['tdkaktif'];
					
					##cek Plan
					/*$cekPlan1 = "SELECT KodePlan from PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3  and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					*/
					
					
					##cek Plan
					$cekPlan1 = "SELECT KodePlan from ".$db_stars."..PlanTrn 
								where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					$reCekPlan1 = mssql_query($cekPlan1,$conn);
					$jmlreCekPlan1 = mssql_num_rows($reCekPlan1);
					
					if($jmlreCekPlan1==0){
						$cekPlan1 = "SELECT KodePlan from ".$db_stars."..PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 ";
						//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);	
						$reCekPlan1 = mssql_query($cekPlan1,$conn);
						$reCekPlan1dat = mssql_fetch_array($reCekPlan1);
						
						$kodeplan = empty($reCekPlan1dat['KodePlan']) ? 1 : $reCekPlan1dat['KodePlan'];					
						
						##generate kodeplan
						$nKode = intval(substr($kodeplan, -4));
						$vKode = TRUE;
						while ($vKode) {
							$kodeplan = "PLRT".date('y').str_pad($nKode, 4, "0",STR_PAD_LEFT);
							/*
							$cek = $this->mod_plan->CekDataPlanTrn(" kodepr='$kodepr' AND kodeprospect = '$kodeprospect' AND kodeplan = '$kodeplan' ");
							$vKode = $cek[0];
							$nKode++;
							*/
							
							$qryKode = mssql_query("select kodepr from ".$db_stars."..plantrn 
											where kodepr='$kodepr' AND kodeprospect = '$kodeprospect' AND kodeplan = '$kodeplan'",$conn);
							//$vKode = $cek[0];
							$jmlKode = mssql_num_rows($qryKode);
							
							if($jmlKode>0){
								$vKode = TRUE;
							} else {
								$vKode = FALSE;
							}
							$nKode++;	
						}

						##ketplan
						$idKetPlan = 49;
						$KetPlan = "Reminder BPKB";
						##buat plan
						/*$plan_stnk = array("SbrPlan"=>"1",
							  "KodePr"=>$kodepr, 
							  "KodeProspect"=>$kodeprospect, 
							  "TglPlan"=>format_date($TglPlan),
							  "Via"=>null,
							  "KodePlan"=>$kodeplan,
							  "TypePlan"=>3,
							  "idKetPlan"=>$idKetPlan,
							  "KetPlan"=>$KetPlan,
							  // "[Yang Berulang Tahun]"=>$namaultah,
							  // "[Tanggal Lahir]"=>$tglultah,
							  "[Tanggal Jatuh Tempo]"=>format_date($tglJthTempo),
							  "NoRangka"=>$norangka,
							  "Type"=>$type,
							  "KodeSales"=>$kdSales, 
							  "Kodeuser"=>"AutoPlanSystem", 
							  "TglEntry"=>$getdate, );
						// var_dump($plan_stnk);
						$inst = $this->mod_plan->InsertPlanTrn($plan_stnk);
						*/
					
						if ($tdkaktif==0) {	
							$inst = mssql_query("insert into ".$db_stars."..plantrn (SbrPlan,KodePr,KodeProspect,TglPlan,
								KodePlan,TypePlan,idKetPlan,KetPlan, 
								[Tanggal Jatuh Tempo], NoRangka, Type,
								KodeSales,Kodeuser,TglEntry)
								values ('1', '".$kodepr."', '".$kodeprospect."', '".$TglPlan."',			
								'".$kodeplan."', 3, '".$idKetPlan."', '".$KetPlan."', 
								'".$tglJthTempo."', '".$norangka."', '".$type."',
								'".$_SESSION['stars_username']."','AutoPlanSystem',getdate())",$conn);
						
						}
					}					
				}

			}
		
		/*END RETENSI BPKB*/
		
		
		
	/*update TglTurunStatus*/
	//$upt = $this->mod_home->UptProfilSPKDB("UPDATE profilsales set TglPlanAuto = '$getdate'");
	//echo "UPDATE ".DB_PROFIL_SPK."..profilsales set TglPlanAuto = getdate()";
	$upt = mssql_query("UPDATE ".DB_PROFIL_SPK."..profilsales set TglPlanAuto = getdate()",$conn);
	
	return;
	
}

function RetensiServiceNoRangkaNew($km,$ret_hansa_tempo){
	global $conn;
	
	$kd_db = getKodeDBDealer($_SESSION['stars_username']);
	$db_stars = DB_STARS.$kd_db;
	
	$kodedealer = BridgingDealer($kd_db);
	
	
	$tgl1 = date("m/01/Y");
	//$tgl2 = date("m/d/Y");
	$tgl2 = date("m/d/Y",strtotime("+$ret_hansa_tempo day"));
	
	
	/*$stmt = "SELECT top 20 a.NoRangka, c.CUSTOMER, C.CUST_ADRES, c.KOTA, '', C.GSM,  C.CDMA, c.KECAMATAN, c.KODEPOS, c.TELPON, A.KmNow, 
			'RETENTION SERVICE BERKALA ' + convert(varchar(12), A.SbeNext), '' 'Proses Sebelumnya', '' 'Agenda Kerja',
			'' 'Comment Spv', '' 'Type Unit', '' 'Warna', C.GSM + '; ' + C.CDMA,
			convert(varchar(10),(CASE WHEN A.JT_Tgl > A.JT_Bln then A.JT_Bln WHEN A.JT_Tgl < A.JT_Bln then A.JT_Tgl 
WHEN A.JT_Tgl = A.JT_Bln then A.Jt_Bln END),105) as DateNextSvc, 

			'' 'Via', '' 'Reason', isnull(e.KodePr,'0') KodePr, isnull(e.KodeProspect,0) KodeProspect, 
			isnull(d.Kode_Sales,'0') Salespr, 
			isnull(j.Kode_Supervisor,0) Kode_Supervisor, 
			h.NamaBarang Type, a.NoWo, a.NoPolisi, isnull(j.tdkaktif,0) tdkaktif
			FROM ".DB_BENGKEL."..M_ESTIMASI A WITH(NOLOCK) 
			Inner Join
					(SELECT G.NORANGKA, (SELECT MAX(SBENEXT) 
					FROM ".DB_BENGKEL."..m_estimasi WITH(NOLOCK) 
					WHERE NORANGKA=G.NORANGKA AND ISNULL(TGLSVCNOW,'') <> '') S, 
					MAX((CASE WHEN G.JT_Tgl > G.JT_Bln then G.JT_Bln WHEN G.JT_Tgl < G.JT_Bln then G.JT_Tgl WHEN G.JT_Tgl = G.JT_Bln then G.JT_Tgl END)) AS JT_TGL
					FROM ".DB_BENGKEL."..M_ESTIMASI G WITH(NOLOCK) 
					Where (CASE WHEN G.JT_Tgl > G.JT_Bln then G.JT_Bln WHEN G.JT_Tgl < G.JT_Bln then G.JT_Tgl WHEN G.JT_Tgl = G.JT_Bln then G.JT_Tgl END) Is Not Null 
					GROUP BY G.NORANGKA) AS Z ON A.NORANGKA = Z.NORANGKA AND A.SBENEXT = Z.S
			INNER JOIN ".DB_BENGKEL."..Custdat c WITH(NOLOCK) On a.NoRangka = c.No_Chassis 
			left join ".DB_SPK99."..dataspk d on a.norangka = d.NoRangka
			left join ".$db_stars."..followspk e on d.SPK_No = e.NoSpk
			left join ".DB_SPK00."..Inmst h on A.NoRangka = h.KodeItem
			left join ".DB_STARS99."..Arsales j on  d.kode_sales = j.KodeSales
			
			Where (CASE WHEN A.JT_Tgl > A.JT_Bln then  A.JT_Bln WHEN A.JT_Tgl < A.JT_Bln then  A.JT_Tgl WHEN A.JT_Tgl = A.JT_Bln then  A.Jt_Bln END)
			between '".$tgl1."' And '".$tgl2."' and a.SBENext = '".$km."' and d.SPK_Status = '1' and j.kodecabang = '".$kodedealer."' ";
	*/
	/* revisi tgl 25-08-2020 by mb tania via wa
	
	SELECT top 20 a.NoRangka, 
	c.NamaCust1+' '+c.NamaCust2+' '+c.namacust3, c.alamat, c.KOTA, '', C.HP1,  C.hp2, c.KECAMATAN, c.KODEPOS, c.Telp, A.KmNow, 
	'RETENTION SERVICE BERKALA ' + convert(varchar(12), A.SbeNext), '' 'Proses Sebelumnya', '' 'Agenda Kerja',
	'' 'Comment Spv', '' 'Type Unit', '' 'Warna', C.HP1 + '; ' + C.hp2,
	convert(varchar(10),(CASE WHEN A.JT_Tgl > A.JT_Bln then A.JT_Bln WHEN A.JT_Tgl < A.JT_Bln then A.JT_Tgl 
	WHEN A.JT_Tgl = A.JT_Bln then A.Jt_Bln END),105) as DateNextSvc, 
	'' 'Via', '' 'Reason', isnull(e.KodePr,'0') KodePr, isnull(e.KodeProspect,0) KodeProspect, 
	isnull(d.KodeSales,'0') Salespr, 
	isnull(j.Kode_Supervisor,0) Kode_Supervisor, 
	h.NamaBarang Type, a.NoWo, a.NoPolisi, isnull(j.tdkaktif,0) tdkaktif
	FROM Bengkel99..M_ESTIMASI A WITH(NOLOCK) 
	Inner Join
			(SELECT G.NORANGKA, (SELECT MAX(SBENEXT) 
			FROM Bengkel99..m_estimasi WITH(NOLOCK) 
			WHERE NORANGKA=G.NORANGKA AND ISNULL(TGLSVCNOW,'') <> '') S, 
			MAX((CASE WHEN G.JT_Tgl > G.JT_Bln then G.JT_Bln WHEN G.JT_Tgl < G.JT_Bln then G.JT_Tgl WHEN G.JT_Tgl = G.JT_Bln then G.JT_Tgl END)) AS JT_TGL
			FROM Bengkel99..M_ESTIMASI G WITH(NOLOCK) 
			Where (CASE WHEN G.JT_Tgl > G.JT_Bln then G.JT_Bln WHEN G.JT_Tgl < G.JT_Bln then G.JT_Tgl WHEN G.JT_Tgl = G.JT_Bln then G.JT_Tgl END) Is Not Null 
			GROUP BY G.NORANGKA) AS Z ON A.NORANGKA = Z.NORANGKA AND A.SBENEXT = Z.S
	inner join SingleIDOri..UnitCustomer U WITH(NOLOCK) On a.NoRangka = U.NoRangka  
	left join SingleIDOri..NewCustomer C WITH(NOLOCK) on U.IDCustService = c.IDCust 
	left join spk99..dataspk d on a.norangka = d.NoRangka
	left join STARS12..followspk e on d.NoSPK = e.NoSpk
	left join spk00..Inmst h on A.NoRangka = h.KodeItem
	left join stars99..Arsales j on d.kodesales = j.KodeSales
	Where (CASE WHEN A.JT_Tgl > A.JT_Bln then  A.JT_Bln WHEN A.JT_Tgl < A.JT_Bln then  A.JT_Tgl WHEN A.JT_Tgl = A.JT_Bln then  A.Jt_Bln END)
	between '01/01/2020' And '12/31/2020' and a.SBENext = '60000' and d.[Status] = '1' and j.kodecabang = '2172'
	*/
	
	$stmt = "SELECT top 20 a.NoRangka, 
			c.NamaCust1+' '+c.NamaCust2+' '+c.namacust3 CUSTOMER, c.alamat CUST_ADRES, c.KOTA, '', C.HP1 GSM,  C.hp2 CDMA, 
			c.KECAMATAN, c.KODEPOS, c.Telp TELPON, A.KmNow, 
			'RETENTION SERVICE BERKALA ' + convert(varchar(12), A.SbeNext), '' 'Proses Sebelumnya', '' 'Agenda Kerja',
			'' 'Comment Spv', '' 'Type Unit', '' 'Warna', C.HP1 + '; ' + C.hp2,
			convert(varchar(10),(CASE WHEN A.JT_Tgl > A.JT_Bln then A.JT_Bln WHEN A.JT_Tgl < A.JT_Bln then A.JT_Tgl 
			WHEN A.JT_Tgl = A.JT_Bln then A.Jt_Bln END),105) as DateNextSvc, 
			'' 'Via', '' 'Reason', isnull(e.KodePr,'0') KodePr, isnull(e.KodeProspect,0) KodeProspect, 
			isnull(d.KodeSales,'0') SalesPr, 
			isnull(j.Kode_Supervisor,0) Kode_Supervisor, 
			h.NamaBarang Type, a.NoWo, a.NoPolisi, isnull(j.tdkaktif,0) tdkaktif                
			FROM ".DB_BENGKEL99."..M_ESTIMASI A WITH(NOLOCK) 
			Inner Join
					(SELECT G.NORANGKA, (SELECT MAX(SBENEXT) 
					FROM ".DB_BENGKEL99."..m_estimasi WITH(NOLOCK) 
					WHERE NORANGKA=G.NORANGKA AND ISNULL(TGLSVCNOW,'') <> '') S, 
					MAX((CASE WHEN G.JT_Tgl > G.JT_Bln then G.JT_Bln WHEN G.JT_Tgl < G.JT_Bln then G.JT_Tgl WHEN G.JT_Tgl = G.JT_Bln then G.JT_Tgl END)) AS JT_TGL
					FROM ".DB_BENGKEL99."..M_ESTIMASI G WITH(NOLOCK) 
					Where (CASE WHEN G.JT_Tgl > G.JT_Bln then G.JT_Bln WHEN G.JT_Tgl < G.JT_Bln then G.JT_Tgl WHEN G.JT_Tgl = G.JT_Bln then G.JT_Tgl END) Is Not Null 
					GROUP BY G.NORANGKA) AS Z ON A.NORANGKA = Z.NORANGKA AND A.SBENEXT = Z.S
			inner join SingleIDOri..UnitCustomer U WITH(NOLOCK) On a.NoRangka = U.NoRangka  
			left join SingleIDOri..NewCustomer C WITH(NOLOCK) on U.IDCustService = c.IDCust 
			left join ".DB_SPK99."..dataspk d on a.norangka = d.NoRangka
			left join ".$db_stars."..followspk e on d.NoSPK = e.NoSpk
			left join ".DB_SPK00."..Inmst h on A.NoRangka = h.KodeItem
			left join ".DB_STARS99."..Arsales j on d.kodesales = j.KodeSales
			Where (CASE WHEN A.JT_Tgl > A.JT_Bln then  A.JT_Bln WHEN A.JT_Tgl < A.JT_Bln then  A.JT_Tgl WHEN A.JT_Tgl = A.JT_Bln then  A.Jt_Bln END)
			between '".$tgl1."' And '".$tgl2."' and a.SBENext = '".$km."' and d.[Status] = '1' and j.kodecabang = '".$kodedealer."' ";

	//echo "<pre>".$stmt."</pre>";
	$qry = mssql_query($stmt,$conn);
	
	return $qry;
	
}

function RetensiServiceNoRangkaCustomer($km,$ret_hansa_tempo_cust){
	global $conn;
		
	$kd_db = getKodeDBDealer($_SESSION['stars_username']);
	$db_stars = DB_STARS.$kd_db;
	
	$kodedealer = BridgingDealer($kd_db);
	
	
	$tgl1 = date("m/01/Y");
	//$tgl2 = date("m/d/Y");
	$tgl2 = date("m/d/Y",strtotime("+$ret_hansa_tempo_cust day"));
	
	
	$stmt = "SELECT top 20 a.NoRangka, c.CUSTOMER, C.CUST_ADRES, c.KOTA, '', C.GSM,  C.CDMA, c.KECAMATAN, c.KODEPOS, c.TELPON, A.KmNow, 
			'RETENTION SERVICE BERKALA ' + convert(varchar(12), A.SbeNext), '' 'Proses Sebelumnya', '' 'Agenda Kerja',
			'' 'Comment Spv', '' 'Type Unit', '' 'Warna', C.GSM + '; ' + C.CDMA,
			convert(varchar(10),(CASE WHEN A.JT_Tgl > A.JT_Bln then A.JT_Bln WHEN A.JT_Tgl < A.JT_Bln then A.JT_Tgl 
WHEN A.JT_Tgl = A.JT_Bln then A.Jt_Bln END),105) as DateNextSvc, 

			'' 'Via', '' 'Reason', isnull(e.KodePr,'0') KodePr, isnull(e.KodeProspect,0) KodeProspect, 
			isnull(d.Kode_Sales,'0') Salespr, 
			isnull(j.Kode_Supervisor,0) Kode_Supervisor, 
			h.NamaBarang Type, a.NoWo, a.NoPolisi, isnull(j.tdkaktif,0) tdkaktif
			FROM ".DB_BENGKEL."..M_ESTIMASI A WITH(NOLOCK) 
			Inner Join
					(SELECT G.NORANGKA, (SELECT MAX(SBENEXT) 
					FROM ".DB_BENGKEL."..m_estimasi WITH(NOLOCK) 
					WHERE NORANGKA=G.NORANGKA AND ISNULL(TGLSVCNOW,'') <> '') S, 
					MAX((CASE WHEN G.JT_Tgl > G.JT_Bln then G.JT_Bln WHEN G.JT_Tgl < G.JT_Bln then G.JT_Tgl WHEN G.JT_Tgl = G.JT_Bln then G.JT_Tgl END)) AS JT_TGL
					FROM ".DB_BENGKEL."..M_ESTIMASI G WITH(NOLOCK) 
					Where (CASE WHEN G.JT_Tgl > G.JT_Bln then G.JT_Bln WHEN G.JT_Tgl < G.JT_Bln then G.JT_Tgl WHEN G.JT_Tgl = G.JT_Bln then G.JT_Tgl END) Is Not Null 
					GROUP BY G.NORANGKA) AS Z ON A.NORANGKA = Z.NORANGKA AND A.SBENEXT = Z.S
			INNER JOIN ".DB_BENGKEL."..Custdat c WITH(NOLOCK) On a.NoRangka = c.No_Chassis 
			left join ".DB_SPK99."..dataspk d on a.norangka = d.NoRangka
			left join ".$db_stars."..followspk e on d.SPK_No = e.NoSpk
			left join ".DB_SPK00."..Inmst h on A.NoRangka = h.KodeItem
			left join ".DB_STARS99."..Arsales j on  d.kode_sales = j.KodeSales
			
			Where (CASE WHEN A.JT_Tgl > A.JT_Bln then  A.JT_Bln WHEN A.JT_Tgl < A.JT_Bln then  A.JT_Tgl WHEN A.JT_Tgl = A.JT_Bln then  A.Jt_Bln END)
			between '".$tgl1."' And '".$tgl2."' and a.SBENext = '".$km."' and d.SPK_Status = '1' and j.kodecabang = '".$kodedealer."' ";
	
	
	//echo "<pre>".$stmt."</pre>";
	$qry = mssql_query($stmt,$conn);
	
	return $qry;
	
}

function InsertMasterProspect($customer,$fs_kd_plg, $alamat, $kota, $kecamatan, $kodepos, $telp, $gsm, $cdma, $norangka, $km_akhir, $salespr, $kodesupervisor) {
	global $conn;
	
	$kd_db = getKodeDBDealer($_SESSION['stars_username']);
	$db_stars = DB_STARS.$kd_db;
	
	$kodedealer = BridgingDealer($kd_db);	
	
	$NewProspect = FALSE;
	
	$arr = array();
	//$arr = split(" ", s2d($row['customer']));
	
	$arr = split(" ", $customer);
	$nama = $customer;
	
	$ParsingMsg = "";
	for ($iParsingMsg = 0; $iParsingMsg < count($arr); $iParsingMsg++) {
		if (strlen($arr[$iParsingMsg]) > 3) {
			if (strlen($ParsingMsg) > 0) {
				$ParsingMsg = $ParsingMsg . "OR CHARINDEX('" . $arr[$iParsingMsg] . "',Nama) >0 ";
			} else {
				$ParsingMsg = "CHARINDEX('" . $arr[$iParsingMsg] . "',Nama) >0 ";
			}
		}
	}
	if ($ParsingMsg == "") {
		$ParsingMsg = "CHARINDEX ('zzzzzzzzzzzzzzzz',nama)>0";
	}
	$Kueri = " salespr='$salespr' and " . $ParsingMsg;
	
							
	$qry_mst = mssql_query("SELECT nama,KodePr, salespr from  [".$db_stars."]..prospectmst 
							where KodeAr='".$fs_kd_plg."' and KoderAr != '0' ",$conn);
	
	$RsAdaCust = mssql_num_rows($qry_mst);
	$data_RsAdaCust = mssql_fetch_array($qry_mst);
	
	$qry_mst2 = mssql_query("SELECT nama,KodePr, salespr from  [".$db_stars."]..prospectmst 
							where rtrim(ltrim(nama))='" . trim($customer) . "' 
							and rtrim(ltrim(alamat))='" . trim($alamat) . "' and rtrim(ltrim(kota))='" . trim($kota) . "'", $conn);
	$RsAdaCust2 = mssql_num_rows($qry_mst2);
	$data_RsAdaCust2 = mssql_fetch_array($qry_mst2);

	$qry_mst3 = mssql_query("SELECT Nama,KodePr, salespr FROM  [".$db_stars."]..ProspectMst WHERE " . $Kueri, $conn);
	
							
	/*check#1 - KODE LANGGAN*/
	if ($RsAdaCust>0) {
		$kodepr = $data_RsAdaCust['KodePr'];
		
		/*$qry_cek = mssql_query("SELECT kodepr from [".$db_stars."]..prospectservice 
						where kodepr='" . $kodepr . "' and norangka='".$norangka. "'", $conn);
		$jml_cek = mssql_num_rows($qry_cek);
						
		if ($jml_cek==0) {
						
			$inst_service = mssql_query("INSERT into [".$db_stars."]..prospectservice (KodePr,NoRangka,Km,KodeUser,TglEntry) 
						values ('".$kodepr."','".$norangka."',".$km_akhir.",'SYSTEM', getdate())",$conn);
			
			$NewProspect = TRUE;
		}*/
		$NewProspect = TRUE;
	
	/*check#2 - NAMA ALAMAT KOTA*/
	} else if ($RsAdaCust2>0) {	
		$kodepr = $data_RsAdaCust['KodePr'];		
	
		/*$qry_cek = mssql_query("SELECT kodepr from [".$db_stars."]..prospectservice 
						where kodepr='" . $kodepr . "' and norangka='".$norangka. "'", $conn);						
		$jml_cek = mssql_num_rows($qry_cek);
						
		if ($jml_cek==0) {
						
			$inst_service = mssql_query("INSERT into [".$db_stars."]..prospectservice (KodePr,NoRangka,Km,KodeUser,TglEntry) 
						values ('".$kodepr."','".$norangka."',".$km_akhir.",'SYSTEM', getdate())",$conn);
			
			//$this->mod_home->UptSalesDB("INSERT into prospectservice (KodePr,NoRangka,Km,KodeUser,TglEntry) values ('".$RsAdaCust[0]['KodePr']."','".$row['no_chassis']."',".$row['fs_km_akhir'].",'".$kdUser."','$getdate')");
			//$kodepr = $RsAdaCust[0]['KodePr'];
			
			$NewProspect = TRUE;
		}*/
		$NewProspect = TRUE;
		
		
	} else { 
	
		if (!empty($nama)) {
			$nKodePr = 1; 
			$StnKodePr = FALSE;
				
			do {
				$kodepr = substr(str_pad(str_replace(" ", "", str_replace("'", "-",  trim(strtoupper($nama))    )),7,"-"),0,7) .
							date("Y", time()) . 
							str_pad($nKodePr, 4, '0', STR_PAD_LEFT);
						
				$qry_kode = mssql_query("select kodepr from [".$db_stars."]..prospectmst where kodepr = '" . $kodepr . "'",$conn);		
				$cek_kode = mssql_num_rows($qry_kode);
				
				if($cek_kode>0) {
					$nKodePr += 1; $StnKodePr = FALSE;
				} else {
					$StnKodePr = TRUE;
				}
			} while ($StnKodePr == FALSE);
			
			
			$stm_insert = "insert into [".$db_stars."]..ProspectMst
								(KodePr,Nama,Alamat,Kota,Kecamatan,KodePos,Telp,GSM,CDMA,KodeAr,KodeUser,TglEntry,Sumber,SalesPr,SbrDt,KodeSupervisor,Grup) 
								values ( '".$kodepr."', '".trim($customer)."', '".trim($alamat)."', '".trim($kota)."', 
								'".trim($kecamatan)."',
								'".trim($kodepos)."', '".trim($telp)."', '".trim($gsm)."', '".trim($cdma)."', 
								'".trim($fs_kd_plg)."',
								'SYSTEM', getdate(), 7, '".$salespr."', '2', 
								'".strtoupper($kodesupervisor)."','1')";
			$qry_insert = mssql_query($stm_insert, $conn);
					
						
			/*if ($qry_insert) {							
				$qry_cek = mssql_query("SELECT kodepr from [".$db_stars."]..prospectservice 
									where kodepr='" . $kodepr . "' and norangka='".$norangka. "'", $conn);
				$jml_cek = mssql_num_rows($qry_cek);
									
				if ($jml_cek==0) {
					$inst_service = mssql_query("INSERT into [".$db_stars."]..prospectservice (KodePr,NoRangka,Km,KodeUser,TglEntry) 
									values ('".$kodepr."','".$norangka."',".$km_akhir.",'SYSTEM', getdate())",$conn);
					$NewProspect = TRUE;
				}
			}*/
			
		}
		
	}
	
	

	if($NewProspect){
		
		$stm_insert_inject = "insert into [".$db_stars."]..ProspectMstInject (KodePr,TglEntryInject) 
							values ( '".$kodepr."',getdate())" ;
		//echo "<pre>$stm_insert</pre>";					
		$qry_insert_inject = mssql_query($stm_insert_inject, $conn);	
		
		#insert into ProspectTrn
		$qry_cek2 = mssql_query("select kodesales from [".DB_STARS99."]..Arsales 
								where kodesales='" . $salespr . "' and isnull(tdkaktif,0)=0 and kodecabang = '".$kodedealer."' ", $conn);
		if ($qry_cek2) {
			#cek new prospect
			$closing = FALSE;
			$qcek = mssql_query("SELECT top 1 KodeProspect, StatusAkhir from [".$db_stars."]..ProspectTrn where kodepr='$kodepr' order by KodeProspect desc",$conn);
			$cek = mssql_fetch_array($qcek);
			$kodeprospect = $cek['KodeProspect'];
			$statusakhir = $cek['StatusAkhir'];
			
			
			if((strtoupper($statusakhir) == 'SPK' || strtoupper($statusakhir) == 'LOST')){
				
				$nKode = 1;
				$vKode = TRUE;
				
				while ($vKode==TRUE) {
					$kodeprospect = "PR".date('y').str_pad($nKode, 4, "0",STR_PAD_LEFT);
					//$cek = $this->mod_plan->CekDataProspectTrn(" kodepr='$kodepr' AND kodeprospect = '$kodeprospect'");
															
					$qryKode = mssql_query("select kodepr,KodeProspect,StatusAkhir 
											from [".$db_stars."]..prospecttrn 
											where kodepr='$kodepr' AND kodeprospect = '$kodeprospect' and statusakhir in ('spk','lost')",$conn);
					
					$jmlKode = mssql_num_rows($qryKode);
					
					if($jmlKode>0){
						$vKode = TRUE;
					} else {
						$vKode = FALSE;
					}
					$nKode++;
				}
						
				$qry_insert = mssql_query("insert into [".$db_stars."]..ProspectTrn (KodePr,KodeProspect,Kodeuser,TglEntry,TglAwalEntry,SalesPr) 
											values 
											('".$kodepr."','".$kodeprospect."','".$salespr."',getdate(), getdate(), '".$salespr."') ",$conn);
											
			}
		}
	}
				
	$hasil = array("kodepr"=>$kodepr, "kodeprospect"=>$kodeprospect);			
	return $hasil;

}

function ListProspect($stars_username){
	global $conn;
	
	$kd_db = getKodeDBDealer($_SESSION['stars_username']);
	$db_stars = DB_STARS.$kd_db;
	
	$kodedealer = BridgingDealer($kd_db);
	
	$ProcessListBengkel = 1; $ProcessListRetail = 1; $ProcessListLeasing = 1;
	
	//$SALES_DB = SALES_DB; $SPK_DB = SPK_DB; $BENGKEL_DB = BENGKEL_DB; 
	//$getdate = Get_Date();
	$getdate = date('m/d/Y');
	
	//$kdSales = $_SESSION['users']['kodesales'];
	//$userid = $_SESSION['users']['userid'];
	
	$kdUser = "SYSTEM";//$_SESSION['users']['userid'];
	// $x = $this->mod_home->QueryProfilSPKDB("SELECT kodesales from usersales where kodesales='$kdSales' and tgllist+'23:59:59:99' < getdate()");
	//$x = $this->mod_home->QueryProfilSPKDB("SELECT kodesales from usersales where [user_id]='$userid' and tgllist+'23:59:59:99' < getdate()");
	
	
	
	//$x = mssql_query("SELECT kodesales from ".DB_PROFIL_SPK."..usersales where [user_id]='$userid' and tgllist+'23:59:59:99' < getdate()", $conn);
	
	if ($_SESSION['stars_profil']==2) {
		$qry_sales = mssql_query("select top 1 kode_supervisor, kodesales
						from ".DB_STARS99."..Arsales 
						where kodecabang = '".$kodedealer."' and tdkaktif = '0' and kode_supervisor = '".$_SESSION['stars_username']."'
						order by tglentry desc",$conn); // kodesales = '".$stars_username."' and 
		$dt_sales = mssql_fetch_array($qry_sales);
		$kodesupervisor = $_SESSION['stars_username'];
		$salespr = $dt_sales['kodesales'];
		
		
	} else if ($_SESSION['stars_profil']==3) {
		$qry_sales = mssql_query("select top 1 kode_supervisor, kodesales
						from ".DB_STARS99."..Arsales 
						where kodecabang = '".$kodedealer."' and tdkaktif = '0' order by tglentry desc",$conn); // kodesales = '".$stars_username."' and 
		$dt_sales = mssql_fetch_array($qry_sales);
		$kodesupervisor = $_SESSION['stars_username'];
		$salespr = $dt_sales['kodesales'];
		
	} else {	// login sales		
		$qry_sales = mssql_query("select kode_supervisor, kodesales
						from ".DB_STARS99."..Arsales 
						where kodecabang = '".$kodedealer."' and tdkaktif = '0' and kodesales = '".$stars_username."'",$conn); 
						// kodesales = '".$stars_username."' and 
		$dt_sales = mssql_fetch_array($qry_sales);
		$kodesupervisor = $dt_sales['kode_supervisor'];
		$salespr = $stars_username;
	}
	
	$ArrListProspect = array();
	//if($qry_grd){
	/*BENGKEL*/
	//	if($_SESSION['users']['kodesales'] != "ALL"){
			if($_SESSION['stars_profil']==2 or $_SESSION['stars_profil']==3){
				$stmt1 = "SELECT top 20 fs_km_akhir,a.customer,a.cust_adres,a.kota,null kecamatan,
							a.kodepos,a.telpon,a.gsm,a.cdma,a.no_chassis,a.fs_km_akhir,a.fs_th_mobil,fs_kd_plg,
							b.kodesales, a.fmdl model
							from [".DB_BENGKEL99."]..custdat a 
							left join [".DB_SPK99."]..dataspk b on a.no_chassis=b.norangka
							left join [".$db_stars."]..prospectservice e on a.no_chassis=e.norangka
							left join [".DB_STARS99."]..Arsales f on b.kodesales=f.kodesales
							Where CONVERT(bigint,fs_km_akhir) > (select top 1 service from [".$db_stars."]..StarsSettingsInject  order by LastUpdate desc) 
								and isnull(e.norangka,'')='' and isnull(f.tdkaktif,1)=1 and f.kodecabang = '".$kodedealer."'
							order by cast(fs_km_akhir as bigint) desc";
			} else {
				$stmt1 = "SELECT top 20 fs_km_akhir,a.customer,a.cust_adres,a.kota,null kecamatan,
							a.kodepos,a.telpon,a.gsm,a.cdma,a.no_chassis,a.fs_km_akhir,a.fs_th_mobil,fs_kd_plg,
							b.kodesales, a.fmdl model
							from [".DB_BENGKEL99."]..custdat a 
							left join [".DB_SPK99."]..dataspk b on a.no_chassis=b.norangka
							left join [".$db_stars."]..prospectservice e on a.no_chassis=e.norangka
							left join [".DB_STARS99."]..Arsales f on b.kodesales=f.kodesales
							Where CONVERT(bigint,fs_km_akhir) > (select top 1 service from [".$db_stars."]..StarsSettingsInject  order by LastUpdate desc) 
							and isnull(e.norangka,'')='' and b.kode_sales= '".$salespr ."' and f.kodecabang = '".$kodedealer."'
							order by cast(fs_km_akhir as bigint) desc";
			}
			//echo "<pre>".$stmt1."</pre>";
			$query1 = mssql_query($stmt1,$conn);
			
		//} else $query1 = array();
		
		if($query1 && $ProcessListBengkel){
			while ($row = mssql_fetch_array($query1)) {
				$model = $row['model'];
				$salespr = $row['kodesales'];
				
				$NewProspect = FALSE;
				
				$arr = array();
				//$arr = split(" ", s2d($row['customer']));
				
				$arr = split(" ", $row['customer']);
				
				$ParsingMsg = "";
				for ($iParsingMsg = 0; $iParsingMsg < count($arr); $iParsingMsg++) {
					if (strlen($arr[$iParsingMsg]) > 3) {
						if (strlen($ParsingMsg) > 0) {
							$ParsingMsg = $ParsingMsg . "OR CHARINDEX('" . $arr[$iParsingMsg] . "',Nama) >0 ";
						} else {
							$ParsingMsg = "CHARINDEX('" . $arr[$iParsingMsg] . "',Nama) >0 ";
						}
					}
				}
				if ($ParsingMsg == "") {
					$ParsingMsg = "CHARINDEX ('zzzzzzzzzzzzzzzz',nama)>0";
				}
				$Kueri = " salespr='$salespr' and " . $ParsingMsg;
				
				
										
				$qry_mst = mssql_query("SELECT nama, KodePr, alamat, gsm, salespr 
										from  [".$db_stars."]..prospectmst 
										where KodeAr='".$row['fs_kd_plg']."' ",$conn);
				
				$qry_mst2 = mssql_query("SELECT nama, KodePr, alamat, gsm, salespr 
										from  [".$db_stars."]..prospectmst 
										where rtrim(ltrim(nama))='" . trim($row['customer']) . "' 
										and rtrim(ltrim(alamat))='" . trim($row['cust_adres']) . "' and rtrim(ltrim(kota))='" . trim($row['kota']) . "'", $conn);
				
				$qry_mst3 = mssql_query("SELECT Nama, KodePr, alamat, gsm, salespr
											FROM  [".$db_stars."]..ProspectMst WHERE " . $Kueri, $conn);
				//echo "SELECT Nama, KodePr FROM  [".$db_stars."]..ProspectMst WHERE " . $Kueri;
										
				/*check#1 - KODE LANGGAN*/
				if ($qry_mst && $row['fs_kd_plg'] !="") {
				
					$RsAdaCust = mssql_num_rows($qry_mst);
					$data_RsAdaCust = mssql_fetch_array($qry_mst);
					
					if ($RsAdaCust>0) {
						$kodepr = $data_RsAdaCust['KodePr'];
						
						$qry_cek = mssql_query("SELECT kodepr from [".$db_stars."]..prospectservice 
										where kodepr='" . $kodepr . "' and norangka='".$row['no_chassis']. "'", $conn);
						$jml_cek = mssql_num_rows($qry_cek);
										
						if ($jml_cek==0) {
							$inst_service = mssql_query("INSERT into [".$db_stars."]..prospectservice (KodePr,NoRangka,Km,KodeUser,TglEntry) 
										values ('".$kodepr."','".$row['no_chassis']."',".$row['fs_km_akhir'].",'SYSTEM', getdate())",$conn);
							
							//$this->mod_home->UptSalesDB("INSERT into prospectservice (KodePr,NoRangka,Km,KodeUser,TglEntry) values ('".$RsAdaCust[0]['KodePr']."','".$row['no_chassis']."',".$row['fs_km_akhir'].",'".$kdUser."','$getdate')");
							//$kodepr = $RsAdaCust[0]['KodePr'];
							
							
							// hansarenkei 2020-09-29 kirim notif
							$qry_sales = mssql_query("select top 1 b.NIK, a.nasa_id, c.kode_supervisor, d.nama namaspv
													from grd_sls..tblbridging_nasa a
													left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
													left join ".DB_STARS99."..arsales c on a.nasa_id = c.kodesales
													left join (select a1.nama, a1.nik, spvnasa_id
													from fkaryawan..datakaryawan a1
													left join ".DB_GRADING."..TBLMasterman b1 on a1.NIK = b1.NIK 
													left join ".DB_GRADING."..TBLBridging_spv c1 on b1.idNama = c1.spv_id  
														and b1.KodeDealer = c1.dealer) d on d.spvnasa_id = c.kode_supervisor
													where a.nasa_id =  '".$data_RsAdaCust['salespr']."' and isnull(tanggalkeluar,0) = 0 
													order by a.tgl_update desc",$conn);
							$dt_sales = mssql_fetch_array($qry_sales);
							$nik_sales = $dt_sales['nik'];
							
							$norangka = $row['no_chassis'];
							$nama_cust = $data_RsAdaCust['nama'];
							$telp_cust = $data_RsAdaCust['gsm']; 
							$alamat_cust = $data_RsAdaCust['alamat'];
							notifdarisistemkesales($kodepr, $norangka, $nik_sales, $nama_cust, $model, $telp_cust, $alamat_cust);
							
							$NewProspect = TRUE;
						}
					}
				/*check#2 - NAMA ALAMAT KOTA*/
				} else if($qry_mst2){ 
				
					//$RsAdaCust = $this->mod_home->QuerySalesDB("SELECT KodePr from prospectmst where rtrim(ltrim(nama))='" . Trim(s2d($row['customer'])) . "' and rtrim(ltrim(alamat))='" . Trim(s2d($row['cust_adres'])) . "' and rtrim(ltrim(kota))='" . Trim($row['kota']) . "'");
					$RsAdaCust = mssql_num_rows($qry_mst2);
					$data_RsAdaCust = mssql_fetch_array($qry_mst2);
					
					$kodepr = $data_RsAdaCust['KodePr'];
					
					if ($RsAdaCust>0) {
						$kodepr = $data_RsAdaCust['KodePr'];
						
						$qry_cek = mssql_query("SELECT kodepr from [".$db_stars."]..prospectservice 
										where kodepr='" . $kodepr . "' and norangka='".$row['no_chassis']. "'", $conn);
						$jml_cek = mssql_num_rows($qry_cek);
										
						if ($jml_cek==0) {
										
							$inst_service = mssql_query("INSERT into [".$db_stars."]..prospectservice (KodePr,NoRangka,Km,KodeUser,TglEntry) 
										values ('".$kodepr."','".$row['no_chassis']."',".$row['fs_km_akhir'].",'SYSTEM', getdate())",$conn);
							
							//$this->mod_home->UptSalesDB("INSERT into prospectservice (KodePr,NoRangka,Km,KodeUser,TglEntry) values ('".$RsAdaCust[0]['KodePr']."','".$row['no_chassis']."',".$row['fs_km_akhir'].",'".$kdUser."','$getdate')");
							//$kodepr = $RsAdaCust[0]['KodePr'];
							
								// hansarenkei 2020-09-29 kirim notif
							$qry_sales = mssql_query("select top 1 b.NIK, a.nasa_id, c.kode_supervisor, d.nama namaspv
													from grd_sls..tblbridging_nasa a
													left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
													left join ".DB_STARS99."..arsales c on a.nasa_id = c.kodesales
													left join (select a1.nama, a1.nik, spvnasa_id
													from fkaryawan..datakaryawan a1
													left join ".DB_GRADING."..TBLMasterman b1 on a1.NIK = b1.NIK 
													left join ".DB_GRADING."..TBLBridging_spv c1 on b1.idNama = c1.spv_id  
														and b1.KodeDealer = c1.dealer) d on d.spvnasa_id = c.kode_supervisor
													where a.nasa_id =  '".$data_RsAdaCust['salespr']."' and isnull(tanggalkeluar,0) = 0 
													order by a.tgl_update desc",$conn);
							$dt_sales = mssql_fetch_array($qry_sales);
							$nik_sales = $dt_sales['nik'];
							
							$norangka = $row['no_chassis'];
							$nama_cust = $data_RsAdaCust['nama'];
							$telp_cust = $data_RsAdaCust['gsm']; 
							$alamat_cust = $data_RsAdaCust['alamat'];
							notifdarisistemkesales($kodepr, $norangka, $nik_sales, $nama_cust, $model, $telp_cust, $alamat_cust);
							
							$NewProspect = TRUE;
						}
					}
					
				/*check#3 - CARI NAMA MIRIP*/
				} else if($qry_mst3){ 
					$list = mssql_query("SELECT kodepr,Nama,alamat,kota, b.kodesales, a.GSM
							 FROM [".$db_stars."]..ProspectMst a
							 left join [".DB_STARS99."]..ArSales b on a.SalesPr=b.KodeSales and isnull(TdkAktif,0)=0
							 WHERE " . $Kueri. " and b.kodecabang = '".$kodedealer."' ", $conn);
					$datalist = mssql_fetch_array($list);
							 
					$ArrListProspect[] = array('IntDtbase'=>1,
												'cust'=>$row,
												'data'=>$datalist,
												'asal'=>'Internal',
												'sumber'=>'7',
												'sbrdt'=>2);
												
				/*check#4 - LANGSUNG MASUK KE DB*/
				} else if($ProcessListBengkel) { 
					
					/*$nKodePr = 1; $StnKodePr = FALSE;
					
					
					do {
						$kodepr = substr(str_pad(str_replace(" ", "", str_replace("'", "-", Trim(strtoupper($row['customer'])))),7,"-"),0,7) .
									date("Y", time()) . 
									str_pad($nKodePr, 4, '0', STR_PAD_LEFT);
						if($this->mod_home->QuerySalesDB("SELECT kodepr from [".$db_stars."]..prospectmst where kodepr='" . $kodepr . "'")) {
							$nKodePr += 1; $StnKodePr = FALSE;
						} else {
							$StnKodePr = TRUE;
						}
					} while ($StnKodePr == FALSE);
					// echo $row['customer'] ." - $kodepr<br>";
					*/
					
							
					$nKodePr = 1; 
					$StnKodePr = FALSE;
						
					do {
						$kodepr = substr(str_pad(str_replace(" ", "", str_replace("'", "-",  trim(strtoupper($nama))    )),7,"-"),0,7) .
									date("Y", time()) . 
									str_pad($nKodePr, 4, '0', STR_PAD_LEFT);
								
						$qry_kode = mssql_query("select kodepr from [".$db_stars."]..prospectmst where kodepr = '" . $kodepr . "'",$conn);		
						$cek_kode = mssql_num_rows($qry_kode);
						
						if($cek_kode>0) {
							$nKodePr += 1; $StnKodePr = FALSE;
						} else {
							$StnKodePr = TRUE;
						}
					} while ($StnKodePr == FALSE);
					
					/*$instData['KodePr'] = $kodepr;
					$instData['Nama'] = s2d(Trim($row['customer']));
					$instData['Alamat'] = s2d(Trim($row['cust_adres']));
					$instData['Kota'] = s2d(Trim($row['kota']));
					$instData['Kecamatan'] = s2d(Trim($row['kecamatan']));
					$instData['KodePos'] = Trim($row['kodepos']);
					$instData['Telp'] = Trim($row['telpon']);
					$instData['GSM'] = s2d(Trim($row['gsm']));
					$instData['CDMA'] = Trim($row['cdma']);
					$instData['KodeAr'] = s2d(Trim($row['fs_kd_plg']));
					$instData['KodeUser'] = $kdUser;
					$instData['TglEntry'] = $getdate;
					$instData['Sumber'] = "7"; 
					$instData['SalesPr'] = $row['kode_sales'];
					$instData['SbrDt'] = "2";  
					$instData['KodeSupervisor'] = ToNull(strtoupper($_SESSION['users']['kode_supervisor']));
					$instData['Grup'] = "1"; /*Perorangan*/
					// $instData['Gender'] = null;
					// $instData['PinBB'] = null;
					// $instData['Email'] = null;

					//$re = $this->mod_cust->InsertProspectMst($instData);
					$stm_insert = "insert into [".$db_stars."]..ProspectMst
										(KodePr,Nama,Alamat,Kota,Kecamatan,KodePos,Telp,GSM,CDMA,KodeAr,KodeUser,TglEntry,Sumber,SalesPr,SbrDt,KodeSupervisor,Grup) 
										values ( '".$kodepr."', '".trim($row['customer'])."', '".trim($row['cust_adres'])."', '".trim($row['kota'])."', 
										'".trim($row['kecamatan'])."',
										'".trim($row['kodepos'])."', '".trim($row['telpon'])."', '".trim($row['gsm'])."', '".trim($row['cdma'])."', 
										'".trim($row['fs_kd_plg'])."',
										'SYSTEM', getdate(), 7, '".$salespr."', '2', 
										'".strtoupper($kodesupervisor)."','1')" ;
					//echo "<pre>$stm_insert</pre>";					
					$re = mssql_query($stm_insert, $conn);
					
					$qry_cek = mssql_query("SELECT kodepr from [".$db_stars."]..prospectservice 
										where kodepr='" . $kodepr . "' and norangka='".$row['no_chassis']. "'", $conn);
										
					if (!$qry_cek) {
						//$this->mod_home->UptSalesDB("INSERT into prospectservice (KodePr,NoRangka,Km,KodeUser,TglEntry) values ('" . $kodepr . "','" . $row['no_chassis'] . "'," . $row['fs_km_akhir'] . ",'" . $kdUser . "', '$getdate')");
						
						$inst_service = mssql_query("INSERT into [".$db_stars."]..prospectservice (KodePr,NoRangka,Km,KodeUser,TglEntry) 
										values ('".$kodepr."','".$row['no_chassis']."',".$row['fs_km_akhir'].",'SYSTEM', getdate())",$conn);
						
						// hansarenkei 2020-09-29 kirim notif
						$qry_sales = mssql_query("select top 1 b.NIK, a.nasa_id, c.kode_supervisor, d.nama namaspv
												from grd_sls..tblbridging_nasa a
												left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
												left join ".DB_STARS99."..arsales c on a.nasa_id = c.kodesales
												left join (select a1.nama, a1.nik, spvnasa_id
												from fkaryawan..datakaryawan a1
												left join ".DB_GRADING."..TBLMasterman b1 on a1.NIK = b1.NIK 
												left join ".DB_GRADING."..TBLBridging_spv c1 on b1.idNama = c1.spv_id  
													and b1.KodeDealer = c1.dealer) d on d.spvnasa_id = c.kode_supervisor
												where a.nasa_id =  '".$salespr."' and isnull(tanggalkeluar,0) = 0 
												order by a.tgl_update desc",$conn);
						$dt_sales = mssql_fetch_array($qry_sales);
						$nik_sales = $dt_sales['nik'];
						
						$norangka = $row['no_chassis'];
						$nama_cust = $row['customer'];
						$telp_cust = $row['gsm']; 
						$alamat_cust = $row['cust_adres'];
						notifdarisistemkesales($kodepr, $norangka, $nik_sales, $nama_cust, $model, $telp_cust, $alamat_cust);
							
						$NewProspect = TRUE;
						
					}
				}
				
				
				if($NewProspect){
					#insert into ProspectTrn
					$qry_cek2 = mssql_query("select kodesales from [".DB_STARS99."]..arsales 
											where kodesales='" . $salespr . "' and isnull(tdkaktif,0)=0 and kodecabang = '".$kodedealer."' ", $conn);
					if ($qry_cek2) {
						#cek new prospect
						$closing = FALSE;
						$qcek = mssql_query("SELECT top 1 KodeProspect, StatusAkhir from [".$db_stars."]..ProspectTrn where kodepr='$kodepr' order by KodeProspect desc",$conn);
						$cek = mssql_fetch_array($qcek);
						$kodeprospect = $cek['KodeProspect'];
						$statusakhir = $cek['StatusAkhir'];
						
						
						if((strtoupper($statusakhir) == 'SPK' || strtoupper($statusakhir) == 'LOST')){
							/*$nKode = 1;
							$vKode = TRUE;
							while ($vKode) {
								$kodeprospect = "PR".date('y').str_pad($nKode, 4, "0",STR_PAD_LEFT);
								$cek = $this->mod_plan->CekDataProspectTrn(" kodepr='$kodepr' AND kodeprospect = '$kodeprospect' and statusakhir in ('spk','lost')");
								$vKode = $cek[0];
								$nKode++;
							}
							$data = array("KodePr"=>$kodepr, 
										  "KodeProspect"=>$kodeprospect, 
										  "Kodeuser"=>$kdUser, 
										  "TglEntry"=>$getdate,
										  "TglAwalEntry"=>$getdate,
										  "SalesPr"=>$row['kode_sales'], );
							$inst = $this->mod_plan->InsertProspectTrn($data);*/
							
							
							$nKode = 1;
							$vKode = TRUE;
							
							while ($vKode==TRUE) {
								$kodeprospect = "PR".date('y').str_pad($nKode, 4, "0",STR_PAD_LEFT);
								//$cek = $this->mod_plan->CekDataProspectTrn(" kodepr='$kodepr' AND kodeprospect = '$kodeprospect'");
																		
								$qryKode = mssql_query("select kodepr,KodeProspect,StatusAkhir 
														from [".$db_stars."]..prospecttrn 
														where kodepr='$kodepr' AND kodeprospect = '$kodeprospect' and statusakhir in ('spk','lost')",$conn);
								
								$jmlKode = mssql_num_rows($qryKode);
								
								if($jmlKode>0){
									$vKode = TRUE;
								} else {
									$vKode = FALSE;
								}
								$nKode++;
							}
															
							$qry_insert = mssql_query("insert into [".$db_stars."]..ProspectTrn (KodePr,KodeProspect,Kodeuser,TglEntry,TglAwalEntry,SalesPr) 
														values 
														('".$kodepr."','".$kodeprospect."','".$salespr."',getdate(), getdate(), '".$salespr."') ",$conn);
																					
														
						}
					}
				}
			}
		}
	/*END BENGKEL*/
	
	
	/*==============================================================================================================*/
	/*RETAIL*/
	//	if($_SESSION['users']['kodesales'] != "ALL"){
			if($_SESSION['stars_profil']==2 or $_SESSION['stars_profil']==3){
				$stmt2 = "SELECT top 20 convert(varchar(10),isnull(j.TglNDCustAkhir,j.tglndcust) , 105) tgl_notadebet,a.nama nama_pemesan,
							a.alamat AlamatSPK,i.kota kota, i.kecamatan kecamatan, i.kodepos kode_pos,
							i.telp telp_rumah ,i.hp1 hp1, i.hp2 hp2, a.kodelgn kode_lgn, a.norangka,a.kodesales, f.namakategory model
							from ".DB_SPK99."..dataspk a
							left join ".DB_STARS99."..Arsales c on a.kodesales=c.kodesales
							left join ".DB_STARS99."..Arsupervisor d on c.kode_supervisor=d.kode_supervisor
							left join ".$db_stars."..prospectretail e on a.norangka=e.norangka
							left join ".DB_SPK00."..InKategory f on a.kodetype = f.kodekategory
							left join profilspk..dealerbd h on a.dealer = h.kodedealer
							left join singleidori..bridging G with(nolock) on a.kodelgn = g.kodelgn and g.kodedealer = h.kdcbg
							left join singleidori..newcustomer i with(nolock) on g.idcust = i.idcust 
							left join ".DB_SPK00."..Inmst j on a.NoRangka = j.KodeItem 
							Where a.status = 1 and c.kodecabang = '".$kodedealer."'
							And dateadd(YEAR,(select top 1 penjualan/12 from [".$db_stars."]..StarsSettingsInject  
								order by LastUpdate desc),ISNULL(j.tglndcustakhir,j.tglndcust)) <= GetDate() 
							and isnull(c.tdkaktif,1)=1
							and isnull(e.norangka,'')=''";
							
						/*"SELECT top 20 convert(varchar(10),a.tgl_notadebet, 105) tgl_notadebet,a.nama_pemesan,
							(isnull(a.alamat1,'')+' '+isnull(a.alamat2,'')) AlamatSPK,a.kota,a.kecamatan,a.kode_pos,
							a.telp_rumah , a.hp1, a.hp2, a.kode_lgn, a.norangka,a.kodesales
							from [".DB_SPK99."]..dataspk a
							left join [".DB_STARS99."]..Arsales c on a.kodesales=c.kodesales
							left join [".DB_STARS99."]..Arsupervisor d on c.kode_supervisor=d.kode_supervisor
							left join [".$db_stars."]..prospectretail e on a.norangka=e.norangka
							Where a.spk_status = 1 and c.kodecabang = '".$kodedealer."'
							And dateadd(YEAR,(select top 1 penjualan/12 from [".$db_stars."]..StarsSettingsInject  order by LastUpdate desc),a.tgl_notadebet) <= GetDate() 
							and isnull(c.tdkaktif,1)=1
							and isnull(e.norangka,'')='' ";*/
							/*Union
							select top 20 z.tgljual tgl_notadebet,z.nama nama_pemesan,z.alamat AlamatSPK,'' kota,'' kecamatan,'' kode_pos,
							z.telprumah telp_rumah,z.telpgsm hp1,z.telpcdma hp2,'' kode_lgn,z.nomorrangka norangka,y.kodesales kode_sales
							from [".$db_stars."]..aas z
							left join [".DB_SPK."]..arsales y on z.kodesales=y.kodesales
							left join [".DB_SPK."]..arsupervisor x on y.kode_supervisor=x.kode_supervisor
							left join [".$db_stars."]..prospectretail w on z.nomorrangka=w.norangka
							Where dateadd(year,(select penjualan/12 from [".$db_stars."]..StarsSettingsInject),z.tgljual) <= GetDate()  
							and isnull(y.tdkaktif,1)=1
							and isnull(w.norangka,'')=''
							order by tgl_notadebet asc ";*/
			} else {
				$stmt2 = "SELECT top 20 convert(varchar(10),isnull(j.TglNDCustAkhir,j.tglndcust) , 105) tgl_notadebet,a.nama nama_pemesan,
							a.alamat AlamatSPK,i.kota kota, i.kecamatan kecamatan, i.kodepos kode_pos,
							i.telp telp_rumah ,i.hp1 hp1, i.hp2 hp2, a.kodelgn kode_lgn, a.norangka,a.kodesales, f.namakategory model
							from ".DB_SPK99."..dataspk a
							left join ".DB_STARS99."..Arsales c on a.kodesales=c.kodesales
							left join ".DB_STARS99."..Arsupervisor d on c.kode_supervisor=d.kode_supervisor
							left join ".$db_stars."..prospectretail e on a.norangka=e.norangka
							left join ".DB_SPK00."..InKategory f on a.kodetype = f.kodekategory
							left join profilspk..dealerbd h on a.dealer = h.kodedealer
							left join singleidori..bridging G with(nolock) on a.kodelgn = g.kodelgn and g.kodedealer = h.kdcbg
							left join singleidori..newcustomer i with(nolock) on g.idcust = i.idcust 
							left join ".DB_SPK00."..Inmst j on a.NoRangka = j.KodeItem 
							Where a.status = 1 and c.kodecabang = '".$kodedealer."'
							And dateadd(YEAR,(select top 1 penjualan/12 from [".$db_stars."]..StarsSettingsInject  
								order by LastUpdate desc),ISNULL(j.tglndcustakhir,j.tglndcust)) <= GetDate() 
							and isnull(c.tdkaktif,1)=1 and a.kodesales='".$salespr."'
							and isnull(e.norangka,'')=''";
							
				/*$stmt2 = "SELECT top 20 convert(varchar(10),a.tgl_notadebet, 105) tgl_notadebet,a.nama_pemesan,
							(isnull(a.alamat1,'')+' '+isnull(a.alamat2,'')) AlamatSPK,a.kota,a.kecamatan,a.kode_pos,
							a.telp_rumah , a.hp1, a.hp2, a.kode_lgn, a.norangka,a.kodesales
							from [".DB_SPK99."]..dataspk a
							left join [".DB_STARS99."]..Arsales c on a.kodesales=c.kodesales
							left join [".DB_STARS99."]..Arsupervisor d on c.kode_supervisor=d.kode_supervisor
							left join [".$db_stars."]..prospectretail e on a.norangka=e.norangka
							Where a.spk_status = 1 and c.kodecabang = '".$kodedealer."'
							And dateadd(year,(select top 1 penjualan/12 from [".$db_stars."]..StarsSettingsInject  order by LastUpdate desc),a.tgl_notadebet) <= GetDate() 
							and a.kode_sales='".$salespr."'
							and isnull(e.norangka,'')='' ";*/
							/*Union
							select top 20 z.tgljual tgl_notadebet,z.nama nama_pemesan,z.alamat AlamatSPK,'' kota,'' kecamatan,'' kode_pos,
							z.telprumah telp_rumah,z.telpgsm hp1,z.telpcdma hp2,'' kode_lgn,z.nomorrangka norangka,y.kodesales kode_sales
							from [".$db_stars."]..aas z
							left join [".DB_SPK."]..arsales y on z.kodesales=y.kodesales
							left join [".DB_SPK."]..arsupervisor x on y.kode_supervisor=x.kode_supervisor
							left join [".$db_stars."]..prospectretail w on z.nomorrangka=w.norangka
							Where dateadd(year,(select penjualan/12 from [".$db_stars."]..StarsSettingsInject),Z.tgljual) <= GetDate()  
							and y.kodesales='$kdSales'
							and isnull(w.norangka,'')=''
							order by tgl_notadebet asc";*/
			}
			//$query2 = $this->mod_home->QuerySalesDB($stmt2);
			
			//echo "<pre>$stmt2</pre>";
			$query2 = mssql_query($stmt2,$conn);
			
		//} else $query2 = array();

		if($query2 && $ProcessListRetail){
			while ($row = mssql_fetch_array($query2)) {
				$model = $row['model'];
				$salespr = $row['kodesales'];
				
				$tgl_notadebet = date("m/d/Y",strtotime($row['tgl_notadebet']));
				
				$NewProspect = FALSE;
				//$getdate = Get_Date();
				$getdate = date('m/d/Y');
				$arr = array();
				$arr = split(" ", $row['nama_pemesan']);
				$ParsingMsg = "";
				for ($iParsingMsg = 0; $iParsingMsg < count($arr); $iParsingMsg++) {
					if (strlen($arr[$iParsingMsg]) > 3) {
						if (strlen($ParsingMsg) > 0) {
							$ParsingMsg = $ParsingMsg . "OR CHARINDEX('" . $arr[$iParsingMsg] . "',Nama) >0 ";
						} else {
							$ParsingMsg = "CHARINDEX('" . $arr[$iParsingMsg] . "',Nama) >0 ";
						}
					}
				}
				if ($ParsingMsg == "") {
					$ParsingMsg = "CHARINDEX ('zzzzzzzzzzzzzzzz',nama)>0";
				}
				$Kueri = " salespr='".$salespr."' and " . $ParsingMsg;
				
				$qry_mst1 = mssql_query("SELECT nama, KodePr, alamat, gsm, salespr
										from ".$db_stars."..prospectmst where KodeAr='".$row['kode_lgn']."'",$conn);
				
				
				$qry_mst2 = mssql_query("SELECT KodePr, nama, alamat, gsm, salespr from [".$db_stars."]..prospectmst 
										where rtrim(ltrim(nama))='".trim($row['nama_pemesan'])."' and rtrim(ltrim(alamat))='".trim($row['AlamatSPK']) . "' 
										and rtrim(ltrim(kota))='" . trim($row['kota']) . "'",$conn);	
										
				$qry_mst3 = mssql_query("SELECT Nama, KodePr, alamat, gsm, salespr
											FROM ".$db_stars."..ProspectMst WHERE ".$Kueri,$conn);
										
				/*check#1 - KODE LANGGAN*/
				if ($qry_mst1 && $row['kode_lgn'] != "") {
					
					/*$RsAdaCust = $this->mod_home->QuerySalesDB("SELECT KodePr from prospectmst where KodeAr='" . $row['kode_lgn'] . "'");
					if (count($RsAdaCust)>0) {
						if (!($this->mod_home->QuerySalesDB("SELECT kodepr from prospectretail where kodepr='".$RsAdaCust[0]['KodePr']."' and norangka='".$row['norangka']."'"))) {
							$kodepr = $RsAdaCust[0]['KodePr'];
							$NewProspect = TRUE;
							$this->mod_home->UptSalesDB("INSERT into prospectretail (KodePr,NoRangka,tglretail,KodeUser,TglEntry) values ('".$RsAdaCust[0]['KodePr']."','".$row['norangka']."','".$row['tgl_notadebet']."','".$kdUser."','$getdate')");
						}
					}
					*/
					
					$RsAdaCust = mssql_num_rows($qry_mst1);
					$data_RsAdaCust = mssql_fetch_array($qry_mst1);
					
					$kodepr = $data_RsAdaCust['KodePr'];
					
					if ($RsAdaCust>0) {
						$kodepr = $data_RsAdaCust['KodePr'];
						
						$qry_cek = mssql_query("SELECT kodepr from [".$db_stars."]..prospectretail 
										where kodepr='" . $kodepr . "' and norangka='".$row['norangka']. "'", $conn);
						$jml_cek = mssql_num_rows($qry_cek);
										
						if ($jml_cek==0) {
													
							$inst_service = mssql_query("INSERT into [".$db_stars."]..prospectretail (KodePr,NoRangka,tglretail,KodeUser,TglEntry) 
										values ('".$kodepr."','".$row['norangka']."','".$tgl_notadebet."','SYSTEM', getdate())",$conn);
							
							//$this->mod_home->UptSalesDB("INSERT into prospectservice (KodePr,NoRangka,Km,KodeUser,TglEntry) values ('".$RsAdaCust[0]['KodePr']."','".$row['no_chassis']."',".$row['fs_km_akhir'].",'".$kdUser."','$getdate')");
							//$kodepr = $RsAdaCust[0]['KodePr'];
							// hansarenkei 2020-09-29 kirim notif
							$qry_sales = mssql_query("select top 1 b.NIK, a.nasa_id, c.kode_supervisor, d.nama namaspv
													from grd_sls..tblbridging_nasa a
													left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
													left join ".DB_STARS99."..arsales c on a.nasa_id = c.kodesales
													left join (select a1.nama, a1.nik, spvnasa_id
													from fkaryawan..datakaryawan a1
													left join ".DB_GRADING."..TBLMasterman b1 on a1.NIK = b1.NIK 
													left join ".DB_GRADING."..TBLBridging_spv c1 on b1.idNama = c1.spv_id  
														and b1.KodeDealer = c1.dealer) d on d.spvnasa_id = c.kode_supervisor
													where a.nasa_id =  '".$data_RsAdaCust['salespr']."' and isnull(tanggalkeluar,0) = 0 
													order by a.tgl_update desc",$conn);
							$dt_sales = mssql_fetch_array($qry_sales);
							$nik_sales = $dt_sales['nik'];
							
							$norangka = $row['norangka'];
							$nama_cust = $data_RsAdaCust['nama'];
							$telp_cust = $data_RsAdaCust['gsm']; 
							$alamat_cust = $data_RsAdaCust['alamat'];
							notifdarisistemkesales($kodepr, $norangka, $nik_sales, $nama_cust, $model, $telp_cust, $alamat_cust);
							
							$NewProspect = TRUE;
						}
					}
				
				/*check#2 - NAMA ALAMAT KOTA*/
				} elseif ($qry_mst2) {
				
					//$RsAdaCust = $this->mod_home->QuerySalesDB("SELECT KodePr from prospectmst where rtrim(ltrim(nama))='".Trim(s2d($row['nama_pemesan']))."' and rtrim(ltrim(alamat))='".Trim(s2d($row['AlamatSPK'])) . "' and rtrim(ltrim(kota))='" . Trim($row['kota']) . "'");
					
					$RsAdaCust = mssql_num_rows($qry_mst2);
					$data_RsAdaCust = mssql_fetch_array($qry_mst2);
					
					$kodepr = $data_RsAdaCust['KodePr'];
					
					if ($RsAdaCust>0) {
						$kodepr = $data_RsAdaCust['KodePr'];
						
						$qry_cek = mssql_query("SELECT kodepr from [".$db_stars."]..prospectretail 
										where kodepr='" . $kodepr . "' and norangka='".$row['norangka']. "'", $conn);										
						$jml_cek = mssql_num_rows($qry_cek);
										
						if ($jml_cek==0) {
										
							$inst_service = mssql_query("INSERT into [".$db_stars."]..prospectretail (KodePr,NoRangka,tglretail,KodeUser,TglEntry) 
										values ('".$kodepr."','".$row['norangka']."','".$tgl_notadebet."','SYSTEM', getdate())",$conn);
							
							//$this->mod_home->UptSalesDB("INSERT into prospectservice (KodePr,NoRangka,Km,KodeUser,TglEntry) values ('".$RsAdaCust[0]['KodePr']."','".$row['no_chassis']."',".$row['fs_km_akhir'].",'".$kdUser."','$getdate')");
							//$kodepr = $RsAdaCust[0]['KodePr'];
							
							// hansarenkei 2020-09-29 kirim notif
							$qry_sales = mssql_query("select top 1 b.NIK, a.nasa_id, c.kode_supervisor, d.nama namaspv
													from grd_sls..tblbridging_nasa a
													left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
													left join ".DB_STARS99."..arsales c on a.nasa_id = c.kodesales
													left join (select a1.nama, a1.nik, spvnasa_id
													from fkaryawan..datakaryawan a1
													left join ".DB_GRADING."..TBLMasterman b1 on a1.NIK = b1.NIK 
													left join ".DB_GRADING."..TBLBridging_spv c1 on b1.idNama = c1.spv_id  
														and b1.KodeDealer = c1.dealer) d on d.spvnasa_id = c.kode_supervisor
													where a.nasa_id =  '".$data_RsAdaCust['salespr']."' and isnull(tanggalkeluar,0) = 0 
													order by a.tgl_update desc",$conn);
							$dt_sales = mssql_fetch_array($qry_sales);
							$nik_sales = $dt_sales['nik'];
							
							$norangka = $row['norangka'];
							$nama_cust = $data_RsAdaCust['nama'];
							$telp_cust = $data_RsAdaCust['gsm']; 
							$alamat_cust = $data_RsAdaCust['alamat'];
							notifdarisistemkesales($kodepr, $norangka, $nik_sales, $nama_cust, $model, $telp_cust, $alamat_cust);
							
							$NewProspect = TRUE;
						}
					}
					
				
				/*check#3 - CARI NAMA MIRIP*/
				} elseif ($qry_mst3) {
					$list = "SELECT kodepr,Nama,alamat,kota, b.kodesales , a.GSM
							 FROM ProspectMst a
							 left join [".DB_STARS99."]..ArSales b on a.SalesPr=b.KodeSales and isnull(TdkAktif,0)=0
							 WHERE " . $Kueri. " and b.kodecabang = '".$kodedealer."' ";
					$ArrListProspect[] = array('IntDtbase'=>2,
											   'cust'=>$row,
											   'data'=>$this->mod_home->QuerySalesDB($list),
											   'asal'=>'Internal',
											   'sumber'=>'1',
											   'sbrdt'=>2);
				/*check#4 - LANGSUNG MASUK KE DB*/
				} else if($ProcessListRetail) {
					
					/*$nKodePr = 1; $StnKodePr = FALSE;
					do {
						$kodepr = substr(str_pad(str_replace(" ", "", str_replace("'", "-", Trim(strtoupper(s2d($row['nama_pemesan']))))),7,"-"),0,7) .
									date("Y", time()) . 
									str_pad($nKodePr, 4, '0', STR_PAD_LEFT);
						if($this->mod_home->QuerySalesDB("SELECT kodepr from prospectmst where kodepr='" . $kodepr . "'")) {
							$nKodePr += 1; $StnKodePr = FALSE;
						} else {
							$StnKodePr = TRUE;
						}
					} while ($StnKodePr == FALSE);
					*/
					
					
							
					$nKodePr = 1; 
					$StnKodePr = FALSE;
						
					do {
						$kodepr = substr(str_pad(str_replace(" ", "", str_replace("'", "-",  trim(strtoupper($row['nama_pemesan']))    )),7,"-"),0,7) .
									date("Y", time()) . 
									str_pad($nKodePr, 4, '0', STR_PAD_LEFT);
								
						$qry_kode = mssql_query("select kodepr from [".$db_stars."]..prospectmst where kodepr = '" . $kodepr . "'",$conn);		
						$cek_kode = mssql_num_rows($qry_kode);
						
						if($cek_kode>0) {
							$nKodePr += 1; $StnKodePr = FALSE;
						} else {
							$StnKodePr = TRUE;
						}
					} while ($StnKodePr == FALSE);
					
					// echo $row['nama_pemesan'] ." - $kodepr<br>";
					/*$instData['KodePr'] = $kodepr;
					$instData['Nama'] = s2d(Trim($row['nama_pemesan']));
					$instData['Alamat'] = s2d(Trim($row['AlamatSPK']));
					$instData['Kota'] = s2d(Trim($row['kota']));
					$instData['Kecamatan'] = s2d(Trim($row['kecamatan']));
					$instData['KodePos'] = Trim($row['kode_pos']);
					$instData['Telp'] = Trim($row['telp_rumah']);
					$instData['GSM'] = s2d(Trim($row['hp1']));
					$instData['CDMA'] = Trim($row['hp2']);
					$instData['KodeAr'] = s2d(Trim($row['kode_lgn']));
					$instData['KodeUser'] = $kdUser;
					$instData['TglEntry'] = $getdate;
					$instData['Sumber'] = "1"; 
					$instData['SalesPr'] = $row['kode_sales'];
					$instData['SbrDt'] = "2"; 
					$instData['KodeSupervisor'] = ToNull(strtoupper($_SESSION['users']['kode_supervisor']));
					$instData['Grup'] = "1"; /*Perorangan*/
					// $instData['Gender'] = null;
					// $instData['PinBB'] = null;
					// $instData['Email'] = null;
					//$re = $this->mod_cust->InsertProspectMst($instData);
					
				
					$re = mssql_query("insert into [".$db_stars."]..ProspectMst
									(KodePr,Nama,Alamat,Kota,Kecamatan,KodePos,Telp,GSM,CDMA,KodeAr,KodeUser,TglEntry,Sumber,SalesPr,SbrDt,KodeSupervisor,Grup) 
									values ( '".$kodepr."', '".trim($row['nama_pemesan'])."', '".trim($row['AlamatSPK'])."', '".trim($row['kota'])."', 
									'".trim($row['kecamatan'])."',
									'".trim($row['kode_pos'])."', '".trim($row['telp_rumah'])."', '".trim($row['hp1'])."', '".trim($row['hp2'])."', 
									'".trim($row['kode_lgn'])."',
									'SYSTEM', getdate(), 1, '".$salespr."', '2', 
									'".strtoupper($kodesupervisor)."','1')" , $conn);
					
					/*if (!($this->mod_home->QuerySalesDB("SELECT kodepr from prospectretail where kodepr='" . $kodepr . "' and norangka='" . $row['norangka'] . "'"))) {
						$NewProspect = TRUE;
						$this->mod_home->UptSalesDB("INSERT into prospectretail (KodePr,NoRangka,tglretail,KodeUser,TglEntry) values ('" . $kodepr . "','" . $row['norangka'] . "','" . $row['tgl_notadebet'] . "','" . $kdUser . "', '$getdate')");
					}
					*/
					
					
					$qry_cek = mssql_query("SELECT kodepr from [".$db_stars."]..prospectretail 
										where kodepr='" . $kodepr . "' and norangka='".$row['norangka']. "'", $conn);
										
					if (!$qry_cek) {
						$inst_service = mssql_query("INSERT into [".$db_stars."]..prospectretail (KodePr,NoRangka,tglretail,KodeUser,TglEntry) 
										values ('".$kodepr."','".$row['norangka']."','".$tgl_notadebet."','SYSTEM', getdate())",$conn);
					
						// hansarenkei 2020-09-29 kirim notif
						$qry_sales = mssql_query("select top 1 b.NIK, a.nasa_id, c.kode_supervisor, d.nama namaspv
												from grd_sls..tblbridging_nasa a
												left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
												left join ".DB_STARS99."..arsales c on a.nasa_id = c.kodesales
												left join (select a1.nama, a1.nik, spvnasa_id
												from fkaryawan..datakaryawan a1
												left join ".DB_GRADING."..TBLMasterman b1 on a1.NIK = b1.NIK 
												left join ".DB_GRADING."..TBLBridging_spv c1 on b1.idNama = c1.spv_id  
													and b1.KodeDealer = c1.dealer) d on d.spvnasa_id = c.kode_supervisor
												where a.nasa_id =  '".$salespr."' and isnull(tanggalkeluar,0) = 0 
												order by a.tgl_update desc",$conn);
						$dt_sales = mssql_fetch_array($qry_sales);
						$nik_sales = $dt_sales['nik'];
						
						$norangka = $row['rangka'];
						$nama_cust = $row['nama_pemesan'];
						$telp_cust = $row['hp1']; 
						$alamat_cust = $row['AlamatSPK'];
						notifdarisistemkesales($kodepr, $norangka, $nik_sales, $nama_cust, $model, $telp_cust, $alamat_cust);
								
						$NewProspect = TRUE;
					}
				}

				if($NewProspect){
					#insert into ProspectTrn
					
					#insert into ProspectTrn
					$qry_cek2 = mssql_query("select kodesales from [".DB_STARS99."]..arsales 
											where kodesales='" . $salespr . "' and isnull(tdkaktif,0)=0 and kodecabang = '".$kodedealer."' ", $conn);
					if ($qry_cek2) {
						#cek new prospect
						$closing = FALSE;
						$qcek = mssql_query("SELECT top 1 KodeProspect, StatusAkhir from [".$db_stars."]..ProspectTrn where kodepr='$kodepr' order by KodeProspect desc",$conn);
						$cek = mssql_fetch_array($qcek);
						$kodeprospect = $cek['KodeProspect'];
						$statusakhir = $cek['StatusAkhir'];
						
						if((strtoupper($statusakhir) == 'SPK' || strtoupper($statusakhir) == 'LOST')){
							
							/*$nKode = 1;
							$vKode = TRUE;
							while ($vKode) {
								$kodeprospect = "PR".date('y').str_pad($nKode, 4, "0",STR_PAD_LEFT);
								$cek = $this->mod_plan->CekDataProspectTrn(" kodepr='$kodepr' AND kodeprospect = '$kodeprospect' and statusakhir in ('spk','lost')");
								$vKode = $cek[0];
								$nKode++;
							}
							$data = array("KodePr"=>$kodepr, 
										  "KodeProspect"=>$kodeprospect, 
										  "Kodeuser"=>$kdUser, 
										  "TglEntry"=>$getdate,
										  "TglAwalEntry"=>$getdate,
										  "SalesPr"=>$row['kode_sales'], );
							$inst = $this->mod_plan->InsertProspectTrn($data);
							*/
							
							
							$nKode = 1;
							$vKode = TRUE;
							
							while ($vKode==TRUE) {
								$kodeprospect = "PR".date('y').str_pad($nKode, 4, "0",STR_PAD_LEFT);
								//$cek = $this->mod_plan->CekDataProspectTrn(" kodepr='$kodepr' AND kodeprospect = '$kodeprospect'");
																		
								$qryKode = mssql_query("select kodepr,KodeProspect,StatusAkhir 
														from [".$db_stars."]..prospecttrn 
														where kodepr='$kodepr' AND kodeprospect = '$kodeprospect' and statusakhir in ('spk','lost')",$conn);
								
								$jmlKode = mssql_num_rows($qryKode);
								
								if($jmlKode>0){
									$vKode = TRUE;
								} else {
									$vKode = FALSE;
								}
								$nKode++;
							}
									
							$qry_insert = mssql_query("insert into [".$db_stars."]..ProspectTrn (KodePr,KodeProspect,Kodeuser,TglEntry,TglAwalEntry,SalesPr) 
														values 
														('".$kodepr."','".$kodeprospect."','".$salespr."',getdate(), getdate(), '".$salespr."') ",$conn);
						}
					}
					// #insert into ProspectTrn
					// if ($this->mod_home->QuerySPKDB("select kodesales from arsales where kodesales='" . $row['kode_sales'] . "' and isnull(tdkaktif,0)=0")) {
					// 	$nKode = 1;
					// 	$vKode = TRUE;
					// 	while ($vKode) {
					// 		$kodeprospect = "PR".date('y').str_pad($nKode, 4, "0",STR_PAD_LEFT);
					// 		$cek = $this->mod_plan->CekDataProspectTrn(" kodepr='$kodepr' AND kodeprospect = '$kodeprospect' and statusakhir in ('spk','lost')");
					// 		$vKode = $cek[0];
					// 		$nKode++;
					// 	}
					// 	$data = array("KodePr"=>$kodepr, 
					// 	 			  "KodeProspect"=>$kodeprospect, 
					// 	 			  "Kodeuser"=>$kdUser, 
					// 	 			  "TglEntry"=>$getdate,
					// 	 			  "TglAwalEntry"=>$getdate,
					// 	 			  "SalesPr"=>$row['kode_sales'], );
					// 	$inst = $this->mod_plan->InsertProspectTrn($data);
					// }
				}
			}
		}
	/*END RETAIL*/
	
	
	/*LEASING*/
		//if($_SESSION['users']['kodesales'] != "ALL"){
			if($_SESSION['stars_profil']==2 or $_SESSION['stars_profil']==3){
				$stmt3 = "SELECT top 20 dateadd(year,Jangka_Waktu,TglPO) Jth_Tempo,
								dateadd(month,(select top 1 pembelian_kredit/-1 from ".$db_stars."..starssettingsinject order by lastupdate desc),
								dateadd(year,Jangka_Waktu,TglPO)) TglPlan,
								convert(varchar(10),TglPO,105) TglPO, Jangka_Waktu, 
								b.nama nama_pemesan, b.alamat AlamatSPK, i.kota kota, i.kecamatan kecamatan, i.kodepos kode_pos,
							i.telp telp_rumah ,i.hp1 hp1, i.hp2 hp2, b.kodelgn kode_lgn,b.norangka, b.kodesales
							from ".DB_STARS99."..Leasing a
							inner join ".DB_SPK99."..dataSPK b on a.SPK_No=b.nospk
							inner join ".DB_STARS99."..ArSales y on b.kodesales=y.KodeSales
							left join ".$db_stars."..ProspectRetail w on b.NoRangka=w.NoRangka

							left join profilspk..dealerbd h on b.dealer = h.kodedealer
							left join singleidori..bridging G with(nolock) on b.kodelgn = g.kodelgn and g.kodedealer = h.kdcbg
							left join singleidori..newcustomer i with(nolock) on g.idcust = i.idcust 

							where ISNULL(a.TglPO,'')<>'' and y.kodecabang = '".$kodedealer."' and a.kodecabang = '".$kodedealer."' 
								and isnull(y.tdkaktif,1)=0 
								and Jangka_Waktu between 1 and 10 
								and b.status = 1 and isnull(w.norangka,'')=''
								and dateadd(month,(select top 1 pembelian_kredit/-1 from ".$db_stars."..starssettingsinject  
							order by LastUpdate),
								dateadd(year,Jangka_Waktu,TglPO))
									between GETDATE() and dateadd(d, 6,GETDATE())
							order by TglPlan";
							
				/*$stmt3 = "SELECT top 20 dateadd(year,Jangka_Waktu,TglPO) Jth_Tempo,
								dateadd(month,(select top 1 pembelian_kredit/-1 from ".$db_stars."..starssettingsinject order by lastupdate desc),
								dateadd(year,Jangka_Waktu,TglPO)) TglPlan,
								convert(varchar(10),TglPO,105) TglPO, Jangka_Waktu, 
								nama_pemesan, (alamat1+' '+alamat2) AlamatSPK, kota, 
								kecamatan, kode_pos, telp_rumah, hp1, hp2, kode_lgn, b.norangka, kodesales
							from [".DB_STARS99."]..Leasing a
							inner join [".DB_SPK99."]..dataSPK b on a.SPK_No=b.SPK_No
							inner join [".DB_STARS99."]..ArSales y on b.kodesales=y.KodeSales
							left join ".$db_stars."..ProspectRetail w on b.NoRangka=w.NoRangka
							where ISNULL(a.TglPO,'')<>'' and y.kodecabang = '".$kodedealer."' and a.kodecabang = '".$kodedealer."' 
								and isnull(y.tdkaktif,1)=0 
								and Jangka_Waktu between 1 and 10 
								and spk_status = 1 and isnull(w.norangka,'')=''
								and dateadd(month,(select top 1 pembelian_kredit/-1 from ".$db_stars."..starssettingsinject  order by LastUpdate),
								dateadd(year,Jangka_Waktu,TglPO))
									between GETDATE() and dateadd(d, 6,GETDATE())
							order by TglPlan";*/
			} else {
				/*$stmt3 = "SELECT top 20 dateadd(year,Jangka_Waktu,TglPO) Jth_Tempo,
							dateadd(month,(select top 1 pembelian_kredit/-1 from ".$db_stars."..starssettingsinject order by lastupdate desc),
							dateadd(year,Jangka_Waktu,TglPO)) TglPlan,
							convert(varchar(10),TglPO,105) TglPO, Jangka_Waktu, 
							nama_pemesan, (alamat1+' '+alamat2) AlamatSPK, kota, 
							kecamatan, kode_pos, telp_rumah, hp1, hp2, kode_lgn, b.norangka, kodesales
							from [".DB_STARS99."]..Leasing a
							inner join [".DB_SPK99."]..dataSPK b on a.SPK_No=b.SPK_No
							inner join [".DB_STARS99."]..ArSales y on b.kodesales=y.KodeSales
							left join ".$db_stars."..ProspectRetail w on b.NoRangka=w.NoRangka
							where ISNULL(a.TglPO,'')<>'' and y.kodecabang = '".$kodedealer."' and a.kodecabang = '".$kodedealer."' 
								and Jangka_Waktu between 1 and 10 
								and spk_status = 1 and isnull(w.norangka,'')=''
								and dateadd(month,(select top 1 pembelian_kredit/-1 from ".$db_stars."..starssettingsinject  order by LastUpdate),
								dateadd(year,Jangka_Waktu,TglPO))
									between GETDATE() and dateadd(d, 6,GETDATE())
								and KodeSales = '".$salespr."'
							order by TglPlan";
					*/
					
				$stmt3 = "SELECT top 20 dateadd(year,Jangka_Waktu,TglPO) Jth_Tempo,
								dateadd(month,(select top 1 pembelian_kredit/-1 from ".$db_stars."..starssettingsinject order by lastupdate desc),
								dateadd(year,Jangka_Waktu,TglPO)) TglPlan,
								convert(varchar(10),TglPO,105) TglPO, Jangka_Waktu, 
								b.nama nama_pemesan, b.alamat AlamatSPK, i.kota kota, i.kecamatan kecamatan, i.kodepos kode_pos,
							i.telp telp_rumah ,i.hp1 hp1, i.hp2 hp2, b.kodelgn kode_lgn,b.norangka, b.kodesales
							from ".DB_STARS99."..Leasing a
							inner join ".DB_SPK99."..dataSPK b on a.SPK_No=b.nospk
							inner join ".DB_STARS99."..ArSales y on b.kodesales=y.KodeSales
							left join ".$db_stars."..ProspectRetail w on b.NoRangka=w.NoRangka

							left join profilspk..dealerbd h on b.dealer = h.kodedealer
							left join singleidori..bridging G with(nolock) on b.kodelgn = g.kodelgn and g.kodedealer = h.kdcbg
							left join singleidori..newcustomer i with(nolock) on g.idcust = i.idcust 

							where ISNULL(a.TglPO,'')<>'' and y.kodecabang = '".$kodedealer."' and a.kodecabang = '".$kodedealer."' 
								and isnull(y.tdkaktif,1)=0 
								and Jangka_Waktu between 1 and 10 
								and b.status = 1 and isnull(w.norangka,'')=''
								and dateadd(month,(select top 1 pembelian_kredit/-1 from ".$db_stars."..starssettingsinject  
							order by LastUpdate),
								dateadd(year,Jangka_Waktu,TglPO))
									between GETDATE() and dateadd(d, 6,GETDATE())
									and b.KodeSales = '".$salespr."'
							order by TglPlan";	
			}
			#$query3 = $this->mod_home->QuerySalesDB($stmt3);
			
			//echo "<pre>$stmt3</pre>";
			$query3 = mssql_query($stmt3,$conn);
			
		//} else $query3 = array();

		if($query3 && $ProcessListLeasing){
			while ($row = mssql_fetch_array($query3)) {
				$model = $row['model'];
				$salespr = $row['kodesales'];
				
				$NewProspect = FALSE;
				$tglPO = date("m/d/Y",strtotime($row['TglPO']));
				
				
				//$getdate = Get_Date();
				$getdate = date('m/d/Y');
				$arr = array();
				$arr = split(" ", $row['nama_pemesan']);
				$ParsingMsg = "";
				for ($iParsingMsg = 0; $iParsingMsg < count($arr); $iParsingMsg++) {
					if (strlen($arr[$iParsingMsg]) > 3) {
						if (strlen($ParsingMsg) > 0) {
							$ParsingMsg = $ParsingMsg . "OR CHARINDEX('" . $arr[$iParsingMsg] . "',Nama) >0 ";
						} else {
							$ParsingMsg = "CHARINDEX('" . $arr[$iParsingMsg] . "',Nama) >0 ";
						}
					}
				}
				if ($ParsingMsg == "") {
					$ParsingMsg = "CHARINDEX ('zzzzzzzzzzzzzzzz',nama)>0";
				}
				$Kueri = " salespr='".$salespr."' and " . $ParsingMsg;
				
				
				$qry_mst1 = mssql_query("SELECT KodePr, nama, alamat, gsm, salespr
										from ".$db_stars."..prospectmst where KodeAr='" . $row['kode_lgn'] . "'",$conn);
				
					
				$qry_mst2 = mssql_query("SELECT KodePr, nama, alamat, gsm, salespr
									from ".$db_stars."..prospectmst 
									where rtrim(ltrim(nama))='".trim($row['nama_pemesan'])."' 
									and rtrim(ltrim(alamat))='".trim($row['AlamatSPK'])."' and rtrim(ltrim(kota))='".trim($row['kota'])."'",$conn);	
									
				$qry_mst3 = mssql_query("SELECT Nama, KodePr, nama, alamat, gsm, salespr
										FROM ".$db_stars."..ProspectMst WHERE ".$Kueri,$conn);
										
				/*check#1 - KODE LANGGAN*/
				if ($qry_mst1 && $row['kode_lgn'] != "") {
					
					/*$RsAdaCust = $this->mod_home->QuerySalesDB("SELECT KodePr from prospectmst where KodeAr='" . $row['kode_lgn'] . "'");
					if (count($RsAdaCust)>0) {
						if (!($this->mod_home->QuerySalesDB("SELECT kodepr from prospectretail where kodepr='".$RsAdaCust[0]['KodePr']."' and norangka='".$row['norangka']."'"))) {
							$kodepr = $RsAdaCust[0]['KodePr'];
							$NewProspect = TRUE;
							$this->mod_home->UptSalesDB("INSERT into prospectretail (KodePr,NoRangka,tglretail,KodeUser,TglEntry,Leasing) values ('".$RsAdaCust[0]['KodePr']."','".$row['norangka']."','".$row['TglPO']."','".$kdUser."','$getdate', 1)");
						}
					}
					*/
					
					$RsAdaCust = mssql_num_rows($qry_mst1);
					$data_RsAdaCust = mssql_fetch_array($qry_mst1);
					
					$kodepr = $data_RsAdaCust['KodePr'];
					
					if ($RsAdaCust>0) {
						$kodepr = $data_RsAdaCust['KodePr'];
						
						$qry_cek = mssql_query("SELECT kodepr from [".$db_stars."]..prospectretail 
										where kodepr='" . $kodepr . "' and norangka='".$row['norangka']. "'", $conn);
						$jml_cek = mssql_num_rows($qry_cek);
										
						if ($jml_cek==0) {
										
							$inst_service = mssql_query("INSERT into [".$db_stars."]..prospectretail (KodePr,NoRangka,tglretail,KodeUser,TglEntry, Leasing) 
										values ('".$kodepr."','".$row['norangka']."','".$tglPO."','SYSTEM', getdate(), 1)",$conn);
							
							//$this->mod_home->UptSalesDB("INSERT into prospectservice (KodePr,NoRangka,Km,KodeUser,TglEntry) values ('".$RsAdaCust[0]['KodePr']."','".$row['no_chassis']."',".$row['fs_km_akhir'].",'".$kdUser."','$getdate')");
							//$kodepr = $RsAdaCust[0]['KodePr'];
							
							// hansarenkei 2020-09-29 kirim notif
							$qry_sales = mssql_query("select top 1 b.NIK, a.nasa_id, c.kode_supervisor, d.nama namaspv
													from grd_sls..tblbridging_nasa a
													left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
													left join ".DB_STARS99."..arsales c on a.nasa_id = c.kodesales
													left join (select a1.nama, a1.nik, spvnasa_id
													from fkaryawan..datakaryawan a1
													left join ".DB_GRADING."..TBLMasterman b1 on a1.NIK = b1.NIK 
													left join ".DB_GRADING."..TBLBridging_spv c1 on b1.idNama = c1.spv_id  
														and b1.KodeDealer = c1.dealer) d on d.spvnasa_id = c.kode_supervisor
													where a.nasa_id =  '".$data_RsAdaCust['salespr']."' and isnull(tanggalkeluar,0) = 0 
													order by a.tgl_update desc",$conn);
							$dt_sales = mssql_fetch_array($qry_sales);
							$nik_sales = $dt_sales['nik'];
							
							$norangka = $row['norangka'];
							$nama_cust = $data_RsAdaCust['nama'];
							$model = "";
							$telp_cust = $data_RsAdaCust['gsm']; 
							$alamat_cust = $data_RsAdaCust['alamat'];
							notifdarisistemkesales($kodepr, $norangka, $nik_sales, $nama_cust, $model, $telp_cust, $alamat_cust);
							
							$NewProspect = TRUE;
						}
					}
					
				
				/*check#2 - NAMA ALAMAT KOTA*/
				} elseif ($qry_mst2) {
					/*$RsAdaCust = $this->mod_home->QuerySalesDB("SELECT KodePr from prospectmst where rtrim(ltrim(nama))='".Trim(s2d($row['nama_pemesan']))."' and rtrim(ltrim(alamat))='".Trim(s2d($row['AlamatSPK'])) . "' and rtrim(ltrim(kota))='" . Trim($row['kota']) . "'");
					if (count($RsAdaCust)>0) {
						if (!($this->mod_home->QuerySalesDB("SELECT kodepr from prospectretail where kodepr='".$RsAdaCust[0]['KodePr']."' and norangka='".$row['norangka']."'"))) {
							$kodepr = $RsAdaCust[0]['KodePr'];
							$NewProspect = TRUE;
							$this->mod_home->UptSalesDB("INSERT into prospectretail (KodePr,NoRangka,tglretail,KodeUser,TglEntry,Leasing) values ('".$RsAdaCust[0]['KodePr']."','".$row['norangka']."','".$row['TglPO']."','".$kdUser."','$getdate', 1)");
						}
					}*/
					
					
					$RsAdaCust = mssql_num_rows($qry_mst2);
					$data_RsAdaCust = mssql_fetch_array($qry_mst2);
					
					$kodepr = $data_RsAdaCust['KodePr'];
					
					if ($RsAdaCust>0) {
						$kodepr = $data_RsAdaCust['KodePr'];
						
						$qry_cek = mssql_query("SELECT kodepr from [".$db_stars."]..prospectretail 
										where kodepr='" . $kodepr . "' and norangka='".$row['norangka']. "'", $conn);
										
						$jml_cek = mssql_num_rows($qry_cek);
										
						if ($jml_cek==0) {
							$inst_service = mssql_query("INSERT into [".$db_stars."]..prospectretail (KodePr,NoRangka,tglretail,KodeUser,TglEntry, Leasing) 
										values ('".$kodepr."','".$row['norangka']."','".$tglPO."','SYSTEM', getdate(), 1)",$conn);
							
							//$this->mod_home->UptSalesDB("INSERT into prospectservice (KodePr,NoRangka,Km,KodeUser,TglEntry) values ('".$RsAdaCust[0]['KodePr']."','".$row['no_chassis']."',".$row['fs_km_akhir'].",'".$kdUser."','$getdate')");
							//$kodepr = $RsAdaCust[0]['KodePr'];
							
							// hansarenkei 2020-09-29 kirim notif
							$qry_sales = mssql_query("select top 1 b.NIK, a.nasa_id, c.kode_supervisor, d.nama namaspv
													from grd_sls..tblbridging_nasa a
													left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
													left join ".DB_STARS99."..arsales c on a.nasa_id = c.kodesales
													left join (select a1.nama, a1.nik, spvnasa_id
													from fkaryawan..datakaryawan a1
													left join ".DB_GRADING."..TBLMasterman b1 on a1.NIK = b1.NIK 
													left join ".DB_GRADING."..TBLBridging_spv c1 on b1.idNama = c1.spv_id  
														and b1.KodeDealer = c1.dealer) d on d.spvnasa_id = c.kode_supervisor
													where a.nasa_id =  '".$data_RsAdaCust['salespr']."' and isnull(tanggalkeluar,0) = 0 
													order by a.tgl_update desc",$conn);
							$dt_sales = mssql_fetch_array($qry_sales);
							$nik_sales = $dt_sales['nik'];
							
							$norangka = $row['norangka'];
							$nama_cust = $data_RsAdaCust['nama'];
							$model = "";
							$telp_cust = $data_RsAdaCust['gsm']; 
							$alamat_cust = $data_RsAdaCust['alamat'];
							notifdarisistemkesales($kodepr, $norangka, $nik_sales, $nama_cust, $model, $telp_cust, $alamat_cust);
							
							$NewProspect = TRUE;
						}
					}
					
				/*check#3 - CARI NAMA MIRIP*/
				} elseif ($qry_mst3) {
					$list = "SELECT kodepr,Nama,alamat,kota, b.kodesales , a.GSM
							 FROM ".$db_stars."..ProspectMst a
							 left join [".DB_STARS99."]..ArSales b on a.SalesPr=b.KodeSales and isnull(TdkAktif,0)=0
							 WHERE " . $Kueri. " and b.kodecabang = '".$kodedealer."' ";
					$ArrListProspect[] = array('IntDtbase'=>3,
											   'cust'=>$row,
											   'data'=>$this->$list,
											   'asal'=>'Internal',
											   'sumber'=>'1',
											   'sbrdt'=>2);
				/*check#4 - LANGSUNG MASUK KE DB*/
				} else if($ProcessListLeasing) {
					
					/*$nKodePr = 1; $StnKodePr = FALSE;
					do {
						$kodepr = substr(str_pad(str_replace(" ", "", str_replace("'", "-", Trim(strtoupper(s2d($row['nama_pemesan']))))),7,"-"),0,7) .
									date("Y", time()) . 
									str_pad($nKodePr, 4, '0', STR_PAD_LEFT);
						if($this->mod_home->QuerySalesDB("SELECT kodepr from prospectmst where kodepr='" . $kodepr . "'")) {
							$nKodePr += 1; $StnKodePr = FALSE;
						} else {
							$StnKodePr = TRUE;
						}
					} while ($StnKodePr == FALSE);*/
					
					$nKodePr = 1; 
					$StnKodePr = FALSE;
						
					do {
						$kodepr = substr(str_pad(str_replace(" ", "", str_replace("'", "-",  trim(strtoupper($nama))    )),7,"-"),0,7) .
									date("Y", time()) . 
									str_pad($nKodePr, 4, '0', STR_PAD_LEFT);
								
						$qry_kode = mssql_query("select kodepr from [".$db_stars."]..prospectmst where kodepr = '" . $kodepr . "'",$conn);		
						$cek_kode = mssql_num_rows($qry_kode);
						
						if($cek_kode>0) {
							$nKodePr += 1; $StnKodePr = FALSE;
						} else {
							$StnKodePr = TRUE;
						}
					} while ($StnKodePr == FALSE);
					
					
					
					
					// echo $row['nama_pemesan'] ." - $kodepr<br>";
					/*$instData['KodePr'] = $kodepr;
					$instData['Nama'] = s2d(Trim($row['nama_pemesan']));
					$instData['Alamat'] = s2d(Trim($row['AlamatSPK']));
					$instData['Kota'] = s2d(Trim($row['kota']));
					$instData['Kecamatan'] = s2d(Trim($row['kecamatan']));
					$instData['KodePos'] = Trim($row['kode_pos']);
					$instData['Telp'] = Trim($row['telp_rumah']);
					$instData['GSM'] = s2d(Trim($row['hp1']));
					$instData['CDMA'] = Trim($row['hp2']);
					$instData['KodeAr'] = s2d(Trim($row['kode_lgn']));
					$instData['KodeUser'] = $kdUser;
					$instData['TglEntry'] = $getdate;
					$instData['Sumber'] = "1"; 
					$instData['SalesPr'] = $row['kode_sales'];
					$instData['SbrDt'] = "2";  
					$instData['KodeSupervisor'] = ToNull(strtoupper($_SESSION['users']['kode_supervisor']));
					$instData['Grup'] = "1"; /*Perorangan*/
					// $instData['Gender'] = null;
					// $instData['PinBB'] = null;
					// $instData['Email'] = null;
					//$re = $this->mod_cust->InsertProspectMst($instData);
					
					
					$re = mssql_query("insert into [".$db_stars."]..ProspectMst
									(KodePr,Nama,Alamat,Kota,Kecamatan,KodePos,Telp,GSM,CDMA,KodeAr,KodeUser,TglEntry,Sumber,SalesPr,SbrDt,KodeSupervisor,Grup) 
									values ( '".$kodepr."', '".trim($row['nama_pemesan'])."', '".trim($row['ALamatSPK'])."', '".trim($row['kota'])."', 
									'".trim($row['kecamatan'])."',
									'".trim($row['kode_pos'])."', '".trim($row['telp_rumah'])."', '".trim($row['hp1'])."', '".trim($row['hp2'])."', 
									'".trim($row['kode_lgn'])."',
									'SYSTEM', getdate(), 1, '".$salespr."', '2', 
									'".strtoupper($kodesupervisor)."','1')" , $conn);
					
					/*if (!($this->mod_home->QuerySalesDB("SELECT kodepr from prospectretail where kodepr='" . $kodepr . "' and norangka='" . $row['norangka'] . "'"))) {
						$NewProspect = TRUE;
						$this->mod_home->UptSalesDB("INSERT into prospectretail (KodePr,NoRangka,tglretail,KodeUser,TglEntry,Leasing) values ('" . $kodepr . "','" . $row['norangka'] . "','" . $row['TglPO'] . "','" . $kdUser . "', '$getdate', 1)");
					}*/
					
					
					$qry_cek = mssql_query("SELECT kodepr from [".$db_stars."]..prospectretail 
										where kodepr='" . $kodepr . "' and norangka='".$row['norangka']. "'", $conn);
										
					if (!$qry_cek) {
						$inst_service = mssql_query("INSERT into [".$db_stars."]..prospectretail (KodePr,NoRangka,tglretail,KodeUser,TglEntry, Leasing) 
										values ('".$kodepr."','".$row['norangka']."','".$tglPO."','SYSTEM', getdate(), 1)",$conn);
						
						// hansarenkei 2020-09-29 kirim notif
						$qry_sales = mssql_query("select top 1 b.NIK, a.nasa_id, c.kode_supervisor, d.nama namaspv
												from grd_sls..tblbridging_nasa a
												left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
												left join ".DB_STARS99."..arsales c on a.nasa_id = c.kodesales
												left join (select a1.nama, a1.nik, spvnasa_id
												from fkaryawan..datakaryawan a1
												left join ".DB_GRADING."..TBLMasterman b1 on a1.NIK = b1.NIK 
												left join ".DB_GRADING."..TBLBridging_spv c1 on b1.idNama = c1.spv_id  
													and b1.KodeDealer = c1.dealer) d on d.spvnasa_id = c.kode_supervisor
												where a.nasa_id =  '".$salespr."' and isnull(tanggalkeluar,0) = 0 
												order by a.tgl_update desc",$conn);
						$dt_sales = mssql_fetch_array($qry_sales);
						$nik_sales = $dt_sales['nik'];
						
						$norangka = $row['norangka'];
						$nama_cust = $row['nama_pemesan'];
						$model = "";
						$telp_cust = $row['hp1']; 
						$alamat_cust = $row['ALamatSPK'];
						notifdarisistemkesales($kodepr, $norangka, $nik_sales, $nama_cust, $model, $telp_cust, $alamat_cust);
							
						$NewProspect = TRUE;
					}
					
					
				}

				if($NewProspect){
					#insert into ProspectTrn
					#insert into ProspectTrn
					$qry_cek2 = mssql_query("select kodesales from [".DB_STARS99."]..arsales 
											where kodesales='" . $salespr . "' and isnull(tdkaktif,0)=0 and kodecabang = '".$kodedealer."' ", $conn);
					if ($qry_cek2) {
						#cek new prospect
						$closing = FALSE;
						$qcek = mssql_query("SELECT top 1 KodeProspect, StatusAkhir from [".$db_stars."]..ProspectTrn where kodepr='$kodepr' order by KodeProspect desc",$conn);
						$cek = mssql_fetch_array($qcek);
						$kodeprospect = $cek['KodeProspect'];
						$statusakhir = $cek['StatusAkhir'];
						
						if((strtoupper($statusakhir) == 'SPK' || strtoupper($statusakhir) == 'LOST')){
							/*$nKode = 1;
							$vKode = TRUE;
							while ($vKode) {
								$kodeprospect = "PR".date('y').str_pad($nKode, 4, "0",STR_PAD_LEFT);
								$cek = $this->mod_plan->CekDataProspectTrn(" kodepr='$kodepr' AND kodeprospect = '$kodeprospect' and statusakhir in ('spk','lost')");
								$vKode = $cek[0];
								$nKode++;
							}
							$data = array("KodePr"=>$kodepr, 
										  "KodeProspect"=>$kodeprospect, 
										  "Kodeuser"=>$kdUser, 
										  "TglEntry"=>$getdate,
										  "TglAwalEntry"=>$getdate,
										  "SalesPr"=>$row['kode_sales'], );
							$inst = $this->mod_plan->InsertProspectTrn($data);
							*/
							
							$nKode = 1;
							$vKode = TRUE;
							
							while ($vKode==TRUE) {
								$kodeprospect = "PR".date('y').str_pad($nKode, 4, "0",STR_PAD_LEFT);
								//$cek = $this->mod_plan->CekDataProspectTrn(" kodepr='$kodepr' AND kodeprospect = '$kodeprospect'");
																		
								$qryKode = mssql_query("select kodepr,KodeProspect,StatusAkhir 
														from [".$db_stars."]..prospecttrn 
														where kodepr='$kodepr' AND kodeprospect = '$kodeprospect' and statusakhir in ('spk','lost')",$conn);
								
								$jmlKode = mssql_num_rows($qryKode);
								
								if($jmlKode>0){
									$vKode = TRUE;
								} else {
									$vKode = FALSE;
								}
								$nKode++;
							}
									
							$qry_insert = mssql_query("insert into [".$db_stars."]..ProspectTrn (KodePr,KodeProspect,Kodeuser,TglEntry,TglAwalEntry,SalesPr) 
														values 
														('".$kodepr."','".$kodeprospect."','".$salespr."',getdate(), getdate(), '".$salespr."') ",$conn);
						}
					}
					// #insert into ProspectTrn
					// if ($this->mod_home->QuerySPKDB("select kodesales from arsales where kodesales='" . $row['kode_sales'] . "' and isnull(tdkaktif,0)=0")) {
					// 	$nKode = 1;
					// 	$vKode = TRUE;
					// 	while ($vKode) {
					// 		$kodeprospect = "PR".date('y').str_pad($nKode, 4, "0",STR_PAD_LEFT);
					// 		$cek = $this->mod_plan->CekDataProspectTrn(" kodepr='$kodepr' AND kodeprospect = '$kodeprospect' and statusakhir in ('spk','lost')");
					// 		$vKode = $cek[0];
					// 		$nKode++;
					// 	}
					// 	$data = array("KodePr"=>$kodepr, 
					// 	 			  "KodeProspect"=>$kodeprospect, 
					// 	 			  "Kodeuser"=>$kdUser, 
					// 	 			  "TglEntry"=>$getdate,
					// 	 			  "TglAwalEntry"=>$getdate,
					// 	 			  "SalesPr"=>$row['kode_sales'], );
					// 	$inst = $this->mod_plan->InsertProspectTrn($data);
					// }
				}	
			}
		}
	/*END LEASING*/
		// #$this->mod_home->UptProfilSPKDB("UPDATE usersales set tgllist=dateadd(dd,datediff(dd,0,getdate()),0) where kodesales='$kdSales'");
		//$this->mod_home->UptProfilSPKDB("UPDATE usersales set tgllist=dateadd(dd,datediff(dd,0,getdate()),0) where [user_id]='$userid'");
		
		
	
	
	//}
	return $ArrListProspect;
	// $data['data']= $ArrListProspect;
	// $this->load->view("home/login_data",$data);
}

function ProspectNew(){
	global $conn;
		
	$kd_db = getKodeDBDealer($_SESSION['stars_username']);
	$db_stars = DB_STARS.$kd_db;
	
	$kodedealer = BridgingDealer($kd_db);
	
	
	$qry = mssql_query("select a.namePattern, c.nameModul, a.idPattern, d.idSpec,a.idmodul 
						from ".$db_stars."..MstPattern a
						left join ".$db_stars."..MstModul c on a.idmodul = c.idModul
						left join ".$db_stars."..MstSpesification d on a.idSpec = d.idSpec
						group by a.namePattern, c.nameModul, a.idPattern, d.idSpec,a.idmodul 
						order by a.idPattern",$conn);
	while ($dt = mssql_fetch_array($qry)) {
		$modul = trim($dt['nameModul']);		
		$id_patern = $dt['idPattern'];
		$spec = $dt['idSpec'];
		$id_modul = $dt['idmodul'];
		
		$query = "";
		$qry_val = mssql_query("select a.namePattern, b.nameSpec, c.nameModul, d.Display, d.Field, a.idValue, a.idSpec, e.Query
						from  ".$db_stars."..MstPattern a
						inner join  ".$db_stars."..MstSpesification b on a.idSpec = b.idSpec
						left join  ".$db_stars."..MstModul c on a.idmodul = c.idModul
						left join  ".$db_stars."..MstKriteria d on a.idKriteria = d.idKriteria
						left join  ".$db_stars."..MstValue e on a.idValue = e.idValue
						where a.idPattern = '".$id_patern."' and a.idSpec = '".$spec."' and a.idmodul = '".$id_modul."' ",$conn);
						
		while ($dt_val = mssql_fetch_array($qry_val)) {
			$query .= " ".str_replace("~","'",$dt_val['Query']);
		}
		
		//echo $query;
		if (!empty($query)) {
		
			// no_chassis']."',".$row['fs_km_akhir cust_adres kota fs_kd_plg
			/*
				select distinct a.*, c.IDCust, c.NamaCust1+' '+c.NamaCust2+' '+c.NamaCust3 customer, c.Pekerjaan, E.Kode_Sales,
				a.alamat cust_adres, a.noka no_chassis, a.kodeLgn fs_kd_plg, a.km fs_km_akhir
				*/
			
			if ($modul=="GRP") {
				/*$qry1 = "select top 1 * from 
						(
						
						select distinct a.*, case when c.IDCust <> '' then c.IDCust else a.kodelgn end idCUst, 
						case when c.NamaCust1 <> '' then c.NamaCust1+' '+c.NamaCust2+' '+c.NamaCust3 else nama end customer, c.Pekerjaan, E.Kode_Sales,
						a.alamat cust_adres, a.noka no_chassis, a.kodeLgn fs_kd_plg, a.km fs_km_akhir

						from (
							select distinct norangka as noka, 'SPK' as smbr, Tgl_NotaDebet as tglretail, y.KodeTahun as thmobil, 0 as km,
							nama_pemesan as nama, (isnull(alamat1,'')+' '+isnull(alamat2,'')) as alamat, kota, kecamatan, kode_pos,
							telp_rumah as Telp, hp1 as gsm, hp2 as cdma, kode_lgn as kodeLgn
							from [".DB_SPK99."]..dataspk x
							left join [".DB_SPK00."]..Inmst y on x.NoRangka = y.KodeItem 
							
							union
							
							select distinct NO_CHASSIS as noka, 'GR' as smbr,
							CONVERT(datetime, SUBSTRING(ddate, 4,2)+'/'+RIGHT(ddate,4)+'/'+LEFT(ddate,2),101) as tglretail, FS_TH_MOBIL as thmobil, FS_KM_AKHIR as km ,
							customer as nama, cust_adres as alamat, kota, kecamatan, kodepos, telpon as Telp, gsm, cdma, FS_KD_PLG as kodeLgn
							from [".DB_BENGKEL99."]..custdat
						) a
						left join SingleIDOri..Bridging b on a.kodeLgn = b.KodeLgn and b.KodeDealer = '".KD_CABANG."'
						left join SingleIDOri..NewCustomer c on b.IDCust = c.IDCust
						left join SingleIDOri..UnitCustomer d on a.noka = d.NoRangka
						left join ".DB_SPK99."..dataspk E on a.noka = E.NoRangka
						left join ".DB_BENGKEL99."..CUSTDAT F on a.noka = F.NO_CHASSIS
						where a.noka not in (
							select distinct z.noka from (
							select distinct norangka as noka from [".$db_stars."]..prospectretail union
							select distinct norangka as noka from [".$db_stars."]..ProspectService
						) Z )
						and a.noka <> '' --and c.NamaCust1 <> ''
						".$query." ) XX  ";
					*/
					
				$qry1 = "select top 1 * from 
						(
						
						select distinct a.*, case when c.IDCust <> '' then c.IDCust else a.kodelgn end idCUst, 
						case when c.NamaCust1 <> '' then c.NamaCust1+' '+c.NamaCust2+' '+c.NamaCust3 else nama end customer, c.Pekerjaan, E.kodesales Kode_Sales,
						a.alamat cust_adres, a.noka no_chassis, a.kodeLgn fs_kd_plg, a.km fs_km_akhir

						from (
							select distinct norangka as noka, 'SPK' as smbr, isnull(y.TglNDCustAkhir,y.tglndcust) as tglretail, 
							y.KodeTahun as thmobil, 0 as km,
							x.nama nama_pemesan,x.alamat,i.kota kota, i.kecamatan kecamatan, i.kodepos kode_pos,
							i.telp Telp ,i.hp1 hp1, i.hp2 hp2, x.kodelgn 

							from [".DB_SPK99."]..dataspk x
							left join [".DB_SPK00."]..Inmst y on x.NoRangka = y.KodeItem 

							left join profilspk..dealerbd h on x.dealer = h.kodedealer
							left join singleidori..bridging G with(nolock) on x.kodelgn = g.kodelgn and g.kodedealer = h.kdcbg
							left join singleidori..newcustomer i with(nolock) on g.idcust = i.idcust 

							
							union
							
							select distinct NO_CHASSIS as noka, 'GR' as smbr,
							CONVERT(datetime, SUBSTRING(ddate, 4,2)+'/'+RIGHT(ddate,4)+'/'+LEFT(ddate,2),101) as tglretail, FS_TH_MOBIL as thmobil, FS_KM_AKHIR as km ,
							customer as nama, cust_adres as alamat, kota, null kecamatan, kodepos, telpon as Telp, gsm, cdma, FS_KD_PLG as kodeLgn
							from [".DB_BENGKEL99."]..custdat
						) a
						left join SingleIDOri..Bridging b on a.kodeLgn = b.KodeLgn and b.KodeDealer = 'KD_CABANG'
						left join SingleIDOri..NewCustomer c on b.IDCust = c.IDCust
						left join SingleIDOri..UnitCustomer d on a.noka = d.NoRangka
						left join ".DB_SPK99."..dataspk E on a.noka = E.NoRangka
						left join ".DB_BENGKEL99."..CUSTDAT F on a.noka = F.NO_CHASSIS
						where a.noka not in (
							select distinct z.noka from (
							select distinct norangka as noka from [".$db_stars."]..prospectretail union
							select distinct norangka as noka from [".$db_stars."]..ProspectService
						) Z )
						and a.noka <> '' --and c.NamaCust1 <> ''
						".$query."  ) XX  ";
					
			} else if ($modul=="BP") {
				// no_chassis']."',".$row['fs_km_akhir cust_adres kota fs_kd_plg
				
				/*$qry1 = "select top 1 JobBook, e.KdDlrPenjual, e.NmDlrPenjual, f.Cabang, 
						case when replace(isnull(e.ddate,'01/01/1900'),'3000-01-01','01/01/1900') = '01/01/1900' then replace(isnull(b.ddate,'01/01/1900'),'3000-01-01','01/01/1900') else e.Ddate end as tglDdate,
						datediff(Year, case when replace(isnull(e.ddate,'01/01/1900'),'3000-01-01','01/01/1900') = '01/01/1900' then replace(isnull(b.ddate,'01/01/1900'),'3000-01-01','01/01/1900') else e.Ddate end, 
						getdate()),
						b.nama customer, b.alamat cust_adres, b.kota, b.kodelg fs_kd_plg,  a.NoRangka no_chassis, '' fs_km_akhir, '' Kode_Sales
						from [".DB_BP."]..INVOICE a
						left join [".DB_BP."]..JOBDTA b on a.NoWo = b.NoWo and a.NoRangka = b.NoRangka
						left join [".DB_BP."]..CUSTDAT G on a.NoRangka = g.NoRangka
						left join [".DB_BP."]..feecabang F on a.NoWo = f.NoWo
						
						left join SingleIDOri..Bridging c on a.kodelg = c.KodeLgn and c.KodeDealer = '".KD_CABANG."'
						left join SingleIDOri..NewCustomer d on c.IDCust = d.IDCust
						left join SingleIDOri..UnitCustomer e on a.NoRangka = e.NoRangka
						where a.NoRangka not in (
							select distinct z.noka from (
							select distinct norangka as noka from [".$db_stars."]..prospectretail union
							select distinct norangka as noka from [".$db_stars."]..ProspectService
						) Z )
						and a.NoRangka <> '' and b.nama <> ''
						$query ";*/
				$qry1 = "select top 1 JobBook, e.KdDlrPenjual, e.NmDlrPenjual, '' Cabang, 
						case when replace(isnull(e.ddate,'01/01/1900'),'3000-01-01','01/01/1900') = '01/01/1900' then replace(isnull(b.ddate,'01/01/1900'),'3000-01-01','01/01/1900') else e.Ddate end as tglDdate,
						datediff(Year, case when replace(isnull(e.ddate,'01/01/1900'),'3000-01-01','01/01/1900') = '01/01/1900' then replace(isnull(b.ddate,'01/01/1900'),'3000-01-01','01/01/1900') else e.Ddate end, 
						getdate()),
						b.nama customer, b.alamat cust_adres, b.kota, b.kodelg fs_kd_plg,  a.NoRangka no_chassis, '' fs_km_akhir, '' Kode_Sales
						from [".DB_BP."]..INVOICE a
						left join [".DB_BP."]..JOBDTA b on a.NoWo = b.NoWo and a.NoRangka = b.NoRangka
						
						left join SingleIDOri..Bridging c on a.kodelg = c.KodeLgn and c.KodeDealer = '".KD_CABANG."'
						left join SingleIDOri..NewCustomer d on c.IDCust = d.IDCust
						left join SingleIDOri..UnitCustomer e on a.NoRangka = e.NoRangka
						where a.NoRangka not in (
							select distinct z.noka from (
							select distinct norangka as noka from [".$db_stars."]..prospectretail union
							select distinct norangka as noka from [".$db_stars."]..ProspectService
						) Z )
						and a.NoRangka <> '' and b.nama <> ''
						$query ";
			
			}
			
			//echo "<pre>$qry1</pre>";
			$query1 = mssql_query($qry1,$conn);
			
			
						
			while ($row = mssql_fetch_array($query1)) {
				$NewProspect = FALSE;
				
				$arr = array();
				//$arr = split(" ", s2d($row['customer']));
				
				$arr = split(" ", $row['customer']);
				$nama = $row['customer'];
				$salespr = $row['Kode_Sales'];
				
				#--------------- BRDIGING NASA SALES
				/*$qry_grd = mssql_query("select top 1 a.nasa_id from ".DB_GRADING."..tblbridging_nasa a
										left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
										where b.nik = '".$stars_username."' and isnull(tanggalkeluar,0) = 0 
										order by a.tgl_update desc",$conn);
				$dt_grd = mssql_fetch_array($qry_grd);
				$salespr = $dt_grd['nasa_id'];
				*/
				
				
				$ParsingMsg = "";
				for ($iParsingMsg = 0; $iParsingMsg < count($arr); $iParsingMsg++) {
					if (strlen($arr[$iParsingMsg]) > 3) {
						if (strlen($ParsingMsg) > 0) {
							$ParsingMsg = $ParsingMsg . "OR CHARINDEX('" . $arr[$iParsingMsg] . "',Nama) >0 ";
						} else {
							$ParsingMsg = "CHARINDEX('" . $arr[$iParsingMsg] . "',Nama) >0 ";
						}
					}
				}
				
				if ($ParsingMsg == "") {
					$ParsingMsg = "CHARINDEX ('zzzzzzzzzzzzzzzz',nama)>0";
				}
				$Kueri = " salespr='$salespr' and " . $ParsingMsg;
				
										
				$qry_mst = mssql_query("SELECT nama, KodePr from  [".$db_stars."]..prospectmst 
										where KodeAr='".$row['fs_kd_plg']."' ",$conn);
				$RsAdaCust = mssql_num_rows($qry_mst);
				$data_RsAdaCust = mssql_fetch_array($qry_mst);
						
				
				$qry_mst2 = mssql_query("SELECT nama, KodePr from  [".$db_stars."]..prospectmst 
										where rtrim(ltrim(nama))='" . trim($row['customer']) . "' 
										and rtrim(ltrim(alamat))='" . trim($row['cust_adres']) . "' and rtrim(ltrim(kota))='" . trim($row['kota']) . "'", $conn);
										
				$RsAdaCus2t = mssql_num_rows($qry_mst2);
				$data_RsAdaCust2= mssql_fetch_array($qry_mst2);
				
				$qry_mst3 = mssql_query("SELECT Nama, KodePr FROM  [".$db_stars."]..ProspectMst WHERE " . $Kueri, $conn);
					
				if ($RsAdaCust>0) {
					$kodepr = $data_RsAdaCust['KodePr'];

					$qry_cek = mssql_query("SELECT kodepr from [".$db_stars."]..prospectservice 
									where kodepr='" . $kodepr . "' and norangka='".$row['no_chassis']. "'", $conn);
					$jml_cek = mssql_num_rows($qry_cek);
									
					if ($jml_cek==0) {
						$inst_service = mssql_query("INSERT into [".$db_stars."]..prospectservice (KodePr,NoRangka,Km,KodeUser,TglEntry, smbr) 
									values ('".$kodepr."','".$row['no_chassis']."',".$row['fs_km_akhir'].",'".$kdUser."', getdate(), '".$modul."')",$conn);
						
						$in_msttype = mssql_query("insert into ".$db_stars."..prospectMstType (kodepr, idspec)
												values ('".$kodepr."', '".$spec."')  ",$conn);
													
						$in_msttype_log = mssql_query("insert into ".$db_stars."..prospectMstTypeLog (kodepr, idspec, userentry, tglentry)
												values ('".$kodepr."', '".$spec."', '".$_SESSION['stars_username']."', getdate())  ",$conn);
												
						$NewProspect = TRUE;
					}
					
				}else if ($RsAdaCust2>0) {
					$kodepr = $data_RsAdaCust2['KodePr'];
					
					$qry_cek = mssql_query("SELECT kodepr from [".$db_stars."]..prospectservice 
									where kodepr='" . $kodepr . "' and norangka='".$row['no_chassis']. "'", $conn);
					$jml_cek = mssql_num_rows($qry_cek);
									
					if ($jml_cek==0) {
									
						$inst_service = mssql_query("INSERT into [".$db_stars."]..prospectservice (KodePr,NoRangka,Km,KodeUser,TglEntry, smbr) 
									values ('".$kodepr."','".$row['no_chassis']."',".$row['fs_km_akhir'].",'".$kdUser."', getdate(), '".$modul."')",$conn);
						
						$in_msttype = mssql_query("insert into ".$db_stars."..prospectMstType (kodepr, idspec)
												values ('".$kodepr."', '".$spec."')  ",$conn);
													
						$in_msttype_log = mssql_query("insert into ".$db_stars."..prospectMstTypeLog (kodepr, idspec, userentry, tglentry)
												values ('".$kodepr."', '".$spec."', '".$_SESSION['stars_username']."', getdate())  ",$conn);
																		
						$NewProspect = TRUE;
					}
					
				}else {
					//echo "masukkkk";	
					//echo $nama."<br>";
					if ($nama!='') {	
						$nKodePr = 1; 
						$StnKodePr = FALSE;
							
						do {
							$kodepr = substr(str_pad(str_replace(" ", "", str_replace("'", "-",  trim(strtoupper($nama))    )),7,"-"),0,7) .
										date("Y", time()) . 
										str_pad($nKodePr, 4, '0', STR_PAD_LEFT);
									
							$qry_kode = mssql_query("select kodepr from [".$db_stars."]..prospectmst where kodepr = '" . $kodepr . "'",$conn);		
							$cek_kode = mssql_num_rows($qry_kode);
							
							if($cek_kode>0) {
								$nKodePr += 1; $StnKodePr = FALSE;
							} else {
								$StnKodePr = TRUE;
							}
						} while ($StnKodePr == FALSE);
						
						
						$stm_insert = "insert into [".$db_stars."]..ProspectMst
											(KodePr,Nama,Alamat,Kota,Kecamatan,KodePos,Telp,GSM,CDMA,KodeAr,KodeUser,TglEntry,Sumber,SalesPr,SbrDt,KodeSupervisor,Grup) 
											values ( '".$kodepr."', '".trim($row['customer'])."', '".trim($row['cust_adres'])."', '".trim($row['kota'])."', 
											'".trim($row['kecamatan'])."',
											'".trim($row['kodepos'])."', '".trim($row['telpon'])."', '".trim($row['gsm'])."', '".trim($row['cdma'])."', 
											'".trim($row['fs_kd_plg'])."',
											'".$kdUser."', getdate(), 7, '".$salespr."', '2', 
											'".strtoupper($kodesupervisor)."','1')" ;
						//echo "<pre>$stm_insert</pre>";					
						$qry_insert = mssql_query($stm_insert, $conn);
						
						if ($qry_insert) {
							$qry_cek = mssql_query("SELECT kodepr from [".$db_stars."]..prospectservice 
												where kodepr='" . $kodepr . "' and norangka='".$row['no_chassis']. "'", $conn);
							$jml_cek = mssql_num_rows($qry_cek);
												
							if ($jml_cek==0) {
							
								$inst_service = mssql_query("INSERT into [".$db_stars."]..prospectservice (KodePr,NoRangka,Km,KodeUser,TglEntry, smbr) 
												values ('".$kodepr."','".$row['no_chassis']."',".$row['fs_km_akhir'].",'".$kdUser."', getdate(), '".$modul."')",$conn);
												
								$in_msttype = mssql_query("insert into ".$db_stars."..prospectMstType (kodepr, idspec)
															values ('".$kodepr."', '".$spec."')  ",$conn);
								$in_msttype_log = mssql_query("insert into ".$db_stars."..prospectMstTypeLog (kodepr, idspec, userentry, tglentry)
															values ('".$kodepr."', '".$spec."', '".$_SESSION['stars_username']."', getdate())  ",$conn);											
									
								$NewProspect = TRUE;
								
							}
						}
						
					}
				}
				
				if($NewProspect){
					
					$stm_insert_inject = "insert into [".$db_stars."]..ProspectMstInject (KodePr,TglEntryInject) 
										values ( '".$kodepr."',getdate())" ;
					//echo "<pre>$stm_insert</pre>";					
					$qry_insert_inject = mssql_query($stm_insert_inject, $conn);
					
					
					#insert into ProspectTrn
					$qry_cek2 = mssql_query("select kodesales from [".DB_STARS99."]..arsales 
											where kodesales='" . $salespr . "' and isnull(tdkaktif,0)=0 and kodecabang = '".$kodedealer."' ", $conn);
					if ($qry_cek2) {
						#cek new prospect
						$closing = FALSE;
						$qcek = mssql_query("SELECT top 1 KodeProspect, StatusAkhir from [".$db_stars."]..ProspectTrn where kodepr='$kodepr' order by KodeProspect desc",$conn);
						$cek = mssql_fetch_array($qcek);
						$kodeprospect = $cek['KodeProspect'];
						$statusakhir = $cek['StatusAkhir'];
						
						
						if((strtoupper($statusakhir) == 'SPK' || strtoupper($statusakhir) == 'LOST')){
							
							$nKode = 1;
							$vKode = TRUE;
							
							while ($vKode==TRUE) {
								$kodeprospect = "PR".date('y').str_pad($nKode, 4, "0",STR_PAD_LEFT);
								//$cek = $this->mod_plan->CekDataProspectTrn(" kodepr='$kodepr' AND kodeprospect = '$kodeprospect'");
																		
								$qryKode = mssql_query("select kodepr,KodeProspect,StatusAkhir 
														from [".$db_stars."]..prospecttrn 
														where kodepr='$kodepr' AND kodeprospect = '$kodeprospect' and statusakhir in ('spk','lost')",$conn);
								
								$jmlKode = mssql_num_rows($qryKode);
								
								if($jmlKode>0){
									$vKode = TRUE;
								} else {
									$vKode = FALSE;
								}
								$nKode++;
							}
															
							$qry_insert = mssql_query("insert into [".$db_stars."]..ProspectTrn (KodePr,KodeProspect,Kodeuser,TglEntry,TglAwalEntry,SalesPr) 
														values 
														('".$kodepr."','".$kodeprospect."','".$salespr."',getdate(), getdate(), '".$salespr."') ",$conn);
													
						}
					}
				}
				
				
			}
			
		
		
		}
			
		
	}
					
}


function CheckStatusTurun($tglstatus){
	global $conn;
	
	$kd_db = getKodeDBDealer($_SESSION['stars_username']);
	$db_stars = DB_STARS.$kd_db;
	
	$kodedealer = BridgingDealer($kd_db);
	
		
	$getdate = date('m/d/Y');
	$TglTurunStatus = date('m/d/Y', strtotime($tglstatus));
	
	if($TglTurunStatus < $getdate){
		$hot = GetPlanByLastStatus("hot");
		if($hot){
			$status = "PROSPECT";
			while ($dt = mssql_fetch_array($hot)) {
				$kodepr = $dt['KodePr'];
				$kodeprospect = $dt['KodeProspect'];
				/*get plan*/
				$plan = mssql_query("SELECT KodePlan, Via, TypePlan, idKetPlan, Penggolongan
									from ".$db_stars."..PlanTrn where NoBaris='".$dt['NoBaris']."'", $conn);
				
				if($plan){
					$detPlan = mssql_fetch_array($plan);
					$kodeplan = $detPlan['KodePlan'];
					/*get last report*/
					$lastReport = mssql_query("SELECT top 1 KodePlan, 
											BersediaKetemu, ReasonBersediaKetemu, BersediaKetemuLagi, ReasonBersediaKetemuLagi,
											CaraBayar, ReasonCaraBayar, Karoseri, ReasonKaroseri, Testdrive, ReasonTestDrive, PenjelasanFitur, ReasonPenjelasanFitur
											from ".$db_stars."..ProspectFollow 
											where KodePr = '$kodepr' and KodeProspect = '$kodeprospect'
											order by TglFollow desc",$conn);
					$dtFollow = mssql_fetch_array($lastReport);
					
					
					/*$lastMobil = mssql_query("SELECT KodePlan 
									from ".$db_stars."..FollowTypeTrn
									where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and KodePlan = '".$dtFollow['KodePlan']."' ",$conn);
					
					while ( $lastMobil = mssql_fetch_array($dtMobil)) {
						//$dtMobil['KodePlan'] = $kodeplan;
						//$instMobil = $this->mod_plan->InsertFollowTypeTrn($dtMobil); #BUKANCOMMENT						
						//$instMobil = mssql_query("insert into ".$db_stars."..FollowTypeTrn (kodeplan) values ('".$kodeplan."')",$conn);
					}
					*/
					
					/*create auto report*/
					$penggolongan = $dtFollow['Testdrive'] || $dtFollow['PenjelasanFitur'] ? 'p' : 'q';
					/*$dtReport = array("KodePr"=>$kodepr,
									  "KodeProspect"=>$kodeprospect,
									  "KodePlan"=>$kodeplan,
									  "TglFollow"=>$getdate,
									  "Via"=>$detPlan['Via'],
									  "KodeUser"=>"AutoReportSystem",//$_SESSION['users']['userid'],
									  "TglEntry"=>$getdate,
									  "TypePlan"=>$detPlan['TypePlan'],
									  "idKetPlan"=>$detPlan['idKetPlan'],
									  "Keterangan"=>$detPlan['KetPlan'],
									  "Penggolongan"=>$penggolongan,//$detPlan['Penggolongan'],
									  "BersediaKetemu"=>$dtFollow['BersediaKetemu'],
									  "ReasonBersediaKetemu"=>$dtFollow['ReasonBersediaKetemu'],
									  "BersediaKetemuLagi"=>$dtFollow['BersediaKetemuLagi'],
									  "ReasonBersediaKetemuLagi"=>$dtFollow['ReasonBersediaKetemuLagi'],
									  "CaraBayar"=>$dtFollow['CaraBayar'],
									  "ReasonCaraBayar"=>$dtFollow['ReasonCaraBayar'],
									  "Karoseri"=>$dtFollow['Karoseri'],
									  "ReasonKaroseri"=>$dtFollow['ReasonKaroseri'],
									  "Testdrive"=>$dtFollow['Testdrive'],
									  "ReasonTestDrive"=>$dtFollow['ReasonTestDrive'],
									  "PenjelasanFitur"=>$dtFollow['PenjelasanFitur'],
									  "ReasonPenjelasanFitur"=>$dtFollow['ReasonPenjelasanFitur'],
									  "Status"=>$status, );
					$inst = $this->mod_plan->InsertProspectFollow($dtReport); #BUKANCOMMENT
					*/
					
					$inst = mssql_query("insert into ".$db_stars."..ProspectFollow
										(KodePr, KodeProspect, KodePlan, TglFollow, Via, KodeUser, 
										TglEntry, TypePlan, idKetPlan, 
										Keterangan, Penggolongan, BersediaKetemu, ReasonBersediaKetemu, BersediaKetemuLagi, 
										ReasonBersediaKetemuLagi, CaraBayar, ReasonCaraBayar, Karoseri, 
										ReasonKaroseri, Testdrive, ReasonTestDrive, PenjelasanFitur, 
										ReasonPenjelasanFitur, Status)
									  values ('".$kodepr."', '".$kodeprospect."', '".$kodeplan."', getdate(), '".$detPlan['Via']."','AutoReportSystem', 
									  getdate(), '".$detPlan['TypePlan']."', '".$detPlan['idKetPlan']."',
									  '".$detPlan['KetPlan']."', '".$penggolongan."', '".$dtFollow['BersediaKetemu']."', '".$dtFollow['ReasonBersediaKetemu']."', 
									  '".$dtFollow['BersediaKetemuLagi']."',
									  '".$dtFollow['ReasonBersediaKetemuLagi']."', '".$dtFollow['CaraBayar']."', '".$dtFollow['ReasonCaraBayar']."', '".$dtFollow['Karoseri']."',
									  '".$dtFollow['ReasonKaroseri']."','".$dtFollow['Testdrive']."', '".$dtFollow['ReasonTestDrive']."',
									  '".$dtFollow['PenjelasanFitur']."',
									  '".$dtFollow['ReasonPenjelasanFitur']."','".$status."')",$conn);
					
					/*create auto plan*/
					//$cek2 = $this->mod'_plan->CekDataPlanTrn(" typeplan = 1 and KodePr = '$kodepr' AND KodeProspect = '$kodeprospect' order by KodePlan desc");
					$cek2 = mssql_query("select kodepr from ".$db_stars."..plantrn
										 where  typeplan = 1 and KodePr = '$kodepr' AND KodeProspect = '$kodeprospect' 
										 order by KodePlan desc",$conn);
					$jmlcek2 = mssql_num_rows($cek2);
					
					//$nFollow = count($cek2[1]) +1;
					$nFollow = $jmlcek2 +1;
					
					$new_plan_date = date("m/d/Y",strtotime($getdate. " +7 days"));
					$new_kodeplan = "PLPR".(intval(substr($kodeplan, -6))+1);
					
					/*$dtPlan = array("SbrPlan"=>"1",
									"KodePr"=>$kodepr, 
									"KodeProspect"=>$kodeprospect, 
									"TglPlan"=>$new_plan_date,
									"Via"=>null,
									"KodePlan"=>$new_kodeplan,
									"TypePlan"=>1,
									"idKetPlan"=>$detPlan['idKetPlan'],
									"KetPlan"=>"Follow ke-$nFollow",
									"Penggolongan"=>$detPlan['Penggolongan'],
									"[Yang Berulang Tahun]"=>null,
									"[Tanggal Lahir]"=>null,
									"KodeSales"=>$_SESSION['users']['kodesales'], 
									"Kodeuser"=>'AutoPlanSystem',//$_SESSION['users']['userid'], 
									"TglEntry"=>$getdate,);
					$inst = $this->mod_plan->InsertPlanTrn($dtPlan); #BUKANCOMMENT
					*/
					
					$inst = mssql_query("insert into  ".$db_stars."..plantrn (
										SbrPlan,KodePr,KodeProspect,TglPlan,Via,KodePlan,
										TypePlan,idKetPlan,KetPlan,Penggolongan,
										[Yang Berulang Tahun],[Tanggal Lahir],KodeSales,Kodeuser,TglEntry) 
									values ( '1', '".$kodepr."', '".$kodeprospect."', '".$new_plan_date."', '','".$new_kodeplan."',1,
									'".$detPlan['idKetPlan']."','Follow ke-$nFollow',
									'".$detPlan['Penggolongan']."','','','', 'AutoPlanSystem', getdate())",$conn);

				}
				
				/*$data = array("statusakhir"=>$status,
							  "StatusBerjalan"=>"HOT",
							  "TglFollowAkhir"=>$getdate,
							  "TglEntry"=>$getdate,);
				$where = array('KodePr'=>$dt['KodePr'],
							   'KodeProspect'=>$dt['KodeProspect'], );	
				$upt = $this->mod_plan->UpdateProspectTrn($data, $where); #BUKANCOMMENT
				*/
				$upt = mssql_query("update ".$db_stars."..prospecttrn set statusakhir = '".$status."', StatusBerjalan = 'HOT', TglFollowAkhir = getdate(), TglEntry = getdate()
							  where kodepr = '".$dt['KodePr']."' and kodeprospect = '".$dt['KodeProspect']."'",$conn);
							  
			}
		}

	
		/*update TglTurunStatus*/
		//$upt = $this->mod_home->UptProfilSPKDB("UPDATE profilsales set TglTurunStatus = dateadd(dd,datediff(dd,0,'$getdate'),0)");
		$upt = mssql_query("UPDATE ".DB_PROFIL_SPK."..profilsales set TglTurunStatus = getdate()",$conn);
	}
}


function GetPlanByLastStatus($status){
	global $conn;
	
	
	$kd_db = getKodeDBDealer($_SESSION['stars_username']);
	$db_stars = DB_STARS.$kd_db;
	
	$kodedealer = BridgingDealer($kd_db);
	
	
	$stmt = "SELECT c.StatusAkhir, a.KodePr, a.KodeProspect, a.NoBaris
				from ".$db_stars."..PlanTrn a
				left join ".$db_stars."..ProspectFollow b on a.KodePr=b.KodePr and a.KodeProspect=b.KodeProspect and a.KodePlan=b.KodePlan
				inner join ".$db_stars."..ProspectTrn c on a.KodePr=c.KodePr and a.KodeProspect=c.KodeProspect 
				where b.KodePlan is null and a.TypePlan = 1 ";
	if($status == 'hot'){
		$stmt .= " and c.StatusAkhir = 'hot'
				and dateadd(day,7,a.TglPlan) < getdate() ";
	} else if($status == 'medium'){
		$stmt .= " and c.StatusAkhir = 'medium'
				and dateadd(day,15,a.TglPlan) < getdate() ";
	// } else if($status == 'low'){
	// 	$stmt .= " and c.StatusAkhir = 'low'
	// 			and dateadd(day,31,a.TglPlan) < '$getdate' ";
	} else if($status == 'low'){
		$stmt .= " and isnull(c.StatusAkhir,'') in ('low','')
				and dateadd(day,31,a.TglPlan) < getdate() ";
	}
	$stmt .= " order by TglPlan desc ";
	
	$qry = mssql_query($stmt,$conn);
	return $qry;
	
}


function SettingTargetKPI(){
	global $conn;
	
	
	$kd_db = getKodeDBDealer($_SESSION['stars_username']);
	$db_stars = DB_STARS.$kd_db;
	
	$kodedealer = BridgingDealer($kd_db);
	
	
	$stmt1 = "SELECT salespr, kodesupervisor, salespr,
						target1_newfollow, target2_newfollow, target3_newfollow, target4_newfollow, target5_newfollow, target6_newfollow,
						target1_hot, target2_hot, target3_hot, target4_hot, target5_hot, target6_hot,
						target1_spk, target2_spk, target3_spk, target4_spk, target5_spk, target6_spk,
						target1_rs, target2_rs, target3_rs, target4_rs, target5_rs, target6_rs,	grade,						
						target1_low, target2_low, target3_low, target4_low, target5_low, target6_low,
						target1_medium, target2_medium, target3_medium, target4_medium, target5_medium, target6_medium
						from ".$db_stars."..KPI5 where month(tglentry) = '".date("m")."' and year(tglentry) = '".date("Y")."' ";
	$query_sales = mssql_query($stmt1,$conn);
	$jml_sales = mssql_num_rows($query_sales);
				
	if($jml_sales==0){
		while ($dt_sales = mssql_fetch_array($query_sales)) {
			$grade = $dt_sales['grade'];
			
			$t1nf = $dt_sales['target1_newfollow'];
			$t1low = $dt_sales['target1_low'];
			//$t1medium = $_POST['t1_medium'.$i];
			$t1hot = $dt_sales['target1_hot'];
			$t1spk = $dt_sales["target1_spk"];
			$t1rs = $dt_sales["target1_rs"];
			$sales = $dt_sales['salespr'];
				
			$stm_delete = "delete from ".$db_stars."..KPI5 where salespr = '".$sales."'  and month(tglentry) = '".date("m")."' and year(tglentry) = '".date("Y")."' ";
			$query_cek = mssql_query($stm_delete,$conn);
				
			$stm = "insert into ".$db_stars."..KPI5 (kodesupervisor, salespr,
					target1_newfollow, target2_newfollow, target3_newfollow, target4_newfollow, target5_newfollow, target6_newfollow,
					target1_hot, target2_hot, target3_hot, target4_hot, target5_hot, target6_hot,
					target1_spk, target2_spk, target3_spk, target4_spk, target5_spk, target6_spk,
					target1_rs, target2_rs, target3_rs, target4_rs, target5_rs, target6_rs,	grade,						
					target1_low, target2_low, target3_low, target4_low, target5_low, target6_low,
					target1_medium, target2_medium, target3_medium, target4_medium, target5_medium, target6_medium,
					tglentry) 
					values ('','".$sales."','".$t1nf."','0','0','0','0','0',
					'".$t1hot."','0','0','0','0','0',
					'".$t1spk."','0','0','0','0','0',
					'".$t1rs."','0','0','0','0','0','".$grade."',
					'".$t1low."','0','0','0','0','0',
					'0','0','0','0','0','0',
					getdate())";
					 
		//	echo "<pre>".$stm."</pre>";		
			$query = mssql_query($stm,$conn);
		
		}
					
	}else{
		
	}
}



function sales_act_bulan($status,$kodesales,$tgl1,$tgl2, $bulan, $tahun, $dealer){
	global $conn;
		
	/*$kd_db = getKodeDBDealer($_SESSION['stars_username']);
	$db_stars = DB_STARS.$kd_db;
	
	$kodedealer = BridgingDealer($kd_db);
	*/
	
	
	if ($_SESSION['stars_profil']==4) {	
		if (!empty($_SESSION['SET_DEALER'])) {
			$kd_db = getKodeDBDealer(dekripsi($_SESSION['SET_DEALER']));	
			$judul_dealer = dekripsi($_SESSION['SET_NMDEALER']);
			$db_stars = DB_STARS.$kd_db;	
			$kodedealer = BridgingDealer($kd_db);		
			$where_dealer = " and s.kodecabang = '".$kodedealer."' ";
			$where_salespr = " and AA.salespr = '".$kodesales."' ";
			
		} else {
			$db_stars = DB_STARS."99";
			$where_dealer = " and s.kodecabang in (".$dealer.") ";
			$where_salespr = " and AA.salespr in (select kodesales from ".DB_STARS99."..ArSales where kodecabang in (".$dealer.") ) ";	
		}
	} else {
		$kd_db = getKodeDBDealer($_SESSION['stars_username']);
		$db_stars = DB_STARS.$kd_db;	
		$kodedealer = BridgingDealer($kd_db);	
		$where_dealer = " and s.kodecabang = '".$kodedealer."' ";
		$where_salespr = " and AA.salespr = '".$kodesales."' ";
	} 
	
	
	
	if ($status=="SUSPECT") {
		$stm = " select count(*) jml from (
		
						select distinct '' No_bukti, a.KodePr, a.SalesPr, 'Suspect this month' tp
						FROM ".$db_stars."..ProspectMst a
						inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
						left join 
						(
							select x.kodepr, x.KodeProspect, X.Status, x.tglfollow from ".$db_stars."..ProspectFollow X
							inner join (
							select kodepr, KodeProspect, MAX(tglfollow) as maxFollow
							from ".$db_stars."..ProspectFollow 
							where tglfollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							group by kodepr, KodeProspect) Y on x.KodePr = Y.KodePr and x.TglFollow = Y.maxFollow and x.KodeProspect = Y.KodeProspect 
						) b on a.KodePr = b.KodePr 
						WHERE a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
						and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
						".$where_dealer."
						
						union
						
						select distinct w.No_bukti, a.KodePr, a.SalesPr, 'Suspect carry' tp
						FROM ".DB_SPK99."..Summary w 
						left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
						left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
						left join 
						(
							select x.kodepr, x.KodeProspect, X.Status, x.tglfollow from ".$db_stars."..ProspectFollow X
							inner join (
							select kodepr, KodeProspect, MAX(tglfollow) as maxFollow
							from ".$db_stars."..ProspectFollow 
							where tglfollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							group by kodepr, KodeProspect) Y on x.KodePr = Y.KodePr and x.TglFollow = Y.maxFollow 
						) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
						left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
						inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
						WHERE w.tipetr = 'SPK' and w.status = '1' 
						and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
						and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
						and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
						".$where_dealer."
		
					) AA where AA.salespr is not null ".$where_salespr." ";
		/*$stm = " select count(*) jml from (
		
						select distinct '' No_bukti, a.KodePr, a.SalesPr, 'Suspect this month' tp
						FROM ".$db_stars."..ProspectMst a
						inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
						left join 
						(
							select x.kodepr, x.KodeProspect, X.Status, x.tglfollow from ".$db_stars."..ProspectFollow X
							inner join (
							select kodepr, KodeProspect, MAX(tglfollow) as maxFollow
							from ".$db_stars."..ProspectFollow 
							where tglfollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							group by kodepr, KodeProspect) Y on x.KodePr = Y.KodePr and x.TglFollow = Y.maxFollow and x.KodeProspect = Y.KodeProspect 
						) b on a.KodePr = b.KodePr 
						WHERE a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
						and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
						and s.kodecabang = '".$kodedealer."'
						
						union
						
						select distinct w.No_bukti, a.KodePr, a.SalesPr, 'Suspect carry' tp
						FROM ".DB_SPK99."..Summary w 
						left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
						left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
						left join 
						(
							select x.kodepr, x.KodeProspect, X.Status, x.tglfollow from ".$db_stars."..ProspectFollow X
							inner join (
							select kodepr, KodeProspect, MAX(tglfollow) as maxFollow
							from ".$db_stars."..ProspectFollow 
							where tglfollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							group by kodepr, KodeProspect) Y on x.KodePr = Y.KodePr and x.TglFollow = Y.maxFollow 
						) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
						left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
						inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
						WHERE w.tipetr = 'SPK' and w.status = '1' 
						and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
						and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
						and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
						and s.kodecabang = '".$kodedealer."'
		
						union
										
						select distinct '' No_bukti, a.KodePr, a.SalesPr, 'Prospect this month' tp
							FROM ".$db_stars."..ProspectMst a
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							left join 
							(
								select x.kodepr, x.KodeProspect, X.Status, x.tglfollow from ".$db_stars."..ProspectFollow X
								inner join (
								select kodepr, KodeProspect, MAX(tglfollow) as maxFollow
								from ".$db_stars."..ProspectFollow 
								where tglfollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'  
								group by kodepr, KodeProspect) Y on x.KodePr = Y.KodePr and x.TglFollow = Y.maxFollow and x.KodeProspect = Y.KodeProspect 
							) b on a.KodePr = b.KodePr 
							WHERE a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and b.Status = 'Prospect'
							and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							and s.kodecabang = '".$kodedealer."'
								
							union
							
							select distinct w.No_bukti, a.KodePr, a.SalesPr, 'Prospect carry' tp
							FROM ".DB_SPK99."..Summary w 
							left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
							left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
							left join 
							(
								select x.kodepr, x.KodeProspect, X.Status, x.tglfollow from ".$db_stars."..ProspectFollow X
								inner join (
								select kodepr, KodeProspect, MAX(tglfollow) as maxFollow
								from ".$db_stars."..ProspectFollow 
								where tglfollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
								group by kodepr, KodeProspect) Y on x.KodePr = Y.KodePr and x.TglFollow = Y.maxFollow 
							) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
							left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							WHERE w.tipetr = 'SPK' and w.status = '1' 
							and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
							and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							and b.Status = 'Prospect'
							and s.kodecabang = '".$kodedealer."'
						
							union
							
							select distinct '' No_bukti, a.KodePr, a.SalesPr, 'hot this month' tp
							FROM ".$db_stars."..ProspectMst a
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							left join 
							(
								select x.kodepr, x.KodeProspect, X.Status, x.tglfollow from ".$db_stars."..ProspectFollow X
								inner join (
								select kodepr, KodeProspect, MAX(tglfollow) as maxFollow
								from ".$db_stars."..ProspectFollow 
								where tglfollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
								group by kodepr, KodeProspect) Y on x.KodePr = Y.KodePr and x.TglFollow = Y.maxFollow and x.KodeProspect = Y.KodeProspect 
							) b on a.KodePr = b.KodePr 
							WHERE a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and b.Status = 'hot'  
							and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							and s.kodecabang = '".$kodedealer."'
							
							union
							
							select distinct w.No_bukti, a.KodePr, a.SalesPr, 'hot carry' tp
							FROM ".DB_SPK99."..Summary w 
							left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
							left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
							left join 
							(
								select x.kodepr, x.KodeProspect, X.Status, x.tglfollow from ".$db_stars."..ProspectFollow X
								inner join (
								select kodepr, KodeProspect, MAX(tglfollow) as maxFollow
								from ".$db_stars."..ProspectFollow 
								where tglfollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
								group by kodepr, KodeProspect) Y on x.KodePr = Y.KodePr and x.TglFollow = Y.maxFollow 
							) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
							left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							WHERE w.tipetr = 'SPK' and w.status = '1' 
							and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
							and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							and b.Status = 'hot'
							and s.kodecabang = '".$kodedealer."'
							
							union
											
							select distinct w.No_bukti, a.kodepr, a.salespr, 'spk_this_month' tp
							FROM ".DB_SPK99."..Summary w 
							left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
							left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
							left join 
							(
								select x.kodepr, x.KodeProspect, X.Status, x.tglfollow from ".$db_stars."..ProspectFollow X
								inner join (
								select kodepr, KodeProspect, MAX(tglfollow) as maxFollow
								from ".$db_stars."..ProspectFollow 
								where tglfollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
								group by kodepr, KodeProspect) Y on x.KodePr = Y.KodePr and x.TglFollow = Y.maxFollow and x.KodeProspect = Y.KodeProspect 
							) b on c.KodePr = b.KodePr and c.KodeProspect = b.kodeprospect
							left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							left join ".$db_stars."..FollowTypeTrn f on c.KodePr = f.KodePr AND c.KodeProspect = f.KodeProspect and c.KodePlan = f.KodePlan
							left join ".DB_SPK00."..InKategory g on f.Type = g.KodeKategory
							
							WHERE w.tipetr = 'SPK' and w.status = '0' 
							--and MONTH(a.TglEntry) <= '".$bulan."' AND YEAR(a.TglEntry) <= '".$tahun."'
							and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							and s.kodecabang = '".$kodedealer."'
							
							union 
							
							select distinct w.No_bukti, a.kodepr, a.salespr, 'spk_carry' tp
							FROM ".DB_SPK99."..Summary w 
							left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
							left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
							left join 
							(
								select x.kodepr, x.KodeProspect, X.Status, x.tglfollow from ".$db_stars."..ProspectFollow X
								inner join (
								select kodepr, KodeProspect, MAX(tglfollow) as maxFollow
								from ".$db_stars."..ProspectFollow 
								where tglfollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
								group by kodepr, KodeProspect) Y on x.KodePr = Y.KodePr and x.TglFollow = Y.maxFollow 
							) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
							left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							
							left join ".$db_stars."..FollowTypeTrn f on c.KodePr = f.KodePr AND c.KodeProspect = f.KodeProspect and c.KodePlan = f.KodePlan
							left join ".DB_SPK00."..InKategory g on f.Type = g.KodeKategory				
							WHERE w.tipetr = 'SPK' and w.status = '1' 
							and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
							and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							and s.kodecabang = '".$kodedealer."'
							
							
					) AA where AA.salespr = '".$kodesales."' ";*/
	}
	
	if ($status=="PROSPECT") {
		$stm = " select count(*) jml from (
						select distinct '' No_bukti, a.KodePr, a.SalesPr, 'Prospect this month' tp
							FROM ".$db_stars."..ProspectMst a
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							left join 
							(
								select x.kodepr, x.KodeProspect, X.Status, x.tglfollow from ".$db_stars."..ProspectFollow X
								inner join (
								select kodepr, KodeProspect, MAX(tglfollow) as maxFollow
								from ".$db_stars."..ProspectFollow 
								where tglfollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'  
								group by kodepr, KodeProspect) Y on x.KodePr = Y.KodePr and x.TglFollow = Y.maxFollow and x.KodeProspect = Y.KodeProspect 
							) b on a.KodePr = b.KodePr 
							WHERE a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and b.Status = 'Prospect'
							and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							".$where_dealer."
								
							union
							
							select distinct w.No_bukti, a.KodePr, a.SalesPr, 'Prospect carry' tp
							FROM ".DB_SPK99."..Summary w 
							left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
							left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
							left join 
							(
								select x.kodepr, x.KodeProspect, X.Status, x.tglfollow from ".$db_stars."..ProspectFollow X
								inner join (
								select kodepr, KodeProspect, MAX(tglfollow) as maxFollow
								from ".$db_stars."..ProspectFollow 
								where tglfollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
								group by kodepr, KodeProspect) Y on x.KodePr = Y.KodePr and x.TglFollow = Y.maxFollow 
							) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
							left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							WHERE w.tipetr = 'SPK' and w.status = '1' 
							and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
							and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							and b.Status = 'Prospect'
							".$where_dealer."
						
							union
							
							select distinct '' No_bukti, a.KodePr, a.SalesPr, 'hot this month' tp
							FROM ".$db_stars."..ProspectMst a
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							left join 
							(
								select x.kodepr, x.KodeProspect, X.Status, x.tglfollow from ".$db_stars."..ProspectFollow X
								inner join (
								select kodepr, KodeProspect, MAX(tglfollow) as maxFollow
								from ".$db_stars."..ProspectFollow 
								where tglfollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
								group by kodepr, KodeProspect) Y on x.KodePr = Y.KodePr and x.TglFollow = Y.maxFollow and x.KodeProspect = Y.KodeProspect 
							) b on a.KodePr = b.KodePr 
							WHERE a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and b.Status = 'hot'  
							and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							".$where_dealer."
							
							union
							
							select distinct w.No_bukti, a.KodePr, a.SalesPr, 'hot carry' tp
							FROM ".DB_SPK99."..Summary w 
							left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
							left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
							left join 
							(
								select x.kodepr, x.KodeProspect, X.Status, x.tglfollow from ".$db_stars."..ProspectFollow X
								inner join (
								select kodepr, KodeProspect, MAX(tglfollow) as maxFollow
								from ".$db_stars."..ProspectFollow 
								where tglfollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
								group by kodepr, KodeProspect) Y on x.KodePr = Y.KodePr and x.TglFollow = Y.maxFollow 
							) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
							left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							WHERE w.tipetr = 'SPK' and w.status = '1' 
							and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
							and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							and b.Status = 'hot'
							".$where_dealer."
							
							union
											
							select distinct w.No_bukti, a.kodepr, a.salespr, 'spk_this_month' tp
							FROM ".DB_SPK99."..Summary w 
							left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
							left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
							left join 
							(
								select x.kodepr, x.KodeProspect, X.Status, x.tglfollow from ".$db_stars."..ProspectFollow X
								inner join (
								select kodepr, KodeProspect, MAX(tglfollow) as maxFollow
								from ".$db_stars."..ProspectFollow 
								where tglfollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
								group by kodepr, KodeProspect) Y on x.KodePr = Y.KodePr and x.TglFollow = Y.maxFollow and x.KodeProspect = Y.KodeProspect 
							) b on c.KodePr = b.KodePr and c.KodeProspect = b.kodeprospect
							left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							left join ".$db_stars."..FollowTypeTrn f on c.KodePr = f.KodePr AND c.KodeProspect = f.KodeProspect and c.KodePlan = f.KodePlan
							left join ".DB_SPK00."..InKategory g on f.Type = g.KodeKategory
							
							WHERE w.tipetr = 'SPK' and w.status = '0' 
							--and MONTH(a.TglEntry) <= '".$bulan."' AND YEAR(a.TglEntry) <= '".$tahun."'
							and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							".$where_dealer."
							
							union 
							
							select distinct w.No_bukti, a.kodepr, a.salespr, 'spk_carry' tp
							FROM ".DB_SPK99."..Summary w 
							left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
							left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
							left join 
							(
								select x.kodepr, x.KodeProspect, X.Status, x.tglfollow from ".$db_stars."..ProspectFollow X
								inner join (
								select kodepr, KodeProspect, MAX(tglfollow) as maxFollow
								from ".$db_stars."..ProspectFollow 
								where tglfollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
								group by kodepr, KodeProspect) Y on x.KodePr = Y.KodePr and x.TglFollow = Y.maxFollow 
							) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
							left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							
							left join ".$db_stars."..FollowTypeTrn f on c.KodePr = f.KodePr AND c.KodeProspect = f.KodeProspect and c.KodePlan = f.KodePlan
							left join ".DB_SPK00."..InKategory g on f.Type = g.KodeKategory				
							WHERE w.tipetr = 'SPK' and w.status = '1' 
							and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
							and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							".$where_dealer."
							
					) AA where AA.salespr is not null ".$where_salespr." ";
	}
	
	if ($status=="HOT") {
		$stm = " select count(*) jml from (
						
							select distinct '' No_bukti, a.KodePr, a.SalesPr, 'hot this month' tp
							FROM ".$db_stars."..ProspectMst a
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							left join 
							(
								select x.kodepr, x.KodeProspect, X.Status, x.tglfollow from ".$db_stars."..ProspectFollow X
								inner join (
								select kodepr, KodeProspect, MAX(tglfollow) as maxFollow
								from ".$db_stars."..ProspectFollow 
								where tglfollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
								group by kodepr, KodeProspect) Y on x.KodePr = Y.KodePr and x.TglFollow = Y.maxFollow and x.KodeProspect = Y.KodeProspect 
							) b on a.KodePr = b.KodePr 
							WHERE a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and b.Status = 'hot'  
							and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							".$where_dealer."
							
							union
							
							select distinct w.No_bukti, a.KodePr, a.SalesPr, 'hot carry' tp
							FROM ".DB_SPK99."..Summary w 
							left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
							left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
							left join 
							(
								select x.kodepr, x.KodeProspect, X.Status, x.tglfollow from ".$db_stars."..ProspectFollow X
								inner join (
								select kodepr, KodeProspect, MAX(tglfollow) as maxFollow
								from ".$db_stars."..ProspectFollow 
								where tglfollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
								group by kodepr, KodeProspect) Y on x.KodePr = Y.KodePr and x.TglFollow = Y.maxFollow 
							) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
							left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							WHERE w.tipetr = 'SPK' and w.status = '1' 
							and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
							and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							and b.Status = 'hot'
							".$where_dealer."
							
						union
										
						select distinct w.No_bukti, a.kodepr, a.salespr, 'spk_this_month' tp
						FROM ".DB_SPK99."..Summary w 
						left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
						left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
						left join 
						(
							select x.kodepr, x.KodeProspect, X.Status, x.tglfollow from ".$db_stars."..ProspectFollow X
							inner join (
							select kodepr, KodeProspect, MAX(tglfollow) as maxFollow
							from ".$db_stars."..ProspectFollow 
							where tglfollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							group by kodepr, KodeProspect) Y on x.KodePr = Y.KodePr and x.TglFollow = Y.maxFollow and x.KodeProspect = Y.KodeProspect 
						) b on c.KodePr = b.KodePr and c.KodeProspect = b.kodeprospect
						left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
						inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
						left join ".$db_stars."..FollowTypeTrn f on c.KodePr = f.KodePr AND c.KodeProspect = f.KodeProspect and c.KodePlan = f.KodePlan
						left join ".DB_SPK00."..InKategory g on f.Type = g.KodeKategory
						
						WHERE w.tipetr = 'SPK' and w.status = '0' 
						--and MONTH(a.TglEntry) <= '".$bulan."' AND YEAR(a.TglEntry) <= '".$tahun."'
						and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
						and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
						and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
						".$where_dealer."
						
						union 
						
						select distinct w.No_bukti, a.kodepr, a.salespr, 'spk_carry' tp
						FROM ".DB_SPK99."..Summary w 
						left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
						left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
						left join 
						(
							select x.kodepr, x.KodeProspect, X.Status, x.tglfollow from ".$db_stars."..ProspectFollow X
							inner join (
							select kodepr, KodeProspect, MAX(tglfollow) as maxFollow
							from ".$db_stars."..ProspectFollow 
							where tglfollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							group by kodepr, KodeProspect) Y on x.KodePr = Y.KodePr and x.TglFollow = Y.maxFollow 
						) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
						left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
						inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
						
						left join ".$db_stars."..FollowTypeTrn f on c.KodePr = f.KodePr AND c.KodeProspect = f.KodeProspect and c.KodePlan = f.KodePlan
						left join ".DB_SPK00."..InKategory g on f.Type = g.KodeKategory				
						WHERE w.tipetr = 'SPK' and w.status = '1' 
						and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
						and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
						and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
						".$where_dealer."
						
					) AA where AA.salespr is not null ".$where_salespr." ";
	}

	if ($status=="SPK") {
		$stm = " select count(*) jml from (
					
						select distinct w.No_bukti, a.kodepr, a.salespr, 'spk_this_month' tp
									FROM ".DB_SPK99."..Summary w 
									left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
									left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
									left join 
									(
										select x.kodepr, x.KodeProspect, X.Status, x.tglfollow from ".$db_stars."..ProspectFollow X
										inner join (
										select kodepr, KodeProspect, MAX(tglfollow) as maxFollow
										from ".$db_stars."..ProspectFollow 
										where tglfollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
										group by kodepr, KodeProspect) Y on x.KodePr = Y.KodePr and x.TglFollow = Y.maxFollow and x.KodeProspect = Y.KodeProspect 
									) b on c.KodePr = b.KodePr and c.KodeProspect = b.kodeprospect
									left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
									inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
									left join ".$db_stars."..FollowTypeTrn f on c.KodePr = f.KodePr AND c.KodeProspect = f.KodeProspect and c.KodePlan = f.KodePlan
									left join ".DB_SPK00."..InKategory g on f.Type = g.KodeKategory
									
									WHERE w.tipetr = 'SPK' and w.status = '0' 
									--and MONTH(a.TglEntry) <= '".$bulan."' AND YEAR(a.TglEntry) <= '".$tahun."'
									and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
									and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
									and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
									".$where_dealer."
												
									union 
									
									select distinct w.No_bukti, a.kodepr, a.salespr, 'spk_carry' tp
									FROM ".DB_SPK99."..Summary w 
									left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
									left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
									left join 
									(
										select x.kodepr, x.KodeProspect, X.Status, x.tglfollow from ".$db_stars."..ProspectFollow X
										inner join (
										select kodepr, KodeProspect, MAX(tglfollow) as maxFollow
										from ".$db_stars."..ProspectFollow 
										where tglfollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
										group by kodepr, KodeProspect) Y on x.KodePr = Y.KodePr and x.TglFollow = Y.maxFollow 
									) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
									left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
									inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
									
									left join ".$db_stars."..FollowTypeTrn f on c.KodePr = f.KodePr AND c.KodeProspect = f.KodeProspect and c.KodePlan = f.KodePlan
									left join ".DB_SPK00."..InKategory g on f.Type = g.KodeKategory				
									WHERE w.tipetr = 'SPK' and w.status = '1' 
									and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
									and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
									and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
									".$where_dealer."
									
					) AA where AA.salespr is not null ".$where_salespr."
					";
	}
	
	if ($status=="RS") {
		$stm = " select count(*) jml from (
						
						select distinct w.No_bukti, a.kodepr , a.salespr, 'rs_this_month' tp
									FROM ".DB_SPK99."..Summary w 
									left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
									left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
									left join 
									(
										select x.kodepr, x.KodeProspect, X.Status, x.tglfollow from ".$db_stars."..ProspectFollow X
										inner join (
										select kodepr, KodeProspect, MAX(tglfollow) as maxFollow
										from ".$db_stars."..ProspectFollow 
										where tglfollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
										group by kodepr, KodeProspect) Y on x.KodePr = Y.KodePr and x.TglFollow = Y.maxFollow 
									) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
									left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
									inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
									left join ".$db_stars."..FollowTypeTrn f on c.KodePr = f.KodePr AND c.KodeProspect = f.KodeProspect and c.KodePlan = f.KodePlan
									left join ".DB_SPK00."..InKategory g on f.Type = g.KodeKategory
									
									WHERE w.tipetr = 'SPK' and w.status = '1' 
									--and MONTH(a.TglEntry) <= '".$bulan."' AND YEAR(a.TglEntry) <= '".$tahun."'
									and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
									and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
									and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
									".$where_dealer."
									
									union 
																	
									select distinct w.No_bukti, a.kodepr ,  a.salespr, 'rs_carry' tp
									FROM ".DB_SPK99."..Summary w 
									left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
									left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
									left join 
									(
										select x.kodepr, x.KodeProspect, X.Status, x.tglfollow from ".$db_stars."..ProspectFollow X
										inner join (
										select kodepr, KodeProspect, MAX(tglfollow) as maxFollow
										from ".$db_stars."..ProspectFollow 
										where tglfollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
										group by kodepr, KodeProspect) Y on x.KodePr = Y.KodePr and x.TglFollow = Y.maxFollow 
									) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
									left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
									inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
									
									left join ".$db_stars."..FollowTypeTrn f on c.KodePr = f.KodePr AND c.KodeProspect = f.KodeProspect and c.KodePlan = f.KodePlan
									left join ".DB_SPK00."..InKategory g on f.Type = g.KodeKategory				
									WHERE w.tipetr = 'SPK' and w.status = '1' 
									and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
									and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
									and w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
									".$where_dealer."
									
					) AA where AA.salespr is not null ".$where_salespr."
					";	
	}	
	
	//echo "<pre>$stm</pre>";
	$qry = mssql_query($stm,$conn);
	$dt = mssql_fetch_array($qry);
	$jml = $dt['jml'];
	return $jml;
}

function sales_act($status,$kodesales,$tgl1,$tgl2, $bulan, $tahun, $h, $dealer) {
	global $conn;	
		
	//$kd_db = getKodeDBDealer($_SESSION['stars_username']);
	//$db_stars = DB_STARS.$kd_db;
	
	//$kodedealer = BridgingDealer($kd_db);
	
	if ($_SESSION['stars_profil']==4) {	
		if (!empty($_SESSION['SET_DEALER'])) {
			$kd_db = getKodeDBDealer(dekripsi($_SESSION['SET_DEALER']));	
			$judul_dealer = dekripsi($_SESSION['SET_NMDEALER']);
			$db_stars = DB_STARS.$kd_db;
			$kodedealer = BridgingDealer($kd_db);			
			$where_dealer = " and s.kodecabang = '".$kodedealer."' ";
			$where_salespr = " and AA.salespr = '".$kodesales."' ";
			
		} else {
			$db_stars = DB_STARS."99";
			$where_dealer = " and s.kodecabang in (".$dealer.") ";
			$where_salespr = " and AA.salespr in (select kodesales from ".DB_STARS99."..ArSales where kodecabang in (".$dealer.") ) ";	
		}
	} else {
		$kd_db = getKodeDBDealer($_SESSION['stars_username']);
		$db_stars = DB_STARS.$kd_db;		
		$kodedealer = BridgingDealer($kd_db);
		$where_dealer = " and s.kodecabang = '".$kodedealer."' ";
		$where_salespr = " and AA.salespr = '".$kodesales."' ";
	} 
	
	
	
	
	if ($h==1) {
		$tglfollow = " x.h1 'Status', x.tglh1 'TglFollow' ";
		$where_status = " and x.h1 = '".$status."' and x.tglh1 between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59' ";
	} else if ($h==2) {
		$tglfollow = " x.h2 'Status', x.tglh2 'TglFollow' ";
		$where_status = " and x.h2 = '".$status."' and x.tglh2 between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59' ";
	} else if ($h==3) {
		$tglfollow = " x.h3 'Status', x.tglh3 'TglFollow' ";
		$where_status = " and x.h3 = '".$status."' and x.tglh3 between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59' ";
	} else if ($h==4) {
		$tglfollow = " x.h4 'Status', x.tglh4 'TglFollow' ";
		$where_status = " and x.h4 = '".$status."' and x.tglh4 between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59' ";
	} else if ($h==5) {
		$tglfollow = " x.h5 'Status', x.tglh5 'TglFollow' ";
		$where_status = " and x.h5 = '".$status."' and x.tglh5 between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59' ";
	} else if ($h==6) {
		$tglfollow = " x.h6 'Status', x.tglh6 'TglFollow' ";
		$where_status = " and x.h6 = '".$status."' and x.tglh6 between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59' ";
	}
	
	if ($status=="SUSPECT") {
		/*$stm = " select count(*) jml from (
					select distinct '' No_bukti, a.KodePr, a.SalesPr, 'Suspect this month' tp
					FROM ".$db_stars."..ProspectMst a
					inner join ".DB_STARS99."..Arsales s on a.salespr = s.kodesales
					left join 
					(
						select x.kodepr, x.KodeProspect, X.Status, x.tglfollow from ".$db_stars."..ProspectFollow X
						inner join (
						select kodepr, KodeProspect, MAX(tglfollow) as maxFollow
						from ".$db_stars."..ProspectFollow 
						where tglfollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
						group by kodepr, KodeProspect) Y on x.KodePr = Y.KodePr and x.TglFollow = Y.maxFollow and x.KodeProspect = Y.KodeProspect 
					) b on a.KodePr = b.KodePr 
					WHERE a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
					and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59' and s.kodecabang = '".$kodedealer."'				
					
					union 
					
					select distinct w.No_bukti, a.KodePr, a.SalesPr, 'Suspect carry' tp
					FROM ".DB_SPK99."..Summary w 
					left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
					left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
					left join 
					(
						select x.kodepr, x.KodeProspect, X.Status, x.tglfollow from ".$db_stars."..ProspectFollow X
						inner join (
						select kodepr, KodeProspect, MAX(tglfollow) as maxFollow
						from ".$db_stars."..ProspectFollow 
						where tglfollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
						group by kodepr, KodeProspect) Y on x.KodePr = Y.KodePr and x.TglFollow = Y.maxFollow 
					) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
					left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
					inner join ".DB_STARS99."..Arsales s on a.salespr = s.kodesales
					WHERE w.tipetr = 'SPK' and w.status = '1' 
					and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
					and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
					and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59' and s.kodecabang = '".$kodedealer."'
					
				) AA where AA.salespr = '".$kodesales."' ";
		*/			
		$stm = " select count(*) jml from (
						select distinct '' No_bukti, a.KodePr, a.SalesPr, 'Suspect this month' tp
						FROM ".$db_stars."..ProspectMst a
						inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
						inner join 
						(
							 select x.kodepr, x.kodeprospect, $tglfollow
							  from ".$db_stars."..KPI5harian_status x
							  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status
						) b on a.KodePr = b.KodePr 
						WHERE a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
						and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
						".$where_dealer."
						
						union
						
						select distinct w.No_bukti, a.KodePr, a.SalesPr, 'Suspect carry' tp
						FROM ".DB_SPK99."..Summary w 
						left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
						left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
						inner join 
						(
							  select x.kodepr, x.kodeprospect, $tglfollow
							  from ".$db_stars."..KPI5harian_status x
							  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status
						) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
						left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
						inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
						WHERE w.tipetr = 'SPK' and w.status = '1' 
						and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
						and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
						and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
						".$where_dealer."
							
					) AA where AA.salespr is not null ".$where_salespr." ";
					
	}
	
	if ($status=="PROSPECT") {
		$stm = " select count(*) jml from (
						select distinct '' No_bukti, a.KodePr, a.SalesPr, 'Prospect this month' tp
							FROM ".$db_stars."..ProspectMst a
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							inner join 
							(
								 select x.kodepr, x.kodeprospect, $tglfollow
								  from ".$db_stars."..KPI5harian_status x
								  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status
							) b on a.KodePr = b.KodePr 
							WHERE a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and b.Status = 'Prospect'
							and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							".$where_dealer."
								
							union
							
							select distinct w.No_bukti, a.KodePr, a.SalesPr, 'Prospect carry' tp
							FROM ".DB_SPK99."..Summary w 
							left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
							left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
							inner join 
							(
								 select x.kodepr, x.kodeprospect, $tglfollow
								  from ".$db_stars."..KPI5harian_status x
								  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status								  
							) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
							left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							WHERE w.tipetr = 'SPK' and w.status = '1' 
							and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
							and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							and b.Status = 'Prospect'
							".$where_dealer."
							
							union
							
							select distinct '' No_bukti, a.KodePr, a.SalesPr, 'hot this month' tp
							FROM ".$db_stars."..ProspectMst a
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							inner join 
							(
								 select x.kodepr, x.kodeprospect, $tglfollow
								  from ".$db_stars."..KPI5harian_status x
								  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status
							) b on a.KodePr = b.KodePr 
							WHERE a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and b.Status = 'hot'  
							and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							".$where_dealer."
							
							union
							
							select distinct w.No_bukti, a.KodePr, a.SalesPr, 'hot carry' tp
							FROM ".DB_SPK99."..Summary w 
							left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
							left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
							inner join 
							(
								 select x.kodepr, x.kodeprospect, $tglfollow
								  from ".$db_stars."..KPI5harian_status x
								  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status 
							) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
							left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							WHERE w.tipetr = 'SPK' and w.status = '1' 
							and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
							and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							and b.Status = 'hot'
							".$where_dealer."
							
							union
											
							select distinct w.No_bukti, a.kodepr, a.salespr, 'spk_this_month' tp
							FROM ".DB_SPK99."..Summary w 
							left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
							left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
							inner join 
							(
								 select x.kodepr, x.kodeprospect, $tglfollow
								  from ".$db_stars."..KPI5harian_status x
								  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status
							) b on c.KodePr = b.KodePr and c.KodeProspect = b.kodeprospect
							left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							left join ".$db_stars."..FollowTypeTrn f on c.KodePr = f.KodePr AND c.KodeProspect = f.KodeProspect and c.KodePlan = f.KodePlan
							left join ".DB_SPK00."..InKategory g on f.Type = g.KodeKategory
							
							WHERE w.tipetr = 'SPK' and w.status = '0' 
							--and MONTH(a.TglEntry) <= '".$bulan."' AND YEAR(a.TglEntry) <= '".$tahun."'
							and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							".$where_dealer."
							
							union 
							
							select distinct w.No_bukti, a.kodepr, a.salespr, 'spk_carry' tp
							FROM ".DB_SPK99."..Summary w 
							left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
							left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
							inner join 
							(
								 select x.kodepr, x.kodeprospect, $tglfollow
								  from ".$db_stars."..KPI5harian_status x
								  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status
							) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
							left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							
							left join ".$db_stars."..FollowTypeTrn f on c.KodePr = f.KodePr AND c.KodeProspect = f.KodeProspect and c.KodePlan = f.KodePlan
							left join ".DB_SPK00."..InKategory g on f.Type = g.KodeKategory				
							WHERE w.tipetr = 'SPK' and w.status = '1' 
							and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
							and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							".$where_dealer."
							
					) AA where AA.salespr is not null ".$where_salespr." ";
	}
	
	if ($status=="HOT") {
		$stm = " select count(*) jml from (
						
							select distinct '' No_bukti, a.KodePr, a.SalesPr, 'hot this month' tp
							FROM ".$db_stars."..ProspectMst a
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							inner join 
							(
								  select x.kodepr, x.kodeprospect, $tglfollow
								  from ".$db_stars."..KPI5harian_status x
								  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status
							) b on a.KodePr = b.KodePr 
							WHERE a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and b.Status = 'hot'  
							and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							".$where_dealer."
							
							union
							
							select distinct w.No_bukti, a.KodePr, a.SalesPr, 'hot carry' tp
							FROM ".DB_SPK99."..Summary w 
							left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
							left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
							inner join 
							(
								select x.kodepr, x.kodeprospect, $tglfollow
								from ".$db_stars."..KPI5harian_status x
								where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status
							) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
							left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							WHERE w.tipetr = 'SPK' and w.status = '1' 
							and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
							and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							and b.Status = 'hot'
							".$where_dealer."
							
						union
										
						select distinct w.No_bukti, a.kodepr, a.salespr, 'spk_this_month' tp
						FROM ".DB_SPK99."..Summary w 
						left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
						left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
						inner join 
						(
							  select x.kodepr, x.kodeprospect, $tglfollow
							  from ".$db_stars."..KPI5harian_status x
							  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status
						) b on c.KodePr = b.KodePr and c.KodeProspect = b.kodeprospect
						left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
						inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
						left join ".$db_stars."..FollowTypeTrn f on c.KodePr = f.KodePr AND c.KodeProspect = f.KodeProspect and c.KodePlan = f.KodePlan
						left join ".DB_SPK00."..InKategory g on f.Type = g.KodeKategory
						
						WHERE w.tipetr = 'SPK' and w.status = '0' 
						--and MONTH(a.TglEntry) <= '".$bulan."' AND YEAR(a.TglEntry) <= '".$tahun."'
						and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
						and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
						and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
						".$where_dealer."
						
						union 
						
						select distinct w.No_bukti, a.kodepr, a.salespr, 'spk_carry' tp
						FROM ".DB_SPK99."..Summary w 
						left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
						left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
						inner join 
						(
							select x.kodepr, x.kodeprospect, $tglfollow
							  from ".$db_stars."..KPI5harian_status x
							  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status
						) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
						left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
						inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
						
						left join ".$db_stars."..FollowTypeTrn f on c.KodePr = f.KodePr AND c.KodeProspect = f.KodeProspect and c.KodePlan = f.KodePlan
						left join ".DB_SPK00."..InKategory g on f.Type = g.KodeKategory				
						WHERE w.tipetr = 'SPK' and w.status = '1' 
						and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
						and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
						and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
						".$where_dealer."
						
					) AA where AA.salespr is not null ".$where_salespr."";
	}

	if ($status=="SPK") {
		$stm = " select count(*) jml from (
					
						select distinct w.No_bukti, a.kodepr, a.salespr, 'spk_this_month' tp
									FROM ".DB_SPK99."..Summary w 
									left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
									left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
									inner join 
									(
										 select x.kodepr, x.kodeprospect, $tglfollow
										  from ".$db_stars."..KPI5harian_status x
										  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status
									) b on c.KodePr = b.KodePr and c.KodeProspect = b.kodeprospect
									left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
									inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
									left join ".$db_stars."..FollowTypeTrn f on c.KodePr = f.KodePr AND c.KodeProspect = f.KodeProspect and c.KodePlan = f.KodePlan
									left join ".DB_SPK00."..InKategory g on f.Type = g.KodeKategory
									
									WHERE w.tipetr = 'SPK' and w.status = '0' 
									--and MONTH(a.TglEntry) <= '".$bulan."' AND YEAR(a.TglEntry) <= '".$tahun."'
									and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
									and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
									and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
									".$where_dealer."
												
									union 
									
									select distinct w.No_bukti, a.kodepr, a.salespr, 'spk_carry' tp
									FROM ".DB_SPK99."..Summary w 
									left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
									left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
									inner join 
									(
										select x.kodepr, x.kodeprospect, $tglfollow
										from ".$db_stars."..KPI5harian_status x
										where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status									
									) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
									left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
									inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
									
									left join ".$db_stars."..FollowTypeTrn f on c.KodePr = f.KodePr AND c.KodeProspect = f.KodeProspect and c.KodePlan = f.KodePlan
									left join ".DB_SPK00."..InKategory g on f.Type = g.KodeKategory				
									WHERE w.tipetr = 'SPK' and w.status = '1' 
									and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
									and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
									and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
									".$where_dealer."
									
					) AA where AA.salespr is not null ".$where_salespr."
					";
		
			
	}
	
	if ($status=="RS") {
		$stm = " select count(*) jml from (
						
						select distinct w.No_bukti, a.kodepr , a.salespr, 'rs_this_month' tp
									FROM ".DB_SPK99."..Summary w 
									left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
									left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
									inner join 
									(
										 select x.kodepr, x.kodeprospect, $tglfollow
										  from ".$db_stars."..KPI5harian_status x
										  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status
									) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
									left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
									inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
									left join ".$db_stars."..FollowTypeTrn f on c.KodePr = f.KodePr AND c.KodeProspect = f.KodeProspect and c.KodePlan = f.KodePlan
									left join ".DB_SPK00."..InKategory g on f.Type = g.KodeKategory
									
									WHERE w.tipetr = 'SPK' and w.status = '1' 
									--and MONTH(a.TglEntry) <= '".$bulan."' AND YEAR(a.TglEntry) <= '".$tahun."'
									and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
									and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
									and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
									".$where_dealer."
									
									union 
																	
									select distinct w.No_bukti, a.kodepr ,  a.salespr, 'rs_carry' tp
									FROM ".DB_SPK99."..Summary w 
									left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
									left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
									inner join 
									(
										  select x.kodepr, x.kodeprospect, $tglfollow
										  from ".$db_stars."..KPI5harian_status x
										  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status
									) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
									left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
									inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
									
									left join ".$db_stars."..FollowTypeTrn f on c.KodePr = f.KodePr AND c.KodeProspect = f.KodeProspect and c.KodePlan = f.KodePlan
									left join ".DB_SPK00."..InKategory g on f.Type = g.KodeKategory				
									WHERE w.tipetr = 'SPK' and w.status = '1' 
									and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
									and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
									and w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
									".$where_dealer."
									
					) AA where AA.salespr is not null ".$where_salespr."
					";	
	}	

	if ($h==3 and $kodesales=='1116-3981') {
		//	echo "<pre>$stm</pre>";	
		}
	//echo "<pre>$stm</pre>";		
		
	$qry = mssql_query($stm,$conn);
	$dt = mssql_fetch_array($qry);
	$jml = $dt['jml'];
	return $jml;
}

function sales_act_detail($status,$kodesales,$tgl1,$tgl2,$tgl3,$tgl4,$tgl5,$tgl6, $bulan, $tahun, $h, $dealer){
	global $conn;
	
	//$kd_db = getKodeDBDealer($_SESSION['stars_username']);
	//$db_stars = DB_STARS.$kd_db;
	
	//$kodedealer = BridgingDealer($kd_db);
	if ($_SESSION['stars_profil']==4) {	
		if (!empty($_SESSION['SET_DEALER'])) {
			$kd_db = getKodeDBDealer(dekripsi($_SESSION['SET_DEALER']));	
			$judul_dealer = dekripsi($_SESSION['SET_NMDEALER']);
			$db_stars = DB_STARS.$kd_db;	
			$kodedealer = BridgingDealer($kd_db);		
			$where_dealer = " and s.kodecabang = '".$kodedealer."' ";
			$where_salespr = " and a.salespr = '".$kodesales."' ";
			
		} else {
			$db_stars = DB_STARS."99";
			$where_dealer = " and s.kodecabang in (".$dealer.") ";
			$where_salespr = " and a.salespr in (select kodesales from ".DB_STARS99."..ArSales where kodecabang in (".$dealer.") ) ";
		}
	} else {
		$kd_db = getKodeDBDealer($_SESSION['stars_username']);
		$db_stars = DB_STARS.$kd_db;
		$kodedealer = BridgingDealer($kd_db);		
		$where_dealer = " and s.kodecabang = '".$kodedealer."' ";
		$where_salespr = " and a.salespr = '".$kodesales."' ";
	} 
	
	
	if ($h==1) {
		$tglfollow = " x.h1 'Status', x.tglh1 'TglFollow' ";
		$where_status = " and x.h1 = '".$status."' and x.tglh1 between '".$tgl1." 00:00:00' and '".$tgl5." 23:59:59' ";
	} else if ($h==2) {
		$tglfollow = " x.h2 'Status', x.tglh2 'TglFollow' ";
		$where_status = " and x.h2 = '".$status."' and x.tglh2 between '".$tgl1." 00:00:00' and '".$tgl5." 23:59:59' ";
	} else if ($h==3) {
		$tglfollow = " x.h3 'Status', x.tglh3 'TglFollow' ";
		$where_status = " and x.h3 = '".$status."' and x.tglh3 between '".$tgl1." 00:00:00' and '".$tgl5." 23:59:59' ";
	} else if ($h==4) {
		$tglfollow = " x.h4 'Status', x.tglh4 'TglFollow' ";
		$where_status = " and x.h4 = '".$status."' and x.tglh4 between '".$tgl1." 00:00:00' and '".$tgl5." 23:59:59' ";
	} else if ($h==5) {
		$tglfollow = " x.h5 'Status', x.tglh5 'TglFollow' ";
		$where_status = " and x.h5 = '".$status."' and x.tglh5 between '".$tgl1." 00:00:00' and '".$tgl5." 23:59:59' ";
	} else if ($h==6) {
		if (empty($tgl6)) {
			$tgl5 = $tgl6;
		}
		$tglfollow = " x.h6 'Status', x.tglh6 'TglFollow' ";
		$where_status = " and x.h6 = '".$status."' and x.tglh6 between '".$tgl1." 00:00:00' and '".$tgl5." 23:59:59' ";
	}
	
	if (empty($tgl6)) {
		$tgl6 = $tgl5;
		$pvt = "[$tgl1], [$tgl2], [$tgl3], [$tgl4], [$tgl5]";
	} else {
		$pvt = "[$tgl1], [$tgl2], [$tgl3], [$tgl4], [$tgl5], [$tgl6]";
	}
		
	
	if ($status=="SUSPECT") {
		$stm = "  select * from (
						select xx.kodepr, xx.salespr, xx.tglfollow
						from (
							select distinct convert(varchar(10),b.tglfollow,101) tglfollow, '' No_bukti, a.KodePr, a.SalesPr, 'Suspect this month' tp
							FROM ".$db_stars."..ProspectMst a
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							left join 
							(
								 select x.kodepr, x.kodeprospect, $tglfollow
								  from ".$db_stars."..KPI5harian_status x
								  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status
							) b on a.KodePr = b.KodePr 
							WHERE a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							".$where_salespr." ".$where_dealer."
									
							union 
															
							select distinct convert(varchar(10),b.tglfollow,101) tglfollow,  w.No_bukti, a.KodePr, a.SalesPr, 'Suspect carry' tp
							FROM ".DB_SPK99."..Summary w 
							left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
							left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
							left join 
							(
								  select x.kodepr, x.kodeprospect, $tglfollow
								  from ".$db_stars."..KPI5harian_status x
								  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status
							) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
							left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							WHERE w.tipetr = 'SPK' and w.status = '1' 
							and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
							and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							".$where_salespr." ".$where_dealer."
								
					) xx			 
				) AA pivot(count(kodepr) for tglfollow in ($pvt)) pvt";
	}
	
	if ($status=="PROSPECT") {
		$stm = "  select * from (
						select xx.kodepr, xx.salespr, xx.tglfollow
						from (
							select distinct convert(varchar(10),b.tglfollow,101) tglfollow, '' No_bukti, a.KodePr, a.SalesPr, 'Prospect this month' tp
							FROM ".$db_stars."..ProspectMst a
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							left join 
							(
								 select x.kodepr, x.kodeprospect, $tglfollow
								  from ".$db_stars."..KPI5harian_status x
								  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status
							) b on a.KodePr = b.KodePr 
							WHERE a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and b.Status = 'Prospect'
							and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							".$where_salespr." ".$where_dealer."
									
							union 
															
							select distinct convert(varchar(10),b.tglfollow,101) tglfollow,  w.No_bukti, a.KodePr, a.SalesPr, 'Prospect carry' tp
							FROM ".DB_SPK99."..Summary w 
							left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
							left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
							left join 
							(
								 select x.kodepr, x.kodeprospect, $tglfollow
								  from ".$db_stars."..KPI5harian_status x
								  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status								  
							) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
							left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							WHERE w.tipetr = 'SPK' and w.status = '1' 
							and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
							and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							and b.Status = 'Prospect'
							".$where_salespr." ".$where_dealer."
									
							union 
															
							select distinct convert(varchar(10),b.tglfollow,101) tglfollow,  '' No_bukti, a.KodePr, a.SalesPr, 'hot this month' tp
							FROM ".$db_stars."..ProspectMst a
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							left join 
							(
								 select x.kodepr, x.kodeprospect, $tglfollow
								  from ".$db_stars."..KPI5harian_status x
								  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status
							) b on a.KodePr = b.KodePr 
							WHERE a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and b.Status = 'hot'  
							and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							".$where_salespr." ".$where_dealer."
									
							union 
															
							select distinct convert(varchar(10),b.tglfollow,101) tglfollow,  w.No_bukti, a.KodePr, a.SalesPr, 'hot carry' tp
							FROM ".DB_SPK99."..Summary w 
							left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
							left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
							left join 
							(
								 select x.kodepr, x.kodeprospect, $tglfollow
								  from ".$db_stars."..KPI5harian_status x
								  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status 
							) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
							left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							WHERE w.tipetr = 'SPK' and w.status = '1' 
							and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
							and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							and b.Status = 'hot'
							".$where_salespr." ".$where_dealer."
									
							union 
															
							select distinct convert(varchar(10),b.tglfollow,101) tglfollow,  w.No_bukti, a.kodepr, a.salespr, 'spk_this_month' tp
							FROM ".DB_SPK99."..Summary w 
							left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
							left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
							left join 
							(
								 select x.kodepr, x.kodeprospect, $tglfollow
								  from ".$db_stars."..KPI5harian_status x
								  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status
							) b on c.KodePr = b.KodePr and c.KodeProspect = b.kodeprospect
							left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							left join ".$db_stars."..FollowTypeTrn f on c.KodePr = f.KodePr AND c.KodeProspect = f.KodeProspect and c.KodePlan = f.KodePlan
							left join ".DB_SPK00."..InKategory g on f.Type = g.KodeKategory
							
							WHERE w.tipetr = 'SPK' and w.status = '0' 
							--and MONTH(a.TglEntry) <= '".$bulan."' AND YEAR(a.TglEntry) <= '".$tahun."'
							and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							".$where_salespr." ".$where_dealer."
									
							union 
															
							select distinct convert(varchar(10),b.tglfollow,101) tglfollow,  w.No_bukti, a.kodepr, a.salespr, 'spk_carry' tp
							FROM ".DB_SPK99."..Summary w 
							left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
							left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
							left join 
							(
								 select x.kodepr, x.kodeprospect, $tglfollow
								  from ".$db_stars."..KPI5harian_status x
								  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status
							) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
							left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							
							left join ".$db_stars."..FollowTypeTrn f on c.KodePr = f.KodePr AND c.KodeProspect = f.KodeProspect and c.KodePlan = f.KodePlan
							left join ".DB_SPK00."..InKategory g on f.Type = g.KodeKategory				
							WHERE w.tipetr = 'SPK' and w.status = '1' 
							and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
							and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							".$where_salespr." ".$where_dealer."
									
					) xx			 
				) AA pivot(count(kodepr) for tglfollow in ($pvt)) pvt";
	}
	
	if ($status=="HOT") {
		$stm = " select * from (
						select xx.kodepr, xx.salespr, xx.tglfollow
						from (
							select distinct convert(varchar(10),b.tglfollow,101) tglfollow, '' No_bukti, a.KodePr, a.SalesPr, 'hot this month' tp
							FROM ".$db_stars."..ProspectMst a
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							left join 
							(
								  select x.kodepr, x.kodeprospect, $tglfollow
								  from ".$db_stars."..KPI5harian_status x
								  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status
							) b on a.KodePr = b.KodePr 
							WHERE a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and b.Status = 'hot'  
							and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							".$where_salespr." ".$where_dealer."
							
							union 
									
							select distinct convert(varchar(10),b.tglfollow,101) tglfollow,  w.No_bukti, a.KodePr, a.SalesPr, 'hot carry' tp
							FROM ".DB_SPK99."..Summary w 
							left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
							left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
							left join 
							(
								select x.kodepr, x.kodeprospect, $tglfollow
								from ".$db_stars."..KPI5harian_status x
								where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status
							) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
							left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
							inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
							WHERE w.tipetr = 'SPK' and w.status = '1' 
							and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
							and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
							and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
							and b.Status = 'hot'
							".$where_salespr." ".$where_dealer."
									
						union 
														
						select distinct convert(varchar(10),b.tglfollow,101) tglfollow,  w.No_bukti, a.kodepr, a.salespr, 'spk_this_month' tp
						FROM ".DB_SPK99."..Summary w 
						left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
						left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
						left join 
						(
							  select x.kodepr, x.kodeprospect, $tglfollow
							  from ".$db_stars."..KPI5harian_status x
							  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status
						) b on c.KodePr = b.KodePr and c.KodeProspect = b.kodeprospect
						left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
						inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
						left join ".$db_stars."..FollowTypeTrn f on c.KodePr = f.KodePr AND c.KodeProspect = f.KodeProspect and c.KodePlan = f.KodePlan
						left join ".DB_SPK00."..InKategory g on f.Type = g.KodeKategory
						
						WHERE w.tipetr = 'SPK' and w.status = '0' 
						--and MONTH(a.TglEntry) <= '".$bulan."' AND YEAR(a.TglEntry) <= '".$tahun."'
						and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
						and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
						and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
						".$where_salespr." ".$where_dealer."
									
						union 
														
						select distinct convert(varchar(10),b.tglfollow,101) tglfollow,  w.No_bukti, a.kodepr, a.salespr, 'spk_carry' tp
						FROM ".DB_SPK99."..Summary w 
						left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
						left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
						left join 
						(
							select x.kodepr, x.kodeprospect, $tglfollow
							  from ".$db_stars."..KPI5harian_status x
							  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status
						) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
						left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
						inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
						
						left join ".$db_stars."..FollowTypeTrn f on c.KodePr = f.KodePr AND c.KodeProspect = f.KodeProspect and c.KodePlan = f.KodePlan
						left join ".DB_SPK00."..InKategory g on f.Type = g.KodeKategory				
						WHERE w.tipetr = 'SPK' and w.status = '1' 
						and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
						and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
						and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
						".$where_salespr." ".$where_dealer."
									
					) xx			 
				) AA pivot(count(kodepr) for tglfollow in ($pvt)) pvt";
	}

	if ($status=="SPK") {
		$stm = " select * from (
						select xx.kodepr, xx.salespr, xx.tglfollow
						from (
							select distinct convert(varchar(10),b.tglfollow,101) tglfollow, w.No_bukti, a.kodepr, a.salespr, 'spk_this_month' tp
									FROM ".DB_SPK99."..Summary w 
									left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
									left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
									inner join 
									(
										 select x.kodepr, x.kodeprospect, $tglfollow
										  from ".$db_stars."..KPI5harian_status x
										  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status
									) b on c.KodePr = b.KodePr and c.KodeProspect = b.kodeprospect
									left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
									inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
									left join ".$db_stars."..FollowTypeTrn f on c.KodePr = f.KodePr AND c.KodeProspect = f.KodeProspect and c.KodePlan = f.KodePlan
									left join ".DB_SPK00."..InKategory g on f.Type = g.KodeKategory
									
									WHERE w.tipetr = 'SPK' and w.status = '0' 
									--and MONTH(a.TglEntry) <= '".$bulan."' AND YEAR(a.TglEntry) <= '".$tahun."'
									and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
									and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl5." 23:59:59'
									and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl5." 23:59:59'
									".$where_salespr." ".$where_dealer."
									
									union 
									
									select distinct convert(varchar(10),b.tglfollow,101) tglfollow, w.No_bukti, a.kodepr, a.salespr, 'spk_carry' tp
									FROM ".DB_SPK99."..Summary w 
									left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
									left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
									inner join 
									(
										select x.kodepr, x.kodeprospect, $tglfollow
										from ".$db_stars."..KPI5harian_status x
										where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status									
									) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
									left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
									inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
									
									left join ".$db_stars."..FollowTypeTrn f on c.KodePr = f.KodePr AND c.KodeProspect = f.KodeProspect and c.KodePlan = f.KodePlan
									left join ".DB_SPK00."..InKategory g on f.Type = g.KodeKategory				
									WHERE w.tipetr = 'SPK' and w.status = '1' 
									and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
									and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
									and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl5." 23:59:59'
									".$where_salespr." ".$where_dealer."
						) xx			 
					) AA pivot(count(kodepr) for tglfollow in ($pvt)) pvt";
		
	
			
	}
	
	if ($status=="RS") {
		$stm = "  select * from (
						select xx.kodepr, xx.salespr, xx.tglfollow
						from (
							select distinct convert(varchar(10),b.tglfollow,101) tglfollow, w.No_bukti, a.kodepr , a.salespr, 'rs_this_month' tp
									FROM ".DB_SPK99."..Summary w 
									left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
									left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
									inner join 
									(
										 select x.kodepr, x.kodeprospect, $tglfollow
										  from ".$db_stars."..KPI5harian_status x
										  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status
									) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
									left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
									inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
									left join ".$db_stars."..FollowTypeTrn f on c.KodePr = f.KodePr AND c.KodeProspect = f.KodeProspect and c.KodePlan = f.KodePlan
									left join ".DB_SPK00."..InKategory g on f.Type = g.KodeKategory
									
									WHERE w.tipetr = 'SPK' and w.status = '1' 
									--and MONTH(a.TglEntry) <= '".$bulan."' AND YEAR(a.TglEntry) <= '".$tahun."'
									and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
									and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
									and  w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
									".$where_salespr." ".$where_dealer."
									
									union 
																	
									select distinct convert(varchar(10),b.tglfollow,101) tglfollow, w.No_bukti, a.kodepr ,  a.salespr, 'rs_carry' tp
									FROM ".DB_SPK99."..Summary w 
									left join ".$db_stars."..FollowSPK e on e.NoSpk = w.No_bukti  
									left join ".$db_stars."..PlanTrn c on e.KodePr = c.KodePr AND e.KodeProspect = c.KodeProspect and e.KodePlan = c.KodePlan
									inner join 
									(
										  select x.kodepr, x.kodeprospect, $tglfollow
										  from ".$db_stars."..KPI5harian_status x
										  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status
									) b on c.KodePr = b.KodePr and c.KodeProspect = b.KodeProspect 
									left join ".$db_stars."..ProspectMst a on e.KodePr = a.KodePr
									inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
									
									left join ".$db_stars."..FollowTypeTrn f on c.KodePr = f.KodePr AND c.KodeProspect = f.KodeProspect and c.KodePlan = f.KodePlan
									left join ".DB_SPK00."..InKategory g on f.Type = g.KodeKategory				
									WHERE w.tipetr = 'SPK' and w.status = '1' 
									and a.TglEntry <= dateadd(MONTH, 1, '".$tgl1."')
									and isnull(b.TglFollow,'01/01/1900') < '".$tgl1."'
									and w.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'
									".$where_salespr." ".$where_dealer."
									
						) xx			 
					) AA pivot(count(kodepr) for tglfollow in ($pvt)) pvt";
	}	

	if ($h==3 and $kodesales=='1116-3981') {
		//echo "<pre>$stm</pre>";	
	}
	
	
	/*
	$stm = "select * from (SELECT distinct a.KodePr, a.SalesPr, convert(varchar(10),b.tglfollow,101) tglfollow
				FROM ".$db_stars."..ProspectMst a
				inner join ".DB_STARS99."..ArSales s on a.salespr = s.kodesales
				left join 
				(
					 select x.kodepr, x.kodeprospect, $tglfollow
					  from ".$db_stars."..KPI5harian_status x
					  where bulan = '".$bulan."' and tahun = '".$tahun."' $where_status
				) b on a.KodePr = b.KodePr 
				WHERE
				a.TglEntry <= dateadd(MONTH, 1, '".$bulan."/01/".$tahun."')
				and b.TglFollow between '".$tgl1." 00:00:00' and '".$tgl6." 23:59:59' 
				and a.SalesPr = '".$kodesales."' and s.kodecabang = '".$kodedealer."'
			) AA pivot(count(kodepr) for tglfollow in ($pvt)) pvt";
							*/
							
	// echo "<pre>$stm</pre>";
	//$qry = mssql_query($stm,$conn);
	//$dt = mssql_fetch_array($qry);
	//$jml = $dt['jml'];
	return $stm;
	
}
	
function rumusdetail($lastday, $bulan, $tahun, $status, $dealer) {
	global $conn;
	
		
	//$kd_db = getKodeDBDealer($_SESSION['stars_username']);
	//$db_stars = DB_STARS.$kd_db;
	
	//$kodedealer = BridgingDealer($kd_db);
	
	if ($_SESSION['stars_profil']==4) {	
		if (!empty($_SESSION['SET_DEALER'])) {
			$kd_db = getKodeDBDealer(dekripsi($_SESSION['SET_DEALER']));	
			$judul_dealer = dekripsi($_SESSION['SET_NMDEALER']);
			$db_stars = DB_STARS.$kd_db;			
			$where_dealer = " and s.kodecabang = '".$kodedealer."' ";
			$where_salespr = " and a.salespr = '".$kodesales."' ";
			
		} else {
			if ($dealer=="ALL DEALER") {
				$db_stars = DB_STARS."99";
				$where_dealer = " and s.kodecabang in (".$dealer.") ";
				$where_salespr = " and a.salespr in (select kodesales from ".DB_STARS99."..ArSales where kodecabang in (".$dealer.") ) ";	
				
			} else {
				$kd_db = getKodeDBDealer($dealer);
				$db_stars = DB_STARS.$kd_db;
				
				$kodedealer = BridgingDealer($kd_db);
				$where_dealer = " and s.kodecabang = '".$kodedealer."' ";
				$where_salespr = " and a.salespr = '".$kodesales."' ";	
			}
		}
	} else {
		$kd_db = getKodeDBDealer($_SESSION['stars_username']);
		$db_stars = DB_STARS.$kd_db;		
		$where_dealer = " and s.kodecabang = '".$kodedealer."' ";
		$where_salespr = " and a.salespr = '".$kodesales."' ";
	} 
	
	
	
	#--------------------------- rumus detail harian
	if ($lastday==31) {
		$ha6 = $ha5 + 1;
	} else if ($lastday==30 or $lastday==29 or $lastday==28) {
		$ha5j = $lastday;
	} else {
		$ha5j = $ha5;
	}
	
	$ha5 = 0;
	for ($i=1;$i<=6;$i++) { 						
		$hr = $i;
		$akhir = $hr * 5;
		$awal = $akhir - 4;
		$ha5 = $awal;
		
		$ha6 = "";
		$ha1 = $ha5;
		$ha2 = $ha1 + 1;
		$ha3 = $ha2 + 1;
		$ha4 = $ha3 + 1;
		$ha5 = $ha4 + 1;
		
		if ($i==6) {
			if ($lastday==31) {
				$ha6 = $ha5 + 1;								
				$where_hari = $ha1.",".$ha2.",".$ha3.",".$ha4.",".$ha5.",".$ha6;
			} else if ($lastday==30) {
				$ha5j = $lastday;
				$where_hari = $ha1.",".$ha2.",".$ha3.",".$ha4.",".$ha5;
			} else if ($lastday==29) {
				$ha5j = $lastday;
				$where_hari = $ha1.",".$ha2.",".$ha3.",".$ha4;		
			} else if ($lastday==28) {
				$ha5j = $lastday;
				$where_hari = $ha1.",".$ha2.",".$ha3;
			} else {
				$ha5j = $ha5;
				$where_hari = $ha1.",".$ha2.",".$ha3.",".$ha4.",".$ha5;
			}
		} else {
			$ha5j = $ha5;
			$where_hari = $ha1.",".$ha2.",".$ha3.",".$ha4.",".$ha5;
		}
		
									
		$qry_setting_det = mssql_query("select target, status, hari
									from ".$db_stars."..KPI5harian_setting
									where bulan = '".$bulan."' and tahun = '".$tahun."'
									and hari in ($where_hari) order by hari ",$conn);
		$jml_setting_det = mssql_num_rows($qry_setting_det);
		if ($jml_setting_det==0) {
			$qry_set_det = mssql_query("select top 1 bulan, tahun from ".$db_stars."..KPI5harian_setting
									order by tglentry desc",$conn);
			$dt_set_det = mssql_fetch_array($qry_set_det);
			$bln_set_det = $dt_set_det['bulan'];
			$thn_set_det = $dt_set_det['tahun'];
		
			$qry_setting_det = mssql_query("select target, status, hari
									from ".$db_stars."..KPI5harian_setting
									where bulan = '".$bln_set_det."' and tahun = '".$thn_set_det."' 
									and hari in ($where_hari) order by hari ",$conn);
		} 
		
		while ($dt_setting_det = mssql_fetch_array($qry_setting_det)) {
			$status = $dt_setting_det['status'];
			$hari = $dt_setting_det['hari'];
			
			if ($status=="SUSPECT") {
				if ($ha1==$hari) {
					$suspectd_t1 = $dt_setting_det['target'];
				}
				if ($ha2==$hari) {
					$suspectd_t2 = $dt_setting_det['target'];
				}
				if ($ha3==$hari) {
					$suspectd_t3 = $dt_setting_det['target'];
				}
				if ($ha4==$hari) {
					$suspectd_t4 = $dt_setting_det['target'];
				}
				if ($ha5==$hari) {
					$suspectd_t5 = $dt_setting_det['target'];
				}
				if ($ha6==$hari) {
					$suspectd_t6 = $dt_setting_det['target'];
				}
			}										
			if ($status=="PROSPECT") {
				if ($ha1==$hari) {
					$prospectd_t1 = $dt_setting_det['target'];
				}
				if ($ha2==$hari) {
					$prospectd_t2 = $dt_setting_det['target'];
				}
				if ($ha3==$hari) {
					$prospectd_t3 = $dt_setting_det['target'];
				}
				if ($ha4==$hari) {
					$prospectd_t4 = $dt_setting_det['target'];
				}
				if ($ha5==$hari) {
					$prospectd_t5 = $dt_setting_det['target'];
				}
				if ($ha6==$hari) {
					$prospectd_t6 = $dt_setting_det['target'];
				}
			}																				
			if ($status=="HOT") {
				if ($ha1==$hari) {
					$hotd_t1 = $dt_setting_det['target'];
				}
				if ($ha2==$hari) {
					$hotd_t2 = $dt_setting_det['target'];
				}
				if ($ha3==$hari) {
					$hotd_t3 = $dt_setting_det['target'];
				}
				if ($ha4==$hari) {
					$hotd_t4 = $dt_setting_det['target'];
				}
				if ($ha5==$hari) {	
					$hotd_t5 = $dt_setting_det['target'];
				}
				if ($ha6==$hari) {
					$hotd_t6 = $dt_setting_det['target'];
				}
			}										
			if ($status=="SPK") {
				if ($ha1==$hari) {
					$spkd_t1 = $dt_setting_det['target'];
				}
				if ($ha2==$hari) {
					$spkd_t2 = $dt_setting_det['target'];
				}
				if ($ha3==$hari) {
					$spkd_t3 = $dt_setting_det['target'];
				}
				if ($ha4==$hari) {
					$spkd_t4 = $dt_setting_det['target'];
				}
				if ($ha5==$hari) {
					$spkd_t5 = $dt_setting_det['target'];
				}
				if ($ha6==$hari) {
					$spkd_t6 = $dt_setting_det['target'];
				}
			}										
			if ($status=="RS") {
				if ($ha1==$hari) {
					$rsd_t1 = $dt_setting_det['target'];
				}
				if ($ha2==$hari) {
					$rsd_t2 = $dt_setting_det['target'];
				}
				if ($ha3==$hari) {
					$rsd_t3 = $dt_setting_det['target'];
				}
				if ($ha4==$hari) {
					$rsd_t4 = $dt_setting_det['target'];
				}
				if ($ha5==$hari) {
					$rsd_t5 = $dt_setting_det['target'];
				}
				if ($ha6==$hari) {
					$rsd_t6 = $dt_setting_det['target'];
				}
			}
		}	
	
		if ($i==6) {
			if ($lastday==31) {
			
			} else if ($lastday==30 or $lastday==29 or $lastday==28) {
				$suspectd_t6 = 0;
				$prospectd_t6 = 0;
				$hotd_t6 = 0;
				$spkd_t6 = 0;
				$rsd_t6 = 0;
			} else {														
				$suspectd_t6 = 0;
				$prospectd_t6 = 0;
				$hotd_t6 = 0;
				$spkd_t6 = 0;
				$rsd_t6 = 0;
			}
		} else {
			$suspectd_t6 = 0;
			$prospectd_t6 = 0;
			$hotd_t6 = 0;
			$spkd_t6 = 0;
			$rsd_t6 = 0;
		}
		
		?>
		
		detail(<?=$i?>,<?=$lastday;?>,<?=$suspectd_t1;?>,<?=$suspectd_t2;?>,<?=$suspectd_t3;?>,<?=$suspectd_t4;?>,<?=$suspectd_t5;?>,<?=$suspectd_t6;?>,<?=$prospectd_t1;?>,<?=$prospectd_t2;?>,<?=$prospectd_t3;?>,<?=$prospectd_t4;?>,<?=$prospectd_t5;?>,<?=$prospectd_t6;?>,<?=$hotd_t1;?>,<?=$hotd_t2;?>,<?=$hotd_t3;?>,<?=$hotd_t4;?>,<?=$hotd_t5;?>,<?=$hotd_t6;?>,<?=$spkd_t1;?>,<?=$spkd_t2;?>,<?=$spkd_t3;?>,<?=$spkd_t4;?>,<?=$spkd_t5;?>,<?=$spkd_t6;?>,<?=$rsd_t1;?>,<?=$rsd_t2;?>,<?=$rsd_t3;?>,<?=$rsd_t4;?>,<?=$rsd_t5;?>,<?=$rsd_t6;?>);

<?		
	}		
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




function enkripsi($data)
{
  $enkrip1=base64_encode($data);
  $enkrip2=$enkrip1."_sT4Rs";
  $enkrip3=base64_encode($enkrip2);
	
  return $enkrip3;
}

function dekripsi($data)
{
  $dekrip1=base64_decode($data);
  $dekrip2=explode("_sT4Rs",$dekrip1);
  $dekrip3=base64_decode($dekrip2[0]);
  
  return $dekrip3;
}


//20230309 - ARI - SPM Stage 2 
if(!function_exists('pros')){
	function pros($a = 0, $b = 0)
	{
		$hasil = 0;
		$hasil = ($b == 0 ? 0 : round($a/$b, 2));
		$hasilOK = ($hasil * 100);
		$result = number_format($hasilOK,2, ',', '.');
		return $result;
	}
}

?>