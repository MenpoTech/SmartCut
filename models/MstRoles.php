<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;


/**
 * This is the model class for table "mst_roles".
 *
 * @property integer $id
 * @property string $role_name
 * @property integer $status
 * @property integer $is_deleted
 * @property string $default_route
 */
class MstRoles extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mst_roles';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'is_deleted'], 'integer'],
            [['role_name'], 'string', 'max' => 20],
            [['default_route'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role_name' => 'Role Name',
            'status' => 'Status',
            'is_deleted' => 'Is Deleted',
            'default_route' => 'Default Route',
        ];
    }
    public function search($params)
    {
        $query = MstRoles::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'role_name' => $this->role_name,
                ]);

        $query->andFilterWhere(['like', 'role_name', $this->role_name]);

        return $dataProvider;
    }
}
