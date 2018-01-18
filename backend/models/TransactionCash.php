<?php

namespace restotech\standard\backend\models;

use Yii;

/**
 * This is the model class for table "transaction_cash".
 *
 * @property string $id
 * @property string $account_id
 * @property string $date
 * @property string $jumlah
 * @property string $reference_id
 * @property string $keterangan
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property TransactionAccount $account
 * @property User $userCreated
 * @property User $userUpdated
 */
class TransactionCash extends \sybase\SybaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'transaction_cash';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_id', 'date'], 'required'],
            [['date', 'created_at', 'updated_at'], 'safe'],
            [['jumlah'], 'number'],
            [['keterangan'], 'string'],
            [['account_id'], 'string', 'max' => 16],
            [['reference_id', 'user_created', 'user_updated'], 'string', 'max' => 32],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => TransactionAccount::className(), 'targetAttribute' => ['account_id' => 'id']],
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
            'account_id' => 'Account ID',
            'date' => 'Tanggal',
            'jumlah' => 'Jumlah',
            'reference_id' => 'Reference ID',
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
    public function getAccount()
    {
        return $this->hasOne(TransactionAccount::className(), ['id' => 'account_id']);
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
