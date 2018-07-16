<?php

namespace restotech\standard\backend\models;

use Yii;

/**
 * This is the model class for table "direct_purchase_trx".
 *
 * @property string $id
 * @property string $direct_purchase_id
 * @property string $item_id
 * @property string $item_sku_id
 * @property string $storage_id
 * @property string $storage_rack_id
 * @property double $jumlah_item
 * @property string $harga_satuan
 * @property string $jumlah_harga
 * @property string $keterangan
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property DirectPurchase $directPurchase
 * @property Item $item
 * @property ItemSku $itemSku
 * @property Storage $storage
 * @property StorageRack $storageRack
 * @property User $userCreated
 * @property User $userUpdated
 */
class DirectPurchaseTrx extends \synctech\RtechBaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'direct_purchase_trx';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['direct_purchase_id', 'item_id', 'item_sku_id', 'storage_id', 'jumlah_item'], 'required'],
            [['storage_rack_id'], 'integer'],
            [['jumlah_item', 'harga_satuan', 'jumlah_harga'], 'number'],
            [['keterangan'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['direct_purchase_id'], 'string', 'max' => 13],
            [['item_id', 'item_sku_id'], 'string', 'max' => 16],
            [['storage_id'], 'string', 'max' => 7],
            [['user_created', 'user_updated'], 'string', 'max' => 32],
            [['direct_purchase_id'], 'exist', 'skipOnError' => true, 'targetClass' => DirectPurchase::className(), 'targetAttribute' => ['direct_purchase_id' => 'id']],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Item::className(), 'targetAttribute' => ['item_id' => 'id']],
            [['item_sku_id'], 'exist', 'skipOnError' => true, 'targetClass' => ItemSku::className(), 'targetAttribute' => ['item_sku_id' => 'id']],
            [['storage_id'], 'exist', 'skipOnError' => true, 'targetClass' => Storage::className(), 'targetAttribute' => ['storage_id' => 'id']],
            [['storage_rack_id'], 'exist', 'skipOnError' => true, 'targetClass' => StorageRack::className(), 'targetAttribute' => ['storage_rack_id' => 'id']],
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
            'direct_purchase_id' => 'Direct Purchase ID',
            'item_id' => 'Item ID',
            'item_sku_id' => 'Item No. SKU',
            'storage_id' => 'Storage ID',
            'storage_rack_id' => 'Storage Rack ID',
            'jumlah_item' => 'Jumlah Item',
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
    public function getDirectPurchase()
    {
        return $this->hasOne(DirectPurchase::className(), ['id' => 'direct_purchase_id']);
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
