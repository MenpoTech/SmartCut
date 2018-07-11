<?php
use yii\helpers\Html;
if(!empty($details)) { ?>
    <div class="col-md-12">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Select the Sub Test</h3>
        </div>
        <div class="form-horizontal">
            <div class="box-body">
                <div class='sub_test'>
                    <?php
                    foreach($details as $val) {
                        echo "<div class='checkbox'><label>".Html::checkbox($val['sub_test_name'],true,['class'=>'checkbox','value'=>$val['id']])." ".$val['sub_test_name']."</label></div>";
                    }
                    echo "<center>".Html::button('Add Selected Test',['onclick'=>'add_sub_test()', 'data-dismiss'=>'modal' ,'class'=>'btn btn-primary'])."</center>"; ?>
                </div>
            </div>
        </div>
    </div>
<?php }
?>