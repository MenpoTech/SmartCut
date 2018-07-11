<?php
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\models\Corporates;

$url = \yii\helpers\Url::to(['ip-bill-entry/load-corporates']);
$corporate_name  = (!empty($corporate_id) ?Corporates::findOne(['id'=>$corporate_id])->name:'');
echo Select2::widget([
    'name' => 'corporate_id',
    'initValueText' => $corporate_name,
    'value' => (!empty($corporate_id)?$corporate_id:''),
    'size'=>'md',
    'options' => ['placeholder' => 'Select the Corporate', 'id' => 'corporate_id','onchange'=>'/*javascript: after_corporate_selected(this.value);*/'],
    'pluginOptions' => [
        'allowClear' => false,
        'minimumInputLength' => 3,
        'width'=>'60%',
        'ajax' => [
            'url' => $url,
            'dataType' => 'json',
            'data' => new JsExpression('function(params) { return {q:params.term}; }')
        ],
        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
        'templateResult' => new JsExpression('function(obj) { return obj.text; }'),
        'templateSelection' => new JsExpression('function (obj) { return obj.text;}'),
    ],
]);