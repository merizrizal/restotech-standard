<?php

namespace restotech\standard\backend\models;

use Yii;

/**
 * This is the model class for table "shift".
 *
 * @property string $id
 * @property string $start_time
 * @property string $end_time
 * @property string $keterangan
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property SaldoKasir[] $saldoKasirs
 * @property User $userCreated
 * @property User $userUpdated
 */
class Shift extends \synctech\RtechBaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shift';
    }    
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['start_time', 'end_time', 'created_at', 'updated_at'], 'safe'],
            [['keterangan'], 'string'],
            [['user_created', 'user_updated'], 'string', 'max' => 32],
            [['end_time'], 'compare', 'compareAttribute' => 'start_time', 'operator' => '>', 'message' => 'End Time harus lebih besar dari Start Time'],
            [['user_created'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_created' => 'id']],
            [['user_updated'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_updated' => 'id']]
        ];
    }
    
    public function compareTime($attribute, $params)
    {
        if (strtotime($this->end_time) < strtotime($this->start_time))
            $this->addError('end_time', 'End Time harus lebih besar dari Start Time.');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'keterangan' => 'Keterangan',
            'created_at' => 'Created At',
            'user_created' => 'User Created',
            'updated_at' => 'Updated At',
            'user_updated' => 'User Updated',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSaldoKasirs()
    {
        return $this->hasMany(SaldoKasir::className(), ['shift_id' => 'id']);
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
