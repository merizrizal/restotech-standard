<?php

namespace restotech\standard\backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use restotech\standard\backend\models\PurchaseOrder;

/**
 * PurchaseOrderSearch represents the model behind the search form about `restotech\standard\backend\models\PurchaseOrder`.
 */
class PurchaseOrderSearch extends PurchaseOrder
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'date', 'kd_supplier', 'created_at', 'user_created', 'updated_at', 'user_updated', 'kdSupplier.nama', 'purchaseOrderTrxes.item.nama_item'], 'safe'],
            [['jumlah_item', 'jumlah_harga'], 'number'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributes() {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), ['kdSupplier.nama', 'purchaseOrderTrxes.item.nama_item']);
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
        $query = PurchaseOrder::find()                
                ->joinWith([
                    'kdSupplier',
                    'purchaseOrderTrxes.item',
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
            'purchase_order.date' => $this->date,
            'purchase_order.jumlah_item' => $this->jumlah_item,
            'purchase_order.jumlah_harga' => $this->jumlah_harga,
            'purchase_order.created_at' => $this->created_at,
            'purchase_order.updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'purchase_order.id', $this->id])
            ->andFilterWhere(['like', 'purchase_order.kd_supplier', $this->kd_supplier])
            ->andFilterWhere(['like', 'purchase_order.user_created', $this->user_created])
            ->andFilterWhere(['like', 'purchase_order.user_updated', $this->user_updated])
            ->andFilterWhere(['like', 'supplier.nama',  $this->getAttribute('kdSupplier.nama')])
            ->andFilterWhere(['like', 'item.nama_item',  $this->getAttribute('purchaseOrderTrxes.item.nama_item')]);

        return $dataProvider;
    }
}
