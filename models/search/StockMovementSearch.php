<?php

namespace restotech\standard\backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use restotech\standard\backend\models\StockMovement;

/**
 * StockMovementSearch represents the model behind the search form about `backend\models\StockMovement`.
 */
class StockMovementSearch extends StockMovement
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'storage_rack_from', 'storage_rack_to'], 'integer'],
            [['type', 'item_id', 'item_sku_id', 'storage_from', 'storage_to', 'reference', 'tanggal', 'keterangan', 'created_at', 'user_created', 'updated_at', 'user_updated',
                'item.nama_item', 'itemSku.nama_sku', 'storageFrom.nama_storage', 'storageRackFrom.nama_rak', 'storageTo.nama_storage', 'storageRackTo.nama_rak'], 'safe'],
            [['jumlah'], 'number'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributes() {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), ['item.nama_item', 'itemSku.nama_sku', 'storageFrom.nama_storage', 'storageRackFrom.nama_rak', 'storageTo.nama_storage', 'storageRackTo.nama_rak']);
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
        $query = StockMovement::find()
                ->joinWith([
                    'item',
                    'itemSku',
                    'storageFrom' => function($query) {
                        $query->from('storage storage_from');
                    },
                    'storageRackFrom' => function($query) {
                        $query->from('storage_rack storage_rack_from');
                    },
                    'storageTo' => function($query) {
                        $query->from('storage storage_to');
                    },
                    'storageRackTo' => function($query) {
                        $query->from('storage_rack storage_rack_to');
                    },
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
        
        $dataProvider->sort->attributes['storageFrom.nama_storage'] = [
            'asc' => ['storage_from.nama_storage' => SORT_ASC],
            'desc' => ['storage_from.nama_storage' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['storageRackFrom.nama_rak'] = [
            'asc' => ['storage_rack_from.nama_rak' => SORT_ASC],
            'desc' => ['storage_rack_from.nama_rak' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['storageTo.nama_storage'] = [
            'asc' => ['storage_to.nama_storage' => SORT_ASC],
            'desc' => ['storage_to.nama_storage' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['storageRackTo.nama_rak'] = [
            'asc' => ['storage_rack_to.nama_rak' => SORT_ASC],
            'desc' => ['storage_rack_to.nama_rak' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'stock_movement.id' => $this->id,
            'stock_movement.jumlah' => $this->jumlah,
            'stock_movement.tanggal' => $this->tanggal,
            'stock_movement.created_at' => $this->created_at,
            'stock_movement.updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'stock_movement.type', $this->type])
            ->andFilterWhere(['like', 'stock_movement.item_id', $this->item_id])
            ->andFilterWhere(['like', 'stock_movement.item_sku_id', $this->item_sku_id])
            ->andFilterWhere(['like', 'stock_movement.storage_from', $this->storage_from])
            ->andFilterWhere(['like', 'stock_movement.storage_to', $this->storage_to])
            ->andFilterWhere(['like', 'stock_movement.reference', $this->reference])
            ->andFilterWhere(['like', 'stock_movement.keterangan', $this->keterangan])
            ->andFilterWhere(['like', 'stock_movement.user_created', $this->user_created])
            ->andFilterWhere(['like', 'stock_movement.user_updated', $this->user_updated])
            ->andFilterWhere(['like', 'item.nama_item',  $this->getAttribute('item.nama_item')])
            ->andFilterWhere(['like', 'item_sku.nama_sku',  $this->getAttribute('item_sku.nama_sku')])
            ->andFilterWhere(['like', 'storage_from.nama_storage',  $this->getAttribute('storageFrom.nama_storage')])
            ->andFilterWhere(['like', 'storage_rack_from.nama_rak',  $this->getAttribute('storageRackFrom.nama_rak')])
            ->andFilterWhere(['like', 'storage_to.nama_storage',  $this->getAttribute('storageTo.nama_storage')])
            ->andFilterWhere(['like', 'storage_rack_to.nama_rak',  $this->getAttribute('storageRackTo.nama_rak')]);

        return $dataProvider;
    }
}
