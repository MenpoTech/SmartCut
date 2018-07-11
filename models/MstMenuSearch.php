<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\MstMenus;

/**
 * MstMenuSearch represents the model behind the search form about `app\models\MstMenus`.
 */
class MstMenuSearch extends MstMenus
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'is_deleted', 'menu_parent_id'], 'integer'],
            [['menu_name', 'menu_type', 'menu_url', 'menu_desc'], 'safe'],
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
        $query = MstMenus::find();

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
            'menu_parent_id' => $this->menu_parent_id,
        ]);

        $query->andFilterWhere(['like', 'menu_name', $this->menu_name])
            ->andFilterWhere(['like', 'menu_type', $this->menu_type])
            ->andFilterWhere(['like', 'menu_url', $this->menu_url])
            ->andFilterWhere(['like', 'menu_desc', $this->menu_desc]);

        return $dataProvider;
    }
}
