<?php

namespace restotech\standard\backend\models;

use Yii;

/**
 * This is the model class for table "mtable".
 *
 * @property string $id
 * @property string $mtable_category_id
 * @property string $nama_meja
 * @property integer $kapasitas
 * @property integer $not_active
 * @property string $keterangan
 * @property integer $not_ppn
 * @property integer $not_service_charge
 * @property string $image
 * @property integer $layout_x
 * @property integer $layout_y
 * @property string $shape
 * @property integer $is_deleted
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property MtableCategory $mtableCategory
 * @property User $userCreated
 * @property User $userUpdated
 * @property MtableBooking[] $mtableBookings
 * @property MtableSession[] $mtableSessions
 */
class Mtable extends \synctech\RtechBaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mtable';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'mtable_category_id'], 'required'],
            [['mtable_category_id', 'kapasitas', 'not_active', 'not_ppn', 'not_service_charge', 'layout_x', 'layout_y', 'is_deleted'], 'integer'],
            [['keterangan', 'image', 'shape'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['id'], 'string', 'max' => 24],
            [['nama_meja', 'user_created', 'user_updated'], 'string', 'max' => 32],
            [['id'], 'unique'],
            [['mtable_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => MtableCategory::className(), 'targetAttribute' => ['mtable_category_id' => 'id']],
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
            'id' => 'Meja ID',
            'mtable_category_id' => 'Ruangan ID',
            'nama_meja' => 'Nama Meja',
            'kapasitas' => 'Kapasitas',
            'not_active' => 'Non Aktif',
            'keterangan' => 'Keterangan',
            'not_ppn' => 'Non PPN',
            'not_service_charge' => 'Non Service Charge',
            'image' => 'Image',
            'layout_x' => 'Layout X',
            'layout_y' => 'Layout Y',
            'shape' => 'Shape',
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
    public function getMtableCategory()
    {
        return $this->hasOne(MtableCategory::className(), ['id' => 'mtable_category_id']);
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
    public function getMtableBookings()
    {
        return $this->hasMany(MtableBooking::className(), ['mtable_id' => 'id']);
    }

    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMtableSessions()
    {
        return $this->hasMany(MtableSession::className(), ['mtable_id' => 'id']);
    }
}
