<?php 
    include 'permission.php';
?>
<style type="text/css">
    #klik {
        cursor: pointer; color: #466baf;
    }
    #label {
        text-align:left;padding: 0;font-size: 11px;
    }
    #input {
        font-size: 11px;padding: 0px 10px;height: 21px;
    }
</style> <div class="right_col" role="main">
    <div class="widget widget-inverse" style="margin-bottom: 5px;">
        <div class="widget-head">
            <h4 class="heading">User Config</h4>
        </div>
        <div class="widget-body" style="padding: 10px 5px;">
            <div class="row">
                <div class="col-md-12">
                    <div class="widget widget-heading-simple widget-body-simple" style="margin: 0;">
                        <div class="widget-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <form class="form-horizontal" name="form2" action="home.php?page=user.mod" id="validateSubmitForm" method="POST" autocomplete="off" style=" margin-bottom: 0px;">
                                        <span id="post"></span>
                                        <div class="col-md-12" style="padding: 0px;">
                                            <table class="flexme3" style="display: none"></table>
                                            <script type="text/javascript">
                                                $(".flexme3").flexigrid({
                                                    url : 'flexii/user.php',
                                                    dataType : 'xml',
                                                    colModel : [ 
                                                        {
                                                        display : '#',
                                                        name : 'IdUser',
                                                        width : 40,
                                                        sortable : true,
                                                        align : 'center'
                                                        }, {
                                                            display : 'Kode User',
                                                            name : 'IdUser',
                                                            width : 125,
                                                            sortable : true,
                                                            align : 'left'
                                                        }, {
                                                            display : 'Nama User',
                                                            name : 'namaUser',
                                                            width : 250,
                                                            sortable : true,
                                                            align : 'left'
                                                        }, {
                                                            display : 'Setting User',
                                                            name : 'IdDlr',
                                                            width : 125,
                                                            sortable : true,
                                                            align : 'left'
                                                        } , {
                                                            display : 'Setting Email',
                                                            name : 'IdDlr',
                                                            width : 125,
                                                            sortable : true,
                                                            align : 'left'
                                                        } , {
                                                            display : 'Setting Periode',
                                                            name : 'IdDlr',
                                                            width : 125,
                                                            sortable : true,
                                                            align : 'left'
                                                        } , {
                                                            display : 'Generate Manual',
                                                            name : 'IdDlr',
                                                            width : 125,
                                                            sortable : true,
                                                            align : 'left'
                                                        } 
                                                    ],
                                                    buttons : [ 
                                                        {
                                                            name : 'Add',
                                                            bclass : 'add',
                                                            onpress : button
                                                        },{
                                                            name : 'Edit',
                                                            bclass : 'edit',
                                                            onpress : button
                                                        },{
                                                            name : 'Delete',
                                                            bclass : 'delete',
                                                            onpress : button
                                                        }
                                                    ],
                                                    sortname : "UserId",
                                                    sortorder : "asc",
                                                    usepager : true,
                                                    useRp : true,
                                                    rp : 10,
                                                    rpOptions: [10, 20, 50, 100],
                                                    showToggleBtn : false,
                                                    width : 950,
                                                    height : 200
                                                });
                                                function button(com) {
                                                    if (com == 'Add') {
                                                        $("#post").html('<input type="hidden" name="add" value="">');
                                                        document.forms['form2'].submit();
                                                    } else if (com == 'Edit') {
                                                        var generallen = $("input[name='id[]']:checked").length;
                                                        if (generallen==0) {
                                                            $("#msg").html('Choose one data!!');
                                                            $("#myMessage").dialog({
                                                                modal: true,
                                                                buttons: {
                                                                  Ok: function() {
                                                                    $(this).dialog( "close" );
                                                                  }
                                                                }
                                                            });
                                                            return false;
                                                        } else if (generallen>1) {
                                                            $("#msg").html('Choose one data!!');
                                                            $("#myMessage").dialog({
                                                                modal: true,
                                                                buttons: {
                                                                  Ok: function() {
                                                                    $(this).dialog( "close" );
                                                                  }
                                                                }
                                                            });
                                                            return false;
                                                        } else {
                                                            $("#post").html('<input type="hidden" name="edit" value="">');
                                                            document.forms['form2'].submit();
                                                        }
                                                    } else if (com == 'Delete') {
                                                        var generallen = $("input[name='id[]']:checked").length;
                                                        if (generallen==0) {
                                                            $("#msg").html('Choose one data!!');
                                                            $("#myMessage").dialog({
                                                                modal: true,
                                                                buttons: {
                                                                  Ok: function() {
                                                                    $(this).dialog( "close" );
                                                                  }
                                                                }
                                                            });
                                                            return false;
                                                        } else if (generallen>1) {
                                                            $("#msg").html('Choose one data!!');
                                                            $("#myMessage").dialog({
                                                                modal: true,
                                                                buttons: {
                                                                  Ok: function() {
                                                                    $(this).dialog( "close" );
                                                                  }
                                                                }
                                                            });
                                                            return false;
                                                        } else {
                                                            $("#post").html('<input type="hidden" name="delete" value="">');
                                                            document.forms['form2'].submit();
                                                        }
                                                    }
                                                }
                                            </script>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>