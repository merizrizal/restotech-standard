<?php

namespace restotech\standard\backend\models;

use Yii;

/**
 * This is the model class for table "menu_satuan".
 *
 * @property string $id
 * @property string $nama_satuan
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property Menu[] $menus
 * @property User $userCreated
 * @property User $userUpdated
 */
class MenuSatuan extends \synctech\RtechBaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu_satuan';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [            
            [['id', 'nama_satuan'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['id'], 'string', 'max' => 7],
            [['id'], 'unique'],
            [['nama_satuan', 'user_created', 'user_updated'], 'string', 'max' => 32],
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
            'nama_satuan' => 'Nama Satuan',
            'created_at' => 'Created At',
            'user_created' => 'User Created',
            'updated_at' => 'Updated At',
            'user_updated' => 'User Updated',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenus()
    {
        return $this->hasMany(Menu::className(), ['menu_satuan_id' => 'id']);
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
