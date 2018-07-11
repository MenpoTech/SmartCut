<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "trn_emp_permissions".
 *
 * @property integer $id
 * @property integer $mst_institution_id
 * @property integer $mst_employee_id
 * @property integer $mst_designation_id
 * @property integer $mst_department_id
 * @property integer $mst_leave_type_id
 * @property string $per_from_time
 * @property string $per_to_time
 * @property integer $time_interval
 * @property string $permission_time
 * @property string $reason
 * @property string $permission_status
 * @property integer $status
 * @property integer $is_deleted
 * @property string $created_date
 * @property integer $created_by
 * @property string $modified_date
 * @property integer $modified_by
 * @property string $permission_date
 * @property integer $approved_by
 * @property string $approved_date
 * @property string $remarks
 */
class TrnEmpPermissions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trn_emp_permissions';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db3');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mst_institution_id', 'mst_employee_id', 'mst_designation_id', 'mst_department_id', 'mst_leave_type_id', 'time_interval', 'status', 'is_deleted', 'created_by', 'modified_by', 'approved_by'], 'integer'],
            [['per_from_time', 'per_to_time', 'created_date', 'modified_date', 'permission_date', 'approved_date'], 'safe'],
            [['permission_time', 'reason', 'permission_status', 'remarks'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mst_institution_id' => 'Mst Institution ID',
            'mst_employee_id' => 'Mst Employee ID',
            'mst_designation_id' => 'Mst Designation ID',
            'mst_department_id' => 'Mst Department ID',
            'mst_leave_type_id' => 'Mst Leave Type ID',
            'per_from_time' => 'Per From Time',
            'per_to_time' => 'Per To Time',
            'time_interval' => 'Time Interval',
            'permission_time' => 'Permission Time',
            'reason' => 'Reason',
            'permission_status' => 'Permission Status',
            'status' => 'Status',
            'is_deleted' => 'Is Deleted',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_date' => 'Modified Date',
            'modified_by' => 'Modified By',
            'permission_date' => 'Permission Date',
            'approved_by' => 'Approved By',
            'approved_date' => 'Approved Date',
            'remarks' => 'Remarks',
        ];
    }
}
