<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MstUser */
/* @var $form yii\widgets\ActiveForm */

if($model->isNewRecord) {
    $model->password = md5(Yii::$app->params['default_password']);
    $model->status = 1;
    $model->ext_no = $model->getNewPin();
}else {

}
?>

<div class="mst-user-form">

    <?php $form = ActiveForm::begin(['validationUrl' => ['mst-user/validate-form'],
        'validateOnSubmit' => true,
        'enableAjaxValidation' => true,]); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'password')->passwordInput(['maxlength' => true])->label('Password <span class="small text-red">(Replace if you want to Change it, Otherwise Leave as it is)</span>') ?>

    <?= $form->field($model, 'displayname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ext_no')->textInput() ?>

    <?= $form->field($model, 'status')->checkbox() ?>
  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
