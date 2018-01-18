<?php

namespace restotech\standard\backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use restotech\standard\backend\models\User;

/**
 * UserSearch represents the model behind the search form about `backend\models\User`.
 */
class UserSearch extends User
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'kd_karyawan', 'password', 'kdKaryawan.nama', 'userLevel.nama_level'], 'safe'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributes() {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), ['kdKaryawan.nama', 'userLevel.nama_level']);
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
        $query = User::find();
        $query->joinWith(['kdKaryawan', 'userLevel'])
                ->andWhere(['user.is_deleted' => 0]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array(
                'pageSize' => 15,
            ),
        ]);
        
        $dataProvider->sort->attributes['kdKaryawan.nama'] = [
            'asc' => ['employee.nama' => SORT_ASC],
            'desc' => ['employee.nama' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['userLevel.nama_level'] = [
            'asc' => ['user_level.nama_level' => SORT_ASC],
            'desc' => ['user_level.nama_level' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'user.id', $this->id])
            ->andFilterWhere(['like', 'user.kd_karyawan', $this->kd_karyawan])
            ->andFilterWhere(['like', 'user.password', $this->password])
            ->andFilterWhere(['like', 'employee.nama',  $this->getAttribute('kdKaryawan.nama')])
            ->andFilterWhere(['like', 'user_level.nama_level', $this->getAttribute('userLevel.nama_level')]);

        return $dataProvider;
    }
}
