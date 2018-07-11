<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\MstTests;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\MstSubTests */
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

<div class="mst-sub-tests-form">

    <?php $form = ActiveForm::begin();
    $test = ArrayHelper::map(MstTests::find()->where(['status'=>1])->all(),'id','test_name');
    ?>

    <?= $form->field($model, 'mst_test_id')->dropDownList($test,['prompt'=>'--Select the Test Name-- ']) ?>

    <?= $form->field($model, 'sub_test_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sub_test_order')->textInput() ?>

    <?= $form->field($model, 'status')->checkbox() ?>

    <?= $form->field($model, 'created_by')->hiddenInput()->label(false); ?>

    <?= $form->field($model, 'created_date')->hiddenInput()->label(false); ?>

    <?= $form->field($model, 'modified_by')->hiddenInput()->label(false); ?>

    <?= $form->field($model, 'modified_date')->hiddenInput()->label(false); ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
