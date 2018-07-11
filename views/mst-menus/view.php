<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MstMenus */
?>
<div class="mst-menus-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'menu_name',
            'menu_type',
            'menu_url:url',
            'menu_desc:ntext',
        ],
    ]) ?>

</div>
