<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "stock_movement".
 *
 * @property string $id
 * @property string $type
 * @property string $item_id
 * @property string $item_sku_id
 * @property string $storage_from
 * @property string $storage_rack_from
 * @property string $storage_to
 * @property string $storage_rack_to
 * @property double $jumlah
 * @property string $tanggal
 * @property string $reference
 * @property string $keterangan
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property Item $item
 * @property ItemSku $itemSku
 * @property Storage $storageFrom
 * @property StorageRack $storageRackFrom
 * @property Storage $storageTo
 * @property StorageRack $storageRackTo
 * @property User $userCreated
 * @property User $userUpdated
 */
class StockMovement extends \sybase\SybaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stock_movement';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'item_id', 'item_sku_id'], 'required'],
            [['type', 'keterangan'], 'string'],
            [['storage_rack_from', 'storage_rack_to'], 'integer'],
            [['jumlah'], 'number'],
            [['tanggal', 'created_at', 'updated_at'], 'safe'],
            [['item_id', 'item_sku_id'], 'string', 'max' => 16],
            [['storage_from', 'storage_to'], 'string', 'max' => 7],
            [['reference', 'user_created', 'user_updated'], 'string', 'max' => 32],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Item::className(), 'targetAttribute' => ['item_id' => 'id']],
            [['item_sku_id'], 'exist', 'skipOnError' => true, 'targetClass' => ItemSku::className(), 'targetAttribute' => ['item_sku_id' => 'id']],
            [['storage_from'], 'exist', 'skipOnError' => true, 'targetClass' => Storage::className(), 'targetAttribute' => ['storage_from' => 'id']],
            [['storage_rack_from'], 'exist', 'skipOnError' => true, 'targetClass' => StorageRack::className(), 'targetAttribute' => ['storage_rack_from' => 'id']],
            [['storage_to'], 'exist', 'skipOnError' => true, 'targetClass' => Storage::className(), 'targetAttribute' => ['storage_to' => 'id']],
            [['storage_rack_to'], 'exist', 'skipOnError' => true, 'targetClass' => StorageRack::className(), 'targetAttribute' => ['storage_rack_to' => 'id']],
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
            'type' => 'Type',
            'item_id' => 'Item ID',
            'item_sku_id' => 'No. SKU',
            'storage_from' => 'From Storage',
            'storage_rack_from' => 'From Storage Rack',
            'storage_to' => 'To Storage',
            'storage_rack_to' => 'To Storage Rack',
            'jumlah' => 'Jumlah',
            'tanggal' => 'Tanggal',
            'reference' => 'Reference',
            'keterangan' => 'Keterangan',
            'created_at' => 'Created At',
            'user_created' => 'User Created',
            'updated_at' => 'Updated At',
            'user_updated' => 'User Updated',
            
            'storageFrom.nama_storage' => 'From Storage',
            'storageRackFrom.nama_rak' => 'From Rack',
            'storageTo.nama_storage' => 'To Storage',
            'storageRackTo.nama_rak' => 'To Rack',
        ];
    }
    
    public static function setInflow($type, $itemId, $itemSkuId, $storageTo, $storageRackTo, $jumlah, $tanggal, $reference) {
        $model = new StockMovement();
        $model->type = $type;
        $model->item_id = $itemId;
        $model->item_sku_id = $itemSkuId;
        $model->storage_to = $storageTo;
        $model->storage_rack_to = $storageRackTo;
        $model->jumlah = $jumlah;
        $model->tanggal = $tanggal;
        $model->reference = $reference;
        
        return $model->save();
    }
    
    public static function setOutflow($type, $itemId, $itemSkuId, $storageFrom, $storageRackFrom, $jumlah, $tanggal, $reference) {
        $model = new StockMovement();
        $model->type = $type;
        $model->item_id = $itemId;
        $model->item_sku_id = $itemSkuId;
        $model->storage_from = $storageFrom;
        $model->storage_rack_from = $storageRackFrom;
        $model->jumlah = $jumlah;
        $model->tanggal = $tanggal;
        $model->reference = $reference;
        
        return $model->save();
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
    public function getStorageFrom()
    {
        return $this->hasOne(Storage::className(), ['id' => 'storage_from']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStorageRackFrom()
    {
        return $this->hasOne(StorageRack::className(), ['id' => 'storage_rack_from']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStorageTo()
    {
        return $this->hasOne(Storage::className(), ['id' => 'storage_to']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStorageRackTo()
    {
        return $this->hasOne(StorageRack::className(), ['id' => 'storage_rack_to']);
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
