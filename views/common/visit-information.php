<?php if(!empty($details)) { ?>
    <style>
        .address {
            font-size: 10px;
            font-style: italic;
        }
    </style>
    <div class="box box-default collapsed-box">
        <div class="box-header with-border table-responsive no-padding1" data-widget="collapse" style="cursor: pointer;">
            <i class="pull-right fa fa-plus" style="font-size: 20px;"></i>
            <table class="responsive" border="0" style="width: 98%; font-size: 13px" align="center">
                <tr>
                    <td>Name :<span class="bolder"><?=$details['patient_name']?></span><span class="bolder text-red">(<?=$details['patient_code']?>) </span></td>
                    <td>Age/Gender:<span class="bolder"> <?=$details['age_year']."/".$details['gender']?></span></td>
                    <td>Room No: <span class="bolder"><?=$details['ward_no']." " .$details['bed_no']?></span></td>
                    <td>Ins./Corp. :<span class="bolder"> <?php if($details['corp_id']==3) echo "KG Hospital"; else echo $details['corp_name']. "". (!empty($details[0]['insurance_name'])?' <br>/ '.$details[0]['insurance_name']:'')?></span></td>
                    <td>Admit Date :<span class="bolder"> <?php echo date('d-m-Y h:i A',strtotime($details['admit_date']));?></span></td>
                </tr>
            </table>
        </div>
        <!-- /.box-header -->
        <div class="box-body" style="display: none;">
            <table class="custom-table1" border="0" style="width: 98%;" align="left">
                <tr>
                    <td>F/H/G Name </td><td>&nbsp;:&nbsp;</td><td>  <span class="bolder"><?=$details['father_name']?></span></td>
                    <td>Admitting Unit</td><td>&nbsp;:&nbsp;</td><td>  <span class="bolder"><?=$details['admitting_unit']?></span></td>
                    <!--<td>Admitting Date</td><td>&nbsp;:&nbsp;</td><td>  <span class="bolder"><?/*=date('d-m-Y h:i A',strtotime($details['admit_date']));*/?></span></td>-->
                    <td>Diagnosis</td><td>&nbsp;:&nbsp;</td><td>  <span class="bolder"><?=$details['diagnosis']?></span></td>
                    <td>Mobile No</td><td>&nbsp;:&nbsp;</td><td>  <span class="bolder"><?=$details['mobile_number']?></span></td>
                </tr>
                <tr>
                    <td>Address</td><td>&nbsp;:&nbsp;</td><td colspan="4">  <span class="address"><?=$details['door_no']." ".$details['street']." ".$details['area']?></i></span></td>
                    <td>City</td><td>&nbsp;:&nbsp;</td><td>  <span class="address"><?=$details['city_name']?></span></td>
                    <td>State/Country</td><td>&nbsp;:&nbsp;</td><td>  <span class="address"><?=$details['state_name'].", ".$details['country_name']?></span></td>
                </tr>
            </table>
            <!-- /.col -->
        </div>
    </div>
<?php }
?>