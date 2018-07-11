<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\MstRoles;

/**
 * MstRoleSearch represents the model behind the search form about `app\models\MstRoles`.
 */
class MstRoleSearch extends MstRoles
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'is_deleted'], 'integer'],
            [['role_name', 'default_route'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = MstRoles::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'is_deleted' => $this->is_deleted,
        ]);

        $query->andFilterWhere(['like', 'lower(role_name)', strtolower($this->role_name)])
            ->andFilterWhere(['like', 'lower(default_route)',strtolower($this->default_route)]);

        return $dataProvider;
    }
}
