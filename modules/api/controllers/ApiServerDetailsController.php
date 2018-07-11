<?php

namespace app\modules\api\controllers;
use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\filters\ContentNegotiator;
use yii\helpers\Url;
use yii\filters\VerbFilter;
use yii\rest\Controller;

class ApiServerDetailsController extends Controller
{
	 /**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return ArrayHelper::merge(parent::behaviors(), [
			[
				'class' => ContentNegotiator::className(),
				'only' => [	
					'get-server-date',
					'get-url-details',
				],
				'formats' => [
					'application/json' => Response::FORMAT_JSON,
				],
			],
		]);
	}
	
	protected function verbs()
    {
       return [
            'get-server-date' => ['get'],
			'get-url-details' => ['get'],
		];
    }
	
	/*
	* Get the Server Date
	*/ 
	public function actionGetServerDate()
    {
		$today_date 	= date('Y-m-d H:i:s');
		$today_period	= date('h:i A');
		
		$data[] = [
			'today_date' 	=> $today_date,
			'today_period' 	=> $today_period,
			'ymd_format' 	=> date('Y-m-d'),
			'ymdhis_format' => date('Y-m-d H:i:s'),
			'dmy_format'	=> date('d-m-Y'),
			'24hrs_format'	=> date('H:i'),
			'month_format'  => date('M'),
			'year_format'	=> date('Y'),
		];
		
		return [
            'data'=>$data,
        ];
		
	}
	
	
	/*
	* Get the Server Url Details
	*/ 
	public function actionGetUrlDetails()
    {
		$data[]=[
			'dev-url' => \Yii::$app->params['api-dev-url']['host'],
			'test-url' => \Yii::$app->params['api-test-url']['host'],
			'prod-url' => \Yii::$app->params['api-prod-url']['host'],
		];
		
		return [
            'data'=>$data,
        ];
	}
}
