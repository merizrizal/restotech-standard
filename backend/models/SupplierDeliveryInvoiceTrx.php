<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "supplier_delivery_invoice_trx".
 *
 * @property string $id
 * @property string $supplier_delivery_invoice_id
 * @property string $item_id
 * @property string $item_sku_id
 * @property double $jumlah_item
 * @property string $harga_satuan
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property SupplierDeliveryInvoice $supplierDeliveryInvoice
 * @property Item $item
 * @property ItemSku $itemSku
 * @property User $userCreated
 * @property User $userUpdated
 */
class SupplierDeliveryInvoiceTrx extends \sybase\SybaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'supplier_delivery_invoice_trx';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['supplier_delivery_invoice_id'], 'required'],
            [['jumlah_item', 'harga_satuan'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['supplier_delivery_invoice_id', 'item_id', 'item_sku_id'], 'string', 'max' => 16],
            [['user_created', 'user_updated'], 'string', 'max' => 32],
            [['supplier_delivery_invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => SupplierDeliveryInvoice::className(), 'targetAttribute' => ['supplier_delivery_invoice_id' => 'id']],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Item::className(), 'targetAttribute' => ['item_id' => 'id']],
            [['item_sku_id'], 'exist', 'skipOnError' => true, 'targetClass' => ItemSku::className(), 'targetAttribute' => ['item_sku_id' => 'id']],
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
            'supplier_delivery_invoice_id' => 'Supplier Delivery Invoice ID',
            'item_id' => 'Item ID',
            'item_sku_id' => 'Item Sku ID',
            'jumlah_item' => 'Jumlah Item',
            'harga_satuan' => 'Harga Satuan',
            'created_at' => 'Created At',
            'user_created' => 'User Created',
            'updated_at' => 'Updated At',
            'user_updated' => 'User Updated',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSupplierDeliveryInvoice()
    {
        return $this->hasOne(SupplierDeliveryInvoice::className(), ['id' => 'supplier_delivery_invoice_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['id' => 'item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemSku()
    {
        return $this->hasOne(ItemSku::className(), ['id' => 'item_sku_id']);
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
