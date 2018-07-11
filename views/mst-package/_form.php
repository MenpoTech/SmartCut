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

<div class="mst-packages-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'package_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'short_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->checkbox() ?>

	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>
    <div style="display: none;">
        <?php
        if($model->isNewRecord) {
            $model->status = 1;
            $model->created_date = date('Y-m-d H:i:s');
            $model->created_by = Yii::$app->user->identity->id;
            $model->created_ip = Yii::$app->request->userIP;
            echo $form->field($model,'created_by');
            echo $form->field($model,'created_date');
            echo $form->field($model,'created_ip');
        }else {
            $model->modified_date = date('Y-m-d H:i:s');
            $model->modified_by = Yii::$app->user->identity->id;
            $model->modified_ip = Yii::$app->request->userIP;
            echo $form->field($model,'modified_by');
            echo $form->field($model,'modified_date');
            echo $form->field($model,'modified_ip');
        }
        ?>
    </div>

    <?php ActiveForm::end(); ?>
    
</div>
