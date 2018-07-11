<?php
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\View;
use kartik\grid\GridView;
use yii\widgets\Pjax;

$js=<<<'JS'
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
JS;
$this->registerJs($js,View::POS_READY);
//Pjax::begin(['id'=>'assigned_grid']);
?>
<div class="mst-tests-index">
    <div id="AssignedAjaxCrudDatatable">
        <?=GridView::widget([
            'id'=>'crud-datatable-assigned',
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
//                        return Html::a('details', Url::to(['department/get-tocr-details', 'tocr_number' => $action['tocr_number'], 'status' =>$action['status']]), ['class' => 'btn btn-block btn-primary btn-xs', 'role' => 'modal-remote', 'id' => 'assigned_' . $action['tocr_number'],'pjax'=>0]);
                        return Html::button('details',['onclick'=>'popup_detail(\''.$action['tocr_number'].'\',\''.$action['status'].'\')', 'class' => 'btn btn-block btn-primary btn-xs', 'id' => 'assigned_' . $action['tocr_number'],'data-pjax'=>0]);
                    }
                ],
            ],
            'toolbar'=>['content'=>''],
            'striped' => true,
            'condensed' => true,
            'responsive' => true,
            'panel' => [
                'type' => 'primary',
                'heading' => '<i class="glyphicon glyphicon-list"></i>&nbsp;&nbsp;&nbsp;&nbsp;    Assigned Test List ','<div class="clearfix"></div>',
                'before'=>'',
            ]
        ])?>
    </div>
</div>
<?php //Pjax::end(); ?>
