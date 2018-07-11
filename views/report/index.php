<?php
use yii\helpers\Html;
use kartik\widgets\DatePicker;
use yii\helpers\Url;

$this->title = "Report";
?>

<div id="div_search_form" style="clear: both; display:block" class="no-print">
    <?= Html::beginForm(Url::to(['report/get-details']), 'post', ['data-pjax' => '', 'class' => 'form-inline','id'=>'report']); ?>
    <table border="0" align="center" class="table_filter table noprint" style="width:auto">
        <tr>
            <td class="left_condent">From Date</td>
            <td class="right_condent" width="30%">
                <?php
                echo DatePicker::widget(['name' => 'from_date', 'value' =>(!empty($from_date)?date('d-m-Y',strtotime($from_date)):date('d-m-Y')) ,  'removeButton' => false, 'type' => DatePicker::TYPE_INPUT, 'options' => ['placeholder' => 'From Date', 'id' => 'from_date'], 'pluginOptions' => ['format' => 'dd-mm-yyyy', 'todayHighlight' => true, 'autoclose' => true, 'endDate' => date('d-m-Y')]]);
                ?>
            </td>
            <td class="left_condent">To Date</td>
            <td class="right_condent" width="30%">
                <?php
                echo DatePicker::widget(['name' => 'to_date', 'value' => (!empty($to_date)?date('d-m-Y',strtotime($to_date)):date('d-m-Y')),  'removeButton' => false, 'type' => DatePicker::TYPE_INPUT, 'options' => ['placeholder' => 'To Date', 'id' => 'to_date'], 'pluginOptions' => ['format' => 'dd-mm-yyyy', 'todayHighlight' => true, 'autoclose' => true, 'endDate' => date('d-m-Y')]]);
                ?>
            </td>
            <td><?php echo Html::radioList('type','summary',array('summary'=>'Summary','detail'=>'Detail'),['id'=>'type']) ?></td>
            <td class="center_condent" rowspan="4" >
                <?php echo Html::button('Search',['class'=>'btn btn-primary','onclick'=>'search_report()','id'=>'search']); ?>
            </td>
        </tr>
    </table>
    <?php
    //    \yii\widgets\Pjax::end();
    echo Html::endForm();
    ?>
</div>
<div id="content"></div>
<?php
$js = <<<JS
function search_report()
{
    var form = $('#report');
    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        success: function (response) {
            $('#content').html(response);
        }
    });
}
JS;
$this->registerJs($js,\yii\web\View::POS_HEAD);

if(!empty($mode)) {
    $js = <<<JS
    search_report();
JS;
    $this->registerjs($js,\yii\web\View::POS_READY);
}

?>