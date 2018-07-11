<?php

namespace app\controllers;

use app\models\MstUsers;
use Yii;
use app\models\MstMenus;
use app\models\MstMenuSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * MstMenusController implements the CRUD actions for MstMenus model.
 */
class MstMenusController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'bulk-delete' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction() {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(array('site/login'));
        }else {
            return true;
        }
    }

    /**
     * Lists all MstMenus models.
     * @return mixed
     */
    public function actionIndex()
    {    
        $searchModel = new MstMenuSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single MstMenus model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "MstMenus #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $this->findModel($id),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
        }else{
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }

    /**
     * Creates a new MstMenus model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new MstMenus();  

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Create new MstMenus",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Create new MstMenus",
                    'content'=>'<span class="text-success">Create MstMenus success</span>',
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Create More',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])
        
                ];         
            }else{           
                return [
                    'title'=> "Create new MstMenus",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }
        }else{
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }
       
    }

    /**
     * Updates an existing MstMenus model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);       

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Update MstMenus #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "MstMenus #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
            }else{
                 return [
                    'title'=> "Update MstMenus #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];        
            }
        }else{
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Delete an existing MstMenus model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $this->findModel($id)->delete();

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }


    }

     /**
     * Delete multiple existing MstMenus model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionBulkDelete()
    {        
        $request = Yii::$app->request;
        $pks = explode(',', $request->post( 'pks' )); // Array or selected records primary keys
        foreach ( $pks as $pk ) {
            $model = $this->findModel($pk);
            $model->delete();
        }

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }
       
    }

    /**
     * Finds the MstMenus model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MstMenus the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MstMenus::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionMulti()
    {

        $searchModel = new MstMenus();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $OrderBy = Yii::$app->common->OrderBy();
        $AssignedMenus = $searchModel->actionAssignedMenuList();
        $UnAssignedMenus = $searchModel->actionUnAssignedMenuList();
        $UserRoles = $searchModel->actionGetUserRoles();
        $SubMenus = $searchModel->actionGetSubmenus();
        $def_role = '';
        $def_side = '';

        if (!empty($_POST)) {
            $mst_role_id = $_POST['mst_role_id'];
            $def_role = $_POST['mst_role_id'];
            $def_side = $_POST['menu_parent_id'];
            $parent_id = !empty($_POST['menu_parent_id']) ? $_POST['menu_parent_id'] : 'NULL';
            if ($_POST['submit_btn'] == 1) {
                $i = 0;
                $insert_new_menus = '';
                if (!empty($_POST['menu_parent_id']))
                    $del_existing = "delete from trn_userrole_menus where mst_role_id=$mst_role_id and menu_parent_id=$def_side ;";
                else
                    $del_existing = "delete from trn_userrole_menus where mst_role_id=$mst_role_id and menu_parent_id is null;";
                $con = Yii::$app->getDb();
                $con->createCommand($del_existing)->queryAll();
                foreach ($_POST['d'] as $key => $value) {
                    $i++;
                    if (!empty($_POST['menu_parent_id']))
                        $insert_new_menus = "insert into trn_userrole_menus (mst_role_id,mst_menu_id,menu_type,menu_order,menu_parent_id) SELECT $mst_role_id,$value,(select menu_type from mst_menus where id=$value),$i,$def_side;";
                    else
                        $insert_new_menus = "insert into trn_userrole_menus (mst_role_id,mst_menu_id,menu_type,menu_order) SELECT $mst_role_id,$value,(select menu_type from mst_menus where id=$value),$i;";
                    $con->createCommand($insert_new_menus)->queryAll();
                    //echo '<pre>';print_r($insert_new_menus);
                }
            }
            $AssignedMenus = $searchModel->actionAssignedMenuList($mst_role_id, $parent_id);
            $UnAssignedMenus = $searchModel->actionUnAssignedMenuList($mst_role_id, $parent_id);

        }
    }
    public function actionMultiRole()
    {

        $searchModel = new MstMenus();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $OrderBy = Yii::$app->common->OrderBy();
        $AssignedMenus = $searchModel->actionAssignedMenuList();
        $UnAssignedMenus = $searchModel->actionUnAssignedMenuList();
        $UserRoles = $searchModel->actionGetUserRoles();
        $SubMenus = $searchModel->actionGetSubmenus();
        $def_role = '';
        $def_side = '';

        if(!empty($_POST)) {
            $mst_role_id = $_POST['mst_role_id'];
            $def_role = $_POST['mst_role_id'];
            $def_side = $_POST['menu_parent_id'];
            $parent_id = !empty($_POST['menu_parent_id'])?$_POST['menu_parent_id']:'NULL';
            if($_POST['submit_btn'] ==1 ) {
                $i=0;
                $insert_new_menus ='';
                if(!empty($_POST['menu_parent_id']))
                    $del_existing =  "delete from trn_userrole_menus where mst_role_id=$mst_role_id and menu_parent_id=$def_side ;";
                else
                    $del_existing =  "delete from trn_userrole_menus where mst_role_id=$mst_role_id and menu_parent_id is null;";
                $con= Yii::$app->getDb();
                $con->createCommand($del_existing)->queryAll();
                if(isset($_POST['d'])) {
                    foreach ($_POST['d'] as $key => $value) {
                        $i++;
                        if (!empty($_POST['menu_parent_id']))
                            $insert_new_menus = "insert into trn_userrole_menus (mst_role_id,mst_menu_id,menu_type,menu_order,menu_parent_id) SELECT $mst_role_id,$value,(select menu_type from mst_menus where id=$value),$i,$def_side;";
                        else
                            $insert_new_menus = "insert into trn_userrole_menus (mst_role_id,mst_menu_id,menu_type,menu_order) SELECT $mst_role_id,$value,(select menu_type from mst_menus where id=$value),$i;";
                        $con->createCommand($insert_new_menus)->queryAll();
                        Yii::$app->session->setFlash('success', 'Role Menu setting Saved Successfully');
                        //echo '<pre>';print_r($insert_new_menus);
                    }
                }
            }
            $AssignedMenus = $searchModel->actionAssignedMenuList($mst_role_id,$parent_id);
            $UnAssignedMenus = $searchModel->actionUnAssignedMenuList($mst_role_id,$parent_id);

        }
        return $this->render('multi_role', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'OrderBy'=>$OrderBy,
            'def_role'=>$def_role,
            'def_side'=>$def_side,
            'AssignedMenus'=>$AssignedMenus,
            'UnAssignedMenus'=>$UnAssignedMenus,
            'UserRoles'=>$UserRoles,
            'SubMenus'=>$SubMenus
        ]);
    }
    public function actionMultiUser()
    {

        $searchModel = new MstMenus();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $OrderBy = Yii::$app->common->OrderBy();
        $AssignedMenus = $searchModel->actionAssignedMenuList();
        $UnAssignedMenus = $searchModel->actionUnAssignedMenuList();
        $UserNames = $searchModel->actionGetUsernames();
        $SubMenus = $searchModel->actionGetSubmenus();
        $def_user = '';
        $def_side = '';

        if(!empty($_POST)) {
            $mst_user_id = $_POST['mst_user_id'];
            $def_user = $_POST['mst_user_id'];
            $def_side = $_POST['menu_parent_id'];
            $parent_id = !empty($_POST['menu_parent_id'])?$_POST['menu_parent_id']:'NULL';
            if($_POST['submit_btn'] ==1 ) {
                $i=0;
                $insert_new_menus ='';
                if(!empty($_POST['menu_parent_id']))
                    $del_existing =  "delete from trn_user_menus where mst_user_id=$mst_user_id and menu_parent_id=$def_side ;";
                else
                    $del_existing =  "delete from trn_user_menus where mst_user_id=$mst_user_id and menu_parent_id is null;";
                $con= Yii::$app->getDb();
                $con->createCommand($del_existing)->queryAll();
                if(isset($_POST['d'])) {
                    foreach ($_POST['d'] as $key => $value) {
                        $i++;
                        if (!empty($_POST['menu_parent_id']))
                            $insert_new_menus = "insert into trn_user_menus (mst_user_id,mst_menu_id,menu_type,menu_order,menu_parent_id) SELECT $mst_user_id,$value,(select menu_type from mst_menus where id=$value),$i,$def_side;";
                        else
                            $insert_new_menus = "insert into trn_user_menus (mst_user_id,mst_menu_id,menu_type,menu_order) SELECT $mst_user_id,$value,(select menu_type from mst_menus where id=$value),$i;";
                        $con->createCommand($insert_new_menus)->queryAll();
                        //echo '<pre>';print_r($insert_new_menus);

                    }
                }
                Yii::$app->session->setFlash('success', 'User Menu setting Saved Successfully');
                //   return $this->redirect(['mst-menus/multi-user']);
            }
            $AssignedMenus = $searchModel->actionUserAssignedMenuList($mst_user_id,$parent_id);
            $UnAssignedMenus = $searchModel->actionUserUnAssignedMenuList($mst_user_id,$parent_id);

        }
        return $this->render('multi_user', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'OrderBy'=>$OrderBy,
            'def_user'=>$def_user,
            'def_side'=>$def_side,
            'AssignedMenus'=>$AssignedMenus,
            'UnAssignedMenus'=>$UnAssignedMenus,
            'UserNames'=>$UserNames,
            'SubMenus'=>$SubMenus
        ]);
    }


    public function actionRoleSetting()
    {
        if(!empty(Yii::$app->request->getQueryParam('mst_user_id')) || Yii::$app->request->isPost) {
            $user_id = ((Yii::$app->request->getQueryParam('mst_user_id'))?Yii::$app->request->getQueryParam('mst_user_id'):(!empty($_POST['user_id'])?$_POST['user_id']:0));
            $this->redirect(['mst-menus/edit-role', 'user_id' => $user_id]);
        }
        return $this->render('role_setting');
    }

    public function actionLoadActiveUsers($q='') {
        //  $obj = new MstMenus();
        $obj = new MstUsers();
        return $res = $obj->getUsersList($q);
    }
    public function actionEditRole($q='') {
        $obj = new MstMenus();
        if(isset($_POST['user_id'])) {
            Yii::$app->session->set('role_user_id', $_POST['user_id']);
        }
        $user_id =Yii::$app->session->get('role_user_id');
        $details = $obj->getUserExistingRoles($user_id);
        $user_details = "select id,username,displayname from mst_users where status=1 and id=$user_id";
        $con= Yii::$app->getDb();
        $res = $con->createCommand($user_details)->queryAll();
        //echo '<pre>';print_r($details);exit;
        return $this->render('edit_role',['user_id'=>$user_id,'res'=>$res,'details'=>$details]);
    }
    public function actionSaveRoleSetting()
    {
        $user_id = $_POST['user_id'];
        $role_flag= isset($_POST['role_flag'])?$_POST['role_flag']:0;
        $con= Yii::$app->getDb();
        $del_role = "delete from trn_userrole_settings where mst_user_id=$user_id; ";
        $con->createCommand($del_role)->queryAll();
        if(!empty($role_flag)) {
            foreach ($role_flag as $key => $value) {
                $ins_role = "insert into trn_userrole_settings(mst_user_id, mst_role_id, status, is_deleted) select $user_id,$key,1,0 ;";
                $con->createCommand($ins_role)->queryAll();
            }
        }
        Yii::$app->session->setFlash('success', 'Role Saved Successfully');
        return $this->redirect(['mst-menus/edit-role']);
        //echo '<pre>';print_r($_POST);exit;
    }

}
