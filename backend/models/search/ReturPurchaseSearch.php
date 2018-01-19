<?php

namespace restotech\standard\backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use restotech\standard\backend\models\ReturPurchase;

/**
 * ReturPurchaseSearch represents the model behind the search form about `restotech\standard\backend\models\ReturPurchase`.
 */
class ReturPurchaseSearch extends ReturPurchase
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'date', 'kd_supplier', 'created_at', 'user_created', 'updated_at', 'user_updated', 'kdSupplier.nama', 'returPurchaseTrxes.item.nama_item'], 'safe'],
            [['jumlah_item', 'jumlah_harga'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributes() {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), ['kdSupplier.nama', 'returPurchaseTrxes.item.nama_item']);
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
        $query = ReturPurchase::find()                
                ->joinWith([
                    'kdSupplier',
                    'returPurchaseTrxes.item',
                ])->distinct();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array(
                'pageSize' => 15,
            ),
        ]);
        
        $dataProvider->sort->attributes['kdSupplier.nama'] = [
            'asc' => ['supplier.nama' => SORT_ASC],
            'desc' => ['supplier.nama' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'date' => $this->date,
            'jumlah_item' => $this->jumlah_item,
            'jumlah_harga' => $this->jumlah_harga,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'kd_supplier', $this->kd_supplier])
            ->andFilterWhere(['like', 'user_created', $this->user_created])
            ->andFilterWhere(['like', 'user_updated', $this->user_updated])
            ->andFilterWhere(['like', 'supplier.nama',  $this->getAttribute('kdSupplier.nama')])
            ->andFilterWhere(['like', 'item.nama_item',  $this->getAttribute('returPurchaseTrxes.item.nama_item')]);

        return $dataProvider;
    }
}
