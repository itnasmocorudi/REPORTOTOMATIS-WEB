<?php 
    session_start(); 
	error_reporting(0);

	require_once ("../inc/fungsi_all.php");
	require_once "../lib/nusoap.php";
	$awal=$_REQUEST['awal'];
    $akhir=$_REQUEST['akhir'];

    $params = array('awal'=>$awal,'akhir'=>$akhir); 
    $client = new nusoap_client(URL_SERVICE2."parameter2.php");
	$results = $client->call("matchingx",$params);
	// echo $client->response;
	$no=0;
    //echo $results;
    // echo count($results);
    if (count($results)>1) {
        foreach($results as $row_jenis) {
            if ($no % 2 == 0) { $erow = 'style="background:#f7f7f7"'; } else {$erow="";}
            echo "
                <tr $erow>
                    <td>
                        <div style='width: 160px;padding: 5px;'>
                            <input type='hidden' name='noSpp[]' value='".$row_jenis['noSpp']."'>
                            ".$row_jenis['noSpp']."
                        </div>
                    </td>
                    <td><div style='width: 160px;padding: 5px;'>".$row_jenis['NamaDebitur']."</div></td>
                    <!--<td><div style='width: 160px;padding: 5px;'>".$row_jenis['AlamatDebitur']."</div></td>-->
                    <td><div style='width: 160px;padding: 5px;'>".$row_jenis['kotaDebitur']."</div></td>
                    <td><div style='width: 160px;padding: 5px;'>".$row_jenis['noRangka']."</div></td>
                    <td><div style='width: 160px;padding: 5px;'>".$row_jenis['noMesin']."</div></td>
                    <td>
                        <div style='width: 150px;padding: 0px;'>
                            <div class='input-group' style='padding: 0px;'>
                                <input name='NoSpk[]' id='NoSpk".$no."' class='form-control flex' readonly='' type='text'>
                                <span class='input-group-btn' style=''>
                                    <button type='button' onclick='show(".$no.")' class='btn btn-sm btn-default' style='padding: 3px 5px;border-radius: 0px;'><i class='fa fa-bars'></i></button>
                                </span>
                            </div>
                        </div>
                    </td>
                </tr>
            ";
            $no++;
        }
    } else {
        echo "No Data";
    }
?>