<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TrnTestDetails */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-test-details-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'mst_customer_id')->textInput() ?>

    <?= $form->field($model, 'mst_department_id')->textInput() ?>

    <?= $form->field($model, 'mst_test_id')->textInput() ?>

    <?= $form->field($model, 'test_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mst_sub_test_id')->textInput() ?>

    <?= $form->field($model, 'sub_test_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'assign_date')->textInput() ?>

    <?= $form->field($model, 'assigned_by')->textInput() ?>

    <?= $form->field($model, 'assigned_ip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tocr_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sample_details')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'heat_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sample_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'remarks')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'sample_photo_path')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'received_date')->textInput() ?>

    <?= $form->field($model, 'received_by')->textInput() ?>

    <?= $form->field($model, 'emp_pin')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'received_ip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'completed_by')->textInput() ?>

    <?= $form->field($model, 'completed_date')->textInput() ?>

    <?= $form->field($model, 'completed_ip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_need_witness')->checkbox() ?>

    <?= $form->field($model, 'is_return')->checkbox() ?>

    <?= $form->field($model, 'priority')->textInput() ?>

    <?= $form->field($model, 'test_order')->textInput() ?>

    <?= $form->field($model, 'modified_by')->textInput() ?>

    <?= $form->field($model, 'modified_date')->textInput() ?>

    <?= $form->field($model, 'modified_ip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'witness_date')->textInput() ?>

    <?= $form->field($model, 'report_no_from')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'report_no_to')->textInput(['maxlength' => true]) ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
