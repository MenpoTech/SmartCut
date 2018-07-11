<?php
use yii\helpers\Url;
use yii2fullcalendar\yii2fullcalendar;
use johnitvn\ajaxcrud\CrudAsset;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use app\models\TrnSiteConfigs;

$this->title = "Dashboard";
CrudAsset::register($this);
?>
<section class="content">
    <div class="row">
        <div class="col-sm-6" style="max-height: 450px; overflow: auto;">

            <div id="assigned_test"></div>

            <?php //echo Yii::$app->runAction('/department/get-distinct-tocr-list',  ['type' => 'Assigned']);?>
        </div>
        <div class="col-sm-6" style="max-height: 450px; overflow: auto;">
            <div id="received_test"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6" style="max-height: 450px; overflow: auto;">
            <div id="witness_test"></div>
        </div>
        <div class="col-sm-6" style="max-height: 450px; overflow: auto;">
            <div id="completed_test"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Calendar </h3>
                </div>
                <div class="box body">
                    <div id="calendar">
                        <?= yii2fullcalendar::widget([
                            'options' => [
                                'lang' => 'en',
                            ],
//                            'events'=>[['title'=> 'All Day Event', 'start'=>'2017-05-01']],
                            'ajaxEvents' => Url::to(['department/get-calendar'])
                        ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
Modal::begin([
    "id"=>"ajaxCrudModal",
    "size"=>"modal-lg",
    "footer"=>'',
//    "footer"=>'<center>'.Html::button('close',['class'=>'btn btn-danger','data-dismiss'=>'modal']).'</center>',
//    'options' => [ 'tabindex' => true ],
]);
Modal::end();
?>

<script>
    function popup_detail(tocr_no ,status) {
        $("#ajaxCrudModal").modal('show');
        $('.modal-header').html('<span class="text-center bolder">TOCR : '+tocr_no+'</span>');
        $.ajax({
            url:'<?= Yii::$app->urlManager->createUrl(['department/get-tocr-details']);?>',
            type: 'post',
            data: {tocr_number:tocr_no, status: status},
            beforeSend: function () {
                $(".modal-body").html('Loading...');
            },
            success: function (data) {
                $('.modal-body').html(data);
            },
            error: function (data) {
                $('.modal-body').html('Error Occurred');
            }
        });
    }

    function load_assigned_test_details() {
        $.ajax({
            url:'<?= Yii::$app->urlManager->createUrl(['department/get-assigned-tocr-list']);?>',
            type: 'post',
            data: {type: 'Assigned'},
            beforeSend: function () {
                $("#assigned_test").html('Loading...');
            },
            success: function (data) {
                $('#assigned_test').html(data);
            },
            error: function (data) {
                $('#assigned_test').html('Error Occurred');
            }
        });
    }

    function load_received_test_details() {
        $.ajax({
            url:'<?= Yii::$app->urlManager->createUrl(['department/get-received-tocr-list']);?>',
            type: 'post',
            data: {type: 'Received'},
            beforeSend: function () {
                $("#received_test").html('Loading...');
            },
            success: function (data) {
                $('#received_test').html(data);
            },
            error: function (data) {
                $('#received_test').html('Error Occurred');
            }
        });
    }

    function load_witness_test_details() {
        $.ajax({
            url:'<?= Yii::$app->urlManager->createUrl(['department/get-witness-tocr-list']);?>',
            type: 'post',
            data: {type: 'Witness'},
            beforeSend: function () {
                $("#witness_test").html('Loading...');
            },
            success: function (data) {
                $('#witness_test').html(data);
            },
            error: function (data) {
                $('#witness_test').html('Error Occurred');
            }
        });
    }

    function completed_test_details() {
        $.ajax({
            url:'<?= Yii::$app->urlManager->createUrl(['department/get-completed-tocr-list']);?>',
            type: 'post',
            data: {type: 'Completed'},
            beforeSend: function () {
                $("#completed_test").html('Loading...');
            },
            success: function (data) {
                $('#completed_test').html(data);
            },
            error: function (data) {
                $('#completed_test').html('Error Occurred');
            }
        });
    }

    function receive_test(id,tocr_no='') {
//    Have to open popup to get the user PIN
        swal({
                title: '',
                text: "Enter the PIN : ",
                type: "input",
                inputType :'password',
                tabIndex:99,
                showCancelButton: true,
                closeOnConfirm: false,
                animation: "slide-from-top",
                inputPlaceholder: "Enter your Employee PIN Number"
            },
            function (inputValue) {
                if (inputValue === false) return false;

                if (inputValue.length != 4) {
                    swal.showInputError("You need to enter the 4 digit PIN");
                    return false
                }else {
//                verify the PIN and process the request
                    $.ajax({
                        url:'<?= Yii::$app->urlManager->createUrl(['department/receive-test-with-pin']);?>',
                        type: 'post',
                        data: {id:id,pin: inputValue},
                        beforeSend: function () {
//                        Loading symbol
                        },
                        success: function (data) {
//                        refresh the grid if success
                            if(data.status==1) {
                                swal.close();
                                document.getElementById('assigned_'+data.tocr).click();
                                load_assigned_test_details();
                                load_received_test_details();
                            }else {
                                swal('Error : ' + data.message);
                            }
                        },
                        error: function (data) {
                            swal('something went wrong');
                        }
                    });
                }
            });
//    Then Process the request  then refresh the grid
    }

    function complete_test(id) {
//    Have to open popup to get the user PIN
        swal({
                title: '',
                text: "Enter the PIN : ",
                type: "input",
                showCancelButton: true,
                closeOnConfirm: true,
                animation: "slide-from-top",
                inputPlaceholder: "Enter your Employee PIN Number"
            },
            function (inputValue) {
                if (inputValue === false) return false;

                if (inputValue === "") {
                    swal.showInputError("You need to enter the 4 digit PIN");
                    return false
                }else {
//                verify the PIN and process the request
                    $.ajax({
                        url:'<?= Yii::$app->urlManager->createUrl(['department/complete-test-with-pin']);?>',
                        type: 'post',
                        data: {id:id,pin: inputValue},
                        beforeSend: function () {
//                        Loading symbol
                        },
                        success: function (data) {
//                        refresh the grid if success
                            if(data.status==1) {
                                swal.close();
                                document.getElementById('received_'+data.tocr).click();
                                load_received_test_details();
                                completed_test_details();
                            }else {
                                swal('Error : ' + data.message);
                            }
                        },
                        error: function (data) {
                            swal('something went wrong');
                        }
                    });
                }
            });
//    Then Process the request  then refresh the grid
    }

    function witness_seen(id) {
//    Have to open popup to get the user PIN
        swal({
                title: '',
                text: "Enter the PIN : ",
                type: "input",
                showCancelButton: true,
                closeOnConfirm: true,
                animation: "slide-from-top",
                inputPlaceholder: "Enter your Employee PIN Number"
            },
            function (inputValue) {
                if (inputValue === false) return false;

                if (inputValue === "") {
                    swal.showInputError("You need to enter the 4 digit PIN");
                    return false
                }else {
//                verify the PIN and process the request
                    $.ajax({
                        url:'<?= Yii::$app->urlManager->createUrl(['department/witness-seen-with-pin']);?>',
                        type: 'post',
                        data: {id:id,pin: inputValue},
                        beforeSend: function () {
//                        Loading symbol
                        },
                        success: function (data) {
//                        refresh the grid if success
                            if(data.status==1) {
                                swal.close();
                                load_witness_test_details();
                            }else {
                                swal('Error : ' + data.message);
                            }
                        },
                        error: function (data) {
                            swal('something went wrong');
                        }
                    });
                }
            });
//    Then Process the request  then refresh the grid
    }


    function receive_multi_test(tocr='') {
        var list = '';
        $('.assigned_test').each(function() {
            if($(this).is(':checked'))  {
                list = list + ','+ $(this).val();
            }
        });
        if(list=='') {
            swal('Select at-least one Test');
        }else {
            list = list.substr(1,list.length);
            receive_test(list,tocr);
        }
    }

    function complete_multi_test(tocr='') {
        var list = '';
        $('.received_test').each(function() {
            if($(this).is(':checked'))  {
                list = list + ','+ $(this).val();
            }
        });
        if(list=='') {
            swal('Select at-least one Test');
        }else {
            list = list.substr(1,list.length);
            complete_test(list,tocr);
        }
    }

    function check_all(all) {
        var val = $(all).is(':checked');
        $('.checkbox1').each(function() {
            $(this).prop('checked',val);
        })
    }

</script>

<?php
$Js = <<< JS
$(document).ready(function() {
    load_assigned_test_details();
    load_received_test_details();
    load_witness_test_details();
    completed_test_details();
});
JS;
$this->registerJs($Js, \yii\web\View::POS_READY);
?>
