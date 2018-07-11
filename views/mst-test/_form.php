<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\MstDepartments;
use app\models\MstProducts;

if($model->isNewRecord) {
    $model->status = 1;
    $model->created_date = date('Y-m-d H:i:s');
    $model->created_by = Yii::$app->user->identity->id;
}else {
    $model->modified_date = date('Y-m-d H:i:s');
    $model->modified_by = Yii::$app->user->identity->id;
}
?>

<div class="mst-tests-form">

    <?php $form = ActiveForm::begin();
    $department = ArrayHelper::map(MstDepartments::find()->where(['status'=>1])->all(),'id','dept_name');
    ?>

    <?= $form->field($model, 'mst_department_id')->dropDownList($department,['prompt'=>'--select the department--']) ?>

    <?= $form->field($model, 'test_name')->textInput(['maxlength' => true]) ?>

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
            echo $form->field($model,'created_by');
            echo $form->field($model,'created_date');
        }else {
            $model->modified_date = date('Y-m-d H:i:s');
            $model->modified_by = Yii::$app->user->identity->id;
            echo $form->field($model,'modified_by');
            echo $form->field($model,'modified_date');
        }
        ?>
    </div>

    <?php ActiveForm::end(); ?>
    
</div>
