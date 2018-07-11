<?php
use yii\bootstrap\Html;
use yii\helpers\Url;

if(!empty($details)) {
    $sno=1;
    $title = Yii::$app->session->get('report_title');
    echo '
    <div class="grid-view hide-resize">
        <div class="panel panel-primary">
            <div class="panel-heading">
            <div class="pull-right">
                '.Html::a('<i class="fa fa-file-pdf-o" style="font-weight: bolder; color: white;">&nbsp; PDF </i>',Url::to(['report/generate-pdf'])).'&nbsp;&nbsp;&nbsp;&nbsp;'.Html::a('<i class="fa fa-file-excel-o" style="font-weight: bolder; color: white;">&nbsp; Excel</i>',Url::to(['report/generate-excel'])).'
            </div>
                <h3 class="panel-title bolder"> <i class="glyphicon glyphicon-list"></i> '.$title.' </h3>
            </div>
        <div class="table-responsive kv-grid-container">
        <table class="kv-grid-table table table-bordered table-striped table-condensed kv-table-wrap">
        <thead class="table-header">
        <tr>
            <th>#</td>
            <th>TOCR No</td>
            <th>Customer Name</td>
            <th>Assigned Date</td>
            <th>No of Test</td>
            <th>Status</td>
        </tr>
        </thead><tbody>';
    $total =0;
foreach($details as $val) {
    echo '<tr>
            <td>'.$sno++.'</td>
            <td>'.$val['tocr_number'].'</td>
            <td>'.$val['customer_name'].'</td>
            <td>'.$val['assign_date'].'</td>
            <td align="center">'.$val['no_of_test'].'</td>
            <td>'.(($val['status'])?'In Progress':'Not Started').'</td>
          </tr>';
    $total = $total + $val['no_of_test'];
}
    echo '<tr class="bolder"><td colspan="4" align="right"> Total</td><td align="center">'.$total.'</td><td>&nbsp;</td></tr>';
echo '</tbody>
     </table>
    </div>
</div>';
}else {
    echo '<div class="alert alert-warning"><i class="pull-right fa fa-times" data-dismiss="alert"></i> No Records found</div>';
}
?>