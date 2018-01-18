<?php

namespace restotech\standard\backend\models;

use Yii;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user".
 *
 * @property string $id
 * @property string $kd_karyawan
 * @property string $password
 * @property integer $user_level_id
 * @property integer $not_active
 * @property integer $is_deleted
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Employee $kdKaryawan
 * @property UserLevel $userLevel
 * @property MtableSession[] $mtableSessionsUserOpened
 * @property MtableSession[] $mtableSessionsUserClosed
 * @property MtableOrder[] $mtableOrdersFreeMenu
 * @property MtableOrder[] $mtableOrdersVoid
 */
class User extends \sybase\SybaseModel implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => function($event) {
                    return date('Y-m-d H:i:s');
                },
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {        
        return [            
            [['id', 'kd_karyawan', 'password', 'user_level_id'], 'required'],
            [['user_level_id', 'not_active', 'is_deleted'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['id'], 'string', 'max' => 32],
            [['kd_karyawan'], 'string', 'max' => 25],
            [['password'], 'string', 'max' => 64],
            [['id', 'kd_karyawan'], 'unique'],
            [['id'], 'match', 'pattern' => '/^[a-zA-Z0-9_-]+$/', 'message' => 'Can only contain alphanumeric characters, underscores and dashes.'],
            [['kd_karyawan'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::className(), 'targetAttribute' => ['kd_karyawan' => 'kd_karyawan']],
            [['user_level_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserLevel::className(), 'targetAttribute' => ['user_level_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'kd_karyawan' => 'Kode Karyawan',
            'password' => 'Password',
            'user_level_id' => 'User Level ID',
            'not_active' => 'Non Aktif',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKdKaryawan()
    {
        return $this->hasOne(Employee::className(), ['kd_karyawan' => 'kd_karyawan']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserLevel()
    {
        return $this->hasOne(UserLevel::className(), ['id' => 'user_level_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMtableSessionsUserOpened()
    {
        return $this->hasMany(MtableSession::className(), ['user_opened' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMtableSessionsUserClosed()
    {
        return $this->hasMany(MtableSession::className(), ['user_closed' => 'id']);
    }
    
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getMtableOrdersFreeMenu()
    {
        return $this->hasMany(MtableOrder::className(), ['user_free_menu' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMtableOrdersVoid()
    {
        return $this->hasMany(MtableOrder::className(), ['user_void' => 'id']);
    }

    /////////////////////////////////
    ////IdentityInterface Section////
    /////////////////////////////////
    
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['id' => $username]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        //return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        //return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
}
