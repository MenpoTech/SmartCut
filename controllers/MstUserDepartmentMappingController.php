<?php

namespace app\controllers;

use app\models\MstDepartments;
use app\models\MstDepartmentSearch;
use app\models\MstUser;
use Yii;
use yii\helpers\ArrayHelper;

class MstUserDepartmentMappingController extends \yii\web\Controller
{

    public function beforeAction() {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(array('site/login'));
        }else {
            return true;
        }
    }

    public function actionIndex()
    {
        $dept = new MstDepartments();
        $searchModel = new MstDepartmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $AssignedMenus = $dept->getAssignedDepartments();
        $UnAssignedMenus = $dept->getUnAssignedDepartments();
        $UserNames = ArrayHelper::map(MstUser::find()->where(['status'=>1])->orderBy('username ASC')->all(),'id','username');
        $mst_user_id= '';

        if(!empty($_POST)) {
            $mst_user_id = (!empty($_POST['mst_user_id'])?$_POST['mst_user_id']:0);
            $current_user = Yii::$app->user->identity->id;
            if($_POST['submit_btn'] =='1') {
                $i=0;
                if(!empty($_POST['mst_user_id'])) {
                    $del_existing = "delete from mst_user_department_mappings where mst_user_id=$mst_user_id";
                    Yii::$app->getDb()->createCommand($del_existing)->query();
                }
                if(isset($_POST['d'])) {
                    foreach ($_POST['d'] as $key => $value) {
                        $insert_new_dept = "insert into mst_user_department_mappings (mst_user_id, mst_department_id,created_by, created_date) VALUES ($mst_user_id,$value,$current_user,now())";
                        Yii::$app->getDb()->createCommand($insert_new_dept)->query();
                    }
                }
                Yii::$app->session->setFlash('success', 'User Menu Mapping Saved Successfully');
            }

            $AssignedMenus = $searchModel->getAssignedDepartments($mst_user_id);
            $UnAssignedMenus = $searchModel->getUnAssignedDepartments($mst_user_id);
        }

        return $this->render('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider,'def_user'=>$mst_user_id,'AssignedMenus'=>$AssignedMenus,'UnAssignedMenus'=>$UnAssignedMenus,'UserNames'=>$UserNames,]);
    }

}
