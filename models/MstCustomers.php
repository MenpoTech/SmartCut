<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mst_customers".
 *
 * @property integer $id
 * @property string $customer_name
 * @property string $customer_address
 * @property string $city
 * @property string $state
 * @property string $customer_mobile
 * @property integer $status
 * @property integer $is_deleted
 * @property integer $created_by
 * @property string $created_date
 * @property integer $modified_by
 * @property string $modified_date
 * @property string $customer_email
 */
class MstCustomers extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mst_customers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_name'], 'required'],
            [['customer_email'], 'email'],
            [['status', 'is_deleted', 'created_by', 'modified_by'], 'integer'],
            [['created_date', 'modified_date'], 'safe'],
            [['customer_name', 'city','customer_email'], 'string', 'max' => 150],
            [['customer_address'], 'string', 'max' => 300],
            [['state'], 'string', 'max' => 200],
            [['customer_mobile'], 'string', 'max' => 12],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_name' => 'Customer Name',
            'customer_address' => 'Customer Address',
            'city' => 'City',
            'state' => 'State',
            'customer_mobile' => 'Customer Mobile',
            'status' => 'Status',
            'is_deleted' => 'Is Deleted',
            'created_by' => 'Created By',
            'created_date' => 'Created Date',
            'modified_by' => 'Modified By',
            'modified_date' => 'Modified Date',
            'customer_email' => 'Email ID',
        ];
    }
}
