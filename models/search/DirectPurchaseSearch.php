<?php

namespace restotech\standard\backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use restotech\standard\backend\models\DirectPurchase;

/**
 * DirectPurchaseSearch represents the model behind the search form about `backend\models\DirectPurchase`.
 */
class DirectPurchaseSearch extends DirectPurchase
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'date', 'reference', 'created_at', 'user_created', 'updated_at', 'user_updated', 'directPurchaseTrxes.item.nama_item'], 'safe'],
            [['jumlah_item', 'jumlah_harga'], 'number'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributes() {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), ['directPurchaseTrxes.item.nama_item']);
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
        $query = DirectPurchase::find()                                
                ->joinWith([
                    'directPurchaseTrxes.item',
                ])->distinct();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array(
                'pageSize' => 15,
            ),
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'direct_purchase.date' => $this->date,
            'direct_purchase.jumlah_item' => $this->jumlah_item,
            'direct_purchase.jumlah_harga' => $this->jumlah_harga,
            'direct_purchase.created_at' => $this->created_at,
            'direct_purchase.updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'direct_purchase.id', $this->id])
            ->andFilterWhere(['like', 'direct_purchase.reference', $this->reference])
            ->andFilterWhere(['like', 'direct_purchase.user_created', $this->user_created])
            ->andFilterWhere(['like', 'direct_purchase.user_updated', $this->user_updated])
            ->andFilterWhere(['like', 'item.nama_item',  $this->getAttribute('directPurchaseTrxes.item.nama_item')]);

        return $dataProvider;
    }
}
