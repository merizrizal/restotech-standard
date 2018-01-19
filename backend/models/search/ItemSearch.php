<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Item;

/**
 * ItemSearch represents the model behind the search form about `backend\models\Item`.
 */
class ItemSearch extends Item
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'parent_item_category_id', 'item_category_id', 'nama_item', 'keterangan', 
                'created_at', 'user_created', 'updated_at', 'user_updated',
                'parentItemCategory.nama_category', 'itemCategory.nama_category'], 'safe'],
            [['not_active'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributes() {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), ['parentItemCategory.nama_category', 'itemCategory.nama_category']);
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
        $query = Item::find();
        
        $query->joinWith([
            'parentItemCategory' => function($q) {
                $q->from('item_category parent');
            }
        ]);
        
        $query->joinWith([
            'itemCategory' => function($q) {
                $q->from('item_category child');
            }
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array(
                'pageSize' => 15,
            ),
        ]);
        
        $dataProvider->sort->attributes['parentItemCategory.nama_category'] = [
            'asc' => ['parent.nama_category' => SORT_ASC],
            'desc' => ['parent.nama_category' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['itemCategory.nama_category'] = [
            'asc' => ['child.nama_category' => SORT_ASC],
            'desc' => ['child.nama_category' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'not_active' => $this->not_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'item.id', $this->id])
            ->andFilterWhere(['like', 'item.parent_item_category_id', $this->parent_item_category_id])
            ->andFilterWhere(['like', 'item.item_category_id', $this->item_category_id])
            ->andFilterWhere(['like', 'item.nama_item', $this->nama_item])
            ->andFilterWhere(['like', 'item.keterangan', $this->keterangan])
            ->andFilterWhere(['like', 'item.user_created', $this->user_created])
            ->andFilterWhere(['like', 'item.user_updated', $this->user_updated])
            ->andFilterWhere(['like', 'parent.nama_category',  $this->getAttribute('parentItemCategory.nama_category')])
            ->andFilterWhere(['like', 'child.nama_category',  $this->getAttribute('itemCategory.nama_category')]);

        return $dataProvider;
    }
    
    /**
     * Find Item
     * 
     * @param array $isJoin
     * 
     * @return ActiveQuery
     */
    
    public static function getData($isJoin = false) {
        $query = Item::find();
        
        if ($isJoin) {
            $query->joinWith([
                'parentItemCategory' => function($q) {
                    $q->from('item_category parent');
                }
            ]);
            $query->joinWith([
                'itemCategory' => function($q) {
                    $q->from('item_category child');
                }
            ]);
        }
        
        return $query;
    }
    
}
