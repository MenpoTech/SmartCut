<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\MstUser;

/**
 * MstUserSearch represents the model behind the search form about `app\models\MstUser`.
 */
class MstUserSearch extends MstUser
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'mst_institution_id', 'userref_id', 'is_common_user', 'status', 'is_deleted', 'created_by', 'modified_by', 'first_visited', 'is_email_confirm', 'is_account_activated', 'is_mail_avail', 'login_attempt_count', 'is_account_locked', 'mst_feeheading_id', 'ext_no'], 'integer'],
            [['user_type', 'username', 'password', 'displayname', 'created_date', 'modified_date', 'usr_last_access_ip', 'usr_last_access', 'user_email_id', 'reset_password_token', 'token_created_at', 'usr_status', 'second_password', 'email_confirm_token', 'tmp_password', 'station_id'], 'safe'],
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
        $query = MstUser::find();

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
            'mst_institution_id' => $this->mst_institution_id,
            'userref_id' => $this->userref_id,
            'is_common_user' => $this->is_common_user,
            'status' => $this->status,
            'is_deleted' => $this->is_deleted,
            'created_date' => $this->created_date,
            'created_by' => $this->created_by,
            'modified_date' => $this->modified_date,
            'modified_by' => $this->modified_by,
            'usr_last_access' => $this->usr_last_access,
            'token_created_at' => $this->token_created_at,
            'first_visited' => $this->first_visited,
            'is_email_confirm' => $this->is_email_confirm,
            'is_account_activated' => $this->is_account_activated,
            'is_mail_avail' => $this->is_mail_avail,
            'login_attempt_count' => $this->login_attempt_count,
            'is_account_locked' => $this->is_account_locked,
            'mst_feeheading_id' => $this->mst_feeheading_id,
            'ext_no' => $this->ext_no,
        ]);

        $query->andFilterWhere(['like', 'user_type', $this->user_type])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'displayname', $this->displayname])
            ->andFilterWhere(['like', 'usr_last_access_ip', $this->usr_last_access_ip])
            ->andFilterWhere(['like', 'user_email_id', $this->user_email_id])
            ->andFilterWhere(['like', 'reset_password_token', $this->reset_password_token])
            ->andFilterWhere(['like', 'usr_status', $this->usr_status])
            ->andFilterWhere(['like', 'second_password', $this->second_password])
            ->andFilterWhere(['like', 'email_confirm_token', $this->email_confirm_token])
            ->andFilterWhere(['like', 'tmp_password', $this->tmp_password])
            ->andFilterWhere(['like', 'station_id', $this->station_id]);

        return $dataProvider;
    }
}
