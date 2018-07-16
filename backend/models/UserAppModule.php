<?php

namespace restotech\standard\backend\models;

use Yii;

/**
 * This is the model class for table "user_app_module".
 *
 * @property string $id
 * @property string $sub_program
 * @property string $nama_module
 * @property string $module_action
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property UserAkses[] $userAkses
 * @property User $userCreated
 * @property User $userUpdated
 * @property UserLevel[] $userLevels
 */
class UserAppModule extends \synctech\RtechBaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_app_module';
    }        
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sub_program', 'nama_module', 'module_action'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['nama_module', 'module_action', 'user_created', 'user_updated'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sub_program' => 'Sub Program',
            'nama_module' => 'Nama Module',
            'module_action' => 'Module Action',
            'created_at' => 'Created At',
            'user_created' => 'User Created',
            'updated_at' => 'Updated At',
            'user_updated' => 'User Updated',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserAkses()
    {
        return $this->hasMany(UserAkses::className(), ['user_app_module_id' => 'id']);
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
    public function getUserLevels()
    {
        return $this->hasMany(UserLevel::className(), ['default_action' => 'id']);
    }
}
