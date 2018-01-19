<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\MenuSatuan;

/**
 * MenuSatuanSearch represents the model behind the search form about `backend\models\MenuSatuan`.
 */
class MenuSatuanSearch extends MenuSatuan
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'nama_satuan', 'created_at', 'user_created', 'updated_at', 'user_updated'], 'safe'],
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
        $query = MenuSatuan::find();

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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'nama_satuan', $this->nama_satuan])
            ->andFilterWhere(['like', 'user_created', $this->user_created])
            ->andFilterWhere(['like', 'user_updated', $this->user_updated]);

        return $dataProvider;
    }
}
