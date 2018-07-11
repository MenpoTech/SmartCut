<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

if($model->isNewRecord) {
    $model->status = 1;
    $model->created_date = date('Y-m-d H:i:s');
    $model->created_by = Yii::$app->user->identity->id;
}else {
    $model->modified_date = date('Y-m-d H:i:s');
    $model->modified_by = Yii::$app->user->identity->id;
}
?>

<div class="mst-departments-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'dept_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'dept_short_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->checkbox() ?>

	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
