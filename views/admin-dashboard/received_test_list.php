<?php
use yii\helpers\Html;
if(!empty($details)) {
//    echo "<pre>"; print_r($details); echo "</pre>";
    echo '<div class="box-body">
                <div class="panel panel-primary">
                    <div class="table-responsive kv-grid-container">
        <table class="kv-grid-table table table-bordered table-striped table-condensed kv-table-wrap table-hover ">
            <thead class="table-header">
            <tr>
                <td>#</td>
                <td>Test Name</td>
                <td>Sub Test Name</td>
                <td>Sample Detail</td>
                <td>Heat No</td>
                <td>Sample Id</td>
                <td>Remarks</td>
                <td><label><input type="checkbox" onclick="check_all(this)"> &nbsp;Action</label></td>
            </tr>
            </thead>
            <tbody>';
    $i=1;
    foreach($details as $dept_name=>$val) {
        echo "<tr><td align='center' class='bolder' colspan='8'>".$dept_name."</td></tr>";
        foreach ($val as $id=>$value) {
            echo '<tr>
                <td>' . $i++ . '</td>
                <td>' . $value['test_name'] . '</td>
                <td>' . $value['sub_test_name'] . '</td>
                <td>' . $value['sample_detail'] . '</td>
                <td>' . $value['heat_no'] . '</td>
                <td>' . $value['sample_id'] . '</td>
                <td>' . $value['remarks'] . '</td>
                <td><label>'.Html::checkbox('received_test_'.$id,false,['id'=>'test_received_'.$id,'value'=>$id,'class'=>'received_test checkbox1']).' Select </label></td>
              </tr>';
//            <td><span class="btn btn-block btn-primary btn-xs" onclick="receive_test(' . $id . ',\''.$value['tocr_number'].'\')"> Receive</span></td>
        }
    }
    echo '<tr><td colspan="4" align="right">'.Html::button('Close',['class'=>'btn btn-danger','data-dismiss'=>'modal']).'</td><td colspan="4" align="left">'.Html::button('Complete',['class'=>'btn btn-success', 'onclick'=>'complete_multi_test(\''.$value['tocr_number'].'\')']).'</td></tr></tbody></table></div></div></div>';

    echo Yii::$app->runAction('admin-dashboard/details', ['tocr_number' =>$tocr]);
}else {
    echo '<div class="alert-warning alert fade in" > <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button> <i class="icon fa fa-warning"></i> No Test Found</div>';
    echo '<center><button class="btn btn-danger" data-dismiss="modal">Close</button></center>';
}
?>