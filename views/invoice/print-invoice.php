<style type="text/css">
    table.blueTable {
        width: 700px;
        border-collapse: collapse;
    }
    table.blueTable td {
        border: 1px solid #AAAAAA;
        padding: 3px 2px;
    }
    table.blueTable tbody td {
        font-size: 12px;
    }

    table.blueTable thead {
        border-left: 1px solid #AAAAAA;
        border-right: 1px solid #AAAAAA;
    }

    table.blueTable thead th {
        font-size: 12px;
    }


    table.innerTable {
        width: 695px;
        border-collapse: collapse;
    }
    table.innerTable td {
        padding: 10px 12px;
        font-size: 12px;
    }

    table.innerTable thead {
        border-left: 1px solid #AAAAAA;
        border-right: 1px solid #AAAAAA;
    }

    table.innerTable thead th {
        font-size: 12px;
        border-left: 1px solid #AAAAAA;
        border-right: 1px solid #AAAAAA;
        border-bottom: 1px solid #AAAAAA;
    }

    table.innerTable thead th:last-child {
        border-right: 3px solid white !important;
    }

    table.innerTable thead th:first-child {
        border-left: 3px solid white !important;
    }

    table.innerTable tbody td {
        border-left: 1px solid #AAAAAA;
        border-right: 1px solid #AAAAAA;
        border-top : none !important;
        border-bottom : none !important;
    }

    .first_col {
        border-left: none !important;
    }

    .last_col {
        border-right: none !important;
    }

    .invoice {
        font-size: 18px;
        font-weight: bold;
        text-align: center;
    }
    .remarks {
        font-size: 8px;
        font-style: italic;
    }
</style>
<table class="blueTable">
    <tbody>
    <tr>
        <td colspan="4" style="text-align: center;">
            <img src="<?php echo $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."images/logo.jpg";?>" style="display: block;" height="60px" alt="Photo Not Available" />
            <?php echo $address ?>
        </td>
    </tr>
    <tr>
        <td colspan="4" align="center">
            <span class="invoice">INVOICE</span>
        </td>
    </tr>
    <tr>
        <td colspan="2" rowspan="3" style="vertical-align: top;">
            Billing Address:
            <?php
                echo "<br>".$header_details['customer_name'];
                echo "<br>".$header_details['customer_address'];
                echo "<br>".$header_details['city'];
            ?>
        </td>
        <td>Invoice No : <?php echo $header_details['bill_no']; ?></td>
        <td>Date : <?php echo date('d-m-Y',strtotime($header_details['bill_date'])); ?> </td>
    </tr>
    <tr>
        <td>PO No</td>
        <td>Date</td>
    </tr>
    <tr>
        <td>DC No</td>
        <td>Date</td>
    </tr>
    <tr>
        <td>GSTIN/UIN:<?php echo $header_details['gstin_no']; ?></td>
        <td>State Code : <?php echo $header_details['state_code']; ?></td>
        <td colspan="2" rowspan="1">Terms of Payment<br><br></td>
    </tr>
    <tr>
        <td colspan="2">Terms Of Delivery<br><br></td>
        <td colspan="2" rowspan="1">Despatched Through<br><br></td>
    </tr>
    <tr>
        <td colspan="4">
            <table class="innerTable">
                <thead>
                <tr>
                    <th width="200px" class="first_col">DESCRIPTION OF GOODS</th>
                    <th>PART NO</th>
                    <th>HSN</th>
                    <th>QTY</th>
                    <th>UOM</th>
                    <th>RATE</th>
                    <th>GST</th>
                    <th>CGST</th>
                    <th>SGST</th>
                    <th class="last_col">TOTAL</th>
                </tr>
                </thead>
                <tbody>
                <?php
                    if(!empty($details)) {
                        $sno = 1;
                        foreach($details as $val) {
                            echo '
                <tr>
                    <td class="first_col">'.$sno++.'.  '.$val['item_name'].'</td>
                    <td>'.$val['part_no'].'</td>
                    <td>'.$val['hsn_code'].'</td>
                    <td>'.$val['qty'].'</td>
                    <td>'.$val['uom_name'].'</td>
                    <td>'.$val['uint_amount'].'</td>
                    <td>'.$val['tax_percent'].'</td>
                    <td>'.$val['tax_amount'].'</td>
                    <td>'.$val['tax_amount'].'</td>
                    <td class="last_col">'.$val['net_amount'].'</td>
                </tr>';
                        }
                    }
                ?>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2">&nbsp;</td>
        <td colspan="2" rowspan="1">&nbsp;</td>
    </tr>
    </tbody>
</table>