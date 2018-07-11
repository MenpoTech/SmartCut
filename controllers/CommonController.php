<?php

namespace app\controllers;

use app\models\Patient;
use app\models\TrnIpBilldetail;
use app\models\TrnPrescriptions;

class CommonController extends \yii\web\Controller
{

    public function beforeAction() {
        if (\Yii::$app->user->isGuest) {
            return $this->redirect(array('site/login'));
        }else {
            return true;
        }
    }

    public function actionIndex($visit_id=0)
    {
        $obj= new Patient();
        $details = $obj->getPatientDetailsByPatientCode('','',$visit_id);
        return $this->renderPartial('index',['details'=>$details]);
    }

    public function actionVisitDetails($visit_id=0)
    {
        $obj= new Patient();
        $details = $obj->getPatientDetailsByVisit($visit_id);
        return $this->renderPartial('visit-details',['details'=>$details]);
    }

    public function actionDiscIndex($visit_id=0)
    {
        $obj= new Patient();
        $details = $obj->getPatientDetailsAfterDischargeByPatientCode('','',$visit_id);
        return $this->renderPartial('disc-index',['details'=>$details]);
    }

    public function actionVisitInformation($trn_visit_id=0)
    {
        $obj= new TrnPrescriptions();
        $details = $obj->getPatientInformationByVisitId($trn_visit_id);
        return $this->renderPartial('visit-information',['details'=>$details]);
    }

    public function actionLoadPatientDropDown() {
        return $this->render('patient-drop-down');
    }
    public function actionLoadUserDropDown() {
        return $this->render('user-drop-down');
    }

    public function actionCorporateDropDown() {
        return $this->render('corporate-drop-down');
    }
}
