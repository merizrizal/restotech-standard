<?php

namespace restotech\standard\backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use restotech\standard\backend\models\TransactionCash;

/**
 * TransactionCashSearch represents the model behind the search form about `backend\models\TransactionCash`.
 */
class TransactionCashSearch extends TransactionCash
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['account_id', 'date', 'reference_id', 'keterangan', 'created_at', 'user_created', 'updated_at', 'user_updated',
                'account.nama_account'], 'safe'],
            [['jumlah'], 'number'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributes() {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), ['account.nama_account']);
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
        $query = TransactionCash::find()
                ->joinWith(['account']);        

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array(
                'pageSize' => 15,
            ),
        ]);
        
        $dataProvider->sort->attributes['account.nama_account'] = [
            'asc' => ['transaction_account.nama_account' => SORT_ASC],
            'desc' => ['transaction_account.nama_account' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'transaction_cash.id' => $this->id,
            'transaction_cash.date' => $this->date,
            'transaction_cash.jumlah' => $this->jumlah,
            'transaction_cash.created_at' => $this->created_at,
            'transaction_cash.updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'transaction_cash.account_id', $this->account_id])
            ->andFilterWhere(['like', 'transaction_cash.reference_id', $this->reference_id])
            ->andFilterWhere(['like', 'transaction_cash.keterangan', $this->keterangan])
            ->andFilterWhere(['like', 'transaction_cash.user_created', $this->user_created])
            ->andFilterWhere(['like', 'transaction_cash.user_updated', $this->user_updated])
            ->andFilterWhere(['like', 'transaction_account.nama_account', $this->getAttribute('account.nama_account')]);

        return $dataProvider;
    }
}
