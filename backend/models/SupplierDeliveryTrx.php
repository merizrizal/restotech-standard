<?php

namespace restotech\standard\backend\models;

use Yii;

/**
 * This is the model class for table "supplier_delivery_trx".
 *
 * @property string $id
 * @property string $supplier_delivery_id
 * @property string $purchase_order_id
 * @property string $purchase_order_trx_id
 * @property string $item_id
 * @property string $item_sku_id
 * @property string $storage_id
 * @property string $storage_rack_id
 * @property double $jumlah_order
 * @property double $jumlah_terima
 * @property string $harga_satuan
 * @property string $jumlah_harga
 * @property string $keterangan
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property ReturPurchaseTrx[] $returPurchaseTrxes
 * @property SupplierDelivery $supplierDelivery
 * @property PurchaseOrder $purchaseOrder
 * @property PurchaseOrderTrx $purchaseOrderTrx
 * @property Item $item
 * @property ItemSku $itemSku
 * @property StorageRack $storageRack
 * @property Storage $storage
 * @property User $userCreated
 * @property User $userUpdated
 */
class SupplierDeliveryTrx extends \synctech\RtechBaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'supplier_delivery_trx';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['supplier_delivery_id', 'purchase_order_id', 'purchase_order_trx_id', 'item_id', 'item_sku_id', 'storage_id', 'jumlah_terima'], 'required'],
            [['purchase_order_trx_id', 'storage_rack_id'], 'integer'],
            [['jumlah_order', 'jumlah_terima', 'harga_satuan', 'jumlah_harga'], 'number'],
            [['keterangan'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['supplier_delivery_id', 'purchase_order_id'], 'string', 'max' => 13],
            [['item_id', 'item_sku_id'], 'string', 'max' => 16],
            [['storage_id'], 'string', 'max' => 7],
            [['user_created', 'user_updated'], 'string', 'max' => 32],
            [['supplier_delivery_id'], 'exist', 'skipOnError' => true, 'targetClass' => SupplierDelivery::className(), 'targetAttribute' => ['supplier_delivery_id' => 'id']],
            [['purchase_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => PurchaseOrder::className(), 'targetAttribute' => ['purchase_order_id' => 'id']],
            [['purchase_order_trx_id'], 'exist', 'skipOnError' => true, 'targetClass' => PurchaseOrderTrx::className(), 'targetAttribute' => ['purchase_order_trx_id' => 'id']],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Item::className(), 'targetAttribute' => ['item_id' => 'id']],
            [['item_sku_id'], 'exist', 'skipOnError' => true, 'targetClass' => ItemSku::className(), 'targetAttribute' => ['item_sku_id' => 'id']],
            [['storage_rack_id'], 'exist', 'skipOnError' => true, 'targetClass' => StorageRack::className(), 'targetAttribute' => ['storage_rack_id' => 'id']],
            [['storage_id'], 'exist', 'skipOnError' => true, 'targetClass' => Storage::className(), 'targetAttribute' => ['storage_id' => 'id']],
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
            'supplier_delivery_id' => 'Supplier Delivery ID',
            'purchase_order_id' => 'Purchase Order ID',
            'purchase_order_trx_id' => 'Purchase Order Trx ID',
            'item_id' => 'Item ID',
            'item_sku_id' => 'Item No. SKU',
            'storage_id' => 'Storage ID',
            'storage_rack_id' => 'Storage Rack ID',
            'jumlah_order' => 'Jumlah Order',
            'jumlah_terima' => 'Jumlah Terima',
            'harga_satuan' => 'Harga Satuan',
            'jumlah_harga' => 'Jumlah Harga',
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
    public function getReturPurchaseTrxes()
    {
        return $this->hasMany(ReturPurchaseTrx::className(), ['supplier_delivery_trx_id' => 'id']);
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
    public function getPurchaseOrder()
    {
        return $this->hasOne(PurchaseOrder::className(), ['id' => 'purchase_order_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseOrderTrx()
    {
        return $this->hasOne(PurchaseOrderTrx::className(), ['id' => 'purchase_order_trx_id']);
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
    public function getStorageRack()
    {
        return $this->hasOne(StorageRack::className(), ['id' => 'storage_rack_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStorage()
    {
        return $this->hasOne(Storage::className(), ['id' => 'storage_id']);
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
