<?php

namespace restotech\standard\backend\models;

use Yii;

/**
 * This is the model class for table "menu_hpp".
 *
 * @property string $id
 * @property string $date
 * @property string $menu_id
 * @property string $harga_pokok
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property Menu $menu
 * @property User $userCreated
 * @property User $userUpdated
 */
class MenuHpp extends \synctech\SynctBaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu_hpp';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'date', 'menu_id', 'harga_pokok'], 'required'],
            [['date', 'created_at', 'updated_at'], 'safe'],
            [['harga_pokok'], 'number'],
            [['id', 'menu_id', 'user_created', 'user_updated'], 'string', 'max' => 32],
            [['menu_id'], 'exist', 'skipOnError' => true, 'targetClass' => Menu::className(), 'targetAttribute' => ['menu_id' => 'id']],
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
            'date' => 'Date',
            'menu_id' => 'Menu ID',
            'harga_pokok' => 'Harga Pokok',
            'created_at' => 'Created At',
            'user_created' => 'User Created',
            'updated_at' => 'Updated At',
            'user_updated' => 'User Updated',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenu()
    {
        return $this->hasOne(Menu::className(), ['id' => 'menu_id']);
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
