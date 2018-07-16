<?php

namespace restotech\standard\backend\models;

use Yii;

/**
 * This is the model class for table "menu_category".
 *
 * @property string $id
 * @property string $nama_category
 * @property string $parent_category_id
 * @property string $color
 * @property string $keterangan
 * @property integer $is_antrian
 * @property integer $not_active
 * @property integer $not_discount
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property Menu[] $menus
 * @property MenuCategory $parentCategory
 * @property MenuCategory[] $menuCategories
 * @property User $userCreated
 * @property User $userUpdated
 * @property MenuCategoryPrinter[] $menuCategoryPrinters
 */
class MenuCategory extends \synctech\RtechBaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu_category';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'nama_category'], 'required'],
            [['keterangan'], 'string'],
            [['is_antrian', 'not_active', 'not_discount'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['id', 'parent_category_id', 'user_created', 'user_updated'], 'string', 'max' => 32],
            [['nama_category'], 'string', 'max' => 128],
            [['color'], 'string', 'max' => 7],
            [['id'], 'unique'],
            [['parent_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => MenuCategory::className(), 'targetAttribute' => ['parent_category_id' => 'id']],
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
            'nama_category' => 'Nama Category',
            'parent_category_id' => 'Parent Category ID',
            'color' => 'Color',
            'keterangan' => 'Keterangan',
            'is_antrian' => 'Is Antrian',
            'not_active' => 'Non Aktif',
            'not_discount' => 'Not Discount',
            'created_at' => 'Created At',
            'user_created' => 'User Created',
            'updated_at' => 'Updated At',
            'user_updated' => 'User Updated',
            
            'parentCategory.nama_category' => 'Nama Parent Category'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenus()
    {
        return $this->hasMany(Menu::className(), ['menu_category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParentCategory()
    {
        return $this->hasOne(MenuCategory::className(), ['id' => 'parent_category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuCategories()
    {
        return $this->hasMany(MenuCategory::className(), ['parent_category_id' => 'id']);
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
    public function getMenuCategoryPrinters()
    {
        return $this->hasMany(MenuCategoryPrinter::className(), ['menu_category_id' => 'id']);
    }
}
