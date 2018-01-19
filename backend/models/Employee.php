<?php

namespace restotech\standard\backend\models;

use Yii;

/**
 * This is the model class for table "employee".
 *
 * @property string $kd_karyawan
 * @property string $password_absen
 * @property string $nama
 * @property string $alamat
 * @property string $jenis_kelamin
 * @property string $phone1
 * @property string $phone2
 * @property double $limit_officer
 * @property double $sisa
 * @property boolean $not_active
 * @property string $image
 * @property integer $is_deleted
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property User $userCreated
 * @property User $userUpdated
 * @property User $user
 */
class Employee extends \sybase\SybaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'employee';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kd_karyawan', 'nama', 'alamat', 'jenis_kelamin'], 'required'],
            [['alamat', 'jenis_kelamin', 'image'], 'string'],
            [['limit_officer', 'sisa'], 'number', 'min' => 0],
            [['not_active', 'is_deleted'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['kd_karyawan'], 'string', 'max' => 25],
            [['password_absen', 'user_created', 'user_updated'], 'string', 'max' => 32],
            [['nama'], 'string', 'max' => 64],
            [['phone1', 'phone2'], 'string', 'max' => 15],
            [['kd_karyawan'], 'unique'],
            [['kd_karyawan'], 'match', 'pattern' => '/^[a-zA-Z0-9_-]+$/', 'message' => 'Can only contain alphanumeric characters, underscores and dashes.'],
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
            'kd_karyawan' => 'Kode Karyawan',
            'password_absen' => 'Password Absen',
            'nama' => 'Nama',
            'alamat' => 'Alamat',
            'jenis_kelamin' => 'Jenis Kelamin',
            'phone1' => 'Phone1',
            'phone2' => 'Phone2',
            'limit_officer' => 'Limit Officer',
            'sisa' => 'Sisa',
            'not_active' => 'Non Aktif',
            'image' => 'Image',
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
    public function getUser()
    {
        return $this->hasOne(User::className(), ['kd_karyawan' => 'kd_karyawan']);
    }
}
