<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mst_control_numbers".
 *
 * @property integer $id
 * @property string $number_type
 * @property string $number_next
 * @property string $number_logic
 * @property string $prefix
 * @property string $suffix
 */
class MstControlNumbers extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mst_control_numbers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['number_type', 'number_logic'], 'required'],
            [['number_next'], 'number'],
            [['number_type', 'number_logic'], 'string', 'max' => 200],
            [['prefix', 'suffix'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number_type' => 'Number Type',
            'number_next' => 'Number Next',
            'number_logic' => 'Number Logic',
            'prefix' => 'Prefix',
            'suffix' => 'Suffix',
        ];
    }

    public function getExtId($type='',$logic='',$prefix='') {
        $query = "SELECT id,number_type,number_next,number_logic,prefix,suffix FROM mst_control_numbers where lower(number_type)='$type'  AND number_logic='$logic' ";
        $res = Yii::$app->getDb()->createCommand($query)->queryOne();

        if(!!empty($res)) {
            //Have to Insert a new row
            $q_ins =  "insert into mst_control_numbers(number_type,number_next,number_logic,prefix,suffix) values ('$type',0,'$logic','$prefix','')";
            Yii::$app->getDb()->createCommand($q_ins)->query();
            $id = Yii::$app->getDb()->createCommand("select LAST_INSERT_ID()")->queryScalar();
            $q = "select * from mst_control_numbers where id = ".$id;
        }else {
            //Get the number and update the logic
            $q = " select * from mst_control_numbers where lower(number_type)='$type'  AND number_logic='$logic' FOR UPDATE ";
        }
        $result = Yii::$app->getDb()->createCommand($q)->queryOne();

        $query1= "update mst_control_numbers set number_next = number_next+1 where number_type='$type' ";
        Yii::$app->getDb()->createCommand($query1)->query();
        return $result;
    }

    public function getExtIdForDisplay($type='',$logic='',$prefix='') {
        $query = "SELECT id,number_type,number_next,number_logic,prefix,suffix FROM mst_control_numbers where lower(number_type)='$type'  AND number_logic='$logic' ";
        $res = Yii::$app->getDb()->createCommand($query)->queryOne();

        if(!!empty($res)) {
            //Have to Insert a new row
            $q =  "insert into mst_control_numbers(number_type,number_next,number_logic,prefix,suffix) values ('$type',0,'$logic','$prefix','') RETURNING * ";
        }else {
            //Get the number and update the logic
            $q = " select * from mst_control_numbers where lower(number_type)='$type'  AND number_logic='$logic' FOR UPDATE ";
        }
        $result = Yii::$app->getDb()->createCommand($q)->queryOne();
        return (!empty($result['number_next'])?(!empty($result['prefix'])?$result['prefix'].'/'.sprintf('%04d',$result['number_next']):$result['number_next']):'');
    }

    function updateReportNumber($current_no,$logic='') {
        $query = "SELECT id,number_type,number_next,number_logic,prefix,suffix FROM mst_control_numbers where number_logic='$logic' ";
        $res = Yii::$app->getDb()->createCommand($query)->queryOne();

        if(!!empty($res)) {
            //Have to Insert a new row
            $q =  "insert into mst_control_numbers(number_type,number_next,number_logic,prefix,suffix) values ('report_number',1,'$logic','','') RETURNING * ";
        }else {
            //Get the number and update the logic
            $q = " select * from mst_control_numbers where lower(number_type)='report_number' AND number_logic='$logic' FOR UPDATE ";
        }
        $result = Yii::$app->getDb()->createCommand($q)->queryOne();
        $current_no = (!empty($current_no)?$current_no:$result['number_next']);

        $query1= "update mst_control_numbers set number_next = $current_no+1 where number_type='report_number' ";
        Yii::$app->getDb()->createCommand($query1)->query();
    }
}
