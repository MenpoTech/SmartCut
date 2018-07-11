<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MstSubTests */
?>
<div class="mst-sub-tests-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'mst_test_id',
                /*'format'=>'html',
                'value'  => call_user_func(function ($data) {
                    $test_name = \app\models\MstTests::find()->where(['id'=>$data->mst_test_d]);
                    if(!empty($test_name->test_name)) {
                        return $test_name->test_name;
                    }
                    return '';
                }, $model),*/
            ],
            'sub_test_name',
            'status'
        ],
    ]) ?>

</div>
