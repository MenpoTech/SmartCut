<?php

namespace app\controllers;

use app\models\MstControlNumbers;
use app\models\TrnBillHeaders;
use app\models\TrnTestDetails;
use Dompdf\Dompdf;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use \yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

/**
 * TrnCustomerController implements the CRUD actions for TrnCustomerEntries model.
 */
class TrnCustomerController extends Controller
{
    public function actionEntry() {
        $model = new TrnBillHeaders();
        $ctl= new MstControlNumbers();
        if(Yii::$app->request->isPost) {
            $dept_id = array();
            if(!empty($_POST['TrnTestDetails']['mst_test_id'])) {
                $current_user = Yii::$app->user->identity->id;
                $current_ip = Yii::$app->request->getUserIP();
                $file_name = NULL;
                $uploaded = 0;
                foreach($_POST['TrnTestDetails']['mst_test_id'] as $id=>$test_id) {
                    $obj = new TrnTestDetails();
                    $obj->mst_customer_id       = $_POST['TrnTestDetails']['mst_customer_id'];
                    $obj->mst_department_id     = $_POST['TrnTestDetails']['mst_department_id'][$id];
                    $obj->mst_test_id           = $_POST['TrnTestDetails']['mst_test_id'][$id];
                    $obj->test_name             = $_POST['TrnTestDetails']['test_name'][$id];
                    $obj->mst_sub_test_id       = $_POST['TrnTestDetails']['mst_sub_test_id'][$id];
                    $obj->sub_test_name         = $_POST['TrnTestDetails']['sub_test_name'][$id];
                    $obj->assign_date           = date('Y-m-d H:i:s');
                    $obj->assigned_by           = $current_user;
                    $obj->assigned_ip           = $current_ip;
                    $obj->tocr_number           = $_POST['TrnTestDetails']['tocr_number'];
                    $obj->sample_details        = $_POST['TrnTestDetails']['sample_details'][$id];
                    $obj->heat_no               = $_POST['TrnTestDetails']['heat_no'][$id];
                    $obj->sample_id             = $_POST['TrnTestDetails']['sample_id'][$id];
                    $obj->status                = 'Assigned';
                    $obj->is_return             = $_POST['TrnTestDetails']['is_return'];
                    $obj->is_need_witness       = $_POST['TrnTestDetails']['is_need_witness'];
                    $obj->witness_date          = $_POST['TrnTestDetails']['witness_date'];
                    $obj->remarks               = $_POST['TrnTestDetails']['remarks'];
                    $obj->report_no_from        = $_POST['TrnTestDetails']['report_no_from'];
                    $obj->report_no_to          = $_POST['TrnTestDetails']['report_no_to'];
                    $file                       = UploadedFile::getInstance($model, 'sample_photo_path');

                    $tocr = str_replace('/','-',$_POST['TrnTestDetails']['tocr_number']);
                    $path ='TOCR_Documents/'.date('Y').'/'.$tocr."/";
                    FileHelper::createDirectory($path);
                    $file_name = $path.'img_'.$tocr.'.jpg';

                    if(!empty($file) && $uploaded==0) {
                        move_uploaded_file($file->tempName, $file_name);
                        $x = $file->saveAs($file->baseName . '.' . $file->extension);
                        $uploaded=1;
                    }

                    if($uploaded==1) {
                        $obj->sample_photo_path = $file_name;
                    }

                    if($obj->save()) {
                        //Have to trigger the socket channel to create alert in department page
                        Yii::$app->session->setFlash('success','Saved Successfully');
                        $tocr = str_replace('/','-',$obj->tocr_number);
                        $path ='TOCR_Documents/'.date('Y').'/'.$tocr."/".$tocr.".pdf";
                        Yii::$app->session->setFlash('tocr_print',$path);
                        $dept_id[] = $_POST['TrnTestDetails']['mst_department_id'][$id];
                    }else {
                        echo "<pre>"; print_r($obj->getErrors()); echo "</pre>"; exit;
                        /*foreach($obj->getErrors() as $error) {
                            Yii::$app->session->addFlash('warning',$error);
                        }*/
//                        Yii::$app->session->setFlash('warning','Not Saved');
                    }
                }
                if($_POST['TrnTestDetails']['tocr_number']==$_POST['TrnTestDetails']['tocr_number_1']) {
//                    Update the Tocr Serial
                    $next_number = explode('/',$_POST['TrnTestDetails']['tocr_number']);
                    $next_number = $next_number[1]+1;
                    $ctl->getExtId('tocr_number',date('y'),date('y'));
//                    MstControlNumbers::updateAll(['number_ne
//
//xt'=>$next_number,'prefix'=>date('y')],['number_type'=>'tocr_number','number_logic'=>date('y')]);
                }else {
//                    Else
                }
                $ctl->updateReportNumber($_POST['TrnTestDetails']['report_no_to'],date('my'));
                $this->actionSaveTocrPdf($_POST['TrnTestDetails']['tocr_number']);

//                Socket communication to alert Other User
                $message = implode(',',$dept_id);
                Yii::$app->redis->executeCommand('PUBLISH', [
                    'channel' => 'new_tocr',
                    'message' => Json::encode(['name' => Yii::$app->user->identity->displayname, 'departments' => $message,'message'=>$tocr.' TOCR Added ! '])
                ]);
            }else {
                Yii::$app->session->setFlash('warning','Select the test');
            }
            return $this->redirect(Url::to(['trn-customer/entry']));
        }
        return $this->render('entry',['model'=>$model]);
    }


    public function actionSaveTocrPdf($tocr_number='') {
        $obj = new TrnTestDetails();
        $details = $obj->getTocrDetails($tocr_number);
        $html = $this->renderPartial('tocr-details',['details'=>$details]);
        $dompdf = new Dompdf();
//        $html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8");
        $dompdf->loadHtml(utf8_decode($html), 'UTF-8');

        $dompdf->setPaper('A4');
        $dompdf->render();
        $tocr = str_replace('/','-',$tocr_number);
        $path ='TOCR_Documents/'.date('Y').'/'.$tocr."/";
        FileHelper::createDirectory($path);
        $pdf = $dompdf->output();
//        $dompdf->stream('doc.pdf',array("Attachment" => false));
        $full_path = $path.$tocr.'.pdf';
        file_put_contents($full_path,$pdf);
        TrnTestDetails::updateAll(['tocr_document_path'=>$full_path],['tocr_number'=>$tocr_number]);
    }

    public function actionGetTest() {
        $selected = '';
        $out = array();
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_all_params'];
            if ($parents != null) {
                $mst_product_id = $parents['trntestdetails-mst_department_id'];
                if (!empty($parents['trntestdetails-mst_department_id'])) {
                    $res = MstTests::find()->where(['mst_department_id'=>$mst_product_id])->orderBy('test_name ASC')->all();
                    if(!empty($res)) {
                        $i=0;
                        foreach($res as $val) {
                            $out[$i]['id'] = $val['id'];
                            $out[$i]['name'] = $val['test_name'];
                            $i++;
                        }
                    }
                }
                echo Json::encode(['output'=>$out, 'selected'=>$selected]);
                return;
            }
        }
        echo Json::encode(['output'=>'', 'selected'=>'']);
    }

    public function actionGetSubTest() {
        $selected = '';
        $out = array();
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_all_params'];
            if ($parents != null) {
                $mst_test_id = $parents['trntestdetails-mst_test_id'];
                if (!empty($parents['trntestdetails-mst_test_id'])) {

                    $res = MstSubTests::find()->where(['mst_test_id'=>$mst_test_id])->orderBy('sub_test_name ASC')->all();
                    $out = ArrayHelper::map(MstSubTests::find()->where(['mst_test_id'=>$mst_test_id])->orderBy('sub_test_name ASC')->all(),'id','sub_test_name');
                }
                echo Json::encode(['output'=>[$out]]);
                return;
            }
        }
        echo Json::encode(['output'=>'', 'selected'=>'']);
    }

    public function actionCheckSubTest(){
        if(Yii::$app->request->isPost) {
            if(!empty($_POST['test_id'])) {
                $sub_test = MstSubTests::find()->where(['mst_test_id'=>$_POST['test_id']])->all();
                if(!empty($sub_test)) {
                    return $this->renderPartial('sub-test-details',['details'=>$sub_test]);
                }
            }
        }
    }

    public function actionValidateEntryForm()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = new TrnTestDetails(Yii::$app->getRequest()->getBodyParams()['TrnTestDetails']);
            if (!$model->validate()) {
                return ActiveForm::validate($model);
            }
        }
    }

    public function actionGetTocrNumber($q='')
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $query = "select distinct test.tocr_number as id, test.tocr_number || ' - ' || customer.customer_name as text,test.tocr_number,test.mst_customer_id,customer.customer_name from trn_test_details as test JOIN mst_customers as customer on (customer.id=test.mst_customer_id) where test.tocr_number ilike '%".$q."%'";
        $con= Yii::$app->getDb();
        $res = $con->createCommand($query)->queryAll();
        $out['results'] = array_values($res);
        return $out;
    }

    public function actionGetTestList($q='') {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $query = "select test.id,test.item_name text from mst_items as test where test.status=1  and (test.item_name like :q)  ORDER BY test.item_name";
        $res= Yii::$app->getDb()->createCommand($query,['q'=>'%'.$q.'%'])->queryAll();
        $out['results'] = array_values($res);
        return $out;
    }

    public function actionGetTestDetails(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        if(Yii::$app->request->isAjax) {
            if(!empty($_POST['test_id'])) {
                $q = "select dept.id as dept_id,dept.dept_name,test.id as test_id, test.test_name, sub.id as sub_test_id, sub.sub_test_name from mst_tests as test LEFT JOIN mst_sub_tests as sub on (sub.mst_test_id=test.id) JOIN mst_departments as dept on (dept.id=test.mst_department_id) where test.id=:test_id ";
                if(!empty($_POST['sub_test_id'])) {
                    $q.=" and sub.id = ".$_POST['sub_test_id'];
                }
                return Yii::$app->getDb()->createCommand($q,['test_id'=>$_POST['test_id']])->queryOne();
            }
        }
    }

    public function actionDownloadSamplePhoto($tocr_no='') {
        $obj = TrnTestDetails::findOne(['tocr_number'=>$tocr_no]);
        if(!empty($obj)) {
            $path = Yii::getAlias('@webroot')."/".$obj->sample_photo_path;
            if (file_exists($path)) {
                Yii::$app->response->sendFile($path);
            }else{
                echo 'Requested File Can\'t be found';
            }
        }else {
            echo "Something went wrong, Please try again later";
        }
    }

    public function actionDownloadTocrDocument($tocr_no='') {
        $obj = TrnTestDetails::findOne(['tocr_number'=>$tocr_no]);
        if(!empty($obj)) {
            $path = Yii::getAlias('@webroot')."/".$obj->tocr_document_path;
            if (file_exists($path)) {
                Yii::$app->response->sendFile($path);
            }else{
                echo 'Requested File Can\'t be found';
            }
        }else {
            echo "Something went wrong, Please try again later";
        }
    }

}
