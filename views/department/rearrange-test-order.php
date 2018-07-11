<?php
use kartik\sortinput\SortableInput;
use yii\bootstrap\Html;
use yii\web\JsExpression;
use kartik\select2\Select2;
use yii\helpers\Url;

$this->title ='Test Rearrange';

//Pjax::begin();
echo Html::beginForm(['department/rearrange-test-order'], 'post', ['data-pjax' => '', 'class' => 'form-inline', 'name' => 'test-rearrange']);
$formatJs = <<< 'JS'
                var formatRepo = function (repo) {
                    if (repo.loading) {
                        return repo.tocr_number +' - '+repo.customer_name;
                    }
                    var markup = '<div class="row">' +
                    '<div class="col-sm-12">' +
                    '<div class="col-sm-4"> <b>' + repo.tocr_number + '</b></div>' +
                    '<div class="col-sm-6">' + repo.customer_name + '</div>' +
                    '</div>';
                    return '<div style="overflow:hidden;">' + markup + '</div>';
                };
JS;

// Register the formatting script
$this->registerJs($formatJs, \yii\web\View::POS_HEAD);

$value = '';
$url = \yii\helpers\Url::to(['trn-customer/get-tocr-number']);
$value  = (!empty($tocr_number) ?$tocr_number:'');
echo "<center>".Select2::widget([
    'name' => 'tocr_number',
    'initValueText' => $value,
    'value' => (!empty($tocr_number)?$tocr_number:''),
    'size'=>'md',
    'options' => ['placeholder' => 'Select the TOCR Number', 'id' => 'tocr_number','onchange'=>'javascript:load_details(this.value);'],
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
])."</center>";

if(!empty($items)) {
    echo '<div id="kv-grid-demo" class="grid-view hide-resize" data-krajee-grid="kvGridInit_ed8f6b90">
            <div class="panel panel-primary">
            <div class="panel-heading"><h3 class="panel-title">'.Yii::$app->request->getQueryParam('tocr_number').'</h3></div>
            <div class="rc-handle-container">';
    echo ''.SortableInput::widget([
        'name' => 'test_order',
        'items' => $items,
        'hideInput' => true,
    ]);
    echo "<center>".Html::submitButton('Save Order',['class'=>'btn btn-primary']).'</center>';
    echo '</div></div></div>';
}

echo Html::endForm();
//Pjax::end();
?>
<br> <br>
<?php echo Yii::$app->runAction('/tocr-new/index') ?>

<script>
    function load_details(val) {
        var url = '<?php echo Url::to(['department/rearrange-test-order']) ?>&tocr_number='+val;
        window.location= url;
    }
</script>
