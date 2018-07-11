<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title="Role Setting";

$url = Url::to(['mst-menus/role-setting']);
$formatJs = <<< 'JS'
$(document).ready(function () {
   $('#user_id').select2('open');
});
JS;
$this->registerJs($formatJs, 4);

$formatJs = <<< 'JS'
function load_details(visit_id) {
    document.getElementById("role-setting").submit();
}
JS;
$this->registerJs($formatJs, 1);
?>
<center class="profile-username text-center">User Role Setting</center>
<div class="page-content">
    <div class="row">
        <div class="col-xs-12">
            <?php echo Html::beginForm(['/mst-menus/edit-role'], 'post', ['data-pjax' => '', 'class' => 'form-inline','id'=>'role-setting']); ?>
            <center><?php echo $this->render('//common/user-drop-down')?></center>
            <?= Html::endForm(); ?>

            </div>
        </div>
</div>
<br><br>
<?php echo Yii::$app->runAction('/mst-user/index',['render_partial'=>1]) ?>