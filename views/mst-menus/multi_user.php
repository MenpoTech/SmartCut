<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MstDiagnosis */

$this->title = 'User Menu Assign';
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
<?= Html::beginForm(['/mst-menus/multi-user'], 'post', ['data-pjax' => '', 'class' => 'form-inline','id'=>'multi-user']); ?>
<?php  echo Html::hiddenInput('submit_btn',0,['id'=>'submit_btn']); ?>

<center class="profile-username text-left">User Menu Setting</center>
<div id="load_list">
    <table width="100%">
    <tr>
        <td width="33%" align="right">User Name</td>
        <td width="6%" align="center">:</td>
        <td width="31%" align="left"><div class="col-xs-7 less-padding"><?= Html::dropDownList('mst_user_id', $def_user, $UserNames, ['name'=>'data[mst_user_id]', 'id'=>'mst_user_id','prompt'=>'-Select-','onchange'=>'LoadListByRole()','class'=>'text_input form-control required']); ?></div></td>
        <td width="10%"></td>
    </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td width="33%" align="right">Side Menu</td>
            <td width="6%" align="center">:</td>
            <td width="31%" align="left"><div class="col-xs-7 less-padding"><?= Html::dropDownList('menu_parent_id', $def_side, $SubMenus, ['name'=>'data[mst_role_id]','prompt'=>'-Null-', 'id'=>'menu_parent_id','onchange'=>'LoadListByRole()','class'=>'text_input form-control required']); ?></div></td>
            <td width="10%"></td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td align="center"><b>UnAssigned Menus</b></td>
            <td></td>
            <td align="center"><b>Assigned Menus</b></td>
            <td></td>
        </tr>
    <tr>
        <td width="33%" align="right"><div style="clear:both; float:right" class="col-xs-9 less-padding">  <?= Html::dropDownList('s', 1, $UnAssignedMenus, ['name'=>'data[UnAssigned]', 'id'=>'s', 'size'=>'15','multiple'=>'multiple','class'=>'text_input form-control required']); ?></div></td>
        <td width="6%" align="center"><div style="  float:center; vertical-align:middle">
                <input type="button" value="&gt;&gt;"  class="btn btn-danger" 	onclick="listbox_selectall('s',true);listbox_moveacross('s', 'd');reload_count();" />
                <br />
                <br />
                <input type="button" value="&gt;" class = "btn btn-primary center" 	onclick="listbox_moveacross('s', 'd');reload_count();" />
                <br />
                <br />
                <input type="button" value="&lt;" class = "btn btn-primary center" 	onclick="listbox_moveacross('d', 's');reload_count();" />
                <br />
                <br />
                <input type="button" value="&lt;&lt;"  class="btn btn-danger"  onclick="listbox_selectall('d',true);listbox_moveacross('d', 's');reload_count();" />

            </div></td>
        <td width="31%"><div style="float:left"class="col-xs-9 less-padding"><?= Html::dropDownList('d', 1, $AssignedMenus, ['name'=>'data[Assigned]','multiple'=>'multiple','id'=>'d', 'size'=>'15', 'class'=>'text_input form-control required']); ?> </div></td>
       <td width="10%"> <input type="button"  class="btn btn-danger"  value="Up">
           <br />
           <br />
           <input type="button" class = "btn btn-primary center"  value="Down"></td>
    </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
        <td align="right"><a href="<?php echo  \yii\helpers\Url::to(['mst-menus/multi-user']); ?>" class="btn btn-danger"> Reset </a></td>
        <td></td>
        <td align='left'><?= Html::Button('Submit', ['class' => 'btn btn-primary center','onclick'=>'selectAll()']); ?></td>
        </tr>
    </table>
</div>
<?= Html::endForm(); ?>

<script>
function LoadListByRole()
{
    document.getElementById('submit_btn').value =0;
    document.getElementById('multi-user').submit();
}
function selectAll()
{
    listbox_selectall('d',true);
    listbox_selectall('s',true);
    document.getElementById('submit_btn').value =1;
    document.getElementById('multi-user').submit();
}
function listbox_moveacross(sourceID, destID) {
//alert(sourceID);
var src = document.getElementById(sourceID);
var dest = document.getElementById(destID);
var srcval = src.value;
for(var count=0; count < src.options.length; count++) {

if(src.options[count].selected == true && srcval!='') {
var option = src.options[count];
var newOption = document.createElement("option");
newOption.value = option.value;
newOption.text = option.text;
newOption.selected = true;
try {
dest.add(newOption, null); //Standard
src.remove(count, null);
}catch(error) {
dest.add(newOption); // IE only
src.remove(count);
}
count--;
}
}
}

function listbox_selectall(listID, isSelect) {
var listbox = document.getElementById(listID);
for(var count=0; count < listbox.options.length; count++) {
listbox.options[count].selected = isSelect;
}
}
// Remove All elements in select box
function listbox_removeall(listID1,listID2)
{
var listbox1 = document.getElementById(listID1);
var listbox2 = document.getElementById(listID2);
listbox1.options.length = 0;
listbox2.options.length = 0;
}

</script>
