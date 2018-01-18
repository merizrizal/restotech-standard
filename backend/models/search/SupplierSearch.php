<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Supplier;

/**
 * SupplierSearch represents the model behind the search form about `backend\models\Supplier`.
 */
class SupplierSearch extends Supplier
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kd_supplier', 'nama', 'alamat', 'telp', 'fax', 'keterangan', 'kontak1', 'kontak1_telp', 'kontak2', 'kontak2_telp', 'kontak3', 'kontak3_telp', 'kontak4', 'kontak4_telp', 'created_at', 'user_created', 'updated_at', 'user_updated'], 'safe'],
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
        $query = Supplier::find()
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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'kd_supplier', $this->kd_supplier])
            ->andFilterWhere(['like', 'nama', $this->nama])
            ->andFilterWhere(['like', 'alamat', $this->alamat])
            ->andFilterWhere(['like', 'telp', $this->telp])
            ->andFilterWhere(['like', 'fax', $this->fax])
            ->andFilterWhere(['like', 'keterangan', $this->keterangan])
            ->andFilterWhere(['like', 'kontak1', $this->kontak1])
            ->andFilterWhere(['like', 'kontak1_telp', $this->kontak1_telp])
            ->andFilterWhere(['like', 'kontak2', $this->kontak2])
            ->andFilterWhere(['like', 'kontak2_telp', $this->kontak2_telp])
            ->andFilterWhere(['like', 'kontak3', $this->kontak3])
            ->andFilterWhere(['like', 'kontak3_telp', $this->kontak3_telp])
            ->andFilterWhere(['like', 'kontak4', $this->kontak4])
            ->andFilterWhere(['like', 'kontak4_telp', $this->kontak4_telp])
            ->andFilterWhere(['like', 'user_created', $this->user_created])
            ->andFilterWhere(['like', 'user_updated', $this->user_updated]);

        return $dataProvider;
    }
}
