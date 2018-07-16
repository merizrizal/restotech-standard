<?php

namespace restotech\standard\backend\models;

use Yii;

/**
 * This is the model class for table "item".
 *
 * @property string $id
 * @property string $parent_item_category_id
 * @property string $item_category_id
 * @property string $nama_item
 * @property string $keterangan
 * @property integer $not_active
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property DirectPurchaseTrx[] $directPurchaseTrxes
 * @property ItemCategory $itemCategory
 * @property User $userCreated
 * @property User $userUpdated
 * @property ItemCategory $parentItemCategory
 * @property ItemSku[] $itemSkus
 * @property MenuRecipe[] $menuRecipes
 * @property PurchaseOrderTrx[] $purchaseOrderTrxes
 * @property ReturPurchaseTrx[] $returPurchaseTrxes
 * @property Stock[] $stocks
 * @property StockKoreksi[] $stockKoreksis
 * @property StockMovement[] $stockMovements
 * @property SupplierDeliveryInvoiceTrx[] $supplierDeliveryInvoiceTrxes
 * @property SupplierDeliveryTrx[] $supplierDeliveryTrxes
 */
class Item extends \synctech\RtechBaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'item';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'parent_item_category_id', 'nama_item'], 'required'],
            [['keterangan'], 'string'],
            [['not_active'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['id', 'parent_item_category_id', 'item_category_id'], 'string', 'max' => 16],
            [['nama_item', 'user_created', 'user_updated'], 'string', 'max' => 32],
            [['id'], 'unique'],
            [['item_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => ItemCategory::className(), 'targetAttribute' => ['item_category_id' => 'id']],
            [['user_created'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_created' => 'id']],
            [['user_updated'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_updated' => 'id']],
            [['parent_item_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => ItemCategory::className(), 'targetAttribute' => ['parent_item_category_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_item_category_id' => 'Item Category ID',
            'item_category_id' => 'Sub Item Category ID',
            'nama_item' => 'Nama Item',
            'keterangan' => 'Keterangan',
            'not_active' => 'Non Aktif',
            'created_at' => 'Created At',
            'user_created' => 'User Created',
            'updated_at' => 'Updated At',
            'user_updated' => 'User Updated',
            
            'itemCategory.nama_category' => 'Nama Sub Category',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDirectPurchaseTrxes()
    {
        return $this->hasMany(DirectPurchaseTrx::className(), ['item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemCategory()
    {
        return $this->hasOne(ItemCategory::className(), ['id' => 'item_category_id']);
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
    public function getParentItemCategory()
    {
        return $this->hasOne(ItemCategory::className(), ['id' => 'parent_item_category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemSkus()
    {
        return $this->hasMany(ItemSku::className(), ['item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuRecipes()
    {
        return $this->hasMany(MenuRecipe::className(), ['item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseOrderTrxes()
    {
        return $this->hasMany(PurchaseOrderTrx::className(), ['item_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReturPurchaseTrxes()
    {
        return $this->hasMany(ReturPurchaseTrx::className(), ['item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStocks()
    {
        return $this->hasMany(Stock::className(), ['item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStockKoreksis()
    {
        return $this->hasMany(StockKoreksi::className(), ['item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStockMovements()
    {
        return $this->hasMany(StockMovement::className(), ['item_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSupplierDeliveryInvoiceTrxes()
    {
        return $this->hasMany(SupplierDeliveryInvoiceTrx::className(), ['item_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSupplierDeliveryTrxes()
    {
        return $this->hasMany(SupplierDeliveryTrx::className(), ['item_id' => 'id']);
    }
}