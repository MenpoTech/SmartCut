<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "trn_bill_details".
 *
 * @property integer $id
 * @property integer $trn_bill_header_id
 * @property integer $mst_item_id
 * @property string $item_name
 * @property string $part_no
 * @property string $hsn_code
 * @property double $qty
 * @property integer $mst_uom_id
 * @property string $uom_name
 * @property double $uint_amount
 * @property integer $mst_tax_id
 * @property double $tax_amount
 * @property double $net_amount
 * @property integer $status
 */
class TrnBillDetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trn_bill_details';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['trn_bill_header_id', 'mst_item_id', 'item_name', 'part_no', 'hsn_code', 'qty', 'mst_uom_id', 'uom_name', 'uint_amount', 'mst_tax_id'], 'required'],
            [['trn_bill_header_id', 'mst_item_id', 'mst_uom_id', 'mst_tax_id', 'status'], 'integer'],
            [['qty', 'uint_amount', 'tax_amount', 'net_amount'], 'number'],
            [['item_name'], 'string', 'max' => 200],
            [['part_no', 'hsn_code'], 'string', 'max' => 150],
            [['uom_name'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'trn_bill_header_id' => 'Trn Bill Header ID',
            'mst_item_id' => 'Mst Item ID',
            'item_name' => 'Item Name',
            'part_no' => 'Part No',
            'hsn_code' => 'Hsn Code',
            'qty' => 'Qty',
            'mst_uom_id' => 'Mst Uom ID',
            'uom_name' => 'Uom Name',
            'uint_amount' => 'Uint Amount',
            'mst_tax_id' => 'Mst Tax ID',
            'tax_amount' => 'Tax Amount',
            'net_amount' => 'Net Amount',
            'status' => 'Status',
        ];
    }
}
