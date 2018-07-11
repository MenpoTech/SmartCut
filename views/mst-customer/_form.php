<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MstCustomers */
/* @var $form yii\widgets\ActiveForm */
if($model->isNewRecord) {
    $model->status = 1;
    $model->created_date = date('Y-m-d H:i:s');
    $model->created_by = Yii::$app->user->identity->id;
}else {
    $model->modified_date = date('Y-m-d H:i:s');
    $model->modified_by = Yii::$app->user->identity->id;
}
?>

<div class="mst-customers-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'customer_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'customer_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'state')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'customer_mobile')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'customer_email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput()->checkbox() ?>
  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
