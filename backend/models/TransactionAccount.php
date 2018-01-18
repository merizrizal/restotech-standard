<?php

namespace restotech\standard\backend\models;

use Yii;

/**
 * This is the model class for table "transaction_account".
 *
 * @property string $id
 * @property string $nama_account
 * @property string $account_type
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property User $userCreated
 * @property User $userUpdated
 * @property TransactionCash[] $transactionCashes
 */
class TransactionAccount extends \sybase\SybaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'transaction_account';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'nama_account', 'account_type'], 'required'],
            [['account_type'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['id'], 'string', 'max' => 16],
            [['nama_account', 'user_created', 'user_updated'], 'string', 'max' => 32],
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
            'nama_account' => 'Nama Account',
            'account_type' => 'Account Type',
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
    public function getTransactionCashes()
    {
        return $this->hasMany(TransactionCash::className(), ['account_id' => 'id']);
    }
}
