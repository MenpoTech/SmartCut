<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\TrnTestDetails */
?>
<div class="trn-test-details-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'id',

            ['attribute' => 'mst_customer_id',
            'format'=>'html',
            'value'  => call_user_func(function ($data) {
                $id  = $data['mst_customer_id'];
                $test_name = \app\models\MstCustomers::find()->where(['id'=>$id])->one();

                if(!empty($test_name->customer_name)) {
                    return $test_name->customer_name;
                }else {
                    return '';
                }
            }, $model),
            ],
            ['attribute' => 'mst_department_id',
            'format'=>'html',
            'value'  => call_user_func(function ($data) {
                $id  = $data['mst_department_id'];
                $test_name = \app\models\MstDepartments::find()->where(['id'=>$id])->one();
                if(!empty($test_name->dept_name)) {
                    return $test_name->dept_name;
                }else {
                    return '';
                }
            }, $model),
            ],
            'test_name',
            'sub_test_name',
            [
                'attribute'=>'assign_date',
                'format'=>'html',
                'value'=>call_user_func(function($data) {
                return date('d-m-Y H:i A',strtotime($data['assign_date']));
            },$model)],
            'assigned_by',
            'assigned_ip',
            'tocr_number',
            'sample_details:ntext',
            'heat_no',
            'sample_id',
            'status',
            'remarks:ntext',
//            'sample_photo_path',
            'received_date',
            'received_by',
            'emp_pin',
            'received_ip',
            'completed_by',
            'completed_date',
            'completed_ip',
            'is_need_witness:boolean',
            'is_return:boolean',
            'priority',
            'test_order',
//            'modified_by',
//            'modified_date',
//            'modified_ip',
            'witness_date',
            'report_no_from',
            'report_no_to',
            'witness_seen:boolean',
            'witness_seen_date',
        ],
    ]) ?>

</div>
