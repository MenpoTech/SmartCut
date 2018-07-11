<?php

namespace app\modules\api\controllers;

use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\filters\ContentNegotiator;
use yii\helpers\Url;
use yii\rest\Controller;

use app\models\MstFeedbacks;

class ApiFeedbackController extends Controller{
	public function behaviors(){
		$behaviors = parent::behaviors();
		
		$behaviors['contentNegotiator'] = [
			'class' => ContentNegotiator::className(),
			'only' => [
				'feedforward-entry',
				'feedback-exit',
				'get-language',
			],
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        return $behaviors;
	}
	
    protected function verbs(){
		return [
			'feedforward-entry' 	=> ['POST'],
			'feedback-exit' 		=> ['POST'],
			'get-language'			=> ['GET'],
		];
    }
	
	/*
	* Get the Language
	*/
	public function actionGetLanguage(){
		$languageArr = array('1'=>'English','2'=>'தமிழ்', '3'=>'മലയാളം','4'=>'हिन्दी','5'=>'తెలుగు');
		
		foreach($languageArr as $key => $value){
			$lang[] = [
				'lcode' => $key,
				'lname' => $value,
			];
		}
		return [ 
			'data'=>$lang, 
		];
	}
	
	/*
	* Feedforward Entry Questions
	*/
	public function actionFeedforwardEntry(){
		$param = $_REQUEST['language'];
		$getDepartSQL = "
			SELECT
				id AS dptid,
				code AS dptcode,
				name AS dptname,
				name_local AS dptlocalname
			FROM 
			mst_feedback_departments dpt
			WHERE dpt.status = 2
			ORDER BY order_by 
		";
		
		$command 	= Yii::$app->db->createCommand($getDepartSQL);
		$dplists 	= $command->queryAll();
		
		// Get the Questionnaire 
		foreach ($dplists as $dplist){
			$dptid = $dplist['dptid'];
			// Get the Feedback Questions 
			$fdbckSQL = "
				SELECT
					id,
					institution_id,
					department_id,
					feed_description,
					feed_description_tamil,
					rating_type,
					rating_value
				FROM 
				mst_feedbacks fbck
				WHERE fbck.department_id=".$dptid." AND fbck.status = 1 AND fbck.feed_category = 'ENTRY'
				ORDER BY fbck.department_id,fbck.order_by ASC;
			"; 
			
			$fdbcommand 	= Yii::$app->db->createCommand($fdbckSQL);
			$fdbklists 		= $fdbcommand->queryAll();
			$header_id 		= $dptid;
			switch($param){
				case "1":
					$header = $dplist['dptname'];
					break;
				case "2" : 
					$header = $dplist['dptlocalname'];
					break;
				default:
					$header = $dplist['dptname'];
					break;
			}
			
			foreach($fdbklists as $fdbklist){
				switch($param){
					case "1":
						$questions = $fdbklist['feed_description'];
						break;
					case "2" : 
						$questions = $fdbklist['feed_description_tamil'];
						break;
					default:
						$questions = $fdbklist['feed_description'];
						break;
				}
				
				$child[] = [
					'id' 				=> $fdbklist['id'],
					'question' 			=> $questions,
					'rating_type' 		=> $fdbklist['rating_type'],
					'option' 			=> $fdbklist['rating_value'],
				];
				
			}
			
			$fdbck[] = array(
				'header'		=> $header,
				'header_id'		=> $header_id,
				'child'			=> $child,
			);
			$child = array();
		}
		
		return [ 
			'data'=>$fdbck, 
		];
	}
	
	
	/*
	* Feedback Exit Questions
	*/
	public function actionFeedbackExit(){
		$param = $_REQUEST['language'];
		
		$getDepartSQL = "
			SELECT
				id AS dptid,
				code AS dptcode,
				name AS dptname,
				name_local AS dptlocalname
			FROM 
			mst_feedback_departments dpt
			WHERE dpt.status = 4
			ORDER BY order_by 
		";
		
		$command 	= Yii::$app->db->createCommand($getDepartSQL);
		$dplists 	= $command->queryAll();
		
		// Get the Questionnaire 
		foreach ($dplists as $dplist){
			$dptid = $dplist['dptid'];
			
			// Get the Feedback Questions 
			$fdbckSQL = "
				SELECT
					id,
					institution_id,
					department_id,
					feed_description,
					feed_description_tamil,
					rating_type,
					rating_value
				FROM 
				mst_feedbacks fbck
				WHERE fbck.department_id=".$dptid." AND fbck.status = 1 AND fbck.feed_category = 'EXIT'
				ORDER BY fbck.department_id,fbck.order_by ASC;
			"; 
			
			$fdbcommand 	= Yii::$app->db->createCommand($fdbckSQL);
			$fdbklists 		= $fdbcommand->queryAll();
			$header_id 		= $dptid;
			switch($param){
				case "1":
					$header = $dplist['dptname'];
					break;
				case "2" : 
					$header = $dplist['dptlocalname'];
					break;
				default:
					$header = $dplist['dptname'];
					break;
			}
			
			foreach($fdbklists as $fdbklist){
				switch($param){
					case "1":
						$questions = $fdbklist['feed_description'];
						break;
					case "2" : 
						$questions = $fdbklist['feed_description_tamil'];
						break;
					default:
						$questions = $fdbklist['feed_description'];
						break;
				}
				$child[] = [
					'id' 				=> $fdbklist['id'],
					'question' 			=> $questions,
					'rating_type' 		=> $fdbklist['rating_type'],
					'option' 			=> $fdbklist['rating_value'],
				];
			}
			
			$fdbck[] = array(
				'header'		=> $header,
				'header_id'		=> $header_id,
				'child'			=> $child,
			);
			$child = array();
		}
		
		return [ 
			'data'=>$fdbck, 
		];
	}
}
