<?php

namespace app\modules\api\controllers;

use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\filters\ContentNegotiator;
use yii\helpers\Url;
use yii\filters\VerbFilter;
use yii\rest\Controller;

use app\modules\api\models\TrnEmpLeaveEntries;
use app\modules\api\models\TrnEmpPermissions;

class ApiEmployeeDetailsController extends Controller
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
					'get-daily-attendance',
					'get-dept-employees',
					'get-present-employees',
					'get-absent-employees',
					'get-employee-punch',
					'get-all-employees',
					'post-leave-request',
					'get-leave-types',
					'get-approval-employees',
					'post-permission-request',
					'post-leave-approval',
					'get-employees-profile',
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
            'get-daily-attendance'	=>['get'],
			'get-dept-employees'	=>['get'],
			'get-present-employees'	=>['get'],
			'get-absent-employees'	=>['get'],
			'get-employee-punch'	=>['get'],
			'get-all-employees'		=>['get'],
			'post-leave-request'	=>['post'],
			'get-leave-types'		=>['get'],
			'get-approval-employees' => ['get'],
			'post-permission-request' => ['post'],
			'post-leave-approval' => ['post'],
			'get-employees-profile'=>['get'],

		];
    }
	
	/*
	* Function to Get the Daily Attendance 
	*/
	public function actionGetDailyAttendance($date){
		$getAttendanceSQL = "
			SELECT
				dept.id AS deptid,
				dept.dept_name AS deptname,
				COUNT(emp.id) AS empcount,
				CASE WHEN attendance.count IS NULL THEN 0 ELSE attendance.count END AS present,
				CASE WHEN COUNT(emp.id)- (CASE WHEN attendance.count IS NULL THEN 0 WHEN attendance.count =0 THEN 0 ELSE attendance.count  END)  IS NULL THEN 0 ELSE COUNT(emp.id)- (CASE WHEN attendance.count IS NULL THEN 0 WHEN attendance.count =0 THEN 
0 ELSE attendance.count  END) END AS absent
			FROM mst_employees  emp
			--JOIN temp_employees t_emp ON (t_emp.empno = emp.empno)
			LEFT JOIN mst_departments dept ON (dept.id = emp.mst_department_id AND dept.status = 1 AND dept.is_deleted =0)
			LEFT JOIN (
				SELECT 
					dept_id,
					count(*) 
					FROM ( 
						SELECT 
							DISTINCT dept.id AS dept_id,
							emp.empno 
						FROM trn_emp_attendances AS emp
						LEFT JOIN mst_employees ON (mst_employees.empno=emp.empno)
						JOIN mst_departments dept ON (dept.id = mst_employees.mst_department_id AND dept.status = 1 AND dept.is_deleted =0)
						WHERE att_date =:date
					) AS a
				GROUP BY dept_id) AS attendance ON (dept.id=attendance.dept_id)
			WHERE dept.id IS NOT NULL and emp.status=1
			GROUP BY dept.id ,attendance.count,dept.dept_name
			ORDER BY dept.dept_name
		";
		$last_updated = "select to_char(attd.created_date,'dd-mm-yyyy HH:MI AM') as last_updated from trn_emp_attendances as attd order by id desc limit 1";
		$command_last 	= Yii::$app->ehms->createCommand($last_updated);
		$command 	= Yii::$app->ehms->createCommand($getAttendanceSQL);
		// Bind the Values - SQL Injection Prevention
		$command->bindParam(":date",$date);
		$adetail 	= $command->queryAll();
		$ldetail    = $command_last->queryAll();
		// Get the Total Sum
		$totalsum = 0;
		$presentsum = 0;
		$absentsum = 0;
		foreach($adetail as $item) {
			$totalsum += $item['empcount'];
			$presentsum += $item['present'];
			$absentsum += $item['absent'];
		}
		
		$deptArr = array();
		$i=0;
		foreach($adetail as $detail){
			$deptArr[$i]['deptid'] 	= $detail['deptid'];
			$deptArr[$i]['deptname'] = $detail['deptname'];
			$deptArr[$i]['empcount'] = $detail['empcount'];
			$deptArr[$i]['present'] = $detail['present'];
			$deptArr[$i]['absent'] = $detail['absent'];
			$i++;
		}
		
		$data['totalsum'] = $totalsum;
		$data['presentsum'] = $presentsum;
		$data['absentsum'] = $absentsum;
		$data['last_updated'] = $ldetail[0]['last_updated'];
		$data['details'] = $deptArr;
		
		return [
			'data'=>$data,
		];
	}
	
	/*
	* Function to Get All the Employees for a Particular Department
	*/
	public function actionGetDeptEmployees($deptid,$date){
		//echo '<pre>';print_r($date);
		$getDeptEmployeeSQL = "
			select empid,empname,empmail,empphone,desgname,case when emp_photo is null then 'http://172.30.3.46/staging/HMiS/web/img/employees/' else emp_photo end as emp_photo,age,case when max(in_time) is null then '' else max(in_time) end in_time,case when max(out_time) is null then '' else max(out_time) end as out_time from (
SELECT 
case when in_out_status='In' then to_char(attd.punch_time,'HH:MI AM')  else null end in_time,
case when in_out_status='Out' then to_char(attd.punch_time,'HH:MI AM') else null end out_time,
				emp.empno as empid,
				emp.emp_fname as empname,
				CASE WHEN (emp.emp_pmail IS NULL OR emp.emp_pmail= '') THEN 'null' ELSE emp.emp_pmail END as empmail,
				CASE WHEN emp.emp_handphone = '' THEN 'null' ELSE emp.emp_handphone END as empphone,emp_photo,
				desg.name as desgname,case when emp_dob is null then '' else substring(age(date(now())::timestamp,emp_dob::timestamp)::character varying(100),1,3) end as age
			FROM 
			mst_employees emp 
			left join trn_emp_attendances as attd on (attd.empno=emp.empno and attd.att_date ='$date')
			LEFT JOIN designations desg ON (desg.id = emp.mst_designation_id)
			WHERE emp.mst_department_id =:deptid  and  emp.status=1
			ORDER BY empname) as a
group by empid,empname,empmail,empphone,desgname,emp_photo,age
order by empname;
		";
		
		$command = Yii::$app->ehms->createCommand($getDeptEmployeeSQL);
		// Bind the Values - SQL Injection Prevention
		$command->bindParam(":deptid",$deptid);
		
		//Query Execute
		$empdetails = $command->queryAll();
		
		// Get the Department Details
		$deptname = $this->getDeptName($deptid);
		
		$data['deptid']	  = $deptid;
		$data['deptname'] = $deptname;
		$data['employee'] = $empdetails;
		
		$data1[] = $data;
		
		return [
			'data'=>$data1,
		];
	}
	
	/*
	* Function to Get All the Employees
	*/
	public function actionGetAllEmployees(){
		$getAllEmployeeSQL = "
			SELECT 
				emp.empno as empid
			FROM 
			mst_employees emp 
			LEFT JOIN designations desg ON (desg.id = emp.mst_designation_id)
			WHERE emp.status=1
		";
		
		$command = Yii::$app->ehms->createCommand($getAllEmployeeSQL);
		
		//Query Execute
		$empdetails = $command->queryAll();
		
		return [
			'data'=>$empdetails,
		];
	}
	
	/*
	* Function to Get the Present Employees for a Particular Department
	*/
	public function actionGetPresentEmployees($deptid,$date){
		// Get the Present Employees in the Particular Department
		//echo '<pre>';print_r(\Yii::$app->baseUrl);exit;
		$getPresentEmpSQL = "
			select empid,empname,empmail,empphone,desgname,case when emp_photo is null then 'http://172.30.3.46/staging/HMiS/web/img/employees/' else emp_photo end as emp_photo,age,case when max(in_time) is null then '' else max(in_time) end in_time,case when max(out_time) is null then '' else max(out_time) end as out_time from (
SELECT 
case when in_out_status='In' then to_char(attd.punch_time,'HH:MI AM')  else null end in_time,
case when in_out_status='Out' then to_char(attd.punch_time,'HH:MI AM') else null end out_time,
				emp.empno AS empid,
				emp.emp_fname AS empname,
				CASE WHEN (emp.emp_pmail IS NULL OR emp.emp_pmail= '') THEN 'null' ELSE emp.emp_pmail END as empmail,
				CASE WHEN emp.emp_handphone = '' THEN 'null' ELSE emp.emp_handphone END as empphone,emp_photo,
				desg.name AS desgname,
				case when emp_dob is null then '' else substring(age(date(now())::timestamp,emp_dob::timestamp)::character varying(100),1,3) end as age
			FROM 
			trn_emp_attendances attd 
			JOIN mst_employees emp ON (emp.empno = attd.empno)
			JOIN mst_departments dept ON (dept.id = emp.mst_department_id)
			LEFT JOIN designations desg ON (desg.id = emp.mst_designation_id)
			WHERE attd.att_date =:date AND dept.id =:deptid and  emp.status=1
			ORDER BY emp.emp_fname) as a
group by empid,empname,empmail,empphone,desgname,emp_photo,age
order by empname
		";
		
		$command = Yii::$app->ehms->createCommand($getPresentEmpSQL);
		// Bind the Values - SQL Injection Prevention
		$command->bindParam(":deptid",$deptid);
		$command->bindParam(":date",$date);
		
		//Query Execute
		$emppresent = $command->queryAll();
		
		// Get the Department Details
		$deptname = $this->getDeptName($deptid);
		
		$data['deptid']	  = $deptid;
		$data['deptname'] = $deptname;
		$data['employee'] = $emppresent;
		
		$data1[] = $data;
		
		return [
			'data'=>$data1,
		];
	}
	
	/*
	* Function to Get the Absent Employees for a Particular Department
	*/
	public function actionGetAbsentEmployees($deptid,$date){
		// Get the Absent Employees in the Particular Department
		$getAbsentEmpSQL = "
			(SELECT 
				emp.empno as empid,
				emp.emp_fname as empname,
				CASE WHEN (emp.emp_pmail IS NULL OR emp.emp_pmail= '') THEN 'null' ELSE emp.emp_pmail END as empmail,
				CASE WHEN emp.emp_handphone = '' THEN 'null' ELSE emp.emp_handphone END as empphone,case when emp_photo is null then 'http://172.30.3.46/staging/HMiS/web/img/employees/' else emp_photo end as emp_photo,
				desg.name AS desgname,'' as in_time,'' as out_time,case when emp_dob is null then '' else substring(age(date(now())::timestamp,emp_dob::timestamp)::character varying(100),1,3) end as age
			FROM mst_employees emp 
			--JOIN temp_employees t_emp ON (t_emp.empno = emp.empno)
			LEFT JOIN designations desg ON (desg.id = emp.mst_designation_id)
			WHERE emp.mst_department_id =:deptid and  emp.status=1
			ORDER BY emp.emp_fname)
			EXCEPT
			(SELECT 
				DISTINCT emp.empno AS empid,
				emp.emp_fname AS empname,
				CASE WHEN (emp.emp_pmail IS NULL OR emp.emp_pmail= '') THEN 'null' ELSE emp.emp_pmail END as empmail,
				CASE WHEN emp.emp_handphone = '' THEN 'null' ELSE emp.emp_handphone END as empphone,case when emp_photo is null then 'http://172.30.3.46/staging/HMiS/web/img/employees/' else emp_photo end as emp_photo,
				desg.name AS desgname,'' as in_time,'' as out_time,case when emp_dob is null then '' else substring(age(date(now())::timestamp,emp_dob::timestamp)::character varying(100),1,3) end as age
			FROM trn_emp_attendances attd 
			JOIN mst_employees emp ON (emp.empno = attd.empno)
			JOIN mst_departments dept ON (dept.id = emp.mst_department_id)
			LEFT JOIN designations desg ON (desg.id = emp.mst_designation_id)
			WHERE attd.att_date =:date AND dept.id =:deptid and  emp.status=1
			ORDER BY emp.emp_fname)
		";
		
		$command = Yii::$app->ehms->createCommand($getAbsentEmpSQL);
		// Bind the Values - SQL Injection Prevention
		$command->bindParam(":deptid",$deptid);
		$command->bindParam(":date",$date);
		
		//Query Execute
		$empabsent = $command->queryAll();
		
		// Get the Department Details
		$deptname = $this->getDeptName($deptid);
		
		$data['deptid']	  = $deptid;
		$data['deptname'] = $deptname;
		$data['employee'] = $empabsent;
		
		$data1[] = $data;
		return [
			'data'=>$data1,
		];
	}
	
	/*
	* Function to get the Employee Punch Details for a particular Month
	*/
	public function actionGetEmployeePunch($empid,$month,$year){
		// Get the Complete Punch Details of an Employee for a Particular Month 
		$getEmpPunchSQL = "
			SELECT 
				attd.att_date AS pdate,
				array_to_string(array_agg(to_char(attd.punch_time,'HH:MI AM')),',') ptime,
				array_to_string(array_agg(to_char(attd.punch_time,'YYYY-MM-DD HH:MI:SS AM')),',') dtime
			FROM 
			trn_emp_attendances attd 
			JOIN mst_employees emp ON (emp.empno = attd.empno)
			JOIN mst_departments dept ON (dept.id = emp.mst_department_id)
			WHERE emp.empno =:empid
			GROUP BY attd.att_date
			ORDER BY attd.att_date
		";
		
		$command = Yii::$app->ehms->createCommand($getEmpPunchSQL);
		// Bind the Values - SQL Injection Prevention
		$command->bindParam(":empid",$empid);
		//$command->bindParam(":month",$month);
		//$command->bindParam(":year",$year);
		
		//Query Execute
		$punchdetails = $command->queryAll();
		$cal = $this->getDayCalendar($month,$year);
		$punchArr = array();
		
		for($i=0;$i<count($cal);$i++){
			$punchArr[$i]['date'] 		= $cal[$i]['date'];
			$punchArr[$i]['day'] 		= $cal[$i]['day'];
			$punchArr[$i]['daytype'] 	= $cal[$i]['daytype'];
			$intime 	= '';
			$outtime 	= '';
			$workhr 	= '';
			for($j=0;$j<count($punchdetails);$j++){
				if($punchArr[$i]['date'] == $punchdetails[$j]['pdate']){
					//echo "true";
					//$punchArr[$i]['status'] = 'true'.$punchdetails[$j]['pdate'];
					$str_split = explode(",",$punchdetails[$j]['ptime']);
					$cnt = count($str_split);
					if ($cnt >1){
						$time_split = explode(",",$punchdetails[$j]['dtime']);
						$time_difference = $this->getTimeDifference($time_split[0],$time_split[1]);
						$intime 	= $str_split[0];
						$outtime 	= $str_split[1];
						$workhr 	= $time_difference;
					}else{
						$intime 	= $str_split[0];
						$outtime 	= '';
						$workhr 	= '';
					}
				} 
				$punchArr[$i]['intime'] 	= $intime;
				$punchArr[$i]['outtime'] 	= $outtime;
				$punchArr[$i]['workhr'] 	= $workhr;
			}
		}
		
		echo "<pre>";
		print_r($punchArr);
		echo "</pre>";
		/*return [
			'data'=>$punchArr,
		];*/
	}
	public function actionGetEmployeePunchDetails($empid,$month,$year){
		$cal = $this->getDayCalendar($month,$year);
		//echo '<pre>';print_r(count($cal));exit;
		$from_date  = $cal[0]['date'];
		$to_date = $cal[count($cal)-1]['date'];
		// Get the Complete Punch Details of an Employee for a Particular Month 
		$getEmpPunchSQL = "
			select to_char(pdate,'dd-mm-YYYY') as pdate,daytype,day,(case when max(in1) is null then '' else max(in1) end)AS IN1,(case when max(out1) is null then '' else max(out1) end)AS OUT1,(case when substring((case when to_char(max(out_punch)::timestamp-max(in_punch)::timestamp,'HH:MI') is null 
then '' else to_char(max(out_punch)::timestamp-max(in_punch)::timestamp,'HH:MI') end),1,1)='-' then '' else case when to_char(max(out_punch)::timestamp-max(in_punch)::timestamp,'HH:MI') is null 
then '' else to_char(max(out_punch)::timestamp-max(in_punch)::timestamp,'HH:MI') end end  )as workhr,'' REMARKS   from (
	SELECT 
	cal.date AS pdate,
	case when substring(to_char(cal.date, 'Day'),1,3)='Sun' then 'l' else 'w' end as daytype,
	substring(to_char(cal.date, 'Day'),1,3) as day,
	case when in_out_status='In' then punch_time else null end as in_punch,
	case when in_out_status='Out' then punch_time  else null end as out_punch,
	case when in_out_status='In' then to_char(punch_time, 'HH:MI AM') else null end as in1,
	case when in_out_status='Out' then to_char(punch_time, 'HH:MI AM')  else null end as out1
FROM 
(select to_char(generate_series('$from_date', '$to_date', '1 day'::interval),'YYYY-mm-dd')::date date) cal 
left join trn_emp_attendances as attd on (cal.date=attd.att_date and attd.empno =:empid  )
left JOIN mst_employees emp ON (emp.empno = attd.empno and emp.empno =:empid )
left JOIN mst_departments dept ON (dept.id = emp.mst_department_id)
) as a group by  pdate,daytype,day
order by pdate

		";
		 
		 $getEmpBasicDetails = "select emp_fname,empno,mst_departments.dept_name,mst_designations.design_name,attendance.tot_days,attendance.present_days,
attendance.tot_days-attendance.present_days as absent_days
 from mst_employees 
left join mst_departments on (mst_departments.id=mst_employees.mst_department_id)
left join mst_designations on (mst_designations.id=mst_employees.mst_designation_id)
left join (select count(cal_date) as tot_days,count(att_date) as present_days from (
select cal.date as cal_date,trn_emp_attendances.att_date from (select to_char(generate_series('$from_date', '$to_date', '1 day'::interval),'YYYY-mm-dd')::date as date) cal
left join (select distinct att_date from trn_emp_attendances where empno ='$empid' and att_date between '$from_date' and '$to_date')
as  trn_emp_attendances on (cal.date=trn_emp_attendances.att_date)
) as a) as attendance on (1=1)
where empno ='$empid' ";
		
		$command = Yii::$app->ehms->createCommand($getEmpPunchSQL);
		// Bind the Values - SQL Injection Prevention
		$command->bindParam(":empid",$empid);
		
		$b_command =  Yii::$app->ehms->createCommand($getEmpBasicDetails);
		//$command->bindParam(":month",$month);
		//$command->bindParam(":year",$year);
		
		//Query Execute
		$punchdetails = $command->queryAll();
		$empdetails  = $b_command ->queryall();
		//echo '<pre>';print_r($punchdetails);exit;
		$cal = $this->getDayCalendar($month,$year);
		$punchArr = array();
		
		for($i=0;$i<count($punchdetails);$i++){
			if($i==0)
			{
			$punchArr['data']['empdetails'][$i]['empname'] 		= $empdetails[$i]['emp_fname'];
			$punchArr['data']['empdetails'][$i]['empcode'] 		= $empdetails[$i]['empno'];
			$punchArr['data']['empdetails'][$i]['department'] 	= $empdetails[$i]['dept_name'];
			$punchArr['data']['empdetails'][$i]['desination'] 	= $empdetails[$i]['design_name'];
			$punchArr['data']['empdetails'][$i]['totaldays'] 	= $empdetails[$i]['tot_days'];
			$punchArr['data']['empdetails'][$i]['presentdays'] 	= $empdetails[$i]['present_days'];
			$punchArr['data']['empdetails'][$i]['absentdays'] 	= $empdetails[$i]['absent_days'];
			$punchArr['data']['empdetails'][$i]['leavebalnc'] 	= 0;
			}
			$punchArr['data']['punchdetails'][$i]['date'] 		= $punchdetails[$i]['pdate'];
			$punchArr['data']['punchdetails'][$i]['day'] 		= $punchdetails[$i]['day'];
			$punchArr['data']['punchdetails'][$i]['daytype'] 	= $punchdetails[$i]['daytype'];
			$punchArr['data']['punchdetails'][$i]['intime'] 	= $punchdetails[$i]['in1'];
			$punchArr['data']['punchdetails'][$i]['outtime'] 	= $punchdetails[$i]['out1'];
			$punchArr['data']['punchdetails'][$i]['workhr'] 	= $punchdetails[$i]['workhr'];
			$punchArr['data']['punchdetails'][$i]['remarks'] 	= $punchdetails[$i]['remarks'];

		}
		$puncharr = $punchArr;
		
//echo "<pre>";print_r($punchArr);echo "</pre>";exit;
		echo "";print_r(json_encode($puncharr));exit;
		return [
			'data'=>$puncharr,
		];
	}
	public function actionGetEmployeePunchDetails1($empid,$month,$year){
		// Get the Complete Punch Details of an Employee for a Particular Month 
		$getEmpPunchSQL = "
			select to_char(pdate,'dd-mm-YYYY') as pdate,daytype,day,max(in1) AS IN1,max(out1)AS OUT1,to_char(max(out_punch)::timestamp-max(in_punch)::timestamp,'HH:MI') as workhr,'' REMARKS   from (
	SELECT 
	cal.date AS pdate,
	case when substring(to_char(cal.date, 'Day'),1,3)='Sun' then 'l' else 'w' end as daytype,
	substring(to_char(cal.date, 'Day'),1,3) as day,
	case when in_out_status='In' then punch_time else null end as in_punch,
	case when in_out_status='Out' then punch_time  else null end as out_punch,
	case when in_out_status='In' then to_char(punch_time, 'HH:MI AM') else null end as in1,
	case when in_out_status='Out' then to_char(punch_time, 'HH:MI AM')  else null end as out1
FROM 
(select to_char(generate_series('2016-12-01', '2016-12-31', '1 day'::interval),'YYYY-mm-dd')::date date) cal 
left join trn_emp_attendances as attd on (cal.date=attd.att_date and attd.empno =:empid  )
left JOIN mst_employees emp ON (emp.empno = attd.empno and emp.empno =:empid )
left JOIN mst_departments dept ON (dept.id = emp.mst_department_id)
) as a group by  pdate,daytype,day
order by pdate

		";
		 
		 $getEmpBasicDetails = "select emp_fname,empno,mst_departments.dept_name,mst_designations.design_name,attendance.tot_days,attendance.present_days,
attendance.tot_days-attendance.present_days as absent_days
 from mst_employees 
left join mst_departments on (mst_departments.id=mst_employees.mst_department_id)
left join mst_designations on (mst_designations.id=mst_employees.mst_designation_id)
left join (select count(cal_date) as tot_days,count(att_date) as present_days from (
select cal.date as cal_date,trn_emp_attendances.att_date from (select to_char(generate_series('2016-12-01', '2016-12-31', '1 day'::interval),'YYYY-mm-dd')::date as date) cal
left join (select distinct att_date from trn_emp_attendances where empno ='30937' and att_date between '2016-12-01' and '2016-12-31')
as  trn_emp_attendances on (cal.date=trn_emp_attendances.att_date)
) as a) as attendance on (1=1)
where empno ='30937'";
		
		$command = Yii::$app->ehms->createCommand($getEmpPunchSQL);
		// Bind the Values - SQL Injection Prevention
		$command->bindParam(":empid",$empid);
		
		$b_command =  Yii::$app->ehms->createCommand($getEmpBasicDetails);
		//$command->bindParam(":month",$month);
		//$command->bindParam(":year",$year);
		
		//Query Execute
		$punchdetails = $command->queryAll();
		$empdetails  = $b_command ->queryall();
		//echo '<pre>';print_r($punchdetails);exit;
		$cal = $this->getDayCalendar($month,$year);
		$punchArr = array();
		
		for($i=0;$i<count($punchdetails);$i++){
			/*if($i==0)
			{
			$punchArr['empdetails'][$i]['empname'] 		= $empdetails[$i]['emp_fname'];
			$punchArr['empdetails'][$i]['empcode'] 		= $empdetails[$i]['empno'];
			$punchArr['empdetails'][$i]['department'] 	= $empdetails[$i]['dept_name'];
			$punchArr['empdetails'][$i]['desination'] 	= $empdetails[$i]['design_name'];
			$punchArr['empdetails'][$i]['totaldays'] 	= $empdetails[$i]['tot_days'];
			$punchArr['empdetails'][$i]['presentdays'] 	= $empdetails[$i]['present_days'];
			$punchArr['empdetails'][$i]['absentdays'] 	= $empdetails[$i]['absent_days'];
			$punchArr['empdetails'][$i]['leavebalnc'] 	= 0;
			} */
			$punchArr['punchdetails'][$i]['date'] 		= $punchdetails[$i]['pdate'];
			$punchArr['punchdetails'][$i]['day'] 		= $punchdetails[$i]['day'];
			$punchArr['punchdetails'][$i]['daytype'] 	= $punchdetails[$i]['daytype'];
			$punchArr['punchdetails'][$i]['intime'] 	= $punchdetails[$i]['in1'];
			$punchArr['punchdetails'][$i]['outtime'] 	= $punchdetails[$i]['out1'];
			$punchArr['punchdetails'][$i]['workhr'] 	= $punchdetails[$i]['workhr'];
			$punchArr['punchdetails'][$i]['remarks'] 	= $punchdetails[$i]['remarks'];

		}
		$puncharr[] = $punchArr;
//echo "<pre>";print_r($punchArr);echo "</pre>";exit;
	echo '<pre>';print_r($data1);
		//	echo "<pre>";print_r($puncharr);echo "</pre>";exit;
		return [
			'data'=>$puncharr,
		];
	}
	
	/*
	* Local Function to get the Department Name
	*/
	public function getDeptName($deptid){
		// Get the Department Name 
		$getDeptNameSQL = "
			SELECT 
				dept.dept_name as deptname
			FROM 
			mst_departments dept 
			WHERE dept.id =:deptid
		";
		
		$command = Yii::$app->ehms->createCommand($getDeptNameSQL);
		// Bind the Values - SQL Injection Prevention
		$command->bindParam(":deptid",$deptid);
		
		//Query Execute
		$depname = $command->queryAll();
		
		return $depname[0]['deptname'];
	}
	
	/*
	* Function to get the Leave types 
	*/
	public function actionGetLeaveTypes(){
		// Get Leave Types 
		$getLeaveTypeSQL = "
			SELECT 
				lt.id AS id,
				lt.short_name || ' - ' || lt.leave_name AS lname
			FROM 
			mst_leave_types lt 
			WHERE lt.leave_category=3 AND 
			lt.status=1 
		";
		
		$command = Yii::$app->ehms->createCommand($getLeaveTypeSQL);
		//Query Execute
		$ltypes = $command->queryAll();
		
		
		// Get Leave Types 
		$getPermissionTypeSQL = "
			SELECT 
				lt.id AS id,
				lt.short_name || ' - ' || lt.leave_name AS lname
			FROM 
			mst_leave_types lt 
			WHERE lt.leave_category=4 AND 
			lt.status=1 
		";
		
		$command = Yii::$app->ehms->createCommand($getPermissionTypeSQL);
		//Query Execute
		$ptypes = $command->queryAll();
		
		$data['leave'] = $ltypes;
		$data['permission'] = $ptypes;
		
		return [
			'data'=>$data,
		];
		
		
	}
	
	/*
	* Get the Approval Employees Details
	*/
	public function actionGetApprovalEmployees($deptid,$empid){
		// Get the Department Approval Employee Name 
		$getApprovalEmployeeSQL = "
			SELECT 
				emp.id AS empid,
				emp.empno AS empcode,
				salt.salutation || emp.emp_fname as empname,
				desg.name as designation
			FROM 
			mst_employees emp 
			JOIN designations desg ON (desg.id = emp.mst_designation_id)
			JOIN mst_salutations salt ON (salt.id = emp.salutation)
			WHERE emp.mst_department_id =:deptid  and  emp.status=1 and emp.id NOT IN(:empid)
			ORDER BY empname
		";	
		
		$command = Yii::$app->ehms->createCommand($getApprovalEmployeeSQL);
		// Bind the Values - SQL Injection Prevention
		$command->bindParam(":deptid",$deptid);
		$command->bindParam(":empid",$empid);
		
		//Query Execute
		$ApEmp = $command->queryAll();
		
		return [
			'data'=>$ApEmp,
		];
	}
	
	
	/*
	* Post Leave Requests 
	*/
	public function actionPostLeaveRequest(){
		//$empdata = '{"empid":"50151","deptid":"20","leavetype":"63","from":"20-01-2017","to":"24-01-2017","nod":"5","reason":"fever","approver":"9591"}';
		$empdata = $_REQUEST['data'];
		$var = json_decode($empdata,true);
	
		// Proceed only if we have array values 
		if (!empty($var)){
			$today 		= date('Y-m-d H:i:s');
			$fromdate 	= date('Y-m-d',strtotime($var['from']));
			$todate		= date('Y-m-d',strtotime($var['to']));
			
			$days = $this->createDateRangeArray($fromdate,$todate);
			$cnt = count($days);
			/*print_r($days);
			echo "count".$cnt;
			for($i=0;$i<$cnt;$i++){
				echo "\n count".$days[$i];
				echo "\n count".$i;
			}*/
			//exit;
			if(!empty($days)){
				for($i=0;$i<$cnt;$i++){
					$empmodel	= new TrnEmpLeaveEntries();	
					$month 	= date('m',strtotime($days[$i]));
					$year 	= date('Y',strtotime($days[$i]));
					
					$empmodel->mst_employee_id		= $var['empid'];
					$empmodel->mst_institution_id 	= 1;
					$empmodel->mst_leave_type_id 	= $var['leavetype'];
					$empmodel->ars_date 			= $days[$i];
					$empmodel->ars_month 			= $month;
					$empmodel->ars_year 			= $year;
					$empmodel->reason 				= $var['reason'];
					$empmodel->entry_type			= 'm';
					$empmodel->approved_by 			= $var['approver'];
					$empmodel->created_date 		= $today;
					$empmodel->created_by 			= $var['empid'];
					$empmodel->modified_date 		= $today;
					$empmodel->modified_by 			= $var['empid'];
					$empmodel->status 				= 2; // Leave Request Status 
					$empmodel->save(false);
				}
			}
			$data['status'] 	= 1;
			$data['title'] 		= 'Leave Request';
			$data['Message'] 	= 'Leave Request Submitted Sucessfully';
		}else{
			$data['status'] 	= 0;
			$data['title'] 		= 'Leave Request';
			$data['Message'] 	= 'Problem with your Request Submission';
		}
		
		// Return JSON Data 
		return [
			'data'=>$data,
		];
	}
	
	/*
	* Post Permission Requests 
	*/
	public function actionPostPermissionRequest(){
		//$permdata = '{"empid":"234","deptid":"234324","ltypeid":"234","date":"25-01-2017","from":"12:00 AM","to":"04:00 PM","tothour":"05:00","reason":"marriage function","approver":"2344"}';
		$permdata = $_REQUEST['data'];
		$var = json_decode($permdata,true);
		
		// Proceed only if we have array values 
		if (!empty($var)){
			$today 		= date('Y-m-d H:i:s');
			$permdate 	= date('Y-m-d',strtotime($var['date']));
			$from = $permdate.' '.$var['from'];
			$to = $permdate.' '.$var['to'];
			$fromtime = date('Y-m-d H:i:s',strtotime($from));
			$totime = date('Y-m-d H:i:s',strtotime($to));
			
			$permodel	= new TrnEmpPermissions();
			
			$permodel->mst_institution_id 	= 1;
			$permodel->mst_employee_id 		= $var['empid'];
			$permodel->mst_department_id 	= $var['deptid'];
			$permodel->mst_leave_type_id 	= $var['ltypeid'];
			$permodel->permission_date 		= $permdate;
			$permodel->permission_time 		= $var['tothour'];
			$permodel->per_from_time		= $fromtime;
			$permodel->per_to_time			= $totime;
			$permodel->reason 				= $var['reason'];
			$permodel->approved_by 			= $var['approver'];
			$permodel->created_date 		= $today;
			$permodel->created_by 			= $var['empid'];
			$permodel->modified_date 		= $today;
			$permodel->modified_by 			= $var['empid'];
			$permodel->permission_status	= 2; // Permission Request status
			
			$permodel->save(false);
			
			$data['status'] 	= 1;
			$data['title'] 		= 'Permission Request';
			$data['Message'] 	= 'Permission Request Submitted Sucessfully';
		}else{
			$data['status'] 	= 0;
			$data['title'] 		= 'Permission Request';
			$data['Message'] 	= 'Problem with your Permission Request Submission';
		}
		
		// Return JSON Data 
		return [
			'data'=>$data,
		];
	}
	
	/*
	* Function for Leave Approval
	*/
	public function actionPostLeaveApproval(){
		
	}
	
	
	
	/*
	* Local Function to create a day Calendar for a month
	*/
	public function getDayCalendar($month,$year){
		
		$thismonth = $month.$year;
		$date 	= date('Y-m-d',strtotime('first day of'.$thismonth));
		$last_date = date('Y-m-d',strtotime('last day of'.$thismonth));
		
		$monthArr = array();
		$i=0;
		while (strtotime($date) <= strtotime($last_date)) {
			$monthArr[$i]['date'] = $date;
			$date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
			$day = date('l',strtotime($date));
			$monthArr[$i]['day'] = $day;
			if ($day == "Sunday") {
				$monthArr[$i]['daytype'] = 'H';
			} else {
				$monthArr[$i]['daytype'] = 'W';
			}
			$i++;
		}
		return $monthArr;
	}
	
	/*
	*  Function to get the Date & time Difference 
	*/
	public function getTimeDifference($date1,$date2){
	
		//$to_time = strtotime($date2);
		//$from_time = strtotime($date1);
		$diff_seconds  = strtotime($date2) - strtotime($date1);
		$hours 		= floor($diff_seconds/3600);
		$minutes 	= floor(($diff_seconds%3600)/60);
		
		if ($minutes < 10) {
			$minutes = '0' . $minutes;
		}
		
		return sprintf('%d:%s', $hours, $minutes);
	}
	
	/*
	* Function to get the Date range between two dates 
	*/
	public function createDateRangeArray($strDateFrom,$strDateTo)
	{
		// takes two dates formatted as YYYY-MM-DD and creates an
		// inclusive array of the dates between the from and to dates.

		// could test validity of dates here but I'm already doing
		// that in the main script

		$aryRange=array();

		$iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),substr($strDateFrom,8,2),substr($strDateFrom,0,4));
		$iDateTo=mktime(1,0,0,substr($strDateTo,5,2),substr($strDateTo,8,2),substr($strDateTo,0,4));

		if ($iDateTo>=$iDateFrom)
		{
			array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
			while ($iDateFrom<$iDateTo)
			{
				$iDateFrom+=86400; // add 24 hours
				array_push($aryRange,date('Y-m-d',$iDateFrom));
			}
		}
		return $aryRange;
	}
	
	
	
	
	
	/*
	-------------------------Created by Gokul------------------------->START
	Function to get employee profile
	*/
	public function actionGetEmployeesProfile($empid)
	{
		//echo '<pre>';print_r($date);
		
			$getProfile = "
select emp_pmail as email,emp_fname as name,emp_guardian_name as father,emp_nationality as nationality,emp_gender as gender,emp_religion as religion,emp_dob as dob,emp_pmail,
blood.bloodgroup_name as bloodgroup,designation.design_name as designation
from mst_employees as emp
left join mst_bloodgroups as blood on(blood.id=emp.mst_blood_group_id)
left join mst_designations as designation on(designation.id=emp.mst_designation_id)
 where emp.empno=:empid
		";
		$getCommunication="select emp_paddress as address, emp_pzipcode as pincode,
country.country_name,state.state_name,city.city_name
from mst_employees as emp 
left join mst_countries as country on(country.id=emp.emp_pcountry_id)
left join mst_states as state on(state.id=emp.emp_pstate_id)
left join mst_cities as city on(city.id=emp.emp_pcity_id) where emp.empno=:empid";

		$getOfficial="select to_char(emp_jdate,'dd-mm-yy')emp_jdate ,department.dept_name,designation.design_name,
emp.emp_omail as email,emp.nominee_name as nomineename ,
case when emp_type=1 then 'Doctor' when emp_type=2 then 'Technician'  when emp_type=3 then 'Nursing'
 when emp_type=4 then 'General'  when emp_type=5 then 'Pharmacist'  else null end as emp_type,
 case when nominee_relation=1 then 'Father'
when nominee_relation=2 then 'Mother' when nominee_relation=3 then 'Brother'
when nominee_relation=4 then 'Sister' when nominee_relation=5 then 'Son'
when nominee_relation=6 then 'Daughter' when nominee_relation=7 then 'Spouse' else null end as nominee_relation
from mst_employees as emp
left join mst_departments as department on(department.id=emp.mst_department_id)
left join mst_designations as designation on(designation.id=emp.mst_designation_id) where  emp.empno=:empid";
		
		$getSalary="select emp_basic_pay as basicpay,emp_gross_pay as grosspay,
emp_hra as hra,emp_da_percentage as da,
case when emp_salary_mode=1 then 'Cheque' when emp_salary_mode=2 then 'Cash'
 when emp_salary_mode=3 then 'Credit to Bank Account' else null end as paymode ,next_increment_date as increment ,emp_bank_acc_no as accountno
 from mst_employees emp where emp.empno=:empid ";
		
		$cmdProfile = Yii::$app->ehms->createCommand($getProfile);
		$cmdProfile->bindParam(":empid",$empid);
		
		$cmdCommunication = Yii::$app->ehms->createCommand($getCommunication);
		$cmdCommunication->bindParam(":empid",$empid);
		
		$cmdOfficial=Yii::$app->ehms->createCommand($getOfficial);
		$cmdOfficial->bindParam(":empid",$empid);
		
		$cmdSalary=Yii::$app->ehms->createCommand($getSalary);
		$cmdSalary->bindParam(":empid",$empid);
		//Query Execute
		$empdetails = $cmdProfile->queryAll();
		$communicationdetails= $cmdCommunication->queryAll();
		$officialdetails=$cmdOfficial->queryAll();
		$salarydetails=$cmdSalary->queryAll();
		
		$data['profile']=$empdetails;
		$data['communication']=$communicationdetails;
		$data['official']=$officialdetails;
		$data['salary']=$salarydetails;
		
		return [
			'data'=>$data
		];
	}
	
	//---------------------Created by Gokul----------------------------->END
	
	
}
