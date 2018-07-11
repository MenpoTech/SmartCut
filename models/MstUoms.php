<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mst_uoms".
 *
 * @property integer $id
 * @property string $uom_name
 * @property integer $status
 */
class MstUoms extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mst_uoms';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uom_name'], 'required'],
            [['status'], 'integer'],
            [['uom_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uom_name' => 'Uom Name',
            'status' => 'Status',
        ];
    }
}
