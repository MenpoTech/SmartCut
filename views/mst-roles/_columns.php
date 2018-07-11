<?php
use yii\helpers\Url;
use yii\helpers\Html;

return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'role_name',
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
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index) { 
                return Url::to([$action,'id'=>$key]);
        },
        'viewOptions'=>['role'=>'modal-remote','title'=>'View','data-toggle'=>'tooltip'],
        'updateOptions'=>['role'=>'modal-remote','title'=>'Update', 'data-toggle'=>'tooltip'],
    ],

];   