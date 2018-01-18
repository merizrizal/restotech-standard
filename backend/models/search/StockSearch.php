<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Stock;

/**
 * StockSearch represents the model behind the search form about `backend\models\Stock`.
 */
class StockSearch extends Stock
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'storage_rack_id'], 'integer'],
            [['item_id', 'item_sku_id', 'storage_id', 'created_at', 'user_created', 'updated_at', 'user_updated',
                'item.nama_item', 'itemSku.nama_sku', 'storage.nama_storage', 'storageRack.nama_rak'], 'safe'],
            [['jumlah_stok'], 'number'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributes() {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), ['item.nama_item', 'itemSku.nama_sku', 'storage.nama_storage', 'storageRack.nama_rak']);
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
        $query = Stock::find()
                ->joinWith([
                    'item', 
                    'itemSku',
                    'storage',
                    'storageRack',
                ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array(
                'pageSize' => 15,
            ),
        ]);
        
        $dataProvider->sort->attributes['item.nama_item'] = [
            'asc' => ['item.nama_item' => SORT_ASC],
            'desc' => ['item.nama_item' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['itemSku.nama_sku'] = [
            'asc' => ['item_sku.nama_sku' => SORT_ASC],
            'desc' => ['item_sku.nama_sku' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['storage.nama_storage'] = [
            'asc' => ['storage.nama_storage' => SORT_ASC],
            'desc' => ['storage.nama_storage' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['storageRack.nama_rak'] = [
            'asc' => ['storage_rack.nama_rak' => SORT_ASC],
            'desc' => ['storage_rack.nama_rak' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'stock.id' => $this->id,
            'stock.jumlah_stok' => $this->jumlah_stok,
            'stock.storage_rack_id' => $this->storage_rack_id,
            'stock.created_at' => $this->created_at,
            'stock.updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'stock.item_id', $this->item_id])
            ->andFilterWhere(['like', 'stock.item_sku_id', $this->item_sku_id])
            ->andFilterWhere(['like', 'stock.storage_id', $this->storage_id])
            ->andFilterWhere(['like', 'stock.user_created', $this->user_created])
            ->andFilterWhere(['like', 'stock.user_updated', $this->user_updated])
            ->andFilterWhere(['like', 'item.nama_item',  $this->getAttribute('item.nama_item')])
            ->andFilterWhere(['like', 'itemSku.nama_sku',  $this->getAttribute('item_sku.nama_sku')])
            ->andFilterWhere(['like', 'storage.nama_storage',  $this->getAttribute('storage.nama_storage')])
            ->andFilterWhere(['like', 'storage_rack.nama_rak',  $this->getAttribute('storageRack.nama_rak')]);

        return $dataProvider;
    }        
}
