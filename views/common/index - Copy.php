<style>
    .custom-table > tbody > tr > td {
        padding: 1px;
    }
    .header_row >th {
        background:#438eb9;
        color:#fff;
        padding: 5px;
    }
    .footer_row >td {
        background:lightgrey;
        color:#000;
        padding: 5px;
    }
    .close-icn {
        padding: 0px 10px;
        font-size: 20px;
        color: red;
    }
    .row-height {
        font-size: 13px;
        height: 25px;
        /*padding: 0 0 0 10px;*/
    }
    .less-padding {
        padding-right: 0px;
        padding-left: 8px;
    }
    .align-right {
        text-align: right;
    }
    .bolder {
        font-weight: bold;
    }
    .readonly {
        background-color: #ffffff !important;
    }
    .font15 {
        font-size: 15px;
    }
</style>
<?php if(!empty($details)) { ?>
<div class="box box-default collapsed-box">
    <div class="box-header with-border">
        <div class="col-xs-3">Name :<span class="bolder"><?=$details[0]['patient_name']?></span></div>
        <div class="col-xs-2 less-padding">Code : <span class="bolder"><?=$details[0]['patient_code']?></span></div>
        <div class="col-xs-2">Age/Gender:<span class="bolder"> <?=$details[0]['age_year']."/".$details[0]['gender']?></span></div>
        <div class="col-xs-2">Room No: <span class="bolder"><?=$details[0]['ward_no']." " .$details[0]['bed_no']?></span></div>
        <div class="col-xs-3 less-padding">Corp :<span class="bolder"> <?php if($details[0]['corp_id']==3) echo "KG Hospital"; else $details[0]['corp_name']?></span></div>
        <div class="box-tools pull-right">
            <button data-widget="collapse" class="btn btn-box-tool" type="button"><i class="fa fa-plus"></i></button>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body" style="display: block;">
        <div class="row">
            <table class="table" border="1" style="width: 90%;" align="center">
                <tr>
                    <td>Mobile No: </td>
                    <td><?=$details[0]['mobile_number']?></td>
                </tr>
            </table>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
</div>
<?php } ?>