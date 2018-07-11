<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MstMenus */
/* @var $form yii\widgets\ActiveForm */
if($model->isNewRecord) {
    $model->status = 1;
}else {
}
?>

<div class="mst-menus-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'menu_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'menu_type')->dropDownList(['menu' => 'Menu','sdmenu'=>'Side Menu'],[]) ?>

    <?= $form->field($model, 'menu_url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'menu_desc')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->checkbox() ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
