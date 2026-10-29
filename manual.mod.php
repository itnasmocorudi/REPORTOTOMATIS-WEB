<?php
    require_once ("inc/fungsi_all.php");
  //  require_once "lib/nusoap.php";
   
    $id = isset($_POST['id']) ? $_POST['id'][0] : null;
    $params = array('IdUser' => $id); 
    
	require_once("soap.php");
	$client = new UserClient; 
 
   // $client = new nusoap_client(URL_SERVICE2."parameter.php");
    //$results = $client->call("editUserx",$params);
    // echo $client->response;
?>  
<!-- <script type="text/javascript" src="jquery.min14.js"></script> -->
<script>
  $(function () { 
    //Date picker
    $('#tglAwal').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    $('#tglKirim').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    $('#tglAkhir').datetimepicker({
        format: 'YYYY-MM-DD'
    });

});
</script>
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
                        <input class="form-control" type="hidden" name="post" value="add-manual"/>
                        <?php 
                            $params = array('IdUser' => $_SESSION['UserId']); 
                            $dealers = $client->GetDataDealer($params);
                            $dealers = json_decode($dealers,true); 

                            $results = $client->GetDataTipeReport($params);
                            $results = json_decode($results,true); 
                        ?>
                        <div class="row">
                            <label class="col-3  label-align">Periode<span class="required">*</span></label>
                            <div class="col-1">
                                <select name="tahun" class="form-control">
                                    <?php for($i=2020;$i<=date("Y");$i++){?>
                                        <option value="<?=$i?>"><?=$i?></option>
                                    <?php }?>
                                </select>
                            </div>
                            <div class="col-2" align="center">Bulan</div>
                            <div class="col-1">
                                <select name="bulan" class="form-control">
                                    <?php for($i=1;$i<=12;$i++){?>
                                        <option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>"><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-3  label-align">Tanggal Kirim<span class="required">*</span></label>
                            <div class="col-4">
                                <!--<select name="tglKirim" class="form-control">
                                    <?php for($i=1;$i<=31;$i++){?>
                                        <option value="<?=$i?>"><?=$i?></option>
                                    <?php }?>
                                </select>-->
                                <div class="input-group date" id="tglKirim" data-target-input="nearest">
                                    <input type="text" name="tglKirim" readonly class="form-control datetimepicker-input" data-target="#tglKirim"/>
                                    <div class="input-group-append" data-target="#tglKirim" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div> 
                        </div>
                        <div class="row">
                            <label class="col-3  label-align">Tanggal Awal Report<span class="required">*</span></label>
                            <div class="col-3">
                                <!--<select name="tglKirim" class="form-control">
                                    <?php for($i=1;$i<=31;$i++){?>
                                        <option value="<?=$i?>"><?=$i?></option>
                                    <?php }?>
                                </select>-->
                                <div class="input-group date" id="tglAwal" data-target-input="nearest">
                                    <input type="text" name="tglAwal"   class="form-control datetimepicker-input" data-target="#tglAwal"/>
                                    <div class="input-group-append" data-target="#tglAwal" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div> 
                            <label class="col-1  label-align">S/d </span></label>
                            <div class="col-3"> 
                                <div class="input-group date" id="tglAkhir" data-target-input="nearest">
                                    <input type="text" name="tglAkhir"   class="form-control datetimepicker-input" data-target="#tglAkhir"/>
                                    <div class="input-group-append" data-target="#tglAkhir" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
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
                        <div class="row">
                            <label class="col-3  label-align">Dealer<span class="required">*</span></label>
                            <div class="col-4">
                                <select name="dealer" class="form-control">
                                    <option value="All Dealer">All Dealer</option>
                                    <?php foreach($dealers as $value){?>
                                        <option value="<?=$value['kode']?>"><?=$value['namadealer']?></option>
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
        <?php } else { echo "<script type='text/javascript'> onload =function(){ alert('Pilih salah satu data!');document.location.href='home.php?page=email';}</script>"; } ?>
            </div>
        </div>
    </div>
</div>