<?php

namespace restotech\standard\backend\models;

use Yii;

/**
 * This is the model class for table "retur_purchase".
 *
 * @property string $id
 * @property string $date
 * @property string $kd_supplier
 * @property double $jumlah_item
 * @property string $jumlah_harga
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property Supplier $kdSupplier
 * @property User $userCreated
 * @property User $userUpdated
 * @property ReturPurchaseTrx[] $returPurchaseTrxes
 */
class ReturPurchase extends \synctech\RtechBaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'retur_purchase';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'kd_supplier'], 'required'],
            [['date', 'created_at', 'updated_at'], 'safe'],
            [['jumlah_item', 'jumlah_harga'], 'number'],
            [['id'], 'string', 'max' => 13],
            [['kd_supplier'], 'string', 'max' => 8],
            [['user_created', 'user_updated'], 'string', 'max' => 32],
            [['kd_supplier'], 'exist', 'skipOnError' => true, 'targetClass' => Supplier::className(), 'targetAttribute' => ['kd_supplier' => 'kd_supplier']],
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
            'date' => 'Tanggal',
            'kd_supplier' => 'Kode Supplier',
            'jumlah_item' => 'Jumlah Item',
            'jumlah_harga' => 'Jumlah Harga',
            'created_at' => 'Created At',
            'user_created' => 'User Created',
            'updated_at' => 'Updated At',
            'user_updated' => 'User Updated',
            
            'kdSupplier.nama' => 'Nama Supplier',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKdSupplier()
    {
        return $this->hasOne(Supplier::className(), ['kd_supplier' => 'kd_supplier']);
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
    public function getReturPurchaseTrxes()
    {
        return $this->hasMany(ReturPurchaseTrx::className(), ['retur_purchase_id' => 'id']);
    }
}
