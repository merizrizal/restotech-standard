<?php

namespace restotech\standard\backend\models;

use Yii;

/**
 * This is the model class for table "mtable_booking".
 *
 * @property string $id
 * @property string $mtable_id
 * @property string $nama_pelanggan
 * @property string $date
 * @property string $time
 * @property string $keterangan
 * @property integer $is_closed
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property Mtable $mtable
 * @property User $userCreated
 * @property User $userUpdated
 */
class MtableBooking extends \synctech\SynctBaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mtable_booking';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'mtable_id', 'nama_pelanggan', 'date', 'time'], 'required'],
            [['date', 'time', 'created_at', 'updated_at'], 'safe'],
            [['keterangan'], 'string'],
            [['is_closed'], 'integer'],
            [['id'], 'string', 'max' => 16],
            [['mtable_id'], 'string', 'max' => 24],
            [['nama_pelanggan'], 'string', 'max' => 64],
            [['user_created', 'user_updated'], 'string', 'max' => 32],
            [['mtable_id'], 'exist', 'skipOnError' => true, 'targetClass' => Mtable::className(), 'targetAttribute' => ['mtable_id' => 'id']],
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
            'mtable_id' => 'Meja',
            'nama_pelanggan' => 'Nama Pelanggan',
            'date' => 'Tanggal',
            'time' => 'Jam',
            'keterangan' => 'Keterangan',
            'is_closed' => 'Is Closed',
            'created_at' => 'Created At',
            'user_created' => 'User Created',
            'updated_at' => 'Updated At',
            'user_updated' => 'User Updated',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMtable()
    {
        return $this->hasOne(Mtable::className(), ['id' => 'mtable_id']);
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
