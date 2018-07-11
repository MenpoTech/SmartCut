<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\widgets\ActiveForm;
use kartik\file\SortableAsset;

$this->title = "Edit Package";
SortableAsset::register($this);

echo Html::beginForm(Url::to(['mst-package/update-package']));
if(!empty($package))  {
    $form = ActiveForm::begin([
        'id' => 'entry-form',
        'action' => ['mst-package/update-package'],
        'validationUrl' => ['trn-customer/validate-entry-form'],
        'validateOnSubmit' => false,
        'enableAjaxValidation' => false,
        'options'=>['enctype'=>'multipart/form-data','onsubmit'=>'return validate_form()'],
    ]);

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
    ?>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title bolder">
                <i class="glyphicon glyphicon-list"></i> Package Items
            </h3>
        </div>

        <div class="kv-grid-container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="clearfix">&nbsp;</div>
                    <div class="form-group">
                        <label class="col-sm-2">Test Name</label>
                        <div class="col-sm-8">
                            <?php
                            $url = \yii\helpers\Url::to(['trn-customer/get-test-list']);
                            echo Select2::widget([
                                'name' => 'test_name',
                                'initValueText' => '',
                                'value' => '',
                                'options' => ['class'=>'open-on-focus','placeholder' => 'Search the Test', 'id' => 'test_name', 'onchange' => 'update_others(this.value)'],
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
                            echo $form->field($model,'mst_department_id')->hiddenInput([])->label(false);
                            echo $form->field($model,'mst_test_id')->hiddenInput([])->label(false);
                            echo $form->field($model,'mst_sub_test_id')->hiddenInput([])->label(false);
                            echo $form->field($model,'test_name')->hiddenInput([])->label(false);
                            echo $form->field($model,'sub_test_name')->hiddenInput([])->label(false);
                            echo $form->field($model,'dept_name')->hiddenInput([])->label(false);
                            ?>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <?php
                                    echo Html::button('add',['class'=>'btn btn-primary','onclick'=>'add_test()']);
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
                            <h3 class="box-title">Test Details </h3>
                        </div>
                        <div class="box-body table-responsive no-padding table-responsive">
                            <table id="test_list" class="table table-bordered table-hover table-sripped" width="100%">
                                <thead>
                                <tr class="header_row">
                                    <th>#</th>
                                    <th width="120px">Dept Name</th>
                                    <th>Test Name</th>
                                    <th>Sub Test Name</th>
                                    <th>Sample Details</th>
                                    <th width="120px">Heat No</th>
                                    <th width="120px">Sample ID</th>
                                    <th width="80px">Action</th>
                                    <th width="80px">ReArrange</th>
                                </tr>
                                </thead>
                                <tbody id="tbody">
                                <?php
                                echo Html::hiddenInput('mst_package_id',$package['id']);
                                if(!empty($details)) {
                                    $sno=1;
                                    foreach($details as $val) {
                                        echo '<tr>
                                                <td>'.$sno++.'</td>
                                                <td>'.Html::hiddenInput('data['.$sno.'][mst_department_id]',$val['mst_department_id']).''.Html::textInput('data['.$sno.'][dept_name]',$val['dept_name'],['style'=>'width:100px','class'=>'form-control','alt'=>'data[dept_name]']).'</td>
                                                <td>'.Html::hiddenInput('data['.$sno.'][mst_test_id]',$val['mst_test_id']).''.Html::textInput('data['.$sno.'][test_name]',$val['test_name'],['style'=>'width:120px','class'=>'form-control']).'</td>
                                                <td>'.Html::hiddenInput('data['.$sno.'][mst_sub_test_id]',$val['mst_sub_test_id']).''.Html::textInput('data['.$sno.'][sub_test_name]',$val['sub_test_name'],['style'=>'width:120px','class'=>'form-control']).'</td>
                                                <td>'.Html::textInput('data['.$sno.'][sample_details]',$val['sample_details'],['style'=>'width:80px','class'=>'form-control']).'</td>
                                                <td>'.Html::textInput('data['.$sno.'][heat_no]',$val['heat_no'],['style'=>'width:80px','class'=>'form-control']).'</td>
                                                <td>'.Html::textInput('data['.$sno.'][sample_id]',$val['sample_id'],['style'=>'width:80px','class'=>'form-control']).'</td>
                                                <td><a onclick="remove_this(this)" href="javascript:void(0)"><li class="fa fa-close close-icn"></li></a></td>
                                                <td><i class="fa fa-arrows" style="cursor: pointer; font-size: 20px"></i></td>
                                            </tr>';
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <?php echo '<center>'.Html::submitButton(' Save ',['class'=>'btn btn-primary']).'</center>'; ?>
                </div>
            </div>
        </div>
    </div>

<?php
$form->end();
$js=<<<'JS'
$("#tbody").on( "sort", function( event, ui ) { reset_serial_number(); } );

$(document).ready(function() {
    $(document).keypress(function(e) {
        if(e.keyCode == 113) {
            add_test();
        }
        if(e.keyCode == 120) {
        }
    });

    $("#tbody").sortable({refresh: tbody});
    reset_serial_number();
});

JS;

$this->registerJs($js,\yii\web\View::POS_READY);
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
        var i=500;
        var j=1;

        function validate_necessary() {
            if($('#trntestdetails-mst_test_id').val().trim()=='') {
                focus(); swal('Select the Test','','error'); return false;
            }else {
                return true;
            }
        }

        function add_test() {
            if(validate_necessary()) {
                var test_id= $('#trntestdetails-mst_test_id').val();
                var sub_test_id= $('#trntestdetails-mst_sub_test_id').val();
                var dept_id= $('#trntestdetails-mst_department_id').val();
                var test_name= $('#trntestdetails-test_name').val();
                var dept_name= $('#trntestdetails-dept_name').val();
                var sub_test_name= $('#trntestdetails-sub_test_name').val();
//                var sample_details= $('#trntestdetails-sample_details').val();
//                var sample_id= $('#trntestdetails-sample_id').val();
//                var heat_no= $('#trntestdetails-heat_no').val();

                i= i+1;
                s_id= $('<input>').attr({type: 'text',name: 'data['+i+'][sample_id]',value: '',style:'width:80px', class : 'form-control'})[0].outerHTML;
                s_detail= $('<input>').attr({type: 'text',name: 'data['+i+'][sample_details]',value: '',style:'width:80px', class : 'form-control'})[0].outerHTML;
                h_no= $('<input>').attr({type: 'text',name: 'data['+i+'][heat_no]',value: '',style:'width:80px', class : 'form-control'})[0].outerHTML;

                d_id = $('<input>').attr({type: 'hidden',name: 'data['+i+'][mst_department_id]',value: dept_id})[0].outerHTML;
                d_name = $('<input>').attr({type: 'text',name: 'data['+i+'][dept_name]',value: dept_name,style:'width:100px', class : 'form-control', alt :'data[dept_name]'})[0].outerHTML;
                t_id = $('<input>').attr({type: 'hidden',name: 'data['+i+'][mst_test_id]',value: test_id})[0].outerHTML;
                t_name = $('<input>').attr({type: 'text',name: 'data['+i+'][test_name]',value: test_name,style:'width:120px', class : 'form-control'})[0].outerHTML;
                s_t_id = $('<input>').attr({type: 'hidden',name: 'data['+i+'][mst_sub_test_id]',value: sub_test_id})[0].outerHTML;
                s_t_name = $('<input>').attr({type: 'text',name: 'data['+i+'][sub_test_name]',value: sub_test_name,style:'width:120px', class : 'form-control'})[0].outerHTML;
                $('#tbody').append('' +
                    '<tr>' +
                    '<td class="row_number">'+j+'</td>' +
                    '<td>'+d_id+''+d_name+'</td>' +
                    '<td> '+t_id+''+t_name+'</td>' +
                    '<td>'+s_t_id+''+s_t_name+'</td>' +
                    '<td>'+s_detail+'</td>' +
                    '<td>'+h_no+'</td>' +
                    '<td>'+s_id+'</td>' +
                    '<td><a onclick="remove_this(this)" href="javascript:void(0)">' +'<li class="fa fa-close close-icn"></li></a></td>' +
                    '<td><i class="fa fa-arrows" style="cursor: pointer; font-size: 20px"></i></td>' +
                    '</tr>');
                j++;
                reset_serial_number();
                $("#tbody").sortable({refresh: tbody});
                clear_row();
            }
        }

        function remove_this(r) {
            var i = r.parentNode.parentNode.rowIndex;
            document.getElementById("test_list").deleteRow(i);
            reset_serial_number();
        }

        function reset_serial_number1() {
            var j=1;
            $('#test_list tr').not('.header_row').each(function() {
                if($(this).is(':visible')) {
                    var x =$(this).filter('.row_number');
                    var samp = x.prevObject[0].cells[1];
                    alert(samp.val());
                    x.prevObject[0].cells[0].innerHTML = j++;
                }
            });
        }

        function reset_serial_number() {
            var j=1;
            $("input[alt^='data[dept_name]']").each(function() {
                $(this).parent().prev('td').html(j++);
            });
        }

        function update_others(val) {
            var ids = val.split('~');
            var test_id = ids[0];
            var sub_test_id = ids[1];
            $.ajax({
                type: "POST",
                url: '<?php echo Url::to(['trn-customer/get-test-details']) ?>',
                data: {test_id: test_id,sub_test_id:sub_test_id},
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
            jQuery(function($) {
                $('#test_name').select2('open');
            });
        }

        function validate_form() {
            var cust_id= $('#trntestdetails-mst_customer_id').val();
            var tocr= $('#trntestdetails-tocr_number').val();
            var date= $('#trntestdetails-assign_date').val();
            var witness = $('input[name="TrnTestDetails[is_need_witness]"]:checked').val();
            var witness_date= $('#trntestdetails-witness_date').val();
            var report_no_from = $('#trntestdetails-report_no_from').val();
            var report_no_to = $('#trntestdetails-report_no_to').val();
            var len = $('#tbody >tr').length;
            if(cust_id=='') {
                swal('Select the Customer','','error');
                return false;
            }else if(tocr=='') {
                swal('Enter TOCR Number','','error');
                return false;
            }else if(date.trim()=='') {
                swal('Enter the Date','','error');
                return false;
            }else if(witness==1 && witness_date=='') {
                swal('Enter the Witness Date','','error');
                return false;
            }else if(report_no_from=='' || report_no_to=='') {
                swal('Enter Report From Number & Report to Number','','error');
                return false;
            }else if(report_no_from>report_no_to) {
                swal('Please check the Report Number');
                return false;
            }else if(len==0) {
                swal('Please Add at-least one Test');
                return false;
            }else{
                return true;
            }
        }
    </script>
    <?php
}
?>