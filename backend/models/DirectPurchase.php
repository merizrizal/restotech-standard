<?php

namespace restotech\standard\backend\models;

use Yii;

/**
 * This is the model class for table "direct_purchase".
 *
 * @property string $id
 * @property string $date
 * @property double $jumlah_item
 * @property string $jumlah_harga
 * @property string $reference
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property User $userCreated
 * @property User $userUpdated
 * @property DirectPurchaseTrx[] $directPurchaseTrxes
 */
class DirectPurchase extends \synctech\RtechBaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'direct_purchase';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['date', 'created_at', 'updated_at'], 'safe'],
            [['jumlah_item', 'jumlah_harga'], 'number'],
            [['id'], 'string', 'max' => 13],
            [['id'], 'unique'],
            [['reference', 'user_created', 'user_updated'], 'string', 'max' => 32],
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
            'jumlah_item' => 'Jumlah Item',
            'jumlah_harga' => 'Jumlah Harga',
            'reference' => 'Reference',
            'created_at' => 'Created At',
            'user_created' => 'User Created',
            'updated_at' => 'Updated At',
            'user_updated' => 'User Updated',
        ];
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
    public function getDirectPurchaseTrxes()
    {
        return $this->hasMany(DirectPurchaseTrx::className(), ['direct_purchase_id' => 'id']);
    }
}
