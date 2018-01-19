<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "item_sku".
 *
 * @property string $id
 * @property string $item_id
 * @property string $nama_sku
 * @property integer $no_urut
 * @property double $stok_minimal
 * @property double $per_stok
 * @property string $storage_id
 * @property string $storage_rack_id
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property DirectPurchaseTrx[] $directPurchaseTrxes
 * @property Item $item
 * @property User $userCreated
 * @property User $userUpdated
 * @property Storage $storage
 * @property StorageRack $storageRack
 * @property MenuRecipe[] $menuRecipes
 * @property PurchaseOrderTrx[] $purchaseOrderTrxes
 * @property ReturPurchaseTrx[] $returPurchaseTrxes
 * @property Stock[] $stocks
 * @property StockKoreksi[] $stockKoreksis
 * @property StockMovement[] $stockMovements
 * @property SupplierDeliveryInvoiceTrx[] $supplierDeliveryInvoiceTrxes
 * @property SupplierDeliveryTrx[] $supplierDeliveryTrxes
 */
class ItemSku extends \sybase\SybaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'item_sku';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['id'], 'required'],
            [['no_urut', 'storage_rack_id'], 'integer'],
            [['stok_minimal', 'per_stok'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['id', 'item_id'], 'string', 'max' => 16],
            [['nama_sku', 'user_created', 'user_updated'], 'string', 'max' => 32],
            [['storage_id'], 'string', 'max' => 12],
            [['id'], 'unique'],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Item::className(), 'targetAttribute' => ['item_id' => 'id']],
            [['user_created'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_created' => 'id']],
            [['user_updated'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_updated' => 'id']],
            [['storage_id'], 'exist', 'skipOnError' => true, 'targetClass' => Storage::className(), 'targetAttribute' => ['storage_id' => 'id']],
            [['storage_rack_id'], 'exist', 'skipOnError' => true, 'targetClass' => StorageRack::className(), 'targetAttribute' => ['storage_rack_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'No. SKU',
            'item_id' => 'Item ID',
            'nama_sku' => 'Satuan',
            'no_urut' => 'No Urut',
            'stok_minimal' => 'Stok Minimal',
            'per_stok' => 'Per Stok',
            'storage_id' => 'Storage ID',
            'storage_rack_id' => 'Storage Rack ID',
            'created_at' => 'Created At',
            'user_created' => 'User Created',
            'updated_at' => 'Updated At',
            'user_updated' => 'User Updated',
            
            'item.id' => 'Item ID',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDirectPurchaseTrxes()
    {
        return $this->hasMany(DirectPurchaseTrx::className(), ['item_sku_id' => 'id']);
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
    public function getStorage()
    {
        return $this->hasOne(Storage::className(), ['id' => 'storage_id']);
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
    public function getMenuRecipes()
    {
        return $this->hasMany(MenuRecipe::className(), ['item_sku_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseOrderTrxes()
    {
        return $this->hasMany(PurchaseOrderTrx::className(), ['item_sku_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReturPurchaseTrxes()
    {
        return $this->hasMany(ReturPurchaseTrx::className(), ['item_sku_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStocks()
    {
        return $this->hasMany(Stock::className(), ['item_sku_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStockKoreksis()
    {
        return $this->hasMany(StockKoreksi::className(), ['item_sku_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStockMovements()
    {
        return $this->hasMany(StockMovement::className(), ['item_sku_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSupplierDeliveryInvoiceTrxes()
    {
        return $this->hasMany(SupplierDeliveryInvoiceTrx::className(), ['item_sku_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSupplierDeliveryTrxes()
    {
        return $this->hasMany(SupplierDeliveryTrx::className(), ['item_sku_id' => 'id']);
    }
}
