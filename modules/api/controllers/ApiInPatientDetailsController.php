<?php

namespace app\modules\api\controllers;

use app\models\TrnPatietVisitPhotoGallery;
use Yii;
use yii\helpers\FileHelper;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\filters\ContentNegotiator;
use yii\helpers\Url;
use yii\filters\VerbFilter;
use app\modules\api\models\MstUser;
use yii\web\UploadedFile;

class ApiInPatientDetailsController extends Controller
{
	//public $modelClass = 'app\modules\api\models\MstUser';
	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return ArrayHelper::merge(parent::behaviors(), [
			[
				'class' => ContentNegotiator::className(),
				//'auth' => [$this, 'auth'],
				'only' => [
					'get-all-floors',
					'get-all-buildings',
					'get-all-building-with-status',
					'get-all-floor-for-building',
					'get-floors-buildings',
					'get-rooms-by-floor',
					'get-beds-by-room-id',
					'get-all-floors-rooms',
					'get-profile',
					'search-inpatient',
					'get-lab-report-result',
					'upload-patient-photo',
					'get-patient-photo',
				],
				'formats' => [
					'application/json' => Response::FORMAT_JSON,
				],
			],
		]);
	}

	/*protected function verbs()
	{
		return [
			'get-all-floors'	=>['get'],
			'get-all-buildings'	=>['get'],
			'get-floors-buildings'	=>['get'],
			'get-rooms-by-floor' =>['get'],
			'get-beds-by-room-id' =>['get'],
		];
	}*/

	/*
	* Function to Get the All Floors
	*/
	public function actionGetAllBuildings(){
		$getBuildings = "SELECT id as building_id, initcap(building_name) as building_name FROM mst_buildings WHERE building_type=2;";
		// Bind the Values - SQL Injection Prevention
		$result 	= Yii::$app->getDb()->createCommand($getBuildings)->queryAll();
		$i=0;
		$buildings = array();
		foreach($result as $detail){
			$buildings[$i]['building_id'] 	= $detail['building_id'];
			$buildings[$i]['building_name'] = $detail['building_name'];
			$i++;
		}
		//echo "<pre>"; print_r($buildings); exit;
		//return $buildings;
		return [
			'data'=>$buildings,
		];
	}
	/*
	* Function to Get Buliding with BED status
	*/
	public function actionGetAllBuildingWithStatus(){
		$getBuildings = "select building_id,building_name,room_count.count as room_count,total_bed,alloted,vacant,Active,cleaning from (
select building_id,building_name,sum(total_bed) as total_bed,sum(alloted) as alloted,sum(vacant) as vacant,sum(Active) as Active,sum(cleaning) as cleaning
from (
SELECT bed_status.mst_building_id as building_id,building.building_name,count(bed_status.id) as total_bed,sum(case WHEN (bed_status='Alloted' OR bed_status.bed_status='Attender')THEN 1 else 0 end ) as alloted,sum(case WHEN (bed_status='Vacant')THEN 1 else 0 end) as vacant,sum(case WHEN (bed_status='Maintenance' OR bed_status='Bill Settled')THEN 1 else 0 end ) as service,sum(case WHEN (bed_status='Active')THEN 1 else 0 end ) as Active,sum(case WHEN (bed_status='Cleaning')THEN 1 else 0 end ) as cleaning
from trn_bed_status as bed_status
JOIN mst_buildings as building on (building.id=bed_status.mst_building_id)
JOIN mst_blocks as block on (block.id=bed_status.mst_block_id)
where building.status=1 and bed_status.status=1
GROUP BY bed_status.mst_building_id,building.building_name,bed_status
ORDER BY building.building_name)as a
group by building_id,building_name) as a
join (select mst_building_id,count(*) from (
select distinct mst_building_id,mst_room_id from trn_bed_status where status=1
) as a
group by mst_building_id) as room_count on (room_count.mst_building_id=a.building_id) order by building_name asc

";

		// Bind the Values - SQL Injection Prevention
		$result 	= Yii::$app->getDb()->createCommand($getBuildings)->queryAll();
		$i=0;
		$buildings = array();
		foreach($result as $detail){
			$buildings[$detail['building_id']]['building_id'] 	= $detail['building_id'];
			$buildings[$detail['building_id']]['building_name'] = $detail['building_name'];
			$buildings[$detail['building_id']]['room_count'] = $detail['room_count'];
			$buildings[$detail['building_id']]['total_bed'] = $detail['total_bed'];
			$buildings[$detail['building_id']]['occupied'] = $detail['alloted'];
			$buildings[$detail['building_id']]['cleaning'] = $detail['cleaning'];
			$buildings[$detail['building_id']]['service'] = $detail['service'];
			$buildings[$detail['building_id']]['out_of_service'] = $detail['active'];
			$buildings[$detail['building_id']]['free'] = $detail['vacant'];

			$i++;
		}
		$buildings = array_values($buildings);
//		echo "<pre>"; print_r($buildings); exit;

		//return $buildings;
		return [ 'data'=>$buildings, ];
	}


	public function actionGetAllFloorForBuilding($building_id=0){
		$getBuildings = "  select c.count room_count,b.alloted,b.vacant, b.bed_count,b.floor_name,a.* from (
select  building_id,building_name,mst_floor_id,mst_room_id,room_name,sum(occupied)+sum(free_bed)+sum(service)+sum(cleaning) as total_bed, sum(occupied) occupied,sum(free_bed) free_bed,sum(service) service,sum(cleaning) cleaning ,sum(is_female_room) as is_female_room  from (
SELECT bed_status.mst_building_id as building_id, building.building_name, bed_status.mst_floor_id, floor.floor_name, count(bed_status.mst_bed_id) as total_bed, bed_status.mst_room_id, room.room_name, sum(case WHEN (bed_status='Alloted' OR bed_status.bed_status='Attender')THEN 1 else 0 end ) as occupied,sum(case WHEN (bed_status='Vacant')THEN 1 else 0 end ) as free_bed,sum(case WHEN (bed_status='Maintenance' OR bed_status='Bill Settled')THEN 1 else 0 end ) as service,sum(case WHEN (bed_status='Cleaning')THEN 1 else 0 end ) as cleaning,
case when pat.gender='Female' then 1 else 0 end as is_female_room
from trn_bed_status as bed_status
  JOIN mst_buildings as building on (building.id=bed_status.mst_building_id)
  JOIN mst_floors as floor on (floor.id=bed_status.mst_floor_id)
  JOIN mst_rooms as room on (room.id=bed_status.mst_room_id)
  left join mst_active_patients as pat on (pat.trn_visit_id=bed_status.trn_visit_id and pat.mst_room_id=room.id)
where bed_status.mst_building_id=$building_id AND
 building.status=1 AND bed_status.status=1 AND floor.status=1
GROUP BY bed_status.mst_building_id,building.building_name,bed_status,room.room_name,bed_status.mst_room_id,bed_status.mst_floor_id, floor.floor_name,pat.gender
ORDER BY bed_status.mst_room_id --building.building_name,bed_status.bed_status
) as a
group by building_id,building_name,mst_floor_id,mst_room_id,room_name)
as a
left join (
 SELECT a.mst_building_id,a.mst_block_id,a.mst_floor_id,MstBuilding.building_name,MstBuilding.building_short_name,MstBlock.block_name,MstFloor.floor_name,room_count,b.alloted,b.vacant,(b.alloted + b.vacant) AS bed_count
	FROM ( SELECT a.mst_building_id ,a.mst_block_id ,a.mst_floor_id ,count(*) AS room_count FROM
	(
	SELECT DISTINCT mst_rooms.mst_building_id,mst_rooms.id AS mst_room_id ,mst_blocks.id AS mst_block_id ,mst_floors.id AS mst_floor_id
	FROM mst_rooms
		INNER JOIN mst_buildings ON (mst_buildings.id = mst_rooms.mst_building_id AND mst_buildings.STATUS = 1)
		INNER JOIN mst_blocks ON (mst_blocks.mst_building_id = mst_rooms.mst_building_id AND mst_blocks.STATUS = 1)
		INNER JOIN mst_floors ON (mst_floors.id = mst_rooms.mst_floor_id AND mst_floors.STATUS = 1) AND mst_rooms.mst_floor_id != 1 AND mst_rooms.STATUS = 1
	) AS a
	GROUP BY a.mst_building_id ,a.mst_block_id,a.mst_floor_id ORDER BY a.mst_building_id,a.mst_block_id,a.mst_floor_id
	) AS a
INNER JOIN mst_beds ON (mst_beds.mst_block_id = a.mst_block_id AND mst_beds.mst_floor_id = a.mst_floor_id AND mst_beds.STATUS = 1)
INNER JOIN (
	SELECT trn_bed_status.mst_building_id ,trn_bed_status.mst_block_id,trn_bed_status.mst_floor_id ,sum(CASE WHEN trn_bed_status.bed_status = 'Alloted' THEN 1 ELSE 0 END) AS Alloted,sum(CASE WHEN trn_bed_status.bed_status = 'Vacant' THEN 1 ELSE 0 END) AS Vacant
	FROM trn_bed_status
	INNER JOIN mst_rooms ON (mst_rooms.id = trn_bed_status.mst_room_id AND mst_rooms.STATUS = 1)
	WHERE trn_bed_status.bed_status IN ('Alloted', 'Vacant') AND trn_bed_status.STATUS = 1
	GROUP BY trn_bed_status.mst_building_id ,trn_bed_status.mst_block_id,trn_bed_status.mst_floor_id
	) AS b ON (b.mst_block_id = a.mst_block_id AND b.mst_floor_id = a.mst_floor_id)
INNER JOIN mst_buildings AS MstBuilding ON (MstBuilding.id = a.mst_building_id AND MstBuilding.STATUS = 1)
INNER JOIN mst_blocks AS MstBlock ON (MstBlock.id = a.mst_block_id AND MstBlock.STATUS = 1)
INNER JOIN mst_floors AS MstFloor ON (MstFloor.id = a.mst_floor_id AND MstFloor.STATUS = 1)
INNER JOIN mst_rooms AS MstRoom ON (mst_beds.mst_room_id = MstRoom.id AND MstRoom.STATUS = 1)
WHERE MstBuilding.id = $building_id
GROUP BY a.mst_building_id ,a.mst_block_id,a.mst_floor_id,room_count,MstBuilding.building_name,building_short_name,MstBlock.block_name,MstFloor.floor_name,b.alloted,b.vacant
ORDER BY MstFloor.floor_name)
as b on (b.mst_building_id=a.building_id and b.mst_floor_id=a.mst_floor_id)
left join (SELECT building_id,mst_floor_id,COUNT(*) FROM (
select  building_id,mst_floor_id,mst_room_id,room_name,sum(occupied)+sum(free_bed)+sum(service)+sum(cleaning) as total_bed, sum(occupied) occupied,sum(free_bed) free_bed,sum(service) service,sum(cleaning) cleaning ,sum(is_female_room) as is_female_room  from (
SELECT bed_status.mst_building_id as building_id, building.building_name, bed_status.mst_floor_id, floor.floor_name, count(bed_status.mst_bed_id) as total_bed, bed_status.mst_room_id, room.room_name, sum(case WHEN (bed_status='Alloted' OR bed_status.bed_status='Attender')THEN 1 else 0 end ) as occupied,sum(case WHEN (bed_status='Vacant')THEN 1 else 0 end ) as free_bed,sum(case WHEN (bed_status='Active')THEN 1 else 0 end ) as service,sum(case WHEN (bed_status='Cleaning')THEN 1 else 0 end ) as cleaning,
case when pat.gender='Female' then 1 else 0 end as is_female_room
from trn_bed_status as bed_status
  JOIN mst_buildings as building on (building.id=bed_status.mst_building_id )
  JOIN mst_floors as floor on (floor.id=bed_status.mst_floor_id )
  JOIN mst_rooms as room on (room.id=bed_status.mst_room_id)
  left join mst_active_patients as pat on (pat.trn_visit_id=bed_status.trn_visit_id and pat.mst_room_id=room.id)
where bed_status.mst_building_id=$building_id AND
 building.status=1 AND bed_status.status=1 AND floor.status=1
GROUP BY bed_status.mst_building_id,building.building_name,bed_status,room.room_name,bed_status.mst_room_id,bed_status.mst_floor_id, floor.floor_name,pat.gender
ORDER BY bed_status.mst_room_id --building.building_name,bed_status.bed_status
) as a
group by building_id,building_name,mst_floor_id,mst_room_id,room_name) as a
group by building_id,mst_floor_id) as c on (c.building_id=a.building_id and c.mst_floor_id=a.mst_floor_id)
order by b.floor_name,a.room_name

";



		$result 	= Yii::$app->getDb()->createCommand($getBuildings)->queryAll();

		$i=0;
		$j=0;
		$floors = array();
		$floor_list = array();
		$floor_id = 0;
		foreach($result as $detail){
			if($floor_id!=$detail['mst_floor_id'])
			{
				$floor_id = $detail['mst_floor_id'];
				$i=0;
			}

			$floors[$detail['mst_floor_id']]['total_rooms'] 	= $detail['room_count'];
			$floors[$detail['mst_floor_id']]['occupied_beds'] 	= $detail['alloted'];
			$floors[$detail['mst_floor_id']]['free_beds'] 	= $detail['vacant'];
			$floors[$detail['mst_floor_id']]['total_beds'] 	= $detail['bed_count'];
			$floors[$detail['mst_floor_id']]['service'] 	= $detail['service'];
			$floors[$detail['mst_floor_id']]['floor_name'] 	= $detail['floor_name'];

			$floors[$detail['mst_floor_id']]['building_id'] 	= $detail['building_id'];
			$floors[$detail['mst_floor_id']]['building_name'] = $detail['building_name'];
			$floors[$detail['mst_floor_id']]['mst_floor_id'] = $detail['mst_floor_id'];
			//$i++;
			$floors[$detail['mst_floor_id']]['room_details'][$i]['mst_room_id'] = $detail['mst_room_id'];
			$floors[$detail['mst_floor_id']]['room_details'][$i]['room_name'] = $detail['room_name'];
			$floors[$detail['mst_floor_id']]['room_details'][$i]['is_female'] = $detail['is_female_room'];
			$floors[$detail['mst_floor_id']]['room_details'][$i]['total_bed']  = $detail['total_bed'];
			$floors[$detail['mst_floor_id']]['room_details'][$i]['occupied']  =  $detail['occupied'];
			$floors[$detail['mst_floor_id']]['room_details'][$i]['free_bed']  = $detail['free_bed'];
			$floors[$detail['mst_floor_id']]['room_details'][$i]['service']  = $detail['service'];
			$floors[$detail['mst_floor_id']]['room_details'][$i]['cleaning'] = $detail['cleaning'];


			$i++;
		}

		$floors = array_values($floors);

		//echo "<pre>"; print_r($floors); exit;
		//return $buildings;
		return [ 'data'=>$floors];
	}


	public function actionGetProfile($room_id){

		$getroom_id_query = "select pat.patient_code as patient_code,pat.trn_visit_id,pat.mst_patient_id, photo.name as picture,case when gender='Male' then 'M'  when gender='Female' then 'F' else null end as gender ,age_year as age,case when room_no is null then '' else room_no end  as bed_no,bed.mst_bed_id as bed_id,case when pat.patient_name is not null then pat.patient_name when attendant.attendant_name is not null then attendant.attendant_name else null end as patient_name ,case when doc.name is not null then 'Dr.'||doc.name else '' end as admitted_unit,
to_char(pat.visit_date,'dd-mm-yyyy hh:mm AM') as admitted_date,case when diagnosis is null then '' else diagnosis end as diagnosis,
case when corp.id=3 then '' when corp.id=null then '' else corp.name end as insurance,bed.amount as price,
case when attendant.id is null AND pat.id is not null then 'Patient'  when  attendant.id is not null AND pat.id is null then 'Attender' else null end as pat_type,
mgr.name as grm_manager
 from trn_bed_status as bed
left join mst_active_patients as pat on (pat.trn_visit_id=bed.trn_visit_id and bed.mst_room_id=pat.mst_room_id)
left join photographs as photo on (photo.id=pat.photograph_id)
left join clinicians as doc on (doc.id=pat.mst_doctor_id)
left join trn_doctor_patients as docpat on (docpat.trn_visit_id=bed.trn_visit_id and doc.id=docpat.mst_doctor_id)
left join mst_patient_coordinators as mgr on (mgr.id=docpat.mst_patient_coordinator_id)
left join patient_attendants as attendant on (attendant.station_id=bed.mst_room_id and bed.mst_bed_id=attendant.station_unit_id and attendant.status='Reserved')
left join corporates as corp on (corp.id=pat.mst_corporate_id)
 where bed.mst_room_id =$room_id and bed.status=1 order by room_no asc";

		$result 	= Yii::$app->getDb()->createCommand($getroom_id_query)->queryAll();

		$i=0;
		$bed_list = array();
		foreach($result as $detail){
			$bed_list[$i]['picture'] 	= $detail['picture'];
			$bed_list[$i]['patient_code'] 	= $detail['patient_code'];
			$bed_list[$i]['visit_id'] 	= $detail['trn_visit_id'];
			$bed_list[$i]['patient_id'] 	= $detail['mst_patient_id'];
			$bed_list[$i]['gender'] 	= $detail['gender'];
			$bed_list[$i]['age'] = $detail['age'];
			$bed_list[$i]['bed_no'] = $detail['bed_no'];
			$bed_list[$i]['patient_name'] = $detail['patient_name'];
			$bed_list[$i]['admitted_unit'] = $detail['admitted_unit'];
			$bed_list[$i]['admitted_date'] = $detail['admitted_date'];
			$bed_list[$i]['diagnosis'] = $detail['diagnosis'];
			$bed_list[$i]['insurance'] = $detail['insurance'];
			$bed_list[$i]['price'] = $detail['price'];
			$bed_list[$i]['pat_type'] = $detail['pat_type'];
			$bed_list[$i]['patgrm_manager_type'] = $detail['grm_manager'];


			$i++;
		}
		//$data['floor'] = $floors;
		//echo "<pre>"; print_r($data); exit;
		return [
			'data'=>$bed_list,
		];
	}


	/*
	* Function to Get the All Floors By rooms
	*/
	public function actionGetAllFloorsRooms($building_id=0){
		$get_floor_rooms = "SELECT bed_status.mst_building_id as building_id, building.building_name, bed_status.mst_floor_id, floor.floor_name, count(bed_status.mst_bed_id) as total_bed, bed_status.mst_room_id, room.room_name, sum(case WHEN (bed_status='Alloted' OR bed_status.bed_status='Attender')THEN 1 else 0 end ) as occupied,sum(case WHEN (bed_status='Vacant')THEN 1 else 0 end ) as free_bed,sum(case WHEN (bed_status='Maintenance' OR bed_status='Bill Settled')THEN 1 else 0 end ) as service,sum(case WHEN (bed_status='Cleaning')THEN 1 else 0 end ) as cleaning
from trn_bed_status as bed_status
  JOIN mst_buildings as building on (building.id=bed_status.mst_building_id)
  JOIN mst_floors as floor on (floor.id=bed_status.mst_floor_id)
  JOIN mst_rooms as room on (room.id=bed_status.mst_room_id)
where bed_status.mst_building_id=$building_id AND building.status=1 AND bed_status.status=1 AND floor.status=1
GROUP BY bed_status.mst_building_id,building.building_name,bed_status,room.room_name,bed_status.mst_room_id,bed_status.mst_floor_id, floor.floor_name
ORDER BY building.building_name,bed_status.bed_status;";
		$result1 	= Yii::$app->getDb()->createCommand($get_floor_rooms)->queryAll();
		$j=0;
		$floor_list = array();
		foreach($result1 as $detail){
			$floor_list[$j]['building_id'] 	= $detail['building_id'];
			$floor_list[$j]['building_name'] = $detail['building_name'];
			$floor_list[$j]['mst_floor_id'] = $detail['mst_floor_id'];
			$floor_list[$j]['floor_name'] = $detail['floor_name'];
			$floor_list[$j]['total_bed'] = $detail['total_bed'];
			$floor_list[$j]['mst_room_id'] = $detail['mst_room_id'];
			$floor_list[$j]['room_name'] = $detail['room_name'];
			$floor_list[$j]['occupied'] = $detail['occupied'];
			$floor_list[$j]['free_bed'] = $detail['free_bed'];
			$floor_list[$j]['service'] = $detail['service'];
			$floor_list[$j]['cleaning'] = $detail['cleaning'];

			$j++;
		}
		//$data['floor'] = $floors;
		//echo "<pre>"; print_r($data); exit;
		$floor_list = array_values($floor_list);
		return [
			'data'=>$floor_list,
		];
	}


	/*
	* Function to Get the All Floors
	*/
	public function actionGetAllFloors(){
		$getFloors = "
			SELECT floor.id AS floor_id,
				   floor.floor_name||'-'|| block.block_name AS floor_name
			FROM   mst_floors AS floor
				   JOIN mst_blocks AS block
					 ON ( block.id = floor.mst_block_id )
			WHERE  floor.status = 1
				   AND floor.is_deleted = 0
			ORDER  BY floor.id,
					  floor.floor_name,
					  block.block_name;";
		$result 	= Yii::$app->getDb()->createCommand($getFloors)->queryAll();
		$i=0;
		$floors = array();
		foreach($result as $detail){
			$floors[$i]['floor_id'] 	= $detail['floor_id'];
			$floors[$i]['floor_name'] = $detail['floor_name'];
			$i++;
		}
		//$data['floor'] = $floors;
		//echo "<pre>"; print_r($data); exit;
		return [
			'data'=>$floors,
		];
	}

	/*
	* Function to Get the All Floors By Buildings(building_id)
	*/
	public function actionGetFloorsBuildings($mst_building_id){
		$getfloor = "SELECT floor.id, floor.floor_name FROM mst_buildings AS building
				JOIN mst_blocks AS block ON block.mst_building_id=building.id
				JOIN mst_floors AS floor ON floor.mst_block_id=block.id
				WHERE building.building_type=2";
		if(!empty($mst_building_id)) {
			$getfloor .=" AND building.id=$mst_building_id ";
		}
		$result 	= Yii::$app->getDb()->createCommand($getfloor);
		//$result->bindParam(":mst_building_id",$mst_building_id);
		$res 	= $result->queryAll();
		$i=0;
		$floors = array();
		foreach($res as $detail){
			$floors[$i]['id'] 	= $detail['id'];
			$floors[$i]['floor_name'] = $detail['floor_name'];
			$i++;
		}
		//$data['floor'] = $floors;
		//echo "<pre>"; print_r($floors); exit;
		return [
			'data'=>$floors,
		];
	}

	/*
	* Function to Get the Rooms By Floor Id
	*/
	public function actionGetRoomsByFloor($floor_id)
	{
		if (!empty($floor_id)) {
			$getfloor = "select room_id, room_name, sum(total_beds) as total_beds, sum(vacant_bed) as vacant_bed, sum(alloted_bed) alloted_bed, sum(cleaning_bed) as cleaning_bed FROM (
						select room.id as room_id, room.room_name, count(bds.mst_bed_id) as total_beds, (case when bds.bed_status='Vacant' then count(bed.id) else '0' end) as vacant_bed,(case when (bds.bed_status='Bill Settled' OR bds.bed_status='Maintenance') then count(bed.id) else '0' end) as service_bed, (case when bds.bed_status='Alloted' then count(bed.id) else '0' end) as alloted_bed, (case when bds.bed_status='Cleaning' then count(bed.id) else '0' end) as cleaning_bed FROM (select * from trn_bed_status where status=1 AND is_deleted=0 AND mst_floor_id in($floor_id)) as bds
						  JOIN mst_rooms as room ON(room.id=bds.mst_room_id AND room.status=1 AND room.is_deleted=0)
						  JOIN mst_beds as bed ON (bed.id=bds.mst_bed_id AND bed.status=1 AND bed.is_deleted=0)
						  GROUP BY room.id, room.room_name, bds.bed_status oRDER by room.id) as a
						GROUP BY a.room_id, a.room_name;";
			$result = Yii::$app->getDb()->createCommand($getfloor);
			//$result->bindParam(":mst_building_id",$mst_building_id);
			$res = $result->queryAll();
			$i = 0;
			$floors = array();
			foreach ($res as $detail) {
				$floors[$i]['room_id'] = $detail['room_id'];
				$floors[$i]['room_name'] = $detail['room_name'];
				$floors[$i]['total_beds'] = $detail['total_beds'];
				$floors[$i]['vacant_bed'] = $detail['vacant_bed'];
				$floors[$i]['alloted_bed'] = $detail['alloted_bed'];
				$floors[$i]['cleaning_bed'] = $detail['cleaning_bed'];
				$floors[$i]['service_bed'] = $detail['service_bed'];
				$i++;
			}
			//$data['floor'] = $floors;
			//echo "<pre>"; print_r($floors); exit;
			return [
				'data' => $floors,
			];
		}
	}
	/*
	* Function to Get the Rooms By Floor Id
	*/
	public function actionGetBedsByRoomId($room_id)
	{
		if (!empty($room_id)) {
			$getfloor = "select room.id as room_id, room.room_name, bed.id as bed_id, bed.bed_name, act.patient_code, act.patient_name, act.age_year as age, act.gender
						FROM (select * from trn_bed_status where status=1 AND is_deleted=0 AND mst_room_id in($room_id)) as bds
						JOIN mst_rooms as room ON(room.id=bds.mst_room_id)
						JOIN mst_beds as bed ON (bed.id=bds.mst_bed_id)
						LEFT JOIN mst_active_patients as act ON(act.trn_visit_id=bds.trn_visit_id AND act.patient_visit_type='IP') ORDER by act.patient_name;";
			$result = Yii::$app->getDb()->createCommand($getfloor);
			//$result->bindParam(":mst_building_id",$mst_building_id);
			$res = $result->queryAll();
			$i = 0;
			$floors = array();
			foreach ($res as $detail) {
				$floors[$i]['room_name'] = $detail['room_name'];
				$floors[$i]['bed_name'] = $detail['bed_name'];
				$floors[$i]['patient_code'] = $detail['patient_code'];
				$floors[$i]['patient_name'] = $detail['patient_name'];
				$floors[$i]['age'] = $detail['age'];
				$floors[$i]['gender'] = $detail['gender'];
				$i++;
			}
			//$data['floor'] = $floors;
			//echo "<pre>"; print_r($floors); exit;
			return [
				'data' => $floors,
			];
		}
	}

	function actionSearchInpatient($q='') {
		$query = "SELECT
						v.id ,
						p.extid as patient_code,
						upper(p.name)as patient_name,
						st.name || ' ' || su.name as bed_no,
						CASE  WHEN date_part('year', age(v.visit_date::TIMESTAMP, p.date_of_birth::TIMESTAMP))::TEXT != '0'  THEN date_part('year', age(v.visit_date::TIMESTAMP, p.date_of_birth::TIMESTAMP))::TEXT || ' Year' WHEN date_part('month', age(v.visit_date::TIMESTAMP, p.date_of_birth::TIMESTAMP))::TEXT != '0' THEN date_part('month', age(v.visit_date::TIMESTAMP, p.date_of_birth::TIMESTAMP))::TEXT || ' Month' WHEN date_part('day', age(v.visit_date::TIMESTAMP, p.date_of_birth::TIMESTAMP))::TEXT !='0' THEN date_part('day', age(v.visit_date::TIMESTAMP, p.date_of_birth::TIMESTAMP)) || ' Day' ELSE 'Born Today' END AS age,
						cli.name as admitted_unit,
						ipd.admission_time as admitted_date,
						ipd.diagnosis,
						corp.name as insurance,
						case when bed_status.bed_status='Attender' then 'Attender' else 'Patient' end as pat_type,
						pat_cor.name as patgrm_manager_type,
						bed_status.amount as price,
						g.name as gender
						 from patients as p
						 LEFT JOIN genders as g on (g.id=p.gender_id)
						 JOIN visits as v on (v.patient_id=p.id and patient_type='In Patient')
						 JOIN inpatient_details as ipd on (ipd.visit_id=v.id and ipd.status='Admitted' and discharge_time is NULL)
						 JOIN clinicians as cli on (cli.id=ipd.admit_clinician_id)
						 JOIN corporates as corp on (corp.id=v.corporate_id)
						 JOIN bed_allocations as ba on (ba.inpatient_detail_id=ipd.id and ba.status='Alloted')
						 JOIN trn_bed_status as bed_status on (bed_status.trn_visit_id = v.id)
						 JOIN trn_doctor_patients as doc_pat on (doc_pat.trn_visit_id=v.id)
						 LEFT JOIN mst_patient_coordinators as pat_cor on (pat_cor.id=doc_pat.mst_patient_coordinator_id)
						 LEFT JOIN stations as st on (st.id=ba.station_id)
						 LEFT JOIN station_units as su on (su.id=ba.station_unit_id)
			where p.extid ilike '%".$q."%' OR p.name ilike '%".$q."%' order by p.name";
		$con= Yii::$app->getDb();
		$res = $con->createCommand($query)->queryAll();
		$out['data'] = array_values($res);
		return $out;
	}


	public function actionGetLabReportResult($visit_id=0)
	{
		$query = "SELECT
	--DISTINCT cl.NAME AS doctor_name,
	--p.NAME AS patient_name,
	--p.gender_id,
	--ltrv.sex,
	--ltres.service_unit_id,
	--ltres.machine_id,
	--ltres.lab_test_mode_id,
	--ltres.id AS lab_test_result_id,
	--ltrd.id AS request_detail_id,
	--ltres.colony_count,
	--crpt.is_gram_done,
	--crpt.gram_result,
	--ltres.remark,
	--s.interpretation,
	--ltrv.min_reference_value AS min_ref1,
	--ltrv.max_reference_value AS max_ref1,
	--ltrd.specimen_collected_date_time AS sample_date,
	--ltrd.result_time AS report_date,
	to_char(ltr.request_date,'dd-Mon-yyyy') as request_date,
	s.NAME AS service_name,
	su.NAME AS service_unit_name,
	ltm.test_mode_name AS method_name,
	--spc.NAME AS specimen,
	--spc.id AS specimen_id,
	sumap.default_value AS ref_value,
	ltres.result_value AS result_value,
	su.unit AS unit,
-- 	sga.description,
-- 	sga.electronic_signature_file_name,
-- 	sga.NAME AS sign_authority,
 	s.service_group_id,
	s.positionn,
-- 	s.disclaimer,
-- 	su.service_id,
	su.position,
	ltres.id,
	service_unit_sub_field_id,
	susr.result_values,
	susf.service_unit_sub_field_name AS sub_name,
	susf.min_reference_value AS min_ref,
	susf.max_reference_value AS max_ref,
	susf.position,
	ltres.gross_desc_temp,
	ltres.micro_finding_temp,
	ltres.impression,
	ltres.IMAGE,
	ltres.emr_summary
FROM lab_test_requests ltr
INNER JOIN lab_test_request_details AS ltrd ON (ltrd.lab_test_request_id = ltr.id)
INNER JOIN lab_test_specimens AS lts ON (ltrd.id = lts.lab_test_request_detail_id AND lts.STATUS = 'Active')
LEFT JOIN service_request_details AS srd ON (ltrd.service_request_detail_id = srd.id)
LEFT JOIN service_requests AS sr ON (ltr.service_request_id = sr.id AND srd.service_request_id = sr.id)
INNER JOIN signing_authority_masters AS sga ON (ltrd.approved_by = sga.user_id)
INNER JOIN service_groups AS sg ON (sga.service_group_id = sg.id)
INNER JOIN services AS s ON (ltrd.service_id = s.id AND s.service_group_id = sg.id)
INNER JOIN lab_test_results AS ltres ON (ltres.lab_test_request_detail_id = ltrd.id)
LEFT JOIN culture_reports AS crpt ON (crpt.lab_test_result_id = ltres.id AND crpt.STATUS = 'Active')
INNER JOIN service_units AS su ON (ltres.service_unit_id = su.id)
LEFT JOIN clinicians AS cl ON (ltr.clinician_id = cl.id)
INNER JOIN visits AS v ON (ltr.visit_id = v.id)
INNER JOIN patients AS p ON (ltr.patient_id = p.id)
LEFT JOIN service_unit_machine_mappings AS sumap ON (sumap.service_unit_id = ltres.service_unit_id AND sumap.lab_test_mode_id = ltres.lab_test_mode_id AND sumap.machine_id = ltres.machine_id)
LEFT JOIN lab_test_reference_values AS ltrv ON (
		ltrv.service_unit_id = ltres.service_unit_id AND (CASE WHEN ltres.machine_id IS NULL THEN 1 = 1 ELSE ltres.machine_id = ltrv.machine_id END) AND (CASE WHEN p.gender_id = 1 THEN 68 WHEN p.gender_id = 2 THEN 69 END = ltrv.sex::INT) AND DATE_PART('day', v.visit_date::TIMESTAMP - p.date_of_birth::TIMESTAMP) BETWEEN age_start_day
			AND age_end_day
		)
LEFT JOIN lab_test_modes AS ltm ON (ltm.id = s.lab_test_mode_id)
LEFT JOIN service_unit_sub_field_results AS susr ON (susr.lab_test_result_id = ltres.id)
LEFT JOIN service_unit_sub_fields AS susf ON (susr.service_unit_sub_field_id = susf.id)
INNER JOIN specimens AS spc ON (ltrd.specimen_id = spc.id)
WHERE ltrd.STATUS = 'Approved'
and ltr.visit_id=$visit_id order by s.service_group_id,s.positionn,s.name,su.position,susf.position ";
		$res = Yii::$app->getDb()->createCommand($query)->queryAll();
		$result = array();
		if(!empty($res)) {
			foreach($res as $val) {
				$test_name = ''.$val['service_name'].(($val['service_name']!=$val['service_unit_name'])?' - '.$val['service_unit_name']:'')." ".(!empty($val['sub_name'])?' - '.$val['sub_name']:'');
				$result_value = ''.$val['result_value']."".(!empty($val['result_values'])?$val['result_values']:'');
				$reference_value = '';
				$result['test_name'][$test_name] =$test_name;
				$result['date'][$val['request_date']] = $val['request_date'];
				$result['data'][$test_name][][$val['request_date']] = $result_value ." ".$val['unit'];
			}
			$result['test_name'] = array_values($result['test_name']);
			$result['date'] = array_values($result['date']);
		}


		return $result;
	}

	public function actionUploadPatientPhoto()
	{
		if(Yii::$app->request->isPost) {
			$status =0 ;
			if(!empty($_POST['image'])) {
				$data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $_POST['image']));
				$path = 'patient_photo_uploads/'.$_POST['patient_code']."/".$_POST['visit_id'];
				FileHelper::createDirectory($path,$mode = 0775, $recursive = true);
				$file_path = $path."/". date('YMdHs').".jpg";
				file_put_contents($file_path, $data);
				$q= "insert into trn_patiet_visit_photo_gallery (mst_patient_id,patient_code,trn_visit_id,remarks,photo_path,created_date) values (".$_POST['patient_id'].",'".$_POST['patient_code']."',".$_POST['visit_id'].",'".$_POST['remarks']."','".$file_path."','".date('Y-m-d H:i:s')."'); ";
				$res = Yii::$app->getDb()->createCommand($q)->query();
				if($res) {
					$status = "1";
				}else {
					$status = "0";
				}
			}else {
				echo "No Image Found";
			}
			return array('data'=>array(array('status'=>$status)));
		}
	}

	public function actionGetPatientPhoto($patient_id=0) {
		$arr = array();
		$patient_id = !empty($patient_id)?$patient_id:0;
		$query = "select * from trn_patiet_visit_photo_gallery where mst_patient_id = ".$patient_id." and status=1 ORDER BY id desc ";
		$res = Yii::$app->getDb()->createCommand($query)->queryAll();
		if(!empty($res)) {
			$i=0;
			$base = Yii::getAlias('@web');
			$base = Yii::$app->homeUrl;
//			$base = Yii::$app->request->absoluteUrl;
			$base = Url::base(true);
			foreach($res as $value) {
				$arr[$i]['image_path'] = $base.'/'.$value['photo_path'];
				$arr[$i]['remarks'] = $value['remarks'];
				$i++;
			}
		}
		return ['data'=>$arr];
	}
}