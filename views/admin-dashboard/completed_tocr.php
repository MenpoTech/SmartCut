<?php
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\View;
use kartik\grid\GridView;

if(!empty($data)) {
    /*
    echo GridView::widget([
        'dataProvider' => $data,
        'layout' => "{summary}\n{items}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
    //        'id',
            'tocr_number',
            'assign_date',
            'status',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);

    //    echo "<pre>"; print_r($details); echo "</pre>";
        echo '<table class="table table-bordered table-hover ">
                <tr class="table-header">
                    <th>#</th>
                    <th>TOCR</th>
                    <th>Assigned Date</th>
                    <th>Action</th>
                </tr>';
        $i=1;
        foreach($details as $value) {
            echo '<tr>
                    <td>'.$i++.'</td>
                    <td>'.$value['tocr_number'].'</td>
                    <td>'.$value['assigned_date'].'</td>
                    <td>'.Html::a('details',Url::to(['department/get-tocr-details','tocr_number'=>$value['tocr_number'],'status'=>$value['status']]),['class'=>'btn btn-block btn-primary btn-xs','role'=>'modal-remote','id'=>'assigned_'.$value['tocr_number']]).'</td>
                  </tr>';
        }*/
}
$js =<<< 'JS'
(function (){
    var _swal = window.swal;

    window.swal = function(){

        var previousWindowKeyDown = window.onkeydown;

        _swal.apply(this, Array.prototype.slice.call(arguments, 0));

        window.onkeydown = previousWindowKeyDown;

    };

})();
JS;

$js=<<<'JS'
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
JS;


$this->registerJs($js,View::POS_READY);

//\johnitvn\ajaxcrud\CrudAsset::register($this);
?>

<div class="mst-tests-index">
    <div id="CompletedAjaxCrudDatatable">
        <?=GridView::widget([
            'id'=>'crud-datatable-completed',
            'dataProvider' => $data,
            'filterModel' => $model,
            'pjax'=>true,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'tocr_number',
                'assign_date',
                [
                    'class'=>'\kartik\grid\DataColumn',
                    'attribute'=>'status',
                    'format' => 'raw',
                    'filter'=>'',
                    'value'=> function($action, $model, $key, $index) {
//                        return Html::a('details', Url::to(['department/get-tocr-details', 'tocr_number' => $action['tocr_number'], 'status' =>$action['status']]), ['class' => 'btn btn-block btn-primary btn-xs', 'role' => 'modal-remote', 'id' => 'completed_' . $action['tocr_number'],'pjax'=>0]);
                        return Html::button('details',['onclick'=>'popup_detail(\''.$action['tocr_number'].'\',\''.$action['status'].'\')', 'class' => 'btn btn-block btn-primary btn-xs', 'id' => 'completed_' . $action['tocr_number'],'data-pjax'=>0]);
                    }
                ],
            ],
            'toolbar'=>['content'=>''],
            'striped' => true,
            'condensed' => true,
            'responsive' => true,
            'panel' => [
                'type' => 'primary',
                'heading' => '<i class="glyphicon glyphicon-list"></i>&nbsp;&nbsp;&nbsp;&nbsp;    Completed Test List ','<div class="clearfix"></div>',
                'before'=>'',
            ]
        ])?>
    </div>
</div>
