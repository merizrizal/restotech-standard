<?php

namespace restotech\standard\backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use restotech\standard\backend\models\ItemCategory;

/**
 * ItemCategorySearch represents the model behind the search form about `restotech\standard\backend\models\ItemCategory`.
 */
class ItemCategorySearch extends ItemCategory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'nama_category', 'parent_category_id', 'keterangan', 'created_at', 'user_created', 'updated_at', 'user_updated', 'parentCategory.nama_category'], 'safe'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributes() {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), ['parentCategory.nama_category']);
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
        $query = ItemCategory::find();
        $query->joinWith([
            'parentCategory' => function($q) {
                $q->from('item_category parent');
            }
        ]);

        $query->where(['!=', 'item_category.id', '']);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array(
                'pageSize' => 15,
            ),
        ]);
        
        $dataProvider->sort->attributes['parentCategory.nama_category'] = [
            'asc' => ['parent.nama_category' => SORT_ASC],
            'desc' => ['parent.nama_category' => SORT_DESC],
        ];                

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'item_category.created_at' => $this->created_at,
            'item_category.updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'item_category.id', $this->id])            
            ->andFilterWhere(['like', 'item_category.nama_category', $this->nama_category])
            ->andFilterWhere(['like', 'item_category.parent_category_id', $this->parent_category_id])            
            ->andFilterWhere(['like', 'item_category.keterangan', $this->keterangan])
            ->andFilterWhere(['like', 'item_category.user_created', $this->user_created])
            ->andFilterWhere(['like', 'item_category.user_updated', $this->user_updated])
            ->andFilterWhere(['like', 'parent.nama_category', $this->getAttribute('parentCategory.nama_category')]);

        return $dataProvider;
    }
}
