<?php

namespace restotech\standard\backend\models;

use Yii;

/**
 * This is the model class for table "sale_invoice_correction".
 *
 * @property string $id
 * @property string $sale_invoice_id
 * @property string $date
 * @property string $mtable_session_id
 * @property string $user_operator
 * @property string $jumlah_harga
 * @property string $discount_type
 * @property string $discount
 * @property double $pajak
 * @property double $service_charge
 * @property string $jumlah_bayar
 * @property string $jumlah_kembali
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property SaleInvoice $saleInvoice
 * @property MtableSession $mtableSession
 * @property User $userOperator
 * @property User $userCreated
 * @property User $userUpdated
 * @property SaleInvoicePaymentCorrection[] $saleInvoicePaymentCorrections
 * @property SaleInvoiceTrxCorrection[] $saleInvoiceTrxCorrections
 */
class SaleInvoiceCorrection extends \synctech\RtechBaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sale_invoice_correction';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sale_invoice_id', 'date', 'mtable_session_id'], 'required'],
            [['date', 'created_at', 'updated_at'], 'safe'],
            [['mtable_session_id'], 'integer'],
            [['jumlah_harga', 'discount', 'pajak', 'service_charge', 'jumlah_bayar', 'jumlah_kembali'], 'number'],
            [['discount_type'], 'string'],
            [['sale_invoice_id'], 'string', 'max' => 15],
            [['user_operator', 'user_created', 'user_updated'], 'string', 'max' => 32],
            [['sale_invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => SaleInvoice::className(), 'targetAttribute' => ['sale_invoice_id' => 'id']],
            [['mtable_session_id'], 'exist', 'skipOnError' => true, 'targetClass' => MtableSession::className(), 'targetAttribute' => ['mtable_session_id' => 'id']],
            [['user_operator'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_operator' => 'id']],
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
            'sale_invoice_id' => 'Sale Invoice ID',
            'date' => 'Date',
            'mtable_session_id' => 'Mtable Session ID',
            'user_operator' => 'User Operator',
            'jumlah_harga' => 'Jumlah Harga',
            'discount_type' => 'Discount Type',
            'discount' => 'Discount',
            'pajak' => 'Pajak',
            'service_charge' => 'Service Charge',
            'jumlah_bayar' => 'Jumlah Bayar',
            'jumlah_kembali' => 'Jumlah Kembali',
            'created_at' => 'Created At',
            'user_created' => 'User Created',
            'updated_at' => 'Updated At',
            'user_updated' => 'User Updated',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSaleInvoice()
    {
        return $this->hasOne(SaleInvoice::className(), ['id' => 'sale_invoice_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMtableSession()
    {
        return $this->hasOne(MtableSession::className(), ['id' => 'mtable_session_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserOperator()
    {
        return $this->hasOne(User::className(), ['id' => 'user_operator']);
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
    public function getSaleInvoicePaymentCorrections()
    {
        return $this->hasMany(SaleInvoicePaymentCorrection::className(), ['sale_invoice_correction_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSaleInvoiceTrxCorrections()
    {
        return $this->hasMany(SaleInvoiceTrxCorrection::className(), ['sale_invoice_correction_id' => 'id']);
    }
}
