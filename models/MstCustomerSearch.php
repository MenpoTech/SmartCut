<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\MstCustomers;

/**
 * MstCustomerSearch represents the model behind the search form about `app\models\MstCustomers`.
 */
class MstCustomerSearch extends MstCustomers
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'is_deleted', 'created_by', 'modified_by'], 'integer'],
            [['customer_name', 'customer_address','customer_email', 'city', 'state', 'customer_mobile', 'created_date', 'modified_date'], 'safe'],
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
        $query = MstCustomers::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['customer_name'=>SORT_ASC]]
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
            'created_by' => $this->created_by,
            'created_date' => $this->created_date,
            'modified_by' => $this->modified_by,
            'modified_date' => $this->modified_date,
        ]);

        $query->andFilterWhere(['like', 'lower(customer_name)', strtolower($this->customer_name)])
            ->andFilterWhere(['like', 'lower(customer_address)', strtolower($this->customer_address)])
            ->andFilterWhere(['like', 'lower(city)', strtolower($this->city)])
            ->andFilterWhere(['like', 'lower(state)', strtolower($this->state)])
            ->andFilterWhere(['like', 'lower(customer_mobile)', strtolower($this->customer_mobile)]);

        return $dataProvider;
    }
}
