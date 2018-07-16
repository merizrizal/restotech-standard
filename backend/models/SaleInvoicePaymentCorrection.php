<?php

namespace restotech\standard\backend\models;

use Yii;

/**
 * This is the model class for table "sale_invoice_payment_correction".
 *
 * @property string $id
 * @property string $sale_invoice_correction_id
 * @property string $payment_method_id
 * @property string $jumlah_bayar
 * @property string $keterangan
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property SaleInvoiceCorrection $saleInvoiceCorrection
 * @property PaymentMethod $paymentMethod
 * @property User $userCreated
 * @property User $userUpdated
 */
class SaleInvoicePaymentCorrection extends \synctech\RtechBaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sale_invoice_payment_correction';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sale_invoice_correction_id', 'payment_method_id'], 'required'],
            [['sale_invoice_correction_id'], 'integer'],
            [['jumlah_bayar'], 'number'],
            [['keterangan'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['payment_method_id'], 'string', 'max' => 16],
            [['user_created', 'user_updated'], 'string', 'max' => 32],
            [['sale_invoice_correction_id'], 'exist', 'skipOnError' => true, 'targetClass' => SaleInvoiceCorrection::className(), 'targetAttribute' => ['sale_invoice_correction_id' => 'id']],
            [['payment_method_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentMethod::className(), 'targetAttribute' => ['payment_method_id' => 'id']],
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
            'sale_invoice_correction_id' => 'Sale Invoice Correction ID',
            'payment_method_id' => 'Payment Method ID',
            'jumlah_bayar' => 'Jumlah Bayar',
            'keterangan' => 'Keterangan',
            'created_at' => 'Created At',
            'user_created' => 'User Created',
            'updated_at' => 'Updated At',
            'user_updated' => 'User Updated',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSaleInvoiceCorrection()
    {
        return $this->hasOne(SaleInvoiceCorrection::className(), ['id' => 'sale_invoice_correction_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentMethod()
    {
        return $this->hasOne(PaymentMethod::className(), ['id' => 'payment_method_id']);
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
}
