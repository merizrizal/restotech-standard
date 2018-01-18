<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "sale_invoice_ar_payment".
 *
 * @property string $id
 * @property string $sale_invoice_payment_id
 * @property string $date
 * @property string $jumlah_bayar
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property SaleInvoicePayment $saleInvoicePayment
 * @property User $userCreated
 * @property User $userUpdated
 */
class SaleInvoiceArPayment extends \sybase\SybaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sale_invoice_ar_payment';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sale_invoice_payment_id', 'date', 'jumlah_bayar'], 'required'],
            [['sale_invoice_payment_id'], 'integer'],
            [['date', 'created_at', 'updated_at'], 'safe'],
            [['jumlah_bayar'], 'number'],
            [['user_created'], 'string', 'max' => 32],
            [['user_updated'], 'string', 'max' => 45],
            [['sale_invoice_payment_id'], 'exist', 'skipOnError' => true, 'targetClass' => SaleInvoicePayment::className(), 'targetAttribute' => ['sale_invoice_payment_id' => 'id']],
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
            'sale_invoice_payment_id' => 'Sale Invoice Payment ID',
            'date' => 'Tanggal',
            'jumlah_bayar' => 'Jumlah Bayar',
            'created_at' => 'Created At',
            'user_created' => 'User Created',
            'updated_at' => 'Updated At',
            'user_updated' => 'User Updated',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSaleInvoicePayment()
    {
        return $this->hasOne(SaleInvoicePayment::className(), ['id' => 'sale_invoice_payment_id']);
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
