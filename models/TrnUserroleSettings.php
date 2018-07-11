<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "trn_userrole_settings".
 *
 * @property integer $id
 * @property integer $mst_user_id
 * @property integer $mst_role_id
 * @property integer $status
 * @property integer $is_deleted
 */
class TrnUserroleSettings extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trn_userrole_settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mst_user_id', 'mst_role_id', 'status', 'is_deleted'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mst_user_id' => 'Mst User ID',
            'mst_role_id' => 'Mst Role ID',
            'status' => 'Status',
            'is_deleted' => 'Is Deleted',
        ];
    }
}
