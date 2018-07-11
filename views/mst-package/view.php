<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MstPackages */
?>
<div class="mst-packages-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'package_name',
            'short_name',
            'description:ntext',
            'status',
            'created_date',
            'created_by',
            'created_ip',
            'modified_date',
            'modified_by',
            'modified_ip',
        ],
    ]) ?>

</div>
