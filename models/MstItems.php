<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mst_items".
 *
 * @property string $id
 * @property string $item_name
 * @property string $amount
 * @property integer $mst_uom_id
 * @property string $hns_code
 * @property string $part_no
 * @property integer $rate_varies
 * @property integer $status
 * @property integer $is_deleted
 * @property integer $created_by
 * @property string $created_date
 * @property integer $modified_by
 * @property string $modified_date
 */
class MstItems extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mst_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['amount'], 'number'],
            [['mst_uom_id'], 'required'],
            [['mst_uom_id', 'rate_varies', 'status', 'is_deleted', 'created_by', 'modified_by'], 'integer'],
            [['created_date', 'modified_date'], 'safe'],
            [['item_name'], 'string', 'max' => 150],
            [['hns_code', 'part_no'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'item_name' => 'Item Name',
            'amount' => 'Amount',
            'mst_uom_id' => 'Mst Uom ID',
            'hns_code' => 'Hns Code',
            'part_no' => 'Part No',
            'rate_varies' => 'Rate Varies',
            'status' => 'Status',
            'is_deleted' => 'Is Deleted',
            'created_by' => 'Created By',
            'created_date' => 'Created Date',
            'modified_by' => 'Modified By',
            'modified_date' => 'Modified Date',
        ];
    }
}
