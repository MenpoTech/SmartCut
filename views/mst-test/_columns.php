<?php
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\MstDepartments;
use app\models\MstProducts;
use yii\bootstrap\Html;

return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'mst_department_id',
        'value'=>'department.dept_name',
        'filter'=>ArrayHelper::map(MstDepartments::find()->where(['status'=>1])->all(),'id','dept_name')
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'test_name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'test_order',
    ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'status',
         'filter' => array('1' => 'Active', '0' => 'InActive'),
         'format' => 'html',
         'hAlign' => 'center',
         'vAlign' => 'center',
         'value' => function ($model, $key, $index, $column) {
             switch ($model->status) {
                 case 0:
                     return Html::tag('span', '', ['class' => 'glyphicon glyphicon-minus text-danger  kv-align-center kh-align-middle']);
                     break;
                 case 1:
                     return Html::tag('span', '', ['class' => 'glyphicon glyphicon-ok kv-align-center kv-align-middle text-success']);
                     break;
                 default:
                     return Html::tag('span', '', ['class' => 'glyphicon glyphicon-remove red  kv-align-center kv-align-middle text-warning']);
                     break;
             }
         },
     ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'is_deleted',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'created_by',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'created_date',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'modified_by',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'modified_date',
    // ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index) { 
                return Url::to([$action,'id'=>$key]);
        },
        'viewOptions'=>['role'=>'modal-remote','title'=>'View','data-toggle'=>'tooltip'],
        'updateOptions'=>['role'=>'modal-remote','title'=>'Update', 'data-toggle'=>'tooltip'],
        'deleteOptions'=>['role'=>'modal-remote','title'=>'Delete', 
                          'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                          'data-request-method'=>'post',
                          'data-toggle'=>'tooltip',
                          'data-confirm-title'=>'Are you sure?',
                          'data-confirm-message'=>'Are you sure want to delete this item'], 
    ],

];   