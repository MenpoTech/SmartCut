<?php
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\View;
use kartik\grid\GridView;
use yii\widgets\Pjax;

if(!empty($data)) {
$js=<<<'JS'
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
JS;
$this->registerJs($js,View::POS_READY);
Pjax::begin(['id'=>'received_grid']);
?>

<div class="mst-tests-index">
    <div id="receivedAjaxCrudDatatable">
        <?=GridView::widget([
            'id'=>'crud-datatable-received',
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
//                        return Html::a('details', Url::to(['department/get-tocr-details', 'tocr_number' => $action['tocr_number'], 'status' =>$action['status']]), ['class' => 'btn btn-block btn-primary btn-xs', 'role' => 'modal-remote', 'id' => 'received_' . $action['tocr_number'],'pjax'=>0]);
                        return Html::button('details',['onclick'=>'popup_detail(\''.$action['tocr_number'].'\',\''.$action['status'].'\')', 'class' => 'btn btn-block btn-primary btn-xs', 'id' => 'received_' . $action['tocr_number'],'data-pjax'=>0]);
                    }
                ],
            ],
            'toolbar'=>['content'=>''],
            'striped' => true,
            'condensed' => true,
            'responsive' => true,
            'panel' => [
                'type' => 'primary',
                'heading' => '<i class="glyphicon glyphicon-list"></i>&nbsp;&nbsp;&nbsp;&nbsp;    Received Test List ','<div class="clearfix"></div>',
                'before'=>'',
            ]
        ])?>
    </div>
</div>
<?php Pjax::end();
}else {
    echo "No Record Found";
} ?>