<?php
namespace app\controllers;
use app\models\AssignedTocr;
use app\models\CompletedTocr;
use app\models\MstDepartments;
use app\models\ReceivedTocr;
use app\models\WitnessTocr;
use Yii;
use app\models\TrnTestDetails;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Response;

class DepartmentController extends \yii\web\Controller
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
        $obj = new MstDepartments();
        $user_id = (!empty(Yii::$app->user->identity->id)?Yii::$app->user->identity->id:0);
        $dept = $obj->getUserAssignedDepartments($user_id);
        return $this->render('index',['department_id'=>$dept]);
    }

    public function actionGetAssignedTocrList()
    {
        if (Yii::$app->request->isPjax || Yii::$app->request->isAjax) {
            $searchModel = new AssignedTocr();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            return $this->renderAjax('assigned_tocr', ['data' => $dataProvider, 'model' => $searchModel]);
        }else {
            return $this->redirect(['department/index']);
        }
    }

    public function actionGetReceivedTocrList()
    {
        if (Yii::$app->request->isPjax || Yii::$app->request->isAjax) {
            $searchModel = new ReceivedTocr();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            return $this->renderAjax('received_tocr', ['data' => $dataProvider, 'model' => $searchModel]);
        }else {
            return $this->redirect(['department/index']);
        }
    }

    public function actionGetCompletedTocrList()
    {
        if (Yii::$app->request->isPjax || Yii::$app->request->isAjax) {
            $searchModel = new CompletedTocr();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            return $this->renderAjax('completed_tocr', ['data' => $dataProvider, 'model' => $searchModel]);
        }else {
            return $this->redirect(['department/index']);
        }
    }

    public function actionGetWitnessTocrList($params='')
    {
        if (Yii::$app->request->isPjax || Yii::$app->request->isAjax) {
            $searchModel = new WitnessTocr();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            return $this->renderAjax('witness_tocr', ['data' => $dataProvider, 'model' => $searchModel]);
        }else {
            return $this->redirect(['department/index']);
        }
    }

    public function actionLoadTest() {
        $res = array();
        if(Yii::$app->request->isAjax) {
            $type = $_POST['type'];
            if($type=='assigned_test') {
                $query = "select details.id,dept.dept_name,details.test_name,details.sub_test_name,details.status,details.assign_date,details.assigned_by,details.priority,details.tocr_number from trn_test_details as details JOIN mst_user_department_mappings as map on (map.mst_department_id=details.mst_department_id and map.mst_user_id=1) JOIN mst_departments as dept on (dept.id=details.mst_department_id) WHERE details.status='Assigned' ORDER BY details.priority ASC;";
                $res = Yii::$app->getDb()->createCommand($query)->queryAll();
                return $this->renderPartial('assigned_test_list',['details'=>$res]);
            }

            if($type=='received_test') {
                $query = "select details.id,dept.dept_name,details.test_name,details.sub_test_name,details.status,details.assign_date,details.assigned_by,details.priority,details.tocr_number  from trn_test_details as details JOIN mst_user_department_mappings as map on (map.mst_department_id=details.mst_department_id and map.mst_user_id=1) JOIN mst_departments as dept on (dept.id=details.mst_department_id) WHERE details.status='Received';";
                $res = Yii::$app->getDb()->createCommand($query)->queryAll();
                return $this->renderPartial('received_test_list',['details'=>$res]);
            }

            if($type=='complete_test') {
                $query = "select details.id,dept.dept_name,details.test_name,details.sub_test_name,details.status,details.assign_date,details.assigned_by,details.priority,details.tocr_number,witness_date  from trn_test_details as details JOIN mst_user_department_mappings as map on (map.mst_department_id=details.mst_department_id and map.mst_user_id=1) JOIN mst_departments as dept on (dept.id=details.mst_department_id) WHERE details.is_need_witness is TRUE and witness_seen is FALSE ;";
                $res = Yii::$app->getDb()->createCommand($query)->queryAll();
                return $this->renderPartial('witness_test_list',['details'=>$res]);
            }
        }
    }

    public function actionReceiveTestWithPin() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if(Yii::$app->request->isAjax) {
            $current_user = Yii::$app->user->identity->id;
            $tocr = '';
            $id = $_POST['id'];
            $pin = $_POST['pin'];
            $date = date('Y-m-d H:i:s');
            $ip = Yii::$app->request->userIP;

            $query= "select count(id) from mst_users where ext_no = :pin and status=1";
            $user_id = Yii::$app->getDb()->createCommand($query,['pin'=>$pin])->queryScalar();
            if($user_id) {
                if(!empty($id)) {
//                Have to Receive the test
                    $list = explode(',',$id);
                    foreach($list as $id) {
                        $query = "update trn_test_details set status='Received', received_by=:user, received_date=:date, received_ip=:ip where id=:id RETURNING tocr_number";
                        $tocr = Yii::$app->getDb()->createCommand($query,['user'=>$user_id,'date'=>$date,'ip'=>$ip,'id'=>$id])->queryScalar();
                    }
                    $status = 1;
                    $message = 'Success';
                }
            }else {
                $status = 0;
                $message = "Invalid PIN";
            }
            return ['status'=>$status,'message'=>$message,'tocr'=>$tocr];
        }
    }

    public function actionCompleteTestWithPin() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if(Yii::$app->request->isAjax) {
            $current_user = Yii::$app->user->identity->id;
            $tocr = '';
            $id = $_POST['id'];
            $pin = $_POST['pin'];
            $date = date('Y-m-d H:i:s');
            $ip = Yii::$app->request->userIP;

            $query= "select id from mst_users where ext_no = :pin and status=1";
            $user_id = Yii::$app->getDb()->createCommand($query,['pin'=>$pin])->queryScalar();
            if($user_id) {
//                Have to Complete the test
                if(!empty($id)) {
//                Have to Receive the test
                    $list = explode(',', $id);
                    foreach ($list as $id) {
                        $query = "update trn_test_details set status='Completed', completed_by=:user, completed_date=:date, completed_ip=:ip where id=:id  RETURNING tocr_number";
                        $tocr = Yii::$app->getDb()->createCommand($query, ['user' => $user_id, 'date' => $date, 'ip' => $ip, 'id' => $id])->queryScalar();
                    }
                    $status = 1;
                    $message = 'Success';
                }
            }else {
                $status = 0;
                $message = "Invalid PIN";
            }
            return ['status'=>$status,'message'=>$message,'tocr'=>$tocr];
        }
    }

    public function actionWitnessSeenWithPin() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if(Yii::$app->request->isAjax) {
            $current_user = Yii::$app->user->identity->id;
            $id = $_POST['id'];
            $pin = $_POST['pin'];
            $date = date('Y-m-d H:i:s');
            $ip = Yii::$app->request->userIP;

            $query= "select count(id) from mst_users where ext_no = :pin and status=1";
            $user_id = Yii::$app->getDb()->createCommand($query.['pin'=>$pin])->queryScalar();
            if($user_id) {
//                Have to Receive the test
                $query = "update trn_test_details set witness_seen=TRUE , modified_by= :user, witness_seen_date= :date, modified_ip= :ip where id= :id ";
                Yii::$app->getDb()->createCommand($query.['user'=>$user_id,'date'=>$date,'ip'=>$ip,'id'=>$id])->query();
                $status =1;
                $message = 'Success';
            }else {
                $status = 0;
                $message = "Invalid PIN";
            }
            return ['status'=>$status,'message'=>$message];
        }
    }

    public function actionRearrangeTestOrder($tocr_number='') {
        $items = array();
        if(!empty($tocr_number)) {
            $res = TrnTestDetails::find()->where(['tocr_number'=>$tocr_number])->all();
            if(!empty($res)) {
                foreach($res as $val) {
                    $items[$val['id']]['content']=$val['test_name'];
                }
            }
        } if(!empty($_POST)) {
            if(!empty($_POST['test_order'])) {
                $list = explode(',',$_POST['test_order']);
                $i=1;
                foreach($list as $id) {
                    TrnTestDetails::updateAll(['priority'=>$i],['id'=>$id]);
                    $i++;
                }
                Yii::$app->session->setFlash('success','priority Saved Successfully');
            }else {
                Yii::$app->session->setFlash('warning','No Test found');
            }
        }
        return $this->render('rearrange-test-order',['items'=>$items,'tocr_number'=>$tocr_number]);
    }

    public function actionTrack($tocr_number='') {
        $res  = array();
        $desc  = array();
        if(!empty($tocr_number)) {
            $res = TrnTestDetails::find()->where(['tocr_number'=>$tocr_number])->all();
            if(!empty($res)) {
                $query = "select sum(case when status='' then 1 else 1 end) as total, sum(case when status='Assigned' then 1 end) as assigned , sum(case when status='Received' then 1 end) as received, sum(case when status='Completed' then 1 end) as completed from trn_test_details where tocr_number='$tocr_number';";
                $res = Yii::$app->getDb()->createCommand($query)->queryOne();

                $q = "select dept.dept_name,test.* from trn_test_details as test JOIN mst_departments as dept on (dept.id=test.mst_department_id) where tocr_number = :tocr ORDER BY id ASC";
                $desc = Yii::$app->getDb()->createCommand($q,['tocr'=>$tocr_number])->queryAll();
            }else {
                Yii::$app->session->setFlash('warning','Invalid TOCR Number');
            }
        }
        return $this->render('track',['tocr_number'=>$tocr_number,'details'=>$res,'desc'=>$desc]);
    }

    public function actionGetCalendar($start=NULL,$end=NULL,$_=NULL) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $events = array();

        $Event = new \yii2fullcalendar\models\Event();
        $Event->id = '123';
        $Event->title = 'Witness Test Review';
        $Event->url = Url::to(['department/view','id'=>123]);
        $Event->start = date('Y-m-d');
//        $Event->start = date('Y-m-d\TH:i:s\Z');
        $Event->end = date('Y-m-d');
        $events[] = $Event;

        $Event = new \yii2fullcalendar\models\Event();
        $Event->id = '2354';
        $Event->title = 'Test Review';
        $Event->url = Url::to(['department/view','id'=>123]);
        $Event->start = date('Y-m-d',strtotime('2017-05-28'));
//        $Event->start = date('Y-m-d\TH:i:s\Z');
        $Event->end = '2017-05-28';
//        $Event->end = date('Y-m-d\TH:i:s\Z');

        $events[] = $Event;
        return $events;
    }

    public function actionGetTocrDetails() {
//        Yii::$app->response->format = Response::FORMAT_JSON;
        $tocr_number = $_POST['tocr_number'];
        $status = $_POST['status'];
        $test = array();
        if($status=='Assigned') {
            $file = 'assigned_test_list';
        }else if($status=='Received') {
            $file = 'received_test_list';
        }else if($status=='Completed'){
            $file = 'completed_test_list';
        }
        $q ="select test.tocr_number,dept.dept_name,test.status,test.id as detail_id,test.test_name,test.sub_test_name,test.sample_details,test.heat_no,test.sample_id,test.remarks from trn_test_details as test JOIN mst_departments as dept on (dept.id=test.mst_department_id) JOIN mst_user_department_mappings as map on (map.mst_department_id=test.mst_department_id) where test.tocr_number=:tocr_number and test.status=:status and map.mst_user_id=:user_id";
//        echo $res = Yii::$app->getDb()->createCommand($q,['tocr_number'=>$tocr_number,'status'=>$status,'user_id'=>Yii::$app->user->identity->id])->rawSql; exit;
        $res = Yii::$app->getDb()->createCommand($q,['tocr_number'=>$tocr_number,'status'=>$status,'user_id'=>Yii::$app->user->identity->id])->queryAll();
//        echo "<pre>"; print_r($res); echo "</pre>"; exit;
        if(!empty($res)) {
            foreach($res as $val) {
                $test[$val['dept_name']][$val['detail_id']]['test_name']= $val['test_name'];
                $test[$val['dept_name']][$val['detail_id']]['sub_test_name']= $val['sub_test_name'];
                $test[$val['dept_name']][$val['detail_id']]['sample_detail']= $val['sample_details'];
                $test[$val['dept_name']][$val['detail_id']]['heat_no']= $val['heat_no'];
                $test[$val['dept_name']][$val['detail_id']]['sample_id']= $val['sample_id'];
                $test[$val['dept_name']][$val['detail_id']]['remarks']= $val['remarks'];
                $test[$val['dept_name']][$val['detail_id']]['status']= $val['status'];
                $test[$val['dept_name']][$val['detail_id']]['tocr_number']= $val['tocr_number'];
            }
        }
//        return [ 'title'=>'<center>TOCR Details</center>','content'=>$this->renderAjax($file,['details'=>$test]), 'footer'=>'',];
        return $this->renderAjax($file,['details'=>$test]);
    }
}
