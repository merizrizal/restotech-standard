<?php

namespace restotech\standard\backend\models;

use Yii;

/**
 * This is the model class for table "mtable_session_join".
 *
 * @property string $id
 * @property string $mtable_session_id
 * @property string $mtable_join_id
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property MtableSession $mtableSession
 * @property MtableJoin $mtableJoin
 * @property User $userCreated
 * @property User $userUpdated
 */
class MtableSessionJoin extends \synctech\SynctBaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mtable_session_join';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mtable_session_id'], 'required'],
            [['mtable_session_id', 'mtable_join_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_created', 'user_updated'], 'string', 'max' => 32],
            [['mtable_session_id'], 'unique'],
            [['mtable_session_id'], 'exist', 'skipOnError' => true, 'targetClass' => MtableSession::className(), 'targetAttribute' => ['mtable_session_id' => 'id']],
            [['mtable_join_id'], 'exist', 'skipOnError' => true, 'targetClass' => MtableJoin::className(), 'targetAttribute' => ['mtable_join_id' => 'id']],
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
            'mtable_session_id' => 'Mtable Session ID',
            'mtable_join_id' => 'Mtable Join ID',
            'created_at' => 'Created At',
            'user_created' => 'User Created',
            'updated_at' => 'Updated At',
            'user_updated' => 'User Updated',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMtableSession()
    {
        return $this->hasOne(MtableSession::className(), ['id' => 'mtable_session_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMtableJoin()
    {
        return $this->hasOne(MtableJoin::className(), ['id' => 'mtable_join_id']);
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
