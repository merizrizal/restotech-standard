<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SupplierDeliveryInvoicePayment;

/**
 * SupplierDeliveryInvoicePaymentSearch represents the model behind the search form about `backend\models\SupplierDeliveryInvoicePayment`.
 */
class SupplierDeliveryInvoicePaymentSearch extends SupplierDeliveryInvoicePayment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['supplier_delivery_invoice_id', 'created_at', 'user_created', 'updated_at', 'user_updated'], 'safe'],
            [['jumlah_bayar'], 'number'],
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
        $query = SupplierDeliveryInvoicePayment::find();

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
            'jumlah_bayar' => $this->jumlah_bayar,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'supplier_delivery_invoice_id', $this->supplier_delivery_invoice_id])
            ->andFilterWhere(['like', 'user_created', $this->user_created])
            ->andFilterWhere(['like', 'user_updated', $this->user_updated]);

        return $dataProvider;
    }
}
