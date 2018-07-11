<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "trn_site_configs".
 *
 * @property integer $id
 * @property string $var_name
 * @property string $var_value
 * @property string $created_date
 * @property integer $created_by
 */
class TrnSiteConfigs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trn_site_configs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_date'], 'safe'],
            [['created_by'], 'integer'],
            [['var_name', 'var_value'], 'string', 'max' => 150],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'var_name' => 'Var Name',
            'var_value' => 'Var Value',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
        ];
    }
}
