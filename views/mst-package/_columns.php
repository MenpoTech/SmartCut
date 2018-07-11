<?php
use yii\helpers\Url;

return [
//    [
//        'class' => 'kartik\grid\CheckboxColumn',
//        'width' => '20px',
//    ],
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
        // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'id',
    // ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'package_name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'short_name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'description',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'status',
    ],
//    [
//        'class'=>'\kartik\grid\DataColumn',
//        'attribute'=>'created_date',
//    ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'created_by',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'created_ip',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'modified_date',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'modified_by',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'modified_ip',
    // ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{update} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {edit}',
        'buttons' => [
            'edit' => function ($url) {
                return \yii\helpers\Html::a(
                    '<span class="glyphicon glyphicon-plus"></span>',
                    $url,
                    [
                        'data-toggle'=>'tooltip',
                        'title' => 'Add/Remove Items',
                        'data-pjax' => '0',
                    ]
                );
            },
        ],
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index) { 
                return Url::to([$action,'id'=>$key]);
        },
        'viewOptions'=>['role'=>'modal-remote','title'=>'View','data-toggle'=>'tooltip'],
        'updateOptions'=>['role'=>'modal-remote','title'=>'Update', 'data-toggle'=>'tooltip'],
//        'editOptions'=>['role'=>'modal-remote','title'=>'Update', 'data-toggle'=>'tooltip']
    ],

];   