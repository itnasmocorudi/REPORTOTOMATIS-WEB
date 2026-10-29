<?php
    require_once ("inc/fungsi_all.php");
  //  require_once "lib/nusoap.php";
   
    $id = isset($_POST['id']) ? $_POST['id'][0] : null;
    $params = array('IdUser' => $id); 
    
	require_once("soap.php");
	$client = new UserClient; 

    $results = $client->GetPeriodeByID($params);
   // $client = new nusoap_client(URL_SERVICE2."parameter.php");
    //$results = $client->call("editUserx",$params);
    // echo $client->response;
?>  
<!-- <script type="text/javascript" src="jquery.min14.js"></script> -->
<script language="javascript">
    $(document).ready(function() {
        $('#KodeAsuransi').change(function() { 
            var category = $(this).val();
            $.ajax({
                type: 'GET',
                url: 'area.php',
                data: 'NamaAsuransi=' + category,
                dataType: 'html',
                beforeSend: function() {
                    $('#AreaAsuransi').html('Loading ....');  
                },
                success: function(response) {
                    $('#AreaAsuransi').html(response);
                }
            });
        });
        $('#KodeLeasing').change(function() { 
            var category = $(this).val();
            $.ajax({
                type: 'GET',
                url: 'area.php',
                data: 'namaLeasing=' + category,
                dataType: 'html',
                beforeSend: function() {
                    $('#AreaLeasing').html('Loading ....');  
                },
                success: function(response) {
                    $('#AreaLeasing').html(response);
                }
            });
        });
    });
</script>

    <script src="vendors/validator/multifield.js"></script>
    <script src="vendors/validator/validator.js"></script>

    <script>
        function hideshow(){
            var password = document.getElementById("password1");
            var slash = document.getElementById("slash");
            var eye = document.getElementById("eye");
            
            if(password.type === 'password'){
                password.type = "text";
                slash.style.display = "block";
                eye.style.display = "none";
            }
            else{
                password.type = "password";
                slash.style.display = "none";
                eye.style.display = "block";
            }

        }
    </script>


<div class="right_col" role="main">
     <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
        <?php if (isset($_POST['add'])) { ?> 
                <div class="x_content">
                    <form class="form-horizontal" action="savemydata.php" enctype="multipart/form-data" name="form1" id="validateSubmitForm" method="POST" autocomplete="off" style=" margin-bottom: 10px;">
                       <span class="section">Add Periode Kirim</span>
                        <input class="form-control" type="hidden" name="post" value="add-periode"/>
                        <?php 
                            $params = array('IdUser' => $_SESSION['UserId']); 
                            $results = $client->GetDataTipeReport($params);
                            $results = json_decode($results,true); 
                        ?>
                        <div class="row">
                            <label class="col-3  label-align">Tanggal Periode<span class="required">*</span></label>
                            <div class="col-1">
                                <select name="tglAwal" class="form-control">
                                    <?php for($i=1;$i<=31;$i++){?>
                                        <option value="<?=$i?>"><?=$i?></option>
                                    <?php }?>
                                </select>
                            </div>
                            <div class="col-2" align="center">Sampai Dengan </div>
                            <div class="col-1">
                                <select name="tglAkhir" class="form-control">
                                    <?php for($i=1;$i<=31;$i++){?>
                                        <option value="<?=$i?>"><?=$i?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-3  label-align">Tanggal Kirim<span class="required">*</span></label>
                            <div class="col-1">
                                <select name="tglKirim" class="form-control">
                                    <?php for($i=1;$i<=31;$i++){?>
                                        <option value="<?=$i?>"><?=$i?></option>
                                    <?php }?>
                                </select>
                            </div> 
                        </div>
                        <div class="row">
                            <label class="col-3  label-align">Tipe Report<span class="required">*</span></label>
                            <div class="col-4">
                                <select name="tipe" class="form-control">
                                    <?php foreach($results as $value){?>
                                        <option value="<?=$value['kode']?>"><?=$value['tipe']?></option>
                                    <?php }?>
                                </select>
                            </div> 
                        </div>
                         
                        <hr>

                                        <div class="ln_solid">
                                            <div class="form-group">
                                                <div class="col-md-6 offset-md-3">
                                                    <button type='submit' class="btn btn-primary">Submit</button>
                                                    <button type='reset' class="btn btn-success">Reset</button>
                                                </div>
                                            </div>
                                        </div>
                    </form> 
            </div>
        <?php } else if (isset($_POST['edit']) and isset($_POST['id'][0])) { ?>
            
                <div class="x_content">
                    <form class="form-horizontal" action="savemydata.php" enctype="multipart/form-data" name="form1" id="validateSubmitForm" method="POST" autocomplete="off" style=" margin-bottom: 10px;">
                        <input class="form-control" type="hidden" name="post" value="edit-periode"/>
                       <span class="section">Edit Periode Kirim</span>
                        <?php
                            $results = json_decode($results,true); 
                            $tglawal = 1;
                            $tglakhir = 1;
                            $tglkirim = 1;
                            $idperiode = 1;
                            $tipe = "";
                            foreach($results AS $row_jenis){
                                $tglawal = $row_jenis['tglawal'];
                                $tglakhir = $row_jenis['tglakhir'];
                                $tglkirim = $row_jenis['tglkirim'];
                                $idperiode = $row_jenis['idperiode'];
                                $tipe = $row_jenis['tipe']; 
                            }
                            ?>
                            <?php 
                            $params = array('IdUser' => $_SESSION['UserId']); 
                            $results = $client->GetDataTipeReport($params);
                            $results = json_decode($results,true); 
                        ?>
                        <input class="form-control" type="hidden" name="idperiode" value="<?=$idperiode?>"/>
                        <div class="row">
                            <label class="col-3  label-align">Tanggal Periode<span class="required">*</span></label>
                            <div class="col-1">
                                <select name="tglAwal" class="form-control">
                                    <?php for($i=1;$i<=31;$i++){?>
                                        <option <?php if($i == $tglawal){echo "selected";}?> value="<?=$i?>"><?=$i?></option>
                                    <?php }?>
                                </select>
                            </div>
                            <div class="col-2" align="center">Sampai Dengan </div>
                            <div class="col-1">
                                <select name="tglAkhir" class="form-control">
                                    <?php for($i=1;$i<=31;$i++){?>
                                        <option <?php if($i == $tglakhir){echo "selected";}?> value="<?=$i?>"><?=$i?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-3  label-align">Tanggal Kirim<span class="required">*</span></label>
                            <div class="col-1">
                                <select name="tglKirim" class="form-control">
                                    <?php for($i=1;$i<=31;$i++){?>
                                        <option <?php if($i == $tglkirim){echo "selected";}?> value="<?=$i?>"><?=$i?></option>
                                    <?php }?>
                                </select>
                            </div> 
                        </div>
                        <div class="row">
                            <label class="col-3  label-align">Tipe Report<span class="required">*</span></label>
                            <div class="col-4">
                                <select name="tipe" class="form-control">
                                    <?php foreach($results as $value){?>
                                        <option <?php if($value['kode'] == $tipe){echo "selected";}?> value="<?=$value['kode']?>"><?=$value['tipe']?></option>
                                    <?php }?>
                                </select>
                            </div> 
                        </div>
                                 
                            <?php
                                echo "
                                     
                                       
                                    <div class='ln_solid'>
                                            <div class='form-group'>
                                                <div class='col-md-6 offset-md-3'>
                                                    <button type='submit' class='btn btn-primary'>Submit</button>
                                                    <button type='reset' class='btn btn-success'>Reset</button>
                                                </div>
                                            </div>
                                        </div>
                                ";
                            
                        ?>
                    </form>
                </div>
            </div>
        <?php } else if (isset($_POST['delete']) and isset($_POST['id'][0])) { ?>
            
                <div class="x_content"> 
                    <form class="form-horizontal" action="savemydata.php" enctype="multipart/form-data" name="form1" id="validateSubmitForm" method="POST" autocomplete="off" style=" margin-bottom: 10px;">
                        <div class='field item form-group'>
                            <label class="col-form-label col-md-3 col-sm-3  label-align" style="padding: 0px 0px 5px 0px;color: red;">Are you sure to delete this ?</label>
                            <div class='col-md-6 col-sm-6'>
                                <input class="form-control" type="hidden" name="post" value="delete-periode"/>
                            </div>
                        </div>


                        <?php
                            for ($i=0; $i < count($_POST['id']); $i++) {  
                                $results = json_decode($results,true); 
                                foreach($results AS $row_jenis){
                                    echo "
                                    <input type='hidden' class='form-control' name='idperiode' value='".$_POST['id'][$i]."'> 
                                    <div class='field item form-group'>
                                        <label class='col-form-label col-md-3 col-sm-3  label-align'>Periode<span class='required'>*</span></label>
                                        <div class='col-md-6 col-sm-6'>
                                            <input class='form-control' name='email' value='".utf8_encode($row_jenis['tglawal']) . " sampai dengan " . utf8_encode($row_jenis['tglakhir'])."' readonly  required='required' />
                                        </div>
                                    </div> 

                                    ";
                                }
                            }

                        ?>
                        <div class='field item form-group' style='margin: 0 20px;'>
                            <label class='col-form-label col-md-3 col-sm-3  label-align'>&nbsp;</label>
                            <div class='col-md-6 col-sm-6'>
                                <button type='submit' name='save' class='btn btn-info'><i class='fa fa-check-circle'></i> OK</button>
                                <a href='home.php?page=user' class='btn btn-danger'><i class='fa fa-times'></i> Cancel</a>
                            </div>
                        </div>
                    </form> 
            </div>
        <?php } else { echo "<script type='text/javascript'> onload =function(){ alert('Pilih salah satu data!');document.location.href='home.php?page=email';}</script>"; } ?>
            </div>
        </div>
    </div>
</div>