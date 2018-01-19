<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SaleInvoicePayment;

/**
 * SaleInvoicePaymentSearch represents the model behind the search form about `backend\models\SaleInvoicePayment`.
 */
class SaleInvoicePaymentSearch extends SaleInvoicePayment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['sale_invoice_id', 'payment_method_id', 'keterangan', 'created_at', 'user_created', 'updated_at', 'user_updated',
                'saleInvoice.date', 'paymentMethod.nama_payment'], 'safe'],
            [['jumlah_bayar'], 'number'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributes() {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), ['saleInvoice.date', 'paymentMethod.nama_payment']);
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
        $query = SaleInvoicePayment::find()
                ->joinWith([
                    'paymentMethod',
                    'saleInvoice',
                    'saleInvoiceArPayments',
                ])
                ->distinct();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array(
                'pageSize' => 15,
            ),
        ]);
        
        $dataProvider->sort->attributes['saleInvoice.date'] = [
            'asc' => ['sale_invoice.date' => SORT_ASC],
            'desc' => ['sale_invoice.date' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['paymentMethod.nama_payment'] = [
            'asc' => ['payment_method.nama_payment' => SORT_ASC],
            'desc' => ['payment_method.nama_payment' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'sale_invoice_payment.id' => $this->id,
            'sale_invoice_payment.jumlah_bayar' => $this->jumlah_bayar,
            'sale_invoice_payment.created_at' => $this->created_at,
            'sale_invoice_payment.updated_at' => $this->updated_at,
            'DATE_FORMAT(CONVERT_TZ(sale_invoice.date, "+00:00", "+07:00"), "%Y-%m-%d")' => $this->getAttribute('saleInvoice.date')
        ]);

        $query->andFilterWhere(['like', 'sale_invoice_payment.sale_invoice_id', $this->sale_invoice_id])
            ->andFilterWhere(['like', 'sale_invoice_payment.payment_method_id', $this->payment_method_id])
            ->andFilterWhere(['like', 'sale_invoice_payment.keterangan', $this->keterangan])
            ->andFilterWhere(['like', 'sale_invoice_payment.user_created', $this->user_created])
            ->andFilterWhere(['like', 'sale_invoice_payment.user_updated', $this->user_updated])
            ->andFilterWhere(['like', 'payment_method.nama_payment', $this->getAttribute('paymentMethod.nama_payment')]);

        return $dataProvider;
    }
}
