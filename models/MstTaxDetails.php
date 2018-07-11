<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mst_tax_details".
 *
 * @property integer $id
 * @property integer $mst_tax_id
 * @property string $tax_sub_name
 * @property double $tax_percent
 * @property integer $status
 */
class MstTaxDetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mst_tax_details';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mst_tax_id', 'tax_sub_name', 'tax_percent'], 'required'],
            [['mst_tax_id', 'status'], 'integer'],
            [['tax_percent'], 'number'],
            [['tax_sub_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mst_tax_id' => 'Mst Tax ID',
            'tax_sub_name' => 'Tax Sub Name',
            'tax_percent' => 'Tax Percent',
            'status' => 'Status',
        ];
    }
}
