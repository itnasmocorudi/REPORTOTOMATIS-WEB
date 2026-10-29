<?
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
					
					
					#--------------- BRDIGING NASA SALES
					$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
											left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
											where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
											order by a.tgl_update desc",$conn_ho);
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
					$cekPlan1 = "SELECT KodePlan from ".DB_STARS."..PlanTrn 
								where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					$reCekPlan1 = mssql_query($cekPlan1,$conn);
					$jmlreCekPlan1 = mssql_num_rows($reCekPlan1);
					
					if($jmlreCekPlan1==0){
					
						$cekPlan1 = "SELECT KodePlan from ".DB_STARS."..PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3";
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
							
							$qryKode = mssql_query("select kodepr from ".DB_STARS."..plantrn 
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
							
						$inst = mssql_query("insert into ".DB_STARS."..plantrn (SbrPlan,KodePr,KodeProspect,TglPlan,
							KodePlan,TypePlan,idKetPlan,KetPlan, 
							[Tanggal Jatuh Tempo], NoRangka, Type,
							KodeSales,Kodeuser,TglEntry)
							values ('1', '".$kodepr."', '".$kodeprospect."', '".$TglPlan."',			
							'".$kodeplan."', 3, '".$idKetPlan."', '".$KetPlan."', 
							'".$tglJthTempo."', '".$norangka."', '".$type."',
							'".$_SESSION['stars_username']."','AutoPlanSystem',getdate())",$conn);
							
						if ($inst and HANSA==1) {
							$kodepesan = "REMINDER";
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
					
					
					#--------------- BRDIGING NASA SALES
					$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
											left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
											where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
											order by a.tgl_update desc",$conn_ho);
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
					$cekPlan1 = "SELECT KodePlan from ".DB_STARS."..PlanTrn 
								where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					$reCekPlan1 = mssql_query($cekPlan1,$conn);
					$jmlreCekPlan1 = mssql_num_rows($reCekPlan1);
					
					if($jmlreCekPlan1==0){
						$cekPlan1 = "SELECT KodePlan from ".DB_STARS."..PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3";
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
							
							$qryKode = mssql_query("select kodepr from ".DB_STARS."..plantrn 
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
						
						$inst = mssql_query("insert into ".DB_STARS."..plantrn (SbrPlan,KodePr,KodeProspect,TglPlan,
							KodePlan,TypePlan,idKetPlan,KetPlan, 
							[Tanggal Jatuh Tempo], NoRangka, Type,
							KodeSales,Kodeuser,TglEntry)
							values ('1', '".$kodepr."', '".$kodeprospect."', '".$TglPlan."',			
							'".$kodeplan."', 3, '".$idKetPlan."', '".$KetPlan."', 
							'".$tglJthTempo."', '".$norangka."', '".$type."',
							'".$_SESSION['stars_username']."','AutoPlanSystem',getdate())",$conn);
					
					
						if ($inst and HANSA==1) {
							$kodepesan = "REMINDER";
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
					
					
					#--------------- BRDIGING NASA SALES
					$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
											left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
											where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
											order by a.tgl_update desc",$conn_ho);
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
					$cekPlan1 = "SELECT KodePlan from ".DB_STARS."..PlanTrn 
								where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					$reCekPlan1 = mssql_query($cekPlan1,$conn);
					$jmlreCekPlan1 = mssql_num_rows($reCekPlan1);
					
					if($jmlreCekPlan1==0){
						$cekPlan1 = "SELECT KodePlan from ".DB_STARS."..PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3";
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
							
							$qryKode = mssql_query("select kodepr from ".DB_STARS."..plantrn 
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
						
						$inst = mssql_query("insert into ".DB_STARS."..plantrn (SbrPlan,KodePr,KodeProspect,TglPlan,
							KodePlan,TypePlan,idKetPlan,KetPlan, 
							[Tanggal Jatuh Tempo], NoRangka, Type,
							KodeSales,Kodeuser,TglEntry)
							values ('1', '".$kodepr."', '".$kodeprospect."', '".$TglPlan."',			
							'".$kodeplan."', 3, '".$idKetPlan."', '".$KetPlan."', 
							'".$tglJthTempo."', '".$norangka."', '".$type."',
							'".$_SESSION['stars_username']."','AutoPlanSystem',getdate())",$conn);
							
						if ($inst and HANSA==1) {
							$kodepesan = "REMINDER";
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
					
					
					#--------------- BRDIGING NASA SALES
					$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
											left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
											where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
											order by a.tgl_update desc",$conn_ho);
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
					$cekPlan1 = "SELECT KodePlan from ".DB_STARS."..PlanTrn 
								where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					$reCekPlan1 = mssql_query($cekPlan1,$conn);
					$jmlreCekPlan1 = mssql_num_rows($reCekPlan1);
					
					if($jmlreCekPlan1==0){
						$cekPlan1 = "SELECT KodePlan from ".DB_STARS."..PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3";
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
							
							$qryKode = mssql_query("select kodepr from ".DB_STARS."..plantrn 
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
						
						$inst = mssql_query("insert into ".DB_STARS."..plantrn (SbrPlan,KodePr,KodeProspect,TglPlan,
							KodePlan,TypePlan,idKetPlan,KetPlan, 
							[Tanggal Jatuh Tempo], NoRangka, Type,
							KodeSales,Kodeuser,TglEntry)
							values ('1', '".$kodepr."', '".$kodeprospect."', '".$TglPlan."',			
							'".$kodeplan."', 3, '".$idKetPlan."', '".$KetPlan."', 
							'".$tglJthTempo."', '".$norangka."', '".$type."',
							'".$_SESSION['stars_username']."','AutoPlanSystem',getdate())",$conn);
							
						if ($inst and HANSA==1) {
							$kodepesan = "REMINDER";
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
					
					
					#--------------- BRDIGING NASA SALES
					$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
											left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
											where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
											order by a.tgl_update desc",$conn_ho);
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
					$cekPlan1 = "SELECT KodePlan from ".DB_STARS."..PlanTrn 
								where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					$reCekPlan1 = mssql_query($cekPlan1,$conn);
					$jmlreCekPlan1 = mssql_num_rows($reCekPlan1);
					
					if($jmlreCekPlan1==0){
						$cekPlan1 = "SELECT KodePlan from ".DB_STARS."..PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3";
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
							
							$qryKode = mssql_query("select kodepr from ".DB_STARS."..plantrn 
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
						
						$inst = mssql_query("insert into ".DB_STARS."..plantrn (SbrPlan,KodePr,KodeProspect,TglPlan,
							KodePlan,TypePlan,idKetPlan,KetPlan, 
							[Tanggal Jatuh Tempo], NoRangka, Type,
							KodeSales,Kodeuser,TglEntry)
							values ('1', '".$kodepr."', '".$kodeprospect."', '".$TglPlan."',			
							'".$kodeplan."', 3, '".$idKetPlan."', '".$KetPlan."', 
							'".$tglJthTempo."', '".$norangka."', '".$type."',
							'".$_SESSION['stars_username']."','AutoPlanSystem',getdate())",$conn);
							
						if ($inst and HANSA==1) {
							$kodepesan = "REMINDER";
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
					
					
					#--------------- BRDIGING NASA SALES
					$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
											left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
											where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
											order by a.tgl_update desc",$conn_ho);
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
					$cekPlan1 = "SELECT KodePlan from ".DB_STARS."..PlanTrn 
								where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					$reCekPlan1 = mssql_query($cekPlan1,$conn);
					$jmlreCekPlan1 = mssql_num_rows($reCekPlan1);
					
					if($jmlreCekPlan1==0){
						$cekPlan1 = "SELECT KodePlan from ".DB_STARS."..PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3";
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
							
							$qryKode = mssql_query("select kodepr from ".DB_STARS."..plantrn 
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
						
						$inst = mssql_query("insert into ".DB_STARS."..plantrn (SbrPlan,KodePr,KodeProspect,TglPlan,
							KodePlan,TypePlan,idKetPlan,KetPlan, 
							[Tanggal Jatuh Tempo], NoRangka, Type,
							KodeSales,Kodeuser,TglEntry)
							values ('1', '".$kodepr."', '".$kodeprospect."', '".$TglPlan."',			
							'".$kodeplan."', 3, '".$idKetPlan."', '".$KetPlan."', 
							'".$tglJthTempo."', '".$norangka."', '".$type."',
							'".$_SESSION['stars_username']."','AutoPlanSystem',getdate())",$conn);
							
						if ($inst and HANSA==1) {
							$kodepesan = "REMINDER";
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
					
					
					#--------------- BRDIGING NASA SALES
					$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
											left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
											where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
											order by a.tgl_update desc",$conn_ho);
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
					$cekPlan1 = "SELECT KodePlan from ".DB_STARS."..PlanTrn 
								where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					$reCekPlan1 = mssql_query($cekPlan1,$conn);
					$jmlreCekPlan1 = mssql_num_rows($reCekPlan1);
					
					if($jmlreCekPlan1==0){
						$cekPlan1 = "SELECT KodePlan from ".DB_STARS."..PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3";
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
							
							$qryKode = mssql_query("select kodepr from ".DB_STARS."..plantrn 
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
						$inst = mssql_query("insert into ".DB_STARS."..plantrn (SbrPlan,KodePr,KodeProspect,TglPlan,
							KodePlan,TypePlan,idKetPlan,KetPlan, 
							[Tanggal Jatuh Tempo], NoRangka, Type,
							KodeSales,Kodeuser,TglEntry)
							values ('1', '".$kodepr."', '".$kodeprospect."', '".$TglPlan."',			
							'".$kodeplan."', 3, '".$idKetPlan."', '".$KetPlan."', 
							'".$tglJthTempo."', '".$norangka."', '".$type."',
							'".$_SESSION['stars_username']."','AutoPlanSystem',getdate())",$conn);
							
						if ($inst and HANSA==1) {
							$kodepesan = "REMINDER";
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
					
					
					#--------------- BRDIGING NASA SALES
					$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
											left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
											where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
											order by a.tgl_update desc",$conn_ho);
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
					$cekPlan1 = "SELECT KodePlan from ".DB_STARS."..PlanTrn 
								where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					$reCekPlan1 = mssql_query($cekPlan1,$conn);
					$jmlreCekPlan1 = mssql_num_rows($reCekPlan1);
					
					if($jmlreCekPlan1==0){
						$cekPlan1 = "SELECT KodePlan from ".DB_STARS."..PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3";
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
							
							$qryKode = mssql_query("select kodepr from ".DB_STARS."..plantrn 
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
						
						$inst = mssql_query("insert into ".DB_STARS."..plantrn (SbrPlan,KodePr,KodeProspect,TglPlan,
							KodePlan,TypePlan,idKetPlan,KetPlan, 
							[Tanggal Jatuh Tempo], NoRangka, Type,
							KodeSales,Kodeuser,TglEntry)
							values ('1', '".$kodepr."', '".$kodeprospect."', '".$TglPlan."',			
							'".$kodeplan."', 3, '".$idKetPlan."', '".$KetPlan."', 
							'".$tglJthTempo."', '".$norangka."', '".$type."',
							'".$_SESSION['stars_username']."','AutoPlanSystem',getdate())",$conn);
					
						if ($inst and HANSA==1) {
							$kodepesan = "REMINDER";
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
					
					
					#--------------- BRDIGING NASA SALES
					$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
											left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
											where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
											order by a.tgl_update desc",$conn_ho);
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
					$cekPlan1 = "SELECT KodePlan from ".DB_STARS."..PlanTrn 
								where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					$reCekPlan1 = mssql_query($cekPlan1,$conn);
					$jmlreCekPlan1 = mssql_num_rows($reCekPlan1);
					
					if($jmlreCekPlan1==0){
						$cekPlan1 = "SELECT KodePlan from ".DB_STARS."..PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3";
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
							
							$qryKode = mssql_query("select kodepr from ".DB_STARS."..plantrn 
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
						$inst = mssql_query("insert into ".DB_STARS."..plantrn (SbrPlan,KodePr,KodeProspect,TglPlan,
							KodePlan,TypePlan,idKetPlan,KetPlan, 
							[Tanggal Jatuh Tempo], NoRangka, Type,
							KodeSales,Kodeuser,TglEntry)
							values ('1', '".$kodepr."', '".$kodeprospect."', '".$TglPlan."',			
							'".$kodeplan."', 3, '".$idKetPlan."', '".$KetPlan."', 
							'".$tglJthTempo."', '".$norangka."', '".$type."',
							'".$_SESSION['stars_username']."','AutoPlanSystem',getdate())",$conn);
							
						if ($inst and HANSA==1) {
							$kodepesan = "REMINDER";
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
					
					
					#--------------- BRDIGING NASA SALES
					$qry_grd = mssql_query("select top 1 a.nasa_id, b.nik from ".DB_GRADING."..tblbridging_nasa a
											left join ".DB_GRADING."..tblmaster b on a.kry_id = b.idnama
											where a.nasa_id = '".$kdSales."' and isnull(tanggalkeluar,0) = 0 
											order by a.tgl_update desc",$conn_ho);
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
					$cekPlan1 = "SELECT KodePlan from ".DB_STARS."..PlanTrn 
								where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3 and NoRangka = '$norangka' and tglplan = '$TglPlan'";
					//$reCekPlan1 = $this->mod_home->QuerySalesDB($cekPlan1);
					$reCekPlan1 = mssql_query($cekPlan1,$conn);
					$jmlreCekPlan1 = mssql_num_rows($reCekPlan1);
					
					if($jmlreCekPlan1==0){
						$cekPlan1 = "SELECT KodePlan from ".DB_STARS."..PlanTrn where KodePr = '$kodepr' and KodeProspect = '$kodeprospect' and TypePlan = 3";
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
							
							$qryKode = mssql_query("select kodepr from ".DB_STARS."..plantrn 
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
						
						$inst = mssql_query("insert into ".DB_STARS."..plantrn (SbrPlan,KodePr,KodeProspect,TglPlan,
							KodePlan,TypePlan,idKetPlan,KetPlan, 
							[Tanggal Jatuh Tempo], NoRangka, Type,
							KodeSales,Kodeuser,TglEntry)
							values ('1', '".$kodepr."', '".$kodeprospect."', '".$TglPlan."',			
							'".$kodeplan."', 3, '".$idKetPlan."', '".$KetPlan."', 
							'".$tglJthTempo."', '".$norangka."', '".$type."',
							'".$_SESSION['stars_username']."','AutoPlanSystem',getdate())",$conn);
						
						if ($inst and HANSA==1) {
							$kodepesan = "REMINDER";
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
		
		
		
		?>