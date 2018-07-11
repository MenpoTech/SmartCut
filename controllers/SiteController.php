<?php

namespace app\controllers;

use app\models\MstMenus;
use app\models\TrnTestDetails;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'logout'],
                'rules' => [
                    [
                        'actions' => ['index', 'logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }
    /*public function beforeAction($action)
    {
        if (!Yii::$app->user->identity) {
            return $this->redirect(array('site/login'));
        }
        return parent::beforeAction($action);
    }*/
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
//            return $this->redirect(array('site/login'));
        }
        else {
            return $this->render('index');
        }
    }

    public function actionChat()
    {
        if (Yii::$app->request->post()) {
            $name = Yii::$app->request->post('name');
            $message = Yii::$app->request->post('message');

            //Just to store the chat

            return Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'chat_triggered',
                'message' => Json::encode(['name' => Yii::$app->user->identity->displayname, 'message' => $message,'time'=>date('h:i:s A')])
            ]);
        }
        return $this->render('chat');
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $mst_menu = new MstMenus();
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $user_id = Yii::$app->user->identity->id;
            $get_role_url = $mst_menu->getRoleBasedUrl($user_id);
            if(!empty($get_role_url)) {
                return $this->redirect([$get_role_url[0]['default_route']]);
            }
            else {
            return $this->goBack();
        }
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        unset($_SESSION['array_menus']);
        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionAbout()
    {
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'new_tocr',
            'message' => Json::encode(['name' => Yii::$app->user->identity->displayname, 'message' => '17/00112 TOCR Added ','departments'=>'31,2'])
        ]);
        return $this->render('about');
    }
}
