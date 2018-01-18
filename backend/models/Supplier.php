<?php

namespace restotech\standard\backend\models;

use Yii;

/**
 * This is the model class for table "supplier".
 *
 * @property string $kd_supplier
 * @property string $nama
 * @property string $alamat
 * @property string $telp
 * @property string $fax
 * @property string $keterangan
 * @property string $kontak1
 * @property string $kontak1_telp
 * @property string $kontak2
 * @property string $kontak2_telp
 * @property string $kontak3
 * @property string $kontak3_telp
 * @property string $kontak4
 * @property string $kontak4_telp
 * @property integer $is_deleted
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property PurchaseOrder[] $purchaseOrders
 * @property ReturPurchase[] $returPurchases
 * @property User $userCreated
 * @property User $userUpdated
 * @property SupplierDelivery[] $supplierDeliveries
 */
class Supplier extends \sybase\SybaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'supplier';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kd_supplier', 'nama'], 'required'],
            [['alamat', 'keterangan'], 'string'],
            [['is_deleted'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['kd_supplier'], 'string', 'max' => 8],
            [['nama', 'kontak1', 'kontak2', 'kontak3', 'kontak4'], 'string', 'max' => 48],
            [['telp', 'fax', 'kontak1_telp', 'kontak2_telp', 'kontak3_telp', 'kontak4_telp'], 'string', 'max' => 15],
            [['user_created', 'user_updated'], 'string', 'max' => 32],
            [['kd_supplier'], 'unique'],
            [['kd_supplier'], 'match', 'pattern' => '/^[a-zA-Z0-9_-]+$/', 'message' => 'Can only contain alphanumeric characters, underscores and dashes.'],
            [['telp', 'fax', 'kontak1_telp', 'kontak2_telp', 'kontak3_telp', 'kontak4_telp'], 'number'],
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
            'kd_supplier' => 'Kode Supplier',
            'nama' => 'Nama',
            'alamat' => 'Alamat',
            'telp' => 'Telp',
            'fax' => 'Fax',
            'keterangan' => 'Keterangan',
            'kontak1' => 'Kontak 1',
            'kontak1_telp' => 'Kontak 1 Telp',
            'kontak2' => 'Kontak 2',
            'kontak2_telp' => 'Kontak 2 Telp',
            'kontak3' => 'Kontak 3',
            'kontak3_telp' => 'Kontak 3 Telp',
            'kontak4' => 'Kontak 4',
            'kontak4_telp' => 'Kontak 4 Telp',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'user_created' => 'User Created',
            'updated_at' => 'Updated At',
            'user_updated' => 'User Updated',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::className(), ['kd_supplier' => 'kd_supplier']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReturPurchases()
    {
        return $this->hasMany(ReturPurchase::className(), ['kd_supplier' => 'kd_supplier']);
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
    public function getSupplierDeliveries()
    {
        return $this->hasMany(SupplierDelivery::className(), ['kd_supplier' => 'kd_supplier']);
    }
}
