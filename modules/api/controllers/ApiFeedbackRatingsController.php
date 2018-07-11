<?php

namespace app\modules\api\controllers;

use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\filters\ContentNegotiator;
use yii\helpers\Url;
use yii\rest\Controller;

use app\models\MstFeedbacks;
use app\models\TrnFeedbackHeader;
use app\models\TrnFeedbackRatings;
use app\models\TrnSmsApiQueues;
use app\models\MstActivePatients;
use app\models\MstFeedbackDepartments;

class ApiFeedbackRatingsController extends Controller{
    public function behaviors(){
		$behaviors = parent::behaviors();
		
		$behaviors['contentNegotiator'] = [
			'class' => ContentNegotiator::className(),
			'only' => [
				'inpatient-feedback-create',
			],
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        return $behaviors;
	}
	
    protected function verbs(){
		return [
			'inpatient-feedback-create' => ['POST'],
		];
    }
	
	public function actionInpatientFeedbackCreate(){
		$params=$_REQUEST['feedback'];
		$data = json_decode($params, TRUE);
		
		$fdbckheaders = array();
		$fdbckratings = array();
		$created_date = date('Y-m-d H:i:s');
		
		// Get the Count of the Total records 
		$cnt = count($data['result']);
		
		for($i=0;$i<$cnt;$i++){
			// Feedback headers 
			$fdbckheaders[$i]['patientCode'] = $data['patdetails']['patcode'];
			$fdbckheaders[$i]['patientName'] = $data['patdetails']['patname'];
			$fdbckheaders[$i]['deptCode'] = $data['result'][$i]['deptid'];
			
			// Insert to the Table 
			$hdrmodel	= new TrnFeedbackHeader();
			
			// Assign the values to the Database fields
			$hdrmodel->institution_id 	= 1;
			$hdrmodel->department_id 	= $fdbckheaders[$i]['deptCode'];
			$hdrmodel->patient_name 	= $fdbckheaders[$i]['patientName'];
			$hdrmodel->patient_code 	= $fdbckheaders[$i]['patientCode'];
			$hdrmodel->created_date 	= $created_date;
			
			// Insert ratings details to feedback details 
			if ($hdrmodel->save()) {
				$id 	= $hdrmodel->id;
				
				// Insert record to the Feedback ratings 
				$ratecount = count($data['result'][$i]['feedback']);
				
				for($j=0;$j<$ratecount;$j++){
					$feedid = $data['result'][$i]['feedback'][$j]['ques_id'];
					$rating = $data['result'][$i]['feedback'][$j]['rating'];
					$rating_val = $data['result'][$i]['feedback'][$j]['values'];
					// Ratings Model
					$ratingsmodel 	= new TrnFeedbackRatings();
					$ratingsmodel->trn_feedback_header_id = $id;
					$ratingsmodel->feedback_id = $feedid;
					// Ratings Value 
					switch($rating){
						case 1: 
							$ratingsmodel->rating_1 = 1;
							$ratingsmodel->rating_2 = 0;
							$ratingsmodel->rating_3 = 0;
							$ratingsmodel->rating_4 = 0;
							$ratingsmodel->rating_5 = 0;
						break;
						case 2: 
							$ratingsmodel->rating_1 = 0;
							$ratingsmodel->rating_2 = 1;
							$ratingsmodel->rating_3 = 0;
							$ratingsmodel->rating_4 = 0;
							$ratingsmodel->rating_5 = 0;
						break;
						case 3: 
							$ratingsmodel->rating_1 = 0;
							$ratingsmodel->rating_2 = 0;
							$ratingsmodel->rating_3 = 1;
							$ratingsmodel->rating_4 = 0;
							$ratingsmodel->rating_5 = 0;
						break;
						case 4: 
							$ratingsmodel->rating_1 = 0;
							$ratingsmodel->rating_2 = 0;
							$ratingsmodel->rating_3 = 0;
							$ratingsmodel->rating_4 = 1;
							$ratingsmodel->rating_5 = 0;
						break;
						case 5: 
							$ratingsmodel->rating_1 = 0;
							$ratingsmodel->rating_2 = 0;
							$ratingsmodel->rating_3 = 0;
							$ratingsmodel->rating_4 = 0;
							$ratingsmodel->rating_5 = 1;
						break;
						case 0: 
							$ratingsmodel->rating_1 = 0;
							$ratingsmodel->rating_2 = 0;
							$ratingsmodel->rating_3 = 0;
							$ratingsmodel->rating_4 = 0;
							$ratingsmodel->rating_5 = 0;
						break;	
					}
					$ratingsmodel->rating_comments = $rating_val;
					$ratingsmodel->created_date = $created_date;
					$ratingsmodel->save();
				}
			}
		}
		echo "Feedback Successfully Completed";
	}
}


