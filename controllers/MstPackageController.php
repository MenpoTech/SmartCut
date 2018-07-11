<?php

namespace app\controllers;

use app\models\MstPackageItems;
use app\models\TrnTestDetails;
use Yii;
use app\models\MstPackages;
use app\models\MstPackageSearch;
use yii\db\Exception;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * MstPackageController implements the CRUD actions for MstPackages model.
 */
class MstPackageController extends Controller
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

    public function actionIndex()
    {
        $searchModel = new MstPackageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "MstPackages #".$id,
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

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new MstPackages();  

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Create new MstPackages",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Create new MstPackages",
                    'content'=>'<span class="text-success">Create MstPackages success</span>',
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Create More',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])
        
                ];         
            }else{           
                return [
                    'title'=> "Create new MstPackages",
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
                    'title'=> "Update MstPackages #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "MstPackages #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
            }else{
                 return [
                    'title'=> "Update MstPackages #".$id,
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


    protected function findModel($id)
    {
        if (($model = MstPackages::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionEdit($id=0) {
        $q = "select * from mst_packages where id= :id";
        $pack = Yii::$app->getDb()->createCommand($q,['id'=>$id])->queryOne();

        $q = "select * from mst_package_items where mst_package_id= :package_id";
        $details = Yii::$app->getDb()->createCommand($q,['package_id'=>$id])->queryAll();

        $model = new TrnTestDetails();

        return $this->render('edit',['model'=>$model,'details'=>$details,'package'=>$pack]);
    }

    public function actionUpdatePackage() {
        if(Yii::$app->request->isPost) {
            $db = Yii::$app->getDb()->beginTransaction();
            try {
                $package_id = $_POST['mst_package_id'];
                MstPackageItems::deleteAll(['mst_package_id'=>$package_id]);

                if(!empty($_POST['data'])) {
                    foreach($_POST['data'] as $val) {
                        $obj = new MstPackageItems();
                        $obj->mst_package_id        = $package_id;
                        $obj->mst_department_id     = $val['mst_department_id'];
                        $obj->dept_name             = $val['dept_name'];
                        $obj->mst_test_id           = $val['mst_test_id'];
                        $obj->test_name             = $val['test_name'];
                        $obj->mst_sub_test_id       = $val['mst_sub_test_id'];
                        $obj->sub_test_name         = $val['sub_test_name'];
                        $obj->sample_details        = $val['sample_details'];
                        $obj->heat_no               = $val['heat_no'];
                        $obj->sample_id             = $val['sample_id'];
                        $obj->save();
                    }
                    Yii::$app->session->setFlash('success','Package Items Updated');
                }
                $db->commit();
            }catch (Exception $e) {
                $db->rollBack();
                echo "<pre>"; print_r($obj->getErrors()); echo "</pre>";
                Yii::$app->session->setFlash('warning','Package Items Not Updated');
            }
            return $this->redirect(Url::to(['mst-package/index']));
        }
    }

    public function actionLoadPackageItems() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if(Yii::$app->request->isAjax && !empty($_POST['package_id'])) {
            $q = "select * from mst_package_items where mst_package_id= :mst_package_id";
            $res = Yii::$app->getDb()->createCommand($q,['mst_package_id'=>$_POST['package_id']])->queryAll();
            return $res;
        }
    }
}
