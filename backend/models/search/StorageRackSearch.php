<?php

namespace restotech\standard\backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use restotech\standard\backend\models\StorageRack;

/**
 * StorageRackSearch represents the model behind the search form about `restotech\standard\backend\models\StorageRack`.
 */
class StorageRackSearch extends StorageRack {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id'], 'integer'],
            [['storage_id', 'nama_rak', 'keterangan'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
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
    public function search($params) {
        $query = StorageRack::find()
                ->joinWith([
                    'storage'
                ])
                ->andWhere(['storage_rack.storage_id' => $this->storage_id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array(
                'pageSize' => 15,
            ),
        ]);

        $dataProvider->sort->attributes['storage.nama_storage'] = [
            'asc' => ['storage.nama_storage' => SORT_ASC],
            'desc' => ['storage.nama_storage' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'storage_rack.id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'storage_rack.nama_rak', $this->nama_rak])
            ->andFilterWhere(['like', 'storage_rack.keterangan', $this->keterangan]);

        return $dataProvider;
    }
}
