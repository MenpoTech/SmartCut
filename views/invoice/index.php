<?php
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use app\models\MstCustomers;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\widgets\FileInput;

/**
 * Created by PhpStorm.
 * User: MuraliDharan
 * Date: 16/06/2018
 * Time: 11:54 AM
 */

$this->title = 'Invoice Entry';

$customers = ArrayHelper::map(MstCustomers::find()->where(['status' => 1])->all(), 'id', 'customer_name');
$form = ActiveForm::begin([
    'id' => 'entry-form',
    'action' => ['invoice/save-invoice'],
    'validationUrl' => ['invoice/validate-invoice-form'],
    'validateOnSubmit' => false,
    'enableAjaxValidation' => false,
    'options' => ['enctype' => 'multipart/form-data', 'onsubmit' => 'return validate_form()'],
]);


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
        <div class="clearfix"></div>
        <div class="row clearfix">
            <div class="col-sm-12"><br/>
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
                                class="col-sm-8"> <?php echo $form->field($model, 'bill_date')->widget(DatePicker::className(),
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
                <div class="form-group clearfix">
                    <label class="col-sm-1">Item Name</label>

                    <div class="col-sm-5">
                        <?php
                        $formatJs = <<< 'JS'
                var formatRepo = function (repo) {
                    if (repo.loading) {
                        return repo.text;
                    }
                    var markup = '<div class="row">' +'<div class="col-sm-12">' +'' + repo.text + '</div></div>';
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
                            'name' => 'mst_item_id',
                            'initValueText' => '',
                            'value' => '',
                            'options' => ['class' => 'open-on-focus', 'placeholder' => 'Search the Item', 'id' => 'mst_item_id', 'onchange' => 'update_others(this.value)'],
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
                        echo Html::hiddenInput('item_name','',['id'=>'item_name']);
                        ?>
                    </div>

                    <div class="col-sm-1 less-padding">
                        <div class="form-group">
                            <div class="col-sm-12 less-padding">
                                <?php echo Html::dropDownList('uom','',$uom,['class'=>'form-control','id'=>'uom']); ?></div>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="col-sm-4">Part No</label>
                            <div class="col-sm-8 less-padding">
                                <?php
                                echo Html::textInput('part_no','',['class'=>'form-control','id'=>'part_no']);
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="col-sm-4">HSN Code</label>
                            <div class="col-sm-8">
                                <div class="row">
                                    <?php
                                    echo Html::textInput('hsn_code','',['class'=>'form-control','id'=>'hsn_code']);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="col-sm-4">Qty</label>
                            <div class="col-sm-8">
                                <?php
                                    echo Html::textInput('qty',1,['class'=>'form-control','id'=>'qty','onkeyup'=>'update_total()']);
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="col-sm-4">Unit Rate</label>
                            <div class="col-sm-8 less-padding">
                                <?php
                                    echo Html::textInput('unit_amount','',['class'=>'form-control','id'=>'unit_amount','onkeyup'=>'update_total()']);
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="col-sm-4">Total</label>
                            <div class="col-sm-8 less-padding">
                                <?php
                                    echo Html::textInput('amount','',['readonly'=>true,'class'=>'form-control','id'=>'amount','onkeyup'=>'update_total()']);
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="col-sm-4">TAX</label>
                            <div class="col-sm-8 less-padding">
                                <?php
                                    echo Html::dropDownList('tax_id',NULL,$gst,['class'=>'form-control','id'=>'mst_tax_id','onchange'=>'update_total()']);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="col-sm-4">Tax Amount</label>
                            <div class="col-sm-8 less-padding">
                                <?php
                                    echo Html::hiddenInput('tax_percent',0,['class'=>'form-control','id'=>'tax_percent']);
                                    echo Html::textInput('tax_amount',0,['class'=>'form-control','id'=>'tax_amount']);
                                ?>
                            </div>
                        </div>
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
                                <th width="10px">#</th>
                                <th width="250px">Item Name</th>
                                <th width="80px">Part No</th>
                                <th width="80px">HSN Code</th>
                                <th width="20px">Qty</th>
                                <th width="65px">Unit Price</th>
                                <th width="65px">Total</th>
                                <th width="20px">Tax</th>
                                <th width="65px">Tax(<i class="fa fa-rupee"></i>)</th>
                                <th width="20px">UOM</th>
                                <th width="8px">Action</th>
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
                        <?php //echo $form->field($model, 'sample_details')->textInput(['name' => 'sample_details'])->label(false); ?></div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group">
                    <label class="col-sm-4">Heat No</label>

                    <div class="col-sm-8">
                        <?php //echo $form->field($model, 'heat_no')->textInput()->label(false); ?></div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group">
                    <label class="col-sm-4">Sample ID</label>

                    <div class="col-sm-8">
                        <?php //echo $form->field($model, 'sample_id')->textInput()->label(false); ?></div>
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
    var tax = <?php echo json_encode($tax); ?>;

    function update_others(item_id) {
        $.ajax({
            type: "POST",
            url: '<?php echo Url::to(['invoice/get-item-details']) ?>',
            data: {item_id :item_id},
            beforeSend: function () {
            },
            success: function (data) {
                var select = document.getElementById('uom');
                select.value = data['mst_uom_id'];
                $('#unit_amount').val(data['amount']);
                $('#part_no').val(data['part_no']);
                $('#hsn_code').val(data['hsn_code']);
                $('#item_name').val(data['item_name']);
                update_total();
            }
        });
    }

    function validate_necessary() {
        if ($('#trnbillheaders-mst_customer_id').val().trim() == '') {
            $('#trnbillheaders-mst_customer_id').focus();
            swal('Select the Customer', '', 'error');
            return false;
        } else if ($('#trnbillheaders-bill_date').val().trim() == '') {
            $('#trnbillheaders-bill_date').focus();
            swal('Select the Date', '', 'error');
            return false;
        } else if ($('#mst_item_id').val().trim() == '') {
            $('#mst_item_id').focus();
            swal('Select the Item', '', 'error');
            return false;
        } else if ($('#uom').val().trim() == '') {
            $('#uom').focus();
            swal('Select the UOM', '', 'error');
            return false;
        } else if ($('#part_no').val().trim() == '') {
            swal('Enter Part No', '', 'error');
            return false;
        } else if ($('#hsn_code').val().trim() == '') {
            $('#hsn_code').focus();
            swal('Enter HSN Code', '', 'error');
            return false;
        } else if ($('#qty').val().trim() == '') {
            $('#qty').focus();
            swal('Enter Quantity', '', 'error');
            return false;
        } else if ($('#amount').val().trim() == '') {
            $('#amount').focus();
            swal('Amount Can\'t be Empty', '', 'error');
            return false;
        } else if ($('#qty').val().trim() <=0) {
            $('#qty').focus();
            swal('Quantity Must be Greater than or Equal 1', '', 'error');
            return false;
        } else {
            return true;
        }
    }

    function add_test() {
        if (validate_necessary()) {
            var item_id = $('#mst_item_id').val();
            var uom = $('#uom').val();
            var uom_name = $("#uom option:selected").html();
            var item_name = $('#item_name').val();
            var part_no = $('#part_no').val();
            var hsn_code = $('#hsn_code').val();
            var unit_amount = $('#unit_amount').val();
            var amount = $('#amount').val();
            var qty = $('#qty').val();
            var tax_id = $('#mst_tax_id').val();
            var tax_amount = $('#tax_amount').val();
            var tax_percent = $('#tax_percent').val();

            i = i + 1;
            item_id = $('<input>').attr({
                type: 'hidden',
                name: 'TrnBillDetails[mst_item_id][' + i + ']',
                value: item_id
            })[0].outerHTML;

            item_name = $('<input>').attr({
                type: 'text',
                name: 'TrnBillDetails[item_name][' + i + ']',
                value: item_name,
                class:'form-control',
                readonly : 'readonly',
            })[0].outerHTML;

            uom_name = $('<input>').attr({
                type: 'text',
                name: 'TrnBillDetails[uom_name][' + i + ']',
                value: uom_name,
                class:'form-control',
                readonly : 'readonly',
            })[0].outerHTML;

            uom = $('<input>').attr({
                type: 'hidden',
                name: 'TrnBillDetails[mst_uom_id][' + i + ']',
                value: uom,
                class:'form-control'
            })[0].outerHTML;

            qty = $('<input>').attr({
                type: 'text',
                name: 'TrnBillDetails[qty][' + i + ']',
                class:'form-control',
                value: qty,
                readonly : 'readonly',
            })[0].outerHTML;

            unit_amount = $('<input>').attr({
                type: 'text',
                name: 'TrnBillDetails[unit_amount][' + i + ']',
                value: unit_amount,
                class:'form-control text-right',
                readonly:'readonly',
            })[0].outerHTML;

            amount = $('<input>').attr({
                type: 'text',
                name: 'TrnBillDetails[total_amount][' + i + ']',
                class:'form-control text-right',
                value: amount,
                readonly : 'readonly',
            })[0].outerHTML;

            part_no = $('<input>').attr({
                type: 'text',
                name: 'TrnBillDetails[part_no][' + i + ']',
                class:'form-control',
                value: part_no,
                readonly : 'readonly',
            })[0].outerHTML;

            hsn_code = $('<input>').attr({
                type: 'text',
                name: 'TrnBillDetails[hsn_code][' + i + ']',
                class:'form-control',
                value: hsn_code,
                readonly : 'readonly',
            })[0].outerHTML;

            tax_id = $('<input>').attr({
                type: 'hidden',
                name: 'TrnBillDetails[mst_tax_id][' + i + ']',
                class:'form-control',
                value: tax_id,
                readonly : 'readonly',
            })[0].outerHTML;

            tax_percent = $('<input>').attr({
                type: 'hidden',
                name: 'TrnBillDetails[tax_percent][' + i + ']',
                class:'form-control',
                value: tax_percent,
                readonly : 'readonly',
            })[0].outerHTML;

            tax_amount = $('<input>').attr({
                type: 'text',
                name: 'TrnBillDetails[tax_amount][' + i + ']',
                class:'form-control text-right',
                value: tax_amount,
                readonly : 'readonly',
            })[0].outerHTML;

            $('#tbody').append('' +
                '<tr>' +
                '<td class="row_number">' + j + '</td>' +
                '<td>' +''+item_id + item_name + '</td>' +
                '<td>' + part_no+ '</td>' +
                '<td>' + hsn_code + '</td>' +
                '<td>' + qty + '</td>' +
                '<td>' + unit_amount + '</td>' +
                '<td>' + amount + '</td>' +
                '<td>' + tax_id+''+tax_percent + '</td>' +
                '<td>' + tax_amount + '</td>' +
                '<td>' + uom + '' + uom_name + '</td>' +
                '<td><a onclick="remove_this(this)" href="javascript:void(0)"><li class="fa fa-close close-icn"></li></a></td>' +
                '</tr>');
            j++;
            reset_serial_number();
            $("#tbody").sortable({refresh: tbody});
            clear_row();
        }
    }

    function update_total() {
        var qty = $('#qty').val();
        var unit = $('#unit_amount').val();
        var mst_tax_id  = $('#mst_tax_id').val();
        if(isNaN(qty) && isNaN(unit)) {
            swal('Enter Numeric values','Number Only','error');
        }else {
            var total = parseFloat(qty)*parseFloat(unit);
            var tax_percent = tax[mst_tax_id].tax_percent;
            var tax_amount = ((total/100)*tax_percent).toFixed(2);
            $('#amount').val(total.toFixed(2));
            $('#tax_amount').val(parseFloat(tax_amount));
            $('#tax_percent').val(parseFloat(tax_percent));
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

    function reset_serial_number() {
        var j = 1;
        $('#test_list tr').not('.header_row').each(function () {
            if ($(this).is(':visible')) {
                var x = $(this).filter('.row_number');
                var samp = x.prevObject[0].cells[1];
                x.prevObject[0].cells[0].innerHTML = j++;
            }
        });
    }

    function reset_serial_number1() {
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