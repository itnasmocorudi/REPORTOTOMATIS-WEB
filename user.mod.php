<?php
    require_once ("inc/fungsi_all.php");
  //  require_once "lib/nusoap.php";
   
    $id = isset($_POST['id']) ? $_POST['id'][0] : null;
    $params = array('IdUser' => $id);

    
	require_once("soap.php");
	$client = new UserClient; 

    $results = $client->GetUserByID($params);
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
                       <span class="section">Add User</span>
                        <input class="form-control" type="hidden" name="post" value="add-user"/>
                        <div class="field item form-group">
                            <label class="col-form-label col-md-3 col-sm-3  label-align">NIK<span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6">
                                <input class="form-control" maxlength="9" data-validate-length-range="9" data-validate-words="2" name="nik" placeholder="xxxx-xxxx" required="required" />
                            </div>
                        </div>
                        <div class="field item form-group">
                            <label class="col-form-label col-md-3 col-sm-3  label-align">Nama Lengkap<span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6">
                                <input class="form-control" maxlength="30" data-validate-length-range="9" data-validate-words="2" name="nama" placeholder="Nama Lengkap" required="required" />
                            </div>
                        </div>

                        <div class="field item form-group">
                            <label class="col-form-label col-md-3 col-sm-3  label-align">Password<span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6">
                                <input class="form-control" type="password" id="password1" name="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}" title="Minimal 8 karakter , termasuk huruf besar dan kecil, nomor dan karakter unik." required />
                                
                                <span style="position: absolute;right:15px;top:7px;" onclick="hideshow()" >
                                    <i id="slash" class="fa fa-eye-slash"></i>
                                    <i id="eye" class="fa fa-eye"></i>
                                </span>
                            </div>
                        </div>
                        
                         
                         
                        <div class="field item form-group">
                            <label class="col-form-label col-md-3 col-sm-3  label-align">Menu :</label>
                            <div class="col-md-6 col-sm-6"> 
                                <div class='col-md-6' style='margin: 2px 0px 0px 0px;'><input type='checkbox' value='1' name='Uconfig'/><span style='font-size: 13px;padding-left: 5px;'>Setting User</span></div>
                                <div class='col-md-6' style='margin: 2px 0px 0px 0px;'><input type='checkbox' value='1' name='masteremail'/><span style='font-size: 13px;padding-left: 5px;'>Setting Email</span></div>
                                <div class='col-md-6' style='margin: 2px 0px 0px 0px;'><input type='checkbox' value='1' name='masterperiode'/><span style='font-size: 13px;padding-left: 5px;'>Setting Periode</span></div>
                                <div class='col-md-6' style='margin: 2px 0px 0px 0px;'><input type='checkbox' value='1' name='generatemanual'/><span style='font-size: 13px;padding-left: 5px;'>Generate Manual</span></div>
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
                        <input class="form-control" type="hidden" name="post" value="edit-user"/>
                       <span class="section">Edit User</span>
                        <?php
                            $results = json_decode($results,true); 
                            foreach($results AS $row_jenis){
                                $cek1 = ($row_jenis['Uconfig']=='1')?"checked" : "";
                                $cek2 = ($row_jenis['masteremail']=='1')?"checked" : "";
                                $cek3 = ($row_jenis['masterperiode']=='1')?"checked" : "";
                                $cek4 = ($row_jenis['generatemanual']=='1')?"checked" : ""; 
                                echo "
                                    <div class='field item form-group'>
                                        <label class='col-form-label col-md-3 col-sm-3  label-align'>NIK<span class='required'>*</span></label>
                                        <div class='col-md-6 col-sm-6'>
                                            <input class='form-control' maxlength='9' data-validate-length-range='9' data-validate-words='2' name='nik' value='".$row_jenis['IdUser']."' readonly placeholder='xxxx-xxxx' required='required' />
                                        </div>
                                    </div> 
                                    <div class='field item form-group'>
                                        <label class='col-form-label col-md-3 col-sm-3  label-align'>Nama Lengkap<span class='required'>*</span></label>
                                        <div class='col-md-6 col-sm-6'>
                                            <input class='form-control' maxlength='30' data-validate-length-range='9' data-validate-words='2' name='nama' value='".$row_jenis['namaUser']."'  placeholder='xxxx-xxxx' required='required' />
                                        </div>
                                    </div> 
                                        </div>
                                    </div>    
                                      
                                    <div class='form-group' style='margin: 2px;'>
                                        <label class='col-md-3 control-label' style='text-align:left'>Menu :</label>
                                        <div class='col-md-8'>
                                            <div class='col-md-6' style='margin: 2px 0px 0px 0px;'><input type='checkbox' $cek1 value='1' name='Uconfig'/><span style='font-size: 13px;padding-left: 5px;'>Setting User</span></div>
                                                <div class='col-md-6' style='margin: 2px 0px 0px 0px;'><input type='checkbox' $cek2 value='1' name='masteremail'/><span style='font-size: 13px;padding-left: 5px;'>Setting Email</span></div>
                                                <div class='col-md-6' style='margin: 2px 0px 0px 0px;'><input type='checkbox' $cek3 value='1' name='masterperiode'/><span style='font-size: 13px;padding-left: 5px;'>Setting Periode</span></div>
                                                <div class='col-md-6' style='margin: 2px 0px 0px 0px;'><input type='checkbox' $cek4 value='1' name='generatemanual'/><span style='font-size: 13px;padding-left: 5px;'>Generate Manual</span></div>
                                        </div>
                                    </div> 
                                    <div class='ln_solid'>
                                            <div class='form-group'>
                                                <div class='col-md-6 offset-md-3'>
                                                    <button type='submit' class='btn btn-primary'>Submit</button>
                                                    <button type='reset' class='btn btn-success'>Reset</button>
                                                </div>
                                            </div>
                                        </div>
                                ";
                            }
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
                                <input class="form-control" type="hidden" name="post" value="delete-user"/>
                            </div>
                        </div>


                        <?php
                            for ($i=0; $i < count($_POST['id']); $i++) { 
                                $id = isset($_POST['id'][$i]) ? $_POST['id'][$i] : null;
                                $params = array('IdUser' => $id); 
                                //$client = new nusoap_client(URL_SERVICE2."parameter.php");
                                //$results = $client->call("editUserx",$params);
                                
                                $results = $client->GetUserByID($params);
                                $results = json_decode($results,true); 
                                foreach($results AS $row_jenis){
                                    echo "
                                    <input type='hidden' class='form-control' name='id[]' value='".$_POST['id'][$i]."'> 
                                    <div class='field item form-group'>
                                        <label class='col-form-label col-md-3 col-sm-3  label-align'>NIK<span class='required'>*</span></label>
                                        <div class='col-md-6 col-sm-6'>
                                            <input class='form-control' maxlength='9' data-validate-length-range='9' data-validate-words='2' name='KodeUser' value='".$row_jenis['IdUser']."' readonly placeholder='xxxx-xxxx' required='required' />
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
        <?php } else { echo "<script type='text/javascript'> onload =function(){ alert('Pilih salah satu data!');document.location.href='home.php?page=user';}</script>"; } ?>
            </div>
        </div>
    </div>
</div>