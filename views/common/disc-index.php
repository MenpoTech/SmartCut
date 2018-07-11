<?php if(!empty($details)) { ?>
    <style>
        .address {
            font-size: 10px;
            font-style: italic;
        }
    </style>
<div class="box box-default collapsed-box">
    <div class="box-header with-border" data-widget="collapse" style="cursor: pointer;">
        <i class="pull-right fa fa-plus" style="font-size: 20px;"></i>
        <table class="responsive" border="0" style="width: 95%;" align="center">
            <tr>
                <td>Name :<span class="bolder"><?=$details[0]['patient_name']?></span></td>
                <td>Code : <span class="bolder"><?=$details[0]['patient_code']?></span></td>
                <td>Age/Gender:<span class="bolder"> <?=$details[0]['age_year']."/".$details[0]['gender']?></span></td>
                <td>Room No: <span class="bolder"><?=$details[0]['room_no']?></span></td>
                <td>Corp :<span class="bolder"> <?php if($details[0]['corp_id']==3) echo "KG Hospital"; else echo $details[0]['corp_name']. "". (!empty($details[0]['insurance_name'])?' <br>/ '.$details[0]['insurance_name']:'')?></span></td>
                <td>Admit Date :<span class="bolder"> <?php echo date('d-m-Y h:i A',strtotime($details[0]['admit_date']));?></span></td>
            </tr>
            </table>
    </div>
    <!-- /.box-header -->
    <div class="box-body" style="display: none;">
            <table class="custom-table" border="0" style="width: 95%;" align="left">
                <tr>
                    <td>Admitting Unit</td><td>&nbsp;:&nbsp;</td><td colspan="4">  <span class="bolder"><?=$details[0]['admitting_unit']?></span></td>
                    <!--<td>Admitting Date</td><td>&nbsp;:&nbsp;</td><td>  <span class="bolder"><?/*=date('d-m-Y h:i A',strtotime($details[0]['admit_date']));*/?></span></td>-->
                    <td>Diagnosis</td><td>&nbsp;:&nbsp;</td><td>  <span class="bolder"><?=$details[0]['diagnosis']?></span></td>
                    <td>Mobile No</td><td>&nbsp;:&nbsp;</td><td>  <span class="bolder"><?=$details[0]['mobile_number']?></span></td>
                </tr>
                <tr>
                    <td>Address</td><td>&nbsp;:&nbsp;</td><td colspan="4">  <span class="address"><?=$details[0]['door_no']." ".$details[0]['street']." ".$details[0]['area']?></span></td>
                    <td>City</td><td>&nbsp;:&nbsp;</td><td>  <span class="address"><?=$details[0]['city_name']?></span></td>
                    <td>State/Country</td><td>&nbsp;:&nbsp;</td><td>  <span class="address"><?=$details[0]['state_name'].", ".$details[0]['country_name']?></span></td>
                </tr>
            </table>
            <!-- /.col -->
    </div>
</div>
<?php } ?>