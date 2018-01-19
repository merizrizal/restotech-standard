<?php

namespace restotech\standard\backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use restotech\standard\backend\models\SupplierDeliveryInvoice;

/**
 * SupplierDeliveryInvoiceSearch represents the model behind the search form about `restotech\standard\backend\models\SupplierDeliveryInvoice`.
 */
class SupplierDeliveryInvoiceSearch extends SupplierDeliveryInvoice
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'date', 'supplier_delivery_id', 'payment_method', 'created_at', 'user_created', 'updated_at', 'user_updated',
                'supplierDelivery.kdSupplier.nama', 'paymentMethod.nama_payment'], 'safe'],
            [['jumlah_harga', 'jumlah_bayar'], 'number'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributes() {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), ['supplierDelivery.kdSupplier.nama', 'paymentMethod.nama_payment']);
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
        $query = SupplierDeliveryInvoice::find()
                ->joinWith([
                    'supplierDelivery.kdSupplier',
                    'paymentMethod',
                ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array(
                'pageSize' => 15,
            ),
        ]);
        
        $dataProvider->sort->attributes['supplierDelivery.kdSupplier.nama'] = [
            'asc' => ['supplier.nama' => SORT_ASC],
            'desc' => ['supplier.nama' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['paymentMethod.nama_payment'] = [
            'asc' => ['payment_method.nama_payment' => SORT_ASC],
            'desc' => ['payment_method.nama_payment' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'supplier_delivery_invoice.date' => $this->date,
            'supplier_delivery_invoice.jumlah_harga' => $this->jumlah_harga,
            'supplier_delivery_invoice.jumlah_bayar' => $this->jumlah_bayar,
            'supplier_delivery_invoice.created_at' => $this->created_at,
            'supplier_delivery_invoice.updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'supplier_delivery_invoice.id', $this->id])
            ->andFilterWhere(['like', 'supplier_delivery_invoice.supplier_delivery_id', $this->supplier_delivery_id])
            ->andFilterWhere(['like', 'supplier_delivery_invoice.payment_method', $this->payment_method])
            ->andFilterWhere(['like', 'supplier_delivery_invoice.user_created', $this->user_created])
            ->andFilterWhere(['like', 'supplier_delivery_invoice.user_updated', $this->user_updated])
            ->andFilterWhere(['like', 'supplier.nama', $this->getAttribute('supplierDelivery.kdSupplier.nama')])
            ->andFilterWhere(['like', 'payment_method.nama_payment', $this->getAttribute('paymentMethod.nama_payment')]);

        return $dataProvider;
    }
}
