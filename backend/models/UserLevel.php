<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "user_level".
 *
 * @property integer $id
 * @property string $nama_level
 * @property integer $is_super_admin
 * @property integer $default_action
 * @property string $keterangan
 * @property integer $is_deleted
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property User[] $users
 * @property UserAkses[] $userAkses
 * @property UserAppModule $defaultAction
 * @property User $userCreated
 * @property User $userUpdated
 */
class UserLevel extends \sybase\SybaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_level';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nama_level'], 'required'],
            [['is_super_admin', 'default_action', 'is_deleted'], 'integer'],
            [['keterangan'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['nama_level', 'user_created', 'user_updated'], 'string', 'max' => 32],
            [['default_action'], 'exist', 'skipOnError' => true, 'targetClass' => UserAppModule::className(), 'targetAttribute' => ['default_action' => 'id']],
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
            'nama_level' => 'Nama Level',
            'is_super_admin' => 'Is Super Admin',
            'default_action' => 'Default Action',
            'keterangan' => 'Keterangan',
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
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['user_level_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserAkses()
    {
        return $this->hasMany(UserAkses::className(), ['user_level_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDefaultAction()
    {
        return $this->hasOne(UserAppModule::className(), ['id' => 'default_action']);
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
