<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "mtable_order_queue".
 *
 * @property string $id
 * @property string $mtable_order_id
 * @property string $menu_id
 * @property double $jumlah
 * @property string $keterangan
 * @property integer $is_finish
 * @property integer $is_send
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property Menu $menu
 * @property User $userCreated
 * @property User $userUpdated
 * @property MtableOrder $mtableOrder
 */
class MtableOrderQueue extends \sybase\SybaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mtable_order_queue';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mtable_order_id', 'menu_id', 'jumlah'], 'required'],
            [['mtable_order_id', 'is_finish', 'is_send'], 'integer'],
            [['jumlah'], 'number'],
            [['keterangan'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['menu_id', 'user_created', 'user_updated'], 'string', 'max' => 32],
            [['mtable_order_id'], 'unique'],
            [['menu_id'], 'exist', 'skipOnError' => true, 'targetClass' => Menu::className(), 'targetAttribute' => ['menu_id' => 'id']],
            [['user_created'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_created' => 'id']],
            [['user_updated'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_updated' => 'id']],
            [['mtable_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => MtableOrder::className(), 'targetAttribute' => ['mtable_order_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mtable_order_id' => 'Mtable Order ID',
            'menu_id' => 'Menu ID',
            'jumlah' => 'Jumlah',
            'keterangan' => 'Keterangan',
            'is_finish' => 'Is Finish',
            'is_send' => 'Is Send',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMtableOrder()
    {
        return $this->hasOne(MtableOrder::className(), ['id' => 'mtable_order_id']);
    }
}
