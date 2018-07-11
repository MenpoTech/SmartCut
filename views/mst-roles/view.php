<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MstRoles */
?>
<div class="mst-roles-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'role_name',
            'status',
            'default_route',
        ],
    ]) ?>

</div>
