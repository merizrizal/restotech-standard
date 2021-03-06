<?php

namespace restotech\standard\backend\models;

use Yii;

/**
 * This is the model class for table "sale_invoice_retur".
 *
 * @property string $id
 * @property string $sale_invoice_trx_id
 * @property string $date
 * @property string $menu_id
 * @property double $jumlah
 * @property string $discount_type
 * @property string $discount
 * @property string $harga
 * @property string $keterangan
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property SaleInvoiceTrx $saleInvoiceTrx
 * @property Menu $menu
 * @property User $userCreated
 * @property User $userUpdated
 */
class SaleInvoiceRetur extends \synctech\SynctBaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sale_invoice_retur';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sale_invoice_trx_id', 'menu_id'], 'required'],
            [['sale_invoice_trx_id'], 'integer'],
            [['date', 'created_at', 'updated_at'], 'safe'],
            [['jumlah', 'discount', 'harga'], 'number'],
            [['discount_type', 'keterangan'], 'string'],
            [['menu_id', 'user_created', 'user_updated'], 'string', 'max' => 32],
            [['sale_invoice_trx_id'], 'exist', 'skipOnError' => true, 'targetClass' => SaleInvoiceTrx::className(), 'targetAttribute' => ['sale_invoice_trx_id' => 'id']],
            [['menu_id'], 'exist', 'skipOnError' => true, 'targetClass' => Menu::className(), 'targetAttribute' => ['menu_id' => 'id']],
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
            'sale_invoice_trx_id' => 'Sale Invoice Trx ID',
            'date' => 'Date',
            'menu_id' => 'Menu ID',
            'jumlah' => 'Jumlah',
            'discount_type' => 'Discount Type',
            'discount' => 'Discount',
            'harga' => 'Harga',
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
    public function getSaleInvoiceTrx()
    {
        return $this->hasOne(SaleInvoiceTrx::className(), ['id' => 'sale_invoice_trx_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenu()
    {
        return $this->hasOne(Menu::className(), ['id' => 'menu_id']);
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
