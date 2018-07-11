<?php
use kartik\widgets\Select2;
use yii\web\JsExpression;
$formatJs = <<< 'JS'
                var formatRepo = function (repo) {
                    if (repo.loading) {
                        return repo.patient_code +' - '+repo.patient_name;
                    }
                    var markup = '<div class="row">' +
                    '<div class="col-sm-12">' +
                    '<div class="col-sm-4"> <b>' + repo.patient_code + '</b></div>' +
                    '<div class="col-sm-6">' + repo.patient_name + '</div>' +
                    '<div class="col-sm-2" style="padding-left:0px;"><small /*style="color:blue"*/><i><b>' + repo.ward_name + ' - ' + repo.bed_no + '</b></i></small></div>' +
                    '</div>';
                    return '<div style="overflow:hidden;">' + markup + '</div>';
                };
JS;

// Register the formatting script
$this->registerJs($formatJs, \yii\web\View::POS_HEAD);

$value = '';
$url = \yii\helpers\Url::to(['ip-bill-entry/load-in-patients']);
$value  = (!empty($patient_info) ?$patient_info[0]['patient_code']." - ".$patient_info[0]['patient_name']:'');
echo Select2::widget([
    'name' => 'visit_id',
    'initValueText' => $value,
    'value' => (!empty($visit_id)?$visit_id:''),
    'size'=>'md',
    'options' => ['placeholder' => 'Select the Patient', 'id' => 'visit_id','onchange'=>'javascript:load_details(this.value);'],
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
        'templateResult' => new JsExpression('formatRepo'),
        'templateSelection' => new JsExpression('function (obj) { return obj.text;}'),
    ],
]);