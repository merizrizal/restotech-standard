<?php

namespace restotech\standard\backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use restotech\standard\backend\models\Menu;

/**
 * MenuSearch represents the model behind the search form about `restotech\standard\backend\models\Menu`.
 */
class MenuSearch extends Menu
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'nama_menu', 'menu_category_id', 'menu_satuan_id', 'keterangan', 'image', 'created_at', 'user_created', 
                'updated_at', 'user_updated', 'menuCategory.nama_category', 'menuSatuan.nama_satuan'], 'safe'],
            [['not_active'], 'integer'],
            [['harga_pokok', 'harga_jual'], 'number'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributes() {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), ['menuCategory.nama_category', 'menuSatuan.nama_satuan']);
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
        $query = Menu::find()                
                ->joinWith(['menuCategory', 'menuSatuan'])
                ->andWhere(['menu.is_deleted' => 0]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array(
                'pageSize' => 15,
            ),
        ]);
        
        $dataProvider->sort->attributes['menuCategory.nama_category'] = [
            'asc' => ['menu_category.nama_category' => SORT_ASC],
            'desc' => ['menu_category.nama_category' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['menuSatuan.nama_satuan'] = [
            'asc' => ['menu_satuan.nama_satuan' => SORT_ASC],
            'desc' => ['menu_satuan.nama_satuan' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'not_active' => $this->not_active,
            'harga_pokok' => $this->harga_pokok,
            'harga_jual' => $this->harga_jual,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
 
        $query->andFilterWhere(['like', 'menu.id', $this->id])
            ->andFilterWhere(['like', 'menu.nama_menu', $this->nama_menu])
            ->andFilterWhere(['like', 'menu.menu_category_id', $this->menu_category_id])
            ->andFilterWhere(['like', 'menu.menu_satuan_id', $this->menu_satuan_id])
            ->andFilterWhere(['like', 'menu.keterangan', $this->keterangan])
            ->andFilterWhere(['like', 'menu.user_created', $this->user_created])
            ->andFilterWhere(['like', 'menu.user_updated', $this->user_updated])
            ->andFilterWhere(['like', 'menu_category.nama_category',  $this->getAttribute('menuCategory.nama_category')])
            ->andFilterWhere(['like', 'menu_satuan.nama_satuan',  $this->getAttribute('menuSatuan.nama_satuan')]);

        return $dataProvider;
    }
}
