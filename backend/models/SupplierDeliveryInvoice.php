<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "supplier_delivery_invoice".
 *
 * @property string $id
 * @property string $date
 * @property string $supplier_delivery_id
 * @property string $payment_method
 * @property string $jumlah_harga
 * @property string $jumlah_bayar
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property SupplierDelivery $supplierDelivery
 * @property PaymentMethod $paymentMethod
 * @property User $userCreated
 * @property User $userUpdated
 * @property SupplierDeliveryInvoicePayment[] $supplierDeliveryInvoicePayments
 * @property SupplierDeliveryInvoiceTrx[] $supplierDeliveryInvoiceTrxes
 */
class SupplierDeliveryInvoice extends \sybase\SybaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'supplier_delivery_invoice';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'date', 'supplier_delivery_id', 'payment_method'], 'required'],
            [['date', 'created_at', 'updated_at'], 'safe'],
            [['jumlah_harga', 'jumlah_bayar'], 'number'],
            [['id', 'payment_method'], 'string', 'max' => 16],
            [['supplier_delivery_id'], 'string', 'max' => 13],
            [['user_created', 'user_updated'], 'string', 'max' => 32],
            [['supplier_delivery_id'], 'exist', 'skipOnError' => true, 'targetClass' => SupplierDelivery::className(), 'targetAttribute' => ['supplier_delivery_id' => 'id']],
            [['payment_method'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentMethod::className(), 'targetAttribute' => ['payment_method' => 'id']],
            [['user_created'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_created' => 'id']],
            [['user_updated'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_updated' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Tanggal',
            'supplier_delivery_id' => 'ID Penerimaan',
            'payment_method' => 'ID Metode Pembayaran',
            'jumlah_harga' => 'Jumlah Harga',
            'jumlah_bayar' => 'Jumlah Bayar',
            'created_at' => 'Created At',
            'user_created' => 'User Created',
            'updated_at' => 'Updated At',
            'user_updated' => 'User Updated',
            
            'supplierDelivery.kdSupplier.nama' => 'Supplier',
            'paymentMethod.nama_payment' => 'Metode Pembayaran',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSupplierDelivery()
    {
        return $this->hasOne(SupplierDelivery::className(), ['id' => 'supplier_delivery_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentMethod()
    {
        return $this->hasOne(PaymentMethod::className(), ['id' => 'payment_method']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserCreated()
    {
        return $this->hasOne(User::className(), ['id' => 'user_created']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserUpdated()
    {
        return $this->hasOne(User::className(), ['id' => 'user_updated']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSupplierDeliveryInvoicePayments()
    {
        return $this->hasMany(SupplierDeliveryInvoicePayment::className(), ['supplier_delivery_invoice_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSupplierDeliveryInvoiceTrxes()
    {
        return $this->hasMany(SupplierDeliveryInvoiceTrx::className(), ['supplier_delivery_invoice_id' => 'id']);
    }
}
