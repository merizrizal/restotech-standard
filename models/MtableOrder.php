<?php

namespace restotech\standard\backend\models;

use Yii;

/**
 * This is the model class for table "mtable_order".
 *
 * @property string $id
 * @property string $parent_id
 * @property string $mtable_session_id
 * @property string $menu_id
 * @property string $catatan
 * @property string $discount_type
 * @property string $discount
 * @property string $harga_satuan
 * @property double $jumlah
 * @property integer $is_free_menu
 * @property string $free_menu_at
 * @property string $user_free_menu
 * @property integer $is_void
 * @property string $void_at
 * @property string $user_void
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property MtableOrder $parent
 * @property MtableOrder[] $mtableOrders
 * @property MtableSession $mtableSession
 * @property Menu $menu
 * @property User $userFreeMenu
 * @property User $userVoid
 * @property User $userCreated
 * @property User $userUpdated
 * @property MtableOrderQueue $mtableOrderQueue
 */
class MtableOrder extends \sybase\SybaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mtable_order';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'mtable_session_id', 'is_free_menu', 'is_void'], 'integer'],
            [['mtable_session_id', 'menu_id'], 'required'],
            [['catatan', 'discount_type'], 'string'],
            [['discount', 'harga_satuan', 'jumlah'], 'number'],
            [['free_menu_at', 'void_at', 'created_at', 'updated_at'], 'safe'],
            [['menu_id', 'user_free_menu', 'user_void', 'user_created', 'user_updated'], 'string', 'max' => 32],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => MtableOrder::className(), 'targetAttribute' => ['parent_id' => 'id']],
            [['mtable_session_id'], 'exist', 'skipOnError' => true, 'targetClass' => MtableSession::className(), 'targetAttribute' => ['mtable_session_id' => 'id']],
            [['menu_id'], 'exist', 'skipOnError' => true, 'targetClass' => Menu::className(), 'targetAttribute' => ['menu_id' => 'id']],
            [['user_free_menu'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_free_menu' => 'id']],
            [['user_void'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_void' => 'id']],
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
            'parent_id' => 'Parent ID',
            'mtable_session_id' => 'Mtable Session ID',
            'menu_id' => 'Menu ID',
            'catatan' => 'Catatan',
            'discount_type' => 'Discount Type',
            'discount' => 'Discount',
            'harga_satuan' => 'Harga Satuan',
            'jumlah' => 'Jumlah',
            'is_free_menu' => 'Is Free Menu',
            'free_menu_at' => 'Free Menu At',
            'user_free_menu' => 'User Free Menu',
            'is_void' => 'Is Void',
            'void_at' => 'Void At',
            'user_void' => 'User Void',
            'created_at' => 'Created At',
            'user_created' => 'User Created',
            'updated_at' => 'Updated At',
            'user_updated' => 'User Updated',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(MtableOrder::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMtableOrders()
    {
        return $this->hasMany(MtableOrder::className(), ['parent_id' => 'id']);
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
    public function getMenu()
    {
        return $this->hasOne(Menu::className(), ['id' => 'menu_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserFreeMenu()
    {
        return $this->hasOne(User::className(), ['id' => 'user_free_menu']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserVoid()
    {
        return $this->hasOne(User::className(), ['id' => 'user_void']);
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
    public function getMtableOrderQueue()
    {
        return $this->hasOne(MtableOrderQueue::className(), ['mtable_order_id' => 'id']);
    }
}
