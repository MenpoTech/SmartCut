<?php
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use app\models\MstCustomers;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\file\SortableAsset;
use kartik\widgets\FileInput;

/**
 * Created by PhpStorm.
 * User: MuraliDharan
 * Date: 16/06/2018
 * Time: 11:54 AM
 */

$this->title = 'Invoice Entry';
SortableAsset::register($this);

$customers = ArrayHelper::map(MstCustomers::find()->where(['status' => 1])->all(), 'id', 'customer_name');
$form = ActiveForm::begin([
    'id' => 'entry-form',
    'action' => ['trn-customer/entry'],
    'validationUrl' => ['trn-customer/validate-entry-form'],
    'validateOnSubmit' => false,
    'enableAjaxValidation' => false,
    'options' => ['enctype' => 'multipart/form-data', 'onsubmit' => 'return validate_form()'],
]);

//$model->sample_details = 112;
//$model->heat_no = 32;
//$model->sample_id = '234';

$formatJs = <<< 'JS'
                var formatRepo = function (repo) {
                    if (repo.loading) {
                        return repo.customer_name +' - '+repo.city;
                    }
                    var markup = '<div class="row">' +
                    '<div class="col-sm-12">' +
                    '<div class="col-sm-3"> <b>' + repo.customer_name + '</b></div>' +
                    '<div class="col-sm-4">' + repo.customer_address + '</div>' +
                    '<div class="col-sm-2" style="padding-left:0px;"><small /*style="color:blue"*/><i><b>' + repo.city + '</b></i></small></div>' +
                    '<div class="col-sm-2" style="padding-left:0px;"><small /*style="color:blue"*/><i><b>' + repo.customer_mobile + '</b></i></small></div>' +
                    '</div>';
                    return '<div style="overflow:hidden;">' + markup + '</div>';
                };
JS;
$this->registerJs($formatJs, \yii\web\View::POS_HEAD);
?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title bolder">
            <i class="glyphicon glyphicon-list"></i> Invoice Entry
        </h3>
    </div>
    <div class="kv-grid-container">
        <div class="clearfix"> &nbsp; </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label class="col-sm-2">Customer</label>

                    <div class="col-sm-6">
                        <?php
                        $url = Url::to(['mst-customer/search-customer']);
                        echo $form->field($model, 'mst_customer_id')->widget(\kartik\select2\Select2::className(), [
                            'data' => $customers,
                            'options' => ['placeholder' => 'Select the Customer...'],
                            'pluginOptions' => [
                                'allowClear' => false,
                                'minimumInputLength' => 3,
                                'width' => '100%',
                                'ajax' => [
                                    'url' => $url,
                                    'dataType' => 'json',
                                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                ],
                                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                'templateResult' => new JsExpression('formatRepo'),
                                'templateSelection' => new JsExpression('function (obj) { return obj.text;}'),
                            ],
                        ])->label(false); ?>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="col-sm-4">Date</label>

                            <div
                                class="col-sm-8"> <?php echo $form->field($model, 'assign_date')->widget(DatePicker::className(),
                                    ['type' => DatePicker::TYPE_INPUT,
                                        'pluginOptions' => [
                                            'autoclose' => true,
                                            'format' => 'dd-mm-yyyy'
                                        ]
                                    ])->label(false) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label class="col-sm-2">Item Name</label>

                    <div class="col-sm-8">
                        <?php
                        $formatJs = <<< 'JS'
                var formatRepo = function (repo) {
                    if (repo.loading) {
                        return repo.text;
                    }
                    var markup = '<div class="row">' +'<div class="col-sm-8">' +'' + repo.text + '</div><div class="col-sm-4"> - <span style="color : inherit" class="bolder "><i>' + repo.dept_name + '</i></span>' +
                    '</div>';
                    return '<div style="overflow:hidden;">' + markup + '</div>';
                };
                var formatRepoSelection = function (repo) {
                    return repo.text || repo.dept_name;
                };
JS;
                        $this->registerJs($formatJs, \yii\web\View::POS_HEAD);

                        //have to Display searchable drop down
                        $url = \yii\helpers\Url::to(['trn-customer/get-test-list']);
                        echo Select2::widget([
                            'name' => 'test_name',
                            'initValueText' => '',
                            'value' => '',
                            'options' => ['class' => 'open-on-focus', 'placeholder' => 'Search the Test', 'id' => 'test_name', 'onchange' => 'update_others(this.value)'],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 2,
                                'ajax' => [
                                    'url' => $url,
                                    'dataType' => 'json',
                                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                ],
                                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                'templateResult' => new JsExpression('formatRepo'),
                                'templateSelection' => new JsExpression('formatRepoSelection'),
                            ],
                        ]);
                        echo $form->field($model, 'mst_department_id')->hiddenInput([])->label(false);
                        echo $form->field($model, 'mst_test_id')->hiddenInput([])->label(false);
                        echo $form->field($model, 'mst_sub_test_id')->hiddenInput([])->label(false);
                        echo $form->field($model, 'test_name')->hiddenInput([])->label(false);
                        echo $form->field($model, 'sub_test_name')->hiddenInput([])->label(false);
                        echo $form->field($model, 'dept_name')->hiddenInput([])->label(false);
                        ?>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                            <div class="col-sm-12">
                                <?php
                                echo Html::button('add', ['class' => 'btn btn-primary', 'onclick' => 'add_test()']);
                                ?></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="col-sm-6">UOM</label>

                            <div class="col-sm-6">
                                <?php $model->is_return = 0;
                                echo Html::dropDownList('uom','',array(),['class'=>'from-control']) ?></div>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="col-sm-6">Rate</label>
                            <div class="col-sm-6">
                                <?php
                                    echo Html::textInput('rate','',['class'=>'form-control']);
                                ?>
                                <span id="witness_date" style="display: none;">
                                <?php echo $form->field($model, 'witness_date')->widget(\kartik\widgets\DateTimePicker::className(),
                                    ['type' => DatePicker::TYPE_INPUT,
                                        'pluginOptions' => [
                                            'autoclose' => true,
//                                            'format' => 'dd-mm-yyyy'
                                        ]
                                    ])->label(false) ?>
                            </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="col-sm-3">Remarks</label>

                            <div class="col-sm-9">
                                <?php echo $form->field($model, 'remarks')->textInput()->label(false); ?></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="col-sm-4">Report No</label>

                            <div class="col-sm-8">
                                <div class="row" style="display: ruby;">
                                    <?php echo $form->field($model, 'report_no_from')->textInput(['class' => 'col-sm-6 form-control', 'style' => 'width:80px'])->label(false); ?>
                                    <?php echo $form->field($model, 'report_no_to')->textInput(['class' => 'col-sm-6 form-control', 'style' => 'width:80px'])->label(false); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="form-group">
                            <label class="col-sm-3">Sample Photo</label>

                            <div class="col-sm-9">
                                <?php echo $form->field($model, 'sample_photo_path')->widget(FileInput::className(), ['pluginOptions' => [
                                    'showUpload' => false,
                                    'showPreview' => false,
                                    'browseLabel' => '',
                                    'removeLabel' => '',
                                    'mainClass' => 'input-group-lg'
                                ]])->label(false);
                                ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="col-sm-4">TOCR Number </label>

                    <div class="col-sm-8">
                        <?php
                        echo $form->field($model, 'tocr_number')->textInput()->label(false);
                        echo $form->field($model, 'tocr_number')->hiddenInput(['name' => 'TrnTestDetails[tocr_number_1]'])->label(false);
                        ?></div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="col-sm-4">Package</label>

                    <div class="col-sm-8"> <?php
                        echo Html::dropDownList('mst_packges', '', $package, ['class' => 'form-control', 'prompt' => '-- Select Package --', 'onchange' => 'load_package_items(this.value)']);
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="box box-success box-solid">
                    <div class="box-header">
                        <h3 class="box-title">Goods Details </h3>
                    </div>
                    <div class="box-body table-responsive no-padding table-responsive">
                        <table id="test_list" class="table table-bordered table-hover table-sripped" width="100%">
                            <thead>
                            <tr class="header_row">
                                <th>#</th>
                                <th width="120px">Item Name</th>
                                <th width="120px">Part No</th>
                                <th width="120px">HSN Code</th>
                                <th width="120px">Qty</th>
                                <th>UOM</th>
                                <th>Rate</th>
                                <th width="80px">Action</th>
                                <th width="80px">ReArrange</th>
                            </tr>
                            </thead>
                            <tbody id="tbody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-sm-4">
                <div class="form-group">
                    <label class="col-sm-4">Sample Detail</label>

                    <div class="col-sm-8">
                        <?php echo $form->field($model, 'sample_details')->textInput(['name' => 'sample_details'])->label(false); ?></div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group">
                    <label class="col-sm-4">Heat No</label>

                    <div class="col-sm-8">
                        <?php echo $form->field($model, 'heat_no')->textInput()->label(false); ?></div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group">
                    <label class="col-sm-4">Sample ID</label>

                    <div class="col-sm-8">
                        <?php echo $form->field($model, 'sample_id')->textInput()->label(false); ?></div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-sm-12">
                    <?php echo '<center>' . Html::submitButton(' Save ', ['class' => 'btn btn-primary']) . '</center>'; ?>
                    <?php echo '<div class="hidden1" id="div"></div>'; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$form->end();
$js = <<<'JS'
    $("#tbody").on( "sort", function( event, ui ) { reset_serial_number(); } );

$(document).ready(function() {
    $(document).keypress(function(e) {
        if(e.keyCode == 113) {
            add_test();
        }
        if(e.keyCode == 120) {
        }
    });
});
JS;
$this->registerJs($js, \yii\web\View::POS_READY);
if (Yii::$app->session->has('tocr_print')) {
    $path = Yii::$app->session->get('tocr_print');
    unset(Yii::$app->session['tocr_print']);
    echo $link = Html::a(' ', $path, ['id' => 'print_link', 'target' => '_blank']);
    $js = <<<JS
    document.getElementById('print_link').click();
JS;
    $this->registerJs($js, \yii\web\View::POS_READY);

}
?>
<div id="popup" class="modal fade" role="dialog" data-backdrop="true" data-keyboard="true">
    <div id="popup_size" class="modal-dialog">
        <div class="modal-content" style=" background-color: #ecf0f5;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="smaller lighter blue no-margin">
                    <center><span id="popup_title"></span></center>
                </h3>
            </div>
            <div class="modal-body row">
                <div class="col-sm-12 " id="popup_data"></div>
            </div>
        </div>
    </div>
</div>

<script>
    var i = 100;
    var j = 1;

    function validate_necessary() {
        if ($('#trntestdetails-tocr_number').val().trim() == '') {
            $('#trntestdetails-tocr_number').focus();
            swal('Enter the TOCR Number', '', 'error');
            return false;
        } else if ($('#trntestdetails-sample_details').val().trim() == '') {
            $('#trntestdetails-sample_details').focus();
            swal('Enter Sample Details', '', 'error');
            return false;
        } else if ($('#trntestdetails-heat_no').val().trim() == '') {
            $('#trntestdetails-heat_no').focus();
            swal('Enter Heat Number', '', 'error');
            return false;
        } else if ($('#trntestdetails-sample_id').val().trim() == '') {
            $('#trntestdetails-sample_id').focus();
            swal('Enter Sample ID', '', 'error');
            return false;
        } else if ($('#trntestdetails-mst_test_id').val().trim() == '') {
            focus();
            swal('select the Test', '', 'error');
            return false;
        } else if ($('#trntestdetails-mst_department_id').val().trim() == '') {
            $('#trntestdetails-department_id').focus();
            swal('select the department', '', 'error');
            return false;
        } else {
            return true;
        }
    }

    function add_test() {
        if (validate_necessary()) {
            var test_id = $('#trntestdetails-mst_test_id').val();
            var sub_test_id = $('#trntestdetails-mst_sub_test_id').val();
            var dept_id = $('#trntestdetails-mst_department_id').val();
            var test_name = $('#trntestdetails-test_name').val();
            var dept_name = $('#trntestdetails-dept_name').val();
            var sub_test_name = $('#trntestdetails-sub_test_name').val();
            var sample_details = $('#trntestdetails-sample_details').val();
            var sample_id = $('#trntestdetails-sample_id').val();
            var heat_no = $('#trntestdetails-heat_no').val();

            i = i + 1;
            s_id = $('<input>').attr({
                type: 'text',
                name: 'TrnTestDetails[sample_id][' + i + ']',
                value: sample_id,
                style: 'width:80px'
            })[0].outerHTML;
            s_detail = $('<input>').attr({
                type: 'text',
                name: 'TrnTestDetails[sample_details][' + i + ']',
                value: sample_details,
                style: 'width:80px'
            })[0].outerHTML;
            h_no = $('<input>').attr({
                type: 'text',
                name: 'TrnTestDetails[heat_no][' + i + ']',
                value: heat_no,
                style: 'width:80px'
            })[0].outerHTML;

            d_id = $('<input>').attr({
                type: 'hidden',
                name: 'TrnTestDetails[mst_department_id][' + i + ']',
                value: dept_id
            })[0].outerHTML;
            d_name = $('<input>').attr({
                type: 'text',
                name: 'TrnTestDetails[dept_name][' + i + ']',
                value: dept_name,
                style: 'width:100px'
            })[0].outerHTML;
            t_id = $('<input>').attr({
                type: 'hidden',
                name: 'TrnTestDetails[mst_test_id][' + i + ']',
                value: test_id
            })[0].outerHTML;
            t_name = $('<input>').attr({
                type: 'text',
                name: 'TrnTestDetails[test_name][' + i + ']',
                value: test_name,
                style: 'width:120px'
            })[0].outerHTML;
            s_t_id = $('<input>').attr({
                type: 'hidden',
                name: 'TrnTestDetails[mst_sub_test_id][' + i + ']',
                value: sub_test_id
            })[0].outerHTML;
            s_t_name = $('<input>').attr({
                type: 'text',
                name: 'TrnTestDetails[sub_test_name][' + i + ']',
                value: sub_test_name,
                style: 'width:120px'
            })[0].outerHTML;
            $('#tbody').append('<tr><td class="row_number">' + j + '</td><td>' + s_detail + '</td><td>' + s_id + '</td><td>' + h_no + '</td><td>' + d_id + '' + d_name + '</td><td>' + t_id + '' + t_name + '</td><td>' + s_t_id + '' + s_t_name + '</td><td><a onclick="remove_this(this)" href="javascript:void(0)"><li class="fa fa-close close-icn"></li></a></td><td><i class="fa fa-arrows" style="cursor: pointer; font-size: 20px"></i></td></tr>');
            j++;
            reset_serial_number();
            $("#tbody").sortable({refresh: tbody});
            clear_row();
        }
    }

    function load_package_items(package_id) {
        var j = 1;
        var k = 500;
        clear_grid();

        if (package_id) {
            $.ajax({
                url: '<?php echo Url::to(['mst-package/load-package-items']); ?>',
                type: 'post',
                data: {package_id: package_id},
                success: function (data) {
                    // Process the Items
                    for (var i = 0; i < data.length; i++) {
                        k = k + 1;
                        var s_id = $('<input>').attr({
                            type: 'text',
                            name: 'TrnTestDetails[sample_id][' + k + ']',
                            value: data[i].sample_id,
                            style: 'width:80px'
                        })[0].outerHTML;
                        var s_detail = $('<input>').attr({
                            type: 'text',
                            name: 'TrnTestDetails[sample_details][' + k + ']',
                            value: data[i].sample_details,
                            style: 'width:80px'
                        })[0].outerHTML;
                        var h_no = $('<input>').attr({
                            type: 'text',
                            name: 'TrnTestDetails[heat_no][' + k + ']',
                            value: data[i].heat_no,
                            style: 'width:80px'
                        })[0].outerHTML;

                        var d_id = $('<input>').attr({
                            type: 'hidden',
                            name: 'TrnTestDetails[mst_department_id][' + k + ']',
                            value: data[i].mst_department_id
                        })[0].outerHTML;
                        var d_name = $('<input>').attr({
                            type: 'text',
                            name: 'TrnTestDetails[dept_name][' + k + ']',
                            value: data[i].dept_name,
                            style: 'width:100px'
                        })[0].outerHTML;
                        var t_id = $('<input>').attr({
                            type: 'hidden',
                            name: 'TrnTestDetails[mst_test_id][' + k + ']',
                            value: data[i].mst_test_id
                        })[0].outerHTML;
                        var t_name = $('<input>').attr({
                            type: 'text',
                            name: 'TrnTestDetails[test_name][' + k + ']',
                            value: data[i].test_name,
                            style: 'width:120px'
                        })[0].outerHTML;
                        var s_t_id = $('<input>').attr({
                            type: 'hidden',
                            name: 'TrnTestDetails[mst_sub_test_id][' + k + ']',
                            value: data[i].mst_sub_test_id
                        })[0].outerHTML;
                        var s_t_name = $('<input>').attr({
                            type: 'text',
                            name: 'TrnTestDetails[sub_test_name][' + k + ']',
                            value: data[i].sub_test_name,
                            style: 'width:120px'
                        })[0].outerHTML;
                        $('#tbody').append('<tr><td class="row_number">' + j + '</td><td>' + s_detail + '</td><td>' + s_id + '</td><td>' + h_no + '</td><td>' + d_id + '' + d_name + '</td><td>' + t_id + '' + t_name + '</td><td>' + s_t_id + '' + s_t_name + '</td><td><a onclick="remove_this(this)" href="javascript:void(0)"><li class="fa fa-close close-icn"></li></a></td><td><i class="fa fa-arrows" style="cursor: pointer; font-size: 20px"></i></td></tr>');
                        j++;
                        reset_serial_number();
                        $("#tbody").sortable({refresh: tbody});
                    }
                },
                error: function (data) {
                    swal('Please try Later', '', 'error');
                }
            });
        }
    }

    function clear_grid() {
        $('#tbody').html('');
    }

    function remove_this(r) {
        var i = r.parentNode.parentNode.rowIndex;
        document.getElementById("test_list").deleteRow(i);
        reset_serial_number();
    }

    function reset_serial_number1() {
        var j = 1;
        $('#test_list tr').not('.header_row').each(function () {
            if ($(this).is(':visible')) {
                var x = $(this).filter('.row_number');
                var samp = x.prevObject[0].cells[1];
                alert(samp.val());
                x.prevObject[0].cells[0].innerHTML = j++;
            }
        });
    }

    function reset_serial_number() {
        var i = 1;
        var j = 1;
        var pre_val = '';
        $("input[name^='TrnTestDetails[sample_details]']").each(function () {
            if (i != 1 || pre_val != $(this).val()) {
                $(this).parent().prev('td').html(j++);
            } else {
                $(this).parent().prev('td').html('');
            }
            pre_val = $(this).val();
//            alert($(this).parent().prev('td').html());
        });
    }

    function check_witness() {
        var val = $('input[name="TrnTestDetails[is_need_witness]"]:checked').val();
        if (val == 1) {
            $("#witness_date").show();
            $('#trntestdetails-witness_date').focus();
        } else {
            $('#witness_date').hide();
        }
    }

    function update_others(val) {
        var ids = val.split('~');
        var test_id = ids[0];
        var sub_test_id = ids[1];
        $.ajax({
            type: "POST",
            url: '<?php echo Url::to(['trn-customer/get-test-details']) ?>',
            data: {test_id: test_id, sub_test_id: sub_test_id},
            beforeSend: function () {

            },
            success: function (data) {
                $('#trntestdetails-mst_department_id').val(data.dept_id);
                $('#trntestdetails-mst_test_id').val(data.test_id);
                $('#trntestdetails-mst_sub_test_id').val(data.sub_test_id);
                $('#trntestdetails-test_name').val(data.test_name);
                $('#trntestdetails-sub_test_name').val(data.sub_test_name);
                $('#trntestdetails-dept_name').val(data.dept_name);
            }
        });
    }

    function clear_row() {
        $('#trntestdetails-mst_department_id').val('');
        $('#trntestdetails-mst_test_id').val('');
        $('#trntestdetails-mst_sub_test_id').val('');
        $('#trntestdetails-test_name').val('');
        $('#trntestdetails-sub_test_name').val('');
        $('#trntestdetails-dept_name').val('');
        var v = document.getElementById('select2-test_name-container');
        document.getElementById('select2-test_name-container').innerHTML = '';
        var node = document.createElement("span");
        node.setAttribute('class', 'select2-selection__placeholder');
        node.innerHTML = 'Select the Test';
        v.appendChild(node);
        focus();
    }

    function focus() {
        jQuery(function ($) {
            $('#test_name').select2('open');
        });
    }

    function validate_form() {
        var cust_id = $('#trntestdetails-mst_customer_id').val();
        var tocr = $('#trntestdetails-tocr_number').val();
        var date = $('#trntestdetails-assign_date').val();
        var witness = $('input[name="TrnTestDetails[is_need_witness]"]:checked').val();
        var witness_date = $('#trntestdetails-witness_date').val();
        var report_no_from = $('#trntestdetails-report_no_from').val();
        var report_no_to = $('#trntestdetails-report_no_to').val();
        var len = $('#tbody >tr').length;
        if (cust_id == '') {
            swal('Select the Customer', '', 'error');
            return false;
        } else if (tocr == '') {
            swal('Enter TOCR Number', '', 'error');
            return false;
        } else if (date.trim() == '') {
            swal('Enter the Date', '', 'error');
            return false;
        } else if (witness == 1 && witness_date == '') {
            swal('Enter the Witness Date', '', 'error');
            return false;
        } else if (report_no_from == '' || report_no_to == '') {
            swal('Enter Report From Number & Report to Number', '', 'error');
            return false;
        }/*else if(report_no_from.trim()>report_no_to.trim()) {
         swal('Please check the Report Number');
         return false;
         }*/ else if (len == 0) {
            swal('Please Add at-least one Test');
            return false;
        } else {
            return true;
        }
    }
</script>