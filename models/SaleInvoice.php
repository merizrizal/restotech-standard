<?php

namespace restotech\standard\backend\models;

use Yii;

/**
 * This is the model class for table "sale_invoice".
 *
 * @property string $id
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
 * @property MtableSession $mtableSession
 * @property User $userOperator
 * @property User $userCreated
 * @property User $userUpdated
 * @property SaleInvoiceCorrection[] $saleInvoiceCorrections
 * @property SaleInvoicePayment[] $saleInvoicePayments
 * @property SaleInvoiceTrx[] $saleInvoiceTrxes
 */
class SaleInvoice extends \sybase\SybaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sale_invoice';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'date', 'mtable_session_id'], 'required'],
            [['date', 'created_at', 'updated_at'], 'safe'],
            [['mtable_session_id'], 'integer'],
            [['jumlah_harga', 'discount', 'pajak', 'service_charge', 'jumlah_bayar', 'jumlah_kembali'], 'number'],
            [['discount_type'], 'string'],
            [['id'], 'string', 'max' => 15],
            [['user_operator', 'user_created', 'user_updated'], 'string', 'max' => 32],
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
            'date' => 'Tanggal',
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
            
            'userOperator.kdKaryawan.nama' => 'Operator',
        ];
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
    public function getSaleInvoiceCorrections()
    {
        return $this->hasMany(SaleInvoiceCorrection::className(), ['sale_invoice_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSaleInvoicePayments()
    {
        return $this->hasMany(SaleInvoicePayment::className(), ['sale_invoice_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSaleInvoiceTrxes()
    {
        return $this->hasMany(SaleInvoiceTrx::className(), ['sale_invoice_id' => 'id']);
    }
}
