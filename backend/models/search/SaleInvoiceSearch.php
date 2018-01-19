<?php

namespace restotech\standard\backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use restotech\standard\backend\models\SaleInvoice;

/**
 * SaleInvoiceSearch represents the model behind the search form about `restotech\standard\backend\models\SaleInvoice`.
 */
class SaleInvoiceSearch extends SaleInvoice
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'date', 'user_operator', 'discount_type', 'created_at', 'user_created', 'updated_at', 'user_updated',
                'mtableSession.mtable.nama_meja', 'userOperator.kdKaryawan.nama'], 'safe'],
            [['mtable_session_id'], 'integer'],
            [['jumlah_harga', 'discount', 'pajak', 'service_charge', 'jumlah_bayar', 'jumlah_kembali'], 'number'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributes() {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), ['mtableSession.mtable.nama_meja', 'userOperator.kdKaryawan.nama']);
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
        $query = SaleInvoice::find()
                ->joinWith([
                    'mtableSession',
                    'mtableSession.mtable',
                    'userOperator.kdKaryawan',
                ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array(
                'pageSize' => 15,
            ),
        ]);
        
        $dataProvider->sort->attributes['mtableSession.mtable.nama_meja'] = [
            'asc' => ['mtable.nama_meja' => SORT_ASC],
            'desc' => ['mtable.nama_meja' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['userOperator.kdKaryawan.nama'] = [
            'asc' => ['employee.nama' => SORT_ASC],
            'desc' => ['employee.nama' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([            
            'DATE_FORMAT(CONVERT_TZ(sale_invoice.date, "+00:00", "+07:00"), "%Y-%m-%d")' => $this->date,
            'sale_invoice.mtable_session_id' => $this->mtable_session_id,
            'sale_invoice.jumlah_harga' => $this->jumlah_harga,
            'sale_invoice.discount' => $this->discount,
            'sale_invoice.pajak' => $this->pajak,
            'sale_invoice.service_charge' => $this->service_charge,
            'sale_invoice.jumlah_bayar' => $this->jumlah_bayar,
            'sale_invoice.jumlah_kembali' => $this->jumlah_kembali,
            'sale_invoice.created_at' => $this->created_at,
            'sale_invoice.updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'sale_invoice.id', $this->id])
            ->andFilterWhere(['like', 'sale_invoice.user_operator', $this->user_operator])
            ->andFilterWhere(['like', 'sale_invoice.discount_type', $this->discount_type])
            ->andFilterWhere(['like', 'sale_invoice.user_created', $this->user_created])
            ->andFilterWhere(['like', 'sale_invoice.user_updated', $this->user_updated])
            ->andFilterWhere(['like', 'mtable.nama_meja', $this->getAttribute('mtableSession.mtable.nama_meja')])
            ->andFilterWhere(['like', 'employee.nama', $this->getAttribute('userOperator.kdKaryawan.nama')]);

        return $dataProvider;
    }
}
