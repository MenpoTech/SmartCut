<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\MstCustomers;
use app\models\MstProducts;
use yii\helpers\ArrayHelper;


/* @var $this yii\web\View */
/* @var $model app\models\TrnCustomerEntries */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-customer-entries-form">

    <?php $form = ActiveForm::begin();
    $customers = ArrayHelper::map(MstCustomers::find()->where(['status'=>1])->all(),'id','customer_name');
    $product = ArrayHelper::map(MstProducts::find()->where([])->all(),'id','product_name');
    ?>

    <?= $form->field($model, 'mst_customer_id')->dropDownList($customers,['prompt'=>'--select the customer--']) ?>

    <?= $form->field($model, 'mst_product_id')->dropDownList($product,['prompt'=>'--select the Product--']) ?>

    <?= $form->field($model, 'visit_date')->textInput() ?>

    <?= $form->field($model, 'tcor_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'estimation_complete_date')->textInput() ?>

    <?= $form->field($model, 'remarks')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'is_deleted')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'created_date')->textInput() ?>

    <?= $form->field($model, 'modified_by')->textInput() ?>

    <?= $form->field($model, 'modified_date')->textInput() ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
