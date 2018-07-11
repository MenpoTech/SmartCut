<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "trn_emp_leave_entries".
 *
 * @property integer $id
 * @property integer $mst_employee_id
 * @property integer $mst_institution_id
 * @property integer $ars_year
 * @property integer $ars_month
 * @property string $ars_date
 * @property integer $ars_session
 * @property integer $status
 * @property integer $is_deleted
 * @property integer $mst_leave_type_id
 * @property string $created_date
 * @property integer $created_by
 * @property string $modified_date
 * @property integer $modified_by
 * @property string $remarks
 * @property string $entry_type
 * @property integer $approved_by
 * @property string $approved_date
 */
class TrnEmpLeaveEntries extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trn_emp_leave_entries';
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
            [['mst_employee_id', 'mst_institution_id', 'ars_year', 'ars_month', 'ars_session', 'status', 'is_deleted', 'mst_leave_type_id', 'created_by', 'modified_by', 'approved_by'], 'integer'],
            [['ars_date', 'created_date', 'modified_date', 'approved_date'], 'safe'],
            [['remarks'], 'string'],
            [['entry_type'], 'string', 'max' => 3],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mst_employee_id' => 'Mst Employee ID',
            'mst_institution_id' => 'Mst Institution ID',
            'ars_year' => 'Ars Year',
            'ars_month' => 'Ars Month',
            'ars_date' => 'Ars Date',
            'ars_session' => 'Ars Session',
            'status' => 'Status',
            'is_deleted' => 'Is Deleted',
            'mst_leave_type_id' => 'Mst Leave Type ID',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_date' => 'Modified Date',
            'modified_by' => 'Modified By',
            'remarks' => 'Remarks',
            'entry_type' => 'Entry Type',
            'approved_by' => 'Approved By',
            'approved_date' => 'Approved Date',
        ];
    }
}
