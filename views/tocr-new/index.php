<?php
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\MstCustomers;
use yii\helpers\ArrayHelper;

echo GridView::widget([
    'id' => 'kv-grid-demo',
    'panel' => [
        'heading' => 'TOCR Details',
        'type' => 'primary',
    ],

    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
                    [
                        'class'=>'kartik\grid\ExpandRowColumn',
                        'width'=>'50px',
                        'value'=>function ($model, $key, $index, $column) {
                            return GridView::ROW_COLLAPSED;
                        },
                        'detail'=>function ($model, $key, $index, $column) {
//                            return Yii::$app->controller->runAction('admin-dashboard/details', ['tocr_number' =>$model['tocr_number']]);
                            return Yii::$app->controller->renderPartial('row-detail', ['model'=>$model]);
                        },
                        'headerOptions'=>['class'=>'kartik-sheet-style'],
                        'expandOneOnly'=>true
                    ]
        ,[
            'class'=>'\kartik\grid\DataColumn',
            'attribute'=>'tocr_number',
            'vAlign'=>'center',
            'hAlign'=>'center',
            'value'=>function ($model, $key, $index, $column) {
                if(!empty($model['tocr_document_path'])) {
                    return Html::a($model['tocr_number'],Url::to(['trn-customer/download-tocr-document','tocr_no'=>$model['tocr_number']]),['target'=>'_blank', 'data-pjax'=>"0"]);
                }else {
                    return $model['tocr_number'];
                }
            },
            'format'=>'raw',

        ]
        ,[
            'attribute'=>'mst_customer_id',
            'vAlign'=>'left',
//            'width'=>'180px',
            'value'=>function ($model, $key, $index, $widget) {
                return $model['customer_name'];
            },
            'filterType'=>GridView::FILTER_SELECT2,
            'filter'=>ArrayHelper::map(MstCustomers::find()->where(['status'=>1])->orderBy('customer_name')->asArray()->all(), 'id', 'customer_name'),
            'filterWidgetOptions'=>[
                'pluginOptions'=>['allowClear'=>true],
            ],
            'filterInputOptions'=>['placeholder'=>'All Customer'],
            'format'=>'raw'
        ]
        ,[
            'class'=>'kartik\grid\DataColumn',
            'attribute'=>'assign_date',
            'hAlign'=>'center',
            'vAlign'=>'middle',
            'width'=>'130px',
//            'format'=>'date',
            'value' => function ($model, $key, $index, $column) {
                return '<span class="small date">'.date('d-m-y h:i A',strtotime($model['assign_date'])).'</span>';
            },
//            'xlFormat'=>"\\-mmm\\-dd\\, \\-yyyy",
//            'headerOptions'=>['class'=>'kv-sticky-column'],
//            'contentOptions'=>['class'=>'kv-sticky-column'],
            'format'=>'raw',
        ]
        ,[
            'class'=>'\kartik\grid\DataColumn',
            'attribute'=>'is_need_witness',
            'filter' => array('1' => 'Yes', '0' => 'No'),
            'format' => 'html',
            'hAlign' => 'center',
            'vAlign' => 'center',
            'value' => function ($model, $key, $index, $column) {
                switch ($model['is_need_witness']) {
                    case 0:
                        return Html::tag('span', '', ['class' => 'text-danger  kv-align-center kh-align-middle']);
                        break;
                    case 1:
                        return Html::tag('span', '', ['class' => 'glyphicon glyphicon-ok kv-align-center kv-align-middle text-success']);
                        break;
                    default:
                        return Html::tag('span', '', ['class' => 'kv-align-center kv-align-middle']);
                        break;
                }
            },
        ],[
            'class'=>'\kartik\grid\DataColumn',
            'attribute'=>'is_return',
            'filter' => array('1' => 'Yes', '0' => 'No'),
            'format' => 'html',
            'hAlign' => 'center',
            'vAlign' => 'center',
            'value' => function ($model, $key, $index, $column) {
                switch ($model['is_return']) {
                    case 0:
                        return Html::tag('span', '', ['class' => 'text-danger  kv-align-center kh-align-middle']);
                        break;
                    case 1:
                        return Html::tag('span', '', ['class' => 'glyphicon glyphicon-ok kv-align-center kv-align-middle text-success']);
                        break;
                    default:
                        return Html::tag('span', '', ['class' => 'kv-align-center kv-align-middle']);
                        break;
                }
            },
        ]
//        ,'report_from_no'
//        ,'report_to_no'
        ,[
            'class'=>'\kartik\grid\DataColumn',
            'attribute'=>'sample_photo_path',
            'vAlign'=>'center',
            'hAlign'=>'center',
            'value'=>function ($model, $key, $index, $column) {
                if(!empty($model['sample_photo_path'])) {
                    return Html::a('<i class="fa fa-download">&nbsp;</i>',Url::to(['trn-customer/download-sample-photo','tocr_no'=>$model['tocr_number']]),['target'=>'_blank', 'data-pjax'=>"0"]);
                }else {
                    return '';
                }
            },
            'format'=>'raw',

        ]
        ,'witness_seen'
        ,'witness_date'
        ,[
            'class'=>'\kartik\grid\DataColumn',
            'attribute'=>'total_count',
            'vAlign'=>'center',
            'hAlign'=>'center',
        ]
    ],
    'containerOptions' => ['style' => 'overflow: auto'],
    'headerRowOptions' => ['class' => 'kartik-sheet-style'],
    'filterRowOptions' => ['class' => 'kartik-sheet-style'],
    'pjax' => true,
    'toolbar' =>  [
        ['content' =>Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['index'], ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => Yii::t('app', 'Reset Grid')])
        ],
//        '{export}',
        '{toggleData}',
    ],
        // set export properties
        'export' => [ 'fontAwesome' => true ],
        // parameters from the demo form
        'bordered' => true,
        'striped' => true,
        'condensed' => true,
        'responsive' => true,
        'hover' => true,
        'showPageSummary' => false,
        'persistResize' => false,
        'toggleDataOptions' => ['minCount' => 10],
//        'itemLabelSingle' => 'book',
//        'itemLabelPlural' => 'books'
    ]);
?>

<style>
    .kv-detail-content {
        padding-left: 20px;
        overflow: hidden;
    }
</style>