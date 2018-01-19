<?php

namespace restotech\standard\backend\models;

use Yii;

/**
 * This is the model class for table "storage".
 *
 * @property string $id
 * @property string $nama_storage
 * @property string $keterangan
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property DirectPurchaseTrx[] $directPurchaseTrxes
 * @property ItemSku[] $itemSkus
 * @property ReturPurchaseTrx[] $returPurchaseTrxes
 * @property Stock[] $stocks
 * @property StockKoreksi[] $stockKoreksis
 * @property StockMovement[] $stockMovements
 * @property StockMovement[] $stockMovements0
 * @property User $userCreated
 * @property User $userUpdated
 * @property StorageRack[] $storageRacks
 * @property SupplierDeliveryTrx[] $supplierDeliveryTrxes
 */
class Storage extends \sybase\SybaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'storage';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [            
            [['id', 'nama_storage'], 'required'],
            [['keterangan'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['id'], 'string', 'max' => 7],
            [['nama_storage', 'user_created', 'user_updated'], 'string', 'max' => 32],
            [['id'], 'unique'],
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
            'nama_storage' => 'Nama Storage',
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
    public function getDirectPurchaseTrxes()
    {
        return $this->hasMany(DirectPurchaseTrx::className(), ['storage_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemSkus()
    {
        return $this->hasMany(ItemSku::className(), ['storage_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReturPurchaseTrxes()
    {
        return $this->hasMany(ReturPurchaseTrx::className(), ['storage_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStocks()
    {
        return $this->hasMany(Stock::className(), ['storage_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStockKoreksis()
    {
        return $this->hasMany(StockKoreksi::className(), ['storage_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStockMovements()
    {
        return $this->hasMany(StockMovement::className(), ['storage_from' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStockMovements0()
    {
        return $this->hasMany(StockMovement::className(), ['storage_to' => 'id']);
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
    public function getStorageRacks()
    {
        return $this->hasMany(StorageRack::className(), ['storage_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSupplierDeliveryTrxes()
    {
        return $this->hasMany(SupplierDeliveryTrx::className(), ['storage_id' => 'id']);
    }
}
