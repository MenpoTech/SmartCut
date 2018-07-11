<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mst_taxes".
 *
 * @property integer $id
 * @property string $tax_name
 * @property double $tax_percent
 * @property integer $status
 * @property integer $is_deleted
 * @property string $created_date
 * @property integer $created_by
 * @property string $created_ip
 * @property string $modified_date
 * @property integer $modified_by
 * @property string $modified_ip
 */
class MstTaxes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mst_taxes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tax_name', 'tax_percent', 'created_ip', 'modified_ip'], 'required'],
            [['tax_percent'], 'number'],
            [['status', 'is_deleted', 'created_by', 'modified_by'], 'integer'],
            [['created_date', 'modified_date'], 'safe'],
            [['tax_name'], 'string', 'max' => 50],
            [['created_ip', 'modified_ip'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tax_name' => 'Tax Name',
            'tax_percent' => 'Tax Percent',
            'status' => 'Status',
            'is_deleted' => 'Is Deleted',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'created_ip' => 'Created Ip',
            'modified_date' => 'Modified Date',
            'modified_by' => 'Modified By',
            'modified_ip' => 'Modified Ip',
        ];
    }
}
