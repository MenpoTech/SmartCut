<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Edit Role setting';
/* @var $this yii\web\View */
/* @var $model app\models\MstDiagnosis */

//$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Mst Menus', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$formatJs = <<< 'JS'

$(document).ready(function(){
    $('input[type="button"]').click(function(){
        var $op = $('#d option:selected'),
            $this = $(this);
        if($op.length){
            ($this.val() == 'Up') ?
                $op.first().prev().before($op) :
                $op.last().next().after($op);
        }
    });
});
JS;
$this->registerJs($formatJs, \yii\web\View::POS_READY);
?>
<?php \yii\widgets\Pjax::begin(); ?>
<?= Html::beginForm(['/mst-menus/save-role-setting'], 'post', ['data-pjax' => '', 'class' => 'form-inline','id'=>'edit-role']); ?>
<?php  echo Html::hiddenInput('submit_btn',0,['id'=>'submit_btn']); ?>
<?php  echo Html::hiddenInput('user_id',$user_id,['id'=>'user_id']); ?>


<center class="profile-username text-left">User Role Setting</center>
<div id="load_list">
    <table width="100%">
    <tr>
        <td width="33%" align="right">User Name</td>
        <td width="2%" align="center">:</td>
        <td width="35%" align="left"><b><?php echo $res[0]['username']; ?></b></td>
        <td width="10%"></td>
    </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td  align="right">Display Name</td>
            <td  align="center">:</td>
            <td  align="left"><b><?php echo $res[0]['displayname']; ?></b></td>
            <td ></td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td align="right" COLSPAN="2" align="center"><b>Role Setting</b></td>
            <td align="left"></td>
            <td ></td>
        </tr>


        <tr><td>&nbsp;</td></tr>
        <?php foreach($details as $value) { $class=''; ?>
            <tr class='".$class."' align="right">
            <td><?php echo Html::checkbox('role_flag['.$value['id'].']',$value['role_flag'],['class'=>'from-control']); ?> </td>
            <td class='nope'></td>
            <td align='left'><?php echo $value['role_name']; ?> </td>
        </tr>
        <?php }  ?>
        <tr><td>&nbsp;</td></tr>

        <tr>
        <td align="right"><a href="<?php echo  \yii\helpers\Url::to(['mst-menus/role-setting']); ?>" class="btn btn-danger"> Close </a></td>
        <td></td>
        <td align='left'><?= Html::Button('Submit', ['class' => 'btn btn-primary center','onclick'=>'selectAll()']); ?></td>
        </tr>
    </table>
</div>
<?= Html::endForm(); ?>
<script>
    function selectAll()
    {
      document.getElementById('edit-role').submit();
    }

</script>

