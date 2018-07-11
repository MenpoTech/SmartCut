<?php

namespace app\controllers;

use app\models\TrnTestDetailSearch;

class TocrNewController extends \yii\web\Controller
{

    public function beforeAction() {
        if (\Yii::$app->user->isGuest) {
            return $this->redirect(array('site/login'));
        }else {
            return true;
        }
    }
    public function actionIndex()
    {
        $searchModel = new TrnTestDetailSearch();
        $dataProvider = $searchModel->new_search(\Yii::$app->request->queryParams);

        return $this->renderAjax('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionRowDetail() {

    }

}
