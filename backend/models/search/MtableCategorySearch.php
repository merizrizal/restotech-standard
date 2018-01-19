<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\MtableCategory;

/**
 * MtableCategorySearch represents the model behind the search form about `backend\models\MtableCategory`.
 */
class MtableCategorySearch extends MtableCategory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['nama_category', 'keterangan', 'not_active', 'created_at', 'user_created', 'updated_at', 'user_updated'], 'safe'],
        ];
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
        $query = MtableCategory::find()
                ->andWhere(['is_deleted' => 0]);

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
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'not_active' => $this->not_active,
        ]);

        $query->andFilterWhere(['like', 'nama_category', $this->nama_category])
            ->andFilterWhere(['like', 'keterangan', $this->keterangan])
            ->andFilterWhere(['like', 'user_created', $this->user_created])
            ->andFilterWhere(['like', 'user_updated', $this->user_updated]);

        return $dataProvider;
    }
}
