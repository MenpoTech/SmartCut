<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\TrnCustomerEntries */
?>
<div class="trn-customer-entries-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'mst_customer_id',
            'mst_product_id',
            'visit_date',
            'tcor_number',
            'estimation_complete_date',
            'remarks:ntext',
            'status',
            'is_deleted',
            'created_by',
            'created_date',
            'modified_by',
            'modified_date',
        ],
    ]) ?>

</div>
