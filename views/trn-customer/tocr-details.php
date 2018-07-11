<?php
use dmstr\web\AdminLteAsset;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xml:lang="en" xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <style type="text/css">
        .table {
            margin-bottom: 1px;
            width: 580px;
            font-size : 12px;
            border: 1px solid #ddd;
            border-collapse : collapse;
            line-height: 1.5;
        }
        .inner-table {
            margin-top: -3px;
            margin-left: -3px;
            width: 693px;
            font-size : 10px;
            border: 1px solid #ddd;
            border-left: 1px solid white;;
            border-top: 2px solid #fff;
            border-bottom: 2px solid #fff;
            border-collapse : collapse;
        }
        .table > tbody > tr > td{
            border: 1px solid;
            padding: 2px;
            vertical-align: top;
        }
        .inner-table > tbody > tr > td {
            border: 1px solid;
            padding: 2px;
            vertical-align: top;
        }
        .label {
            /*font-style: italic;*/
            font-size: 10px;
        }
        .value {
            font-size: 10px;
            font-style: normal;
            font-weight: bold;
        }
        .header {
            font-size: 15px;
            font-weight: bolder;
        }
        .tocr_no {
            font-size: 16px;
        }
        .lesser {
            width: 15px;
        }
        .center {
            text-align: center;
        }
        .test-detail {
            font-size: 14px;
            font-weight: bold;
            line-height: 1.8;
            padding: 3px;
            vertical-align:middle;
        }
        .heading {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            align-items: center;
        }
    </style>
</head>
<body>
<span class="heading"><img src="images/logo.jpg" alt=""/> OMEGA TECHNICAL LAB </span>
<br>
<table class="table table-bordered table-striped">
    <tbody>
    <tr>
        <td align="center"> <span class="label">RESULT REQUIRED ON</span></td>
        <td colspan="3" rowspan="3" style="text-align: center; vertical-align: middle"><span class="label header">TEST ORDER-CUM-REPORT (TOCR)</span></td>
        <td colspan="2" rowspan="1" class="lesser"><span class="label">TOCR NO &nbsp;:&nbsp;</span> <span class="value tocr_no"><?php echo "".$details[0]['tocr_number'];?></span></td>
    </tr>
    <tr>
        <td><span class="label">DATE :</span></td>
        <td colspan="2" rowspan="1"><span class="label">DATE &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;</span><span class="value"><?php echo " ".date('d-m-Y',strtotime($details[0]['assign_date']));?></span></td>
    </tr>
    <tr>
        <td><span class="label">TIME :</span></td>
        <td colspan="2" rowspan="1"><span class="label">TIME &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :&nbsp;</span>  <span class="value"><?php echo "".date('h:i A',strtotime($details[0]['assign_date']));?></span></td>
    </tr>
    <tr>
        <td colspan="4" rowspan="1"><span class="label">SAMPLE DESCRIPTION : (MATERIAL SPEC, MATERIAL CONDITION, DIMENSIONS)</span></td>
        <td><span class="label lesser">HEAT CODE NO</span></td>
        <td><span class="label lesser">SAMPLE ID</span></td>
    </tr>
    <?php
    $sno=1;
    if(!empty($details)) {
    foreach($details as $val) { ?>
        <tr>
            <td colspan="4"><span class="test-detail"><?=$sno++;?><?='. '.$val['test_name'].'  '.$val['sub_test_name'];?> </span> </td>
            <td class="center"><span class="test-detail"><?=$val['heat_no'];?> </span> </td>
            <td class="center"><span class="test-detail"><?=$val['sample_id'];?> </span> </td>
        </tr>
    <?php }
    }
//    Lood the 8 Empty Lines if Empty
    for(;$sno<=8;$sno++) {
        echo '<tr> <td colspan="4" class="test-detail">&nbsp;</td> <td class="test-detail">&nbsp;</td> <td class="test-detail">&nbsp;</td> </tr>';
    }
    ?>
    <tr>
        <td colspan="6" rowspan="1">
            <table class="inner-table">
                <tbody>
                <tr>
                    <td colspan="1" rowspan="4" style="text-align: center; vertical-align: middle"><span class="label"> TEST <br>TO BE <br>CARRIED <br>OUT </span></td>
                    <td colspan="2" rowspan="1" style="text-align: center;"><span class="label"> CHEMICAL </span></td>
                    <td colspan="4" rowspan="1" style="text-align: center;"><span class="label"> MECHANICAL </span></td>
                    <td colspan="1" rowspan="2" style="text-align: center;"><span class="label"> METALIO GRAPHY </span></td>
                    <td style="text-align: center;"><span class="label">CORROSION </span></td>
                    <td style="text-align: center;"><span class="label">OTHERS </span></td>
                </tr>
                <tr>
                    <td>OES</td>
                    <td>WET</td>
                    <td>TENSILE</td>
                    <td>BEND/REBEND</td>
                    <td>HARDNESS</td>
                    <td>IMPACT</td>
                    <td colspan="2" rowspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="1" rowspan="2">&nbsp;</td>
                    <td colspan="1" rowspan="2">&nbsp;</td>
                    <td colspan="1" rowspan="2">&nbsp;</td>
                    <td colspan="1" rowspan="2">&nbsp;</td>
                    <td rowspan="2">&nbsp;</td>
                    <td colspan="1">&nbsp;</td>
                    <td colspan="1" rowspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="1">TEST TEMP<br><br><br></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>
                        <p>TEST METHOD</p>
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td rowspan="2" colspan="6">AMENDMENT NOTE</td>
                    <td rowspan="1" colspan="2" class="center">WITNESS</td>
                    <td rowspan="1" colspan="2" class="center">RETURN OF SAMPLE</td>
                </tr>
                <tr>
                    <td class="value"><?php echo ((!empty($details[0]['is_need_witness']) && !empty($details[0]['witness_date']))?date('d-m-Y',strtotime($details[0]['witness_date'])):'') ?> </td>
                    <td class="value"><?php echo ((!empty($details[0]['is_need_witness']) && !empty($details[0]['witness_date']))?date('h:i A',strtotime($details[0]['witness_date'])):'') ?></td>
                    <?php if(!empty($details[0]['is_return'])) {
                        $yes = '&nbsp;&nbsp;&nbsp;<img src="images/tick.png">';
                        $no = '';
                    }else {
                        $yes = '';
                        $no = '&nbsp;&nbsp;&nbsp;<img src="images/tick.png">';
                    } ?>
                    <td>YES <?php echo $yes; ?></td>
                    <td>NO <?php echo $no; ?></td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="6" class="top-border-less">
            <span>DETAILS OF SPECIAL PRECAUTION (IF ANY)</span>

            <p>&nbsp;</p>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <span>TEST REVIEW OBSERVATIONS/ COMPLIANCE OF MATERIAL SPECIFICATION</span>

            <p>&nbsp;</p>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <p>&nbsp;</p>

            <span></span> RECOMMENDED FOR RETEST&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; APPROVED FOR TC&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; MANAGER-TECHNICAL</span>
        </td>
    </tr>
    <tr>
        <td>TEST CERTIFICATE / REPORT <br>NO / DATE</td>
        <td colspan="3" rowspan="1">&nbsp;</td>
        <td colspan="2" rowspan="2">
            <p>REMARKS : (CFB)</p>

            <p>&nbsp;</p>
        </td>
    </tr>
    <tr>
        <td>SAMPLE RETURN <br>DC.NO/DATE</td>
        <td colspan="3" rowspan="1">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="6" style="text-align: right;">
            <p>&nbsp;</p>

            <span>CUSTOMER COORDINATING OFFICER</span>
        </td>
    </tr>
    </tbody>
</table>
</body>
</html>
sp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; MANAGER-TECHNICAL</span>
        </td>
    </tr>
    <tr>
        <td>TEST CERTIFICATE / REPORT <br>NO / DATE</td>
        <td colspan="3" rowspan="1">&nbsp;</td>
        <td colspan="2" rowspan="2">
            <p>REMARKS : (CFB)</p>

            <p>&nbsp;</p>
        </td>
    </tr>
    <tr>
        <td>SAMPLE RETURN <br>DC.NO/DATE</td>
        <td colspan="3" rowspan="1">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="6" style="text-align: right;">
            <p>&nbsp;</p>

            <span>CUSTOMER COORDINATING OFFICER</span>
        </td>
    </tr>
    </tbody>
</table>
</body>
</html>
