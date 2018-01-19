<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\MenuCategory;

/**
 * MenuCategorySearch represents the model behind the search form about `backend\models\MenuCategory`.
 */
class MenuCategorySearch extends MenuCategory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'nama_category', 'parent_category_id', 'color', 'created_at', 'user_created', 'updated_at', 'user_updated', 'parentCategory.nama_category'], 'safe'],
            [['is_antrian', 'not_active', 'not_discount'], 'integer'],
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
        $query = MenuCategory::find();
        $query->joinWith([
            'parentCategory' => function($q) {
                $q->from('menu_category parent');
            }
        ]);
        
       $query->where(['!=', 'menu_category.id', '']);

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
            'menu_category.is_antrian' => $this->is_antrian,
            'menu_category.not_active' => $this->not_active,
            'menu_category.not_discount' => $this->not_discount,
            'menu_category.created_at' => $this->created_at,
            'menu_category.updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'menu_category.id', $this->id])
            ->andFilterWhere(['like', 'menu_category.nama_category', $this->nama_category])
            ->andFilterWhere(['like', 'menu_category.color', $this->color])
            ->andFilterWhere(['like', 'menu_category.user_created', $this->user_created])
            ->andFilterWhere(['like', 'menu_category.user_updated', $this->user_updated])
            ->andFilterWhere(['like', 'parent.nama_category', $this->getAttribute('parentCategory.nama_category')]);

        return $dataProvider;
    }
}
