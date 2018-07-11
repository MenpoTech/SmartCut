<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MstUser */
?>
<div class="mst-user-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'username',
            'displayname',
            'created_date',
            'user_email_id:email',
            'ext_no',
            'status',
        ],
    ]) ?>

</div>
