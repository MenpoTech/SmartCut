<?php
use kartik\widgets\Select2;
use yii\web\JsExpression;
$formatJs = <<< 'JS'
                var formatRepo = function (repo) {
                    if (repo.loading) {
                        return repo.text ;
                    }
                    var markup = '<div class="row">' +
                    '<div class="col-sm-12">' +
                    '<div class="col-sm-4"> <b>' + repo.text + '</b></div>' +
                    '</div>';
                    return '<div style="overflow:hidden;">' + markup + '</div>';
                };
JS;

// Register the formatting script
$this->registerJs($formatJs, \yii\web\View::POS_HEAD);

$value = '';
$url = \yii\helpers\Url::to(['mst-menus/load-active-users']); //echo '<pre>';print_r($patient_info);
$value  = (!empty($patient_info) ?$patient_info[0]['user_name']:'');
echo Select2::widget([
    'name' => 'user_id',
    'initValueText' => $value,
    'value' => (!empty($user_id)?$user_id:''),
    'size'=>'md',
    'options' => ['placeholder' => 'Select the User', 'id' => 'user_id','onchange'=>'javascript:load_details(this.value);'],
    'pluginOptions' => [
        'allowClear' => false,
        'minimumInputLength' => 2,
        'width'=>'60%',
        'ajax' => [
            'url' => $url,
            'dataType' => 'json',
            'data' => new JsExpression('function(params) { return {q:params.term}; }')
        ],
        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
        'templateResult' => new JsExpression('formatRepo'),
        'templateSelection' => new JsExpression('function (obj) { return obj.text;}'),
    ],
]);