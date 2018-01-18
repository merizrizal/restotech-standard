<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "menu".
 *
 * @property string $id
 * @property string $nama_menu
 * @property string $menu_category_id
 * @property string $menu_satuan_id
 * @property string $keterangan
 * @property integer $not_active
 * @property string $harga_pokok
 * @property string $harga_jual
 * @property string $image
 * @property integer $is_deleted
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property User $userCreated
 * @property User $userUpdated
 * @property MenuCategory $menuCategory
 * @property MenuSatuan $menuSatuan
 * @property MenuCondiment[] $menuCondiments
 * @property MenuCondiment[] $menuCondiments0
 * @property MenuHpp[] $menuHpps
 * @property MenuRecipe[] $menuRecipes
 * @property MtableOrder[] $mtableOrders
 * @property MtableOrderQueue[] $mtableOrderQueues
 * @property SaleInvoiceRetur[] $saleInvoiceReturs
 * @property SaleInvoiceTrx[] $saleInvoiceTrxes
 * @property SaleInvoiceTrxCorrection[] $saleInvoiceTrxCorrections
 */
class Menu extends \sybase\SybaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'nama_menu', 'menu_category_id', 'menu_satuan_id'], 'required'],
            [['keterangan', 'image'], 'string'],
            [['not_active', 'is_deleted'], 'integer'],
            [['harga_pokok', 'harga_jual'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['id', 'menu_category_id', 'user_created', 'user_updated'], 'string', 'max' => 32],            
            [['nama_menu'], 'string', 'max' => 128],
            [['menu_satuan_id'], 'string', 'max' => 7],
            [['id'], 'unique'],
            [['user_created'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_created' => 'id']],
            [['user_updated'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_updated' => 'id']],
            [['menu_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => MenuCategory::className(), 'targetAttribute' => ['menu_category_id' => 'id']],
            [['menu_satuan_id'], 'exist', 'skipOnError' => true, 'targetClass' => MenuSatuan::className(), 'targetAttribute' => ['menu_satuan_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nama_menu' => 'Nama Menu',
            'menu_category_id' => 'Menu Category ID',
            'menu_satuan_id' => 'Menu Satuan ID',
            'keterangan' => 'Keterangan',
            'not_active' => 'Non Aktif',
            'harga_pokok' => 'Harga Pokok',
            'harga_jual' => 'Harga Jual',
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
    public function getMenuCategory()
    {
        return $this->hasOne(MenuCategory::className(), ['id' => 'menu_category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuSatuan()
    {
        return $this->hasOne(MenuSatuan::className(), ['id' => 'menu_satuan_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuCondiments()
    {
        return $this->hasMany(MenuCondiment::className(), ['parent_menu_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuCondiments0()
    {
        return $this->hasMany(MenuCondiment::className(), ['menu_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuHpps()
    {
        return $this->hasMany(MenuHpp::className(), ['menu_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuRecipes()
    {
        return $this->hasMany(MenuRecipe::className(), ['menu_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMtableOrders()
    {
        return $this->hasMany(MtableOrder::className(), ['menu_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMtableOrderQueues()
    {
        return $this->hasMany(MtableOrderQueue::className(), ['menu_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSaleInvoiceReturs()
    {
        return $this->hasMany(SaleInvoiceRetur::className(), ['menu_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSaleInvoiceTrxes()
    {
        return $this->hasMany(SaleInvoiceTrx::className(), ['menu_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSaleInvoiceTrxCorrections()
    {
        return $this->hasMany(SaleInvoiceTrxCorrection::className(), ['menu_id' => 'id']);
    }
}