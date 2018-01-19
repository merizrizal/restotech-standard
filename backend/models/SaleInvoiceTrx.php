<?php

namespace restotech\standard\backend\models;

use Yii;

/**
 * This is the model class for table "sale_invoice_trx".
 *
 * @property string $id
 * @property string $sale_invoice_id
 * @property string $menu_id
 * @property string $catatan
 * @property double $jumlah
 * @property string $discount_type
 * @property string $discount
 * @property string $harga_satuan
 * @property integer $is_free_menu
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property SaleInvoiceRetur[] $saleInvoiceReturs
 * @property SaleInvoice $saleInvoice
 * @property Menu $menu
 * @property User $userCreated
 * @property User $userUpdated
 */
class SaleInvoiceTrx extends \sybase\SybaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sale_invoice_trx';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sale_invoice_id', 'menu_id'], 'required'],
            [['catatan', 'discount_type'], 'string'],
            [['jumlah', 'discount', 'harga_satuan'], 'number'],
            [['is_free_menu'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['sale_invoice_id'], 'string', 'max' => 15],
            [['menu_id', 'user_created', 'user_updated'], 'string', 'max' => 32],
            [['sale_invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => SaleInvoice::className(), 'targetAttribute' => ['sale_invoice_id' => 'id']],
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
            'sale_invoice_id' => 'Sale Invoice ID',
            'menu_id' => 'Menu ID',
            'catatan' => 'Catatan',
            'jumlah' => 'Jumlah',
            'discount_type' => 'Discount Type',
            'discount' => 'Discount',
            'harga_satuan' => 'Harga Satuan',
            'is_free_menu' => 'Is Free Menu',
            'created_at' => 'Created At',
            'user_created' => 'User Created',
            'updated_at' => 'Updated At',
            'user_updated' => 'User Updated',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSaleInvoiceReturs()
    {
        return $this->hasMany(SaleInvoiceRetur::className(), ['sale_invoice_trx_id' => 'id']);
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
