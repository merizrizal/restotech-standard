<?php

namespace restotech\standard\backend\models;

use Yii;

/**
 * This is the model class for table "menu_condiment".
 *
 * @property string $id
 * @property string $parent_menu_id
 * @property string $menu_id
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property Menu $parentMenu
 * @property Menu $menu
 * @property User $userCreated
 * @property User $userUpdated
 */
class MenuCondiment extends \sybase\SybaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu_condiment';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_menu_id', 'menu_id'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['parent_menu_id', 'menu_id'], 'string', 'max' => 50],
            [['user_created', 'user_updated'], 'string', 'max' => 32],
            [['parent_menu_id'], 'exist', 'skipOnError' => true, 'targetClass' => Menu::className(), 'targetAttribute' => ['parent_menu_id' => 'id']],
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
            'parent_menu_id' => 'Parent Menu ID',
            'menu_id' => 'Menu ID',
            'created_at' => 'Created At',
            'user_created' => 'User Created',
            'updated_at' => 'Updated At',
            'user_updated' => 'User Updated',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParentMenu()
    {
        return $this->hasOne(Menu::className(), ['id' => 'parent_menu_id']);
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
