<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MstDepartments */
?>
<div class="mst-departments-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'dept_name',
            'dept_short_name',
            'status',
        ],
    ]) ?>

</div>
