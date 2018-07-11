<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "trn_bill_headers".
 *
 * @property integer $id
 * @property integer $mst_customer_id
 * @property string $bill_no
 * @property string $bill_date
 * @property double $bill_amount
 * @property double $tax_amount
 * @property double $discount_amount
 * @property double $round_off_amount
 * @property double $net_amount
 * @property integer $status
 * @property integer $created_by
 * @property string $created_date
 * @property string $created_ip
 */
class TrnBillHeaders extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */

    public $item_name;
    public $uom;
    public static function tableName()
    {
        return 'trn_bill_headers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mst_customer_id', 'bill_date', 'bill_amount'],'required'],
            [['mst_customer_id', 'status', 'created_by'], 'integer'],
            [['bill_date', 'created_date'], 'safe'],
            [['bill_amount', 'tax_amount', 'discount_amount', 'round_off_amount', 'net_amount'], 'number'],
            [['bill_no'], 'string', 'max' => 50],
            [['created_ip'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mst_customer_id' => 'Customer',
            'bill_no' => 'Bill No',
            'bill_date' => 'Bill Date',
            'bill_amount' => 'Bill Amount',
            'tax_amount' => 'Tax Amount',
            'discount_amount' => 'Discount Amount',
            'round_off_amount' => 'Round Off Amount',
            'net_amount' => 'Net Amount',
            'status' => 'Status',
            'created_by' => 'Created By',
            'created_date' => 'Created Date',
            'created_ip' => 'Created Ip',
        ];
    }
}
