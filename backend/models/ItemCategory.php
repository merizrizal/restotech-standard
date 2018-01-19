<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "item_category".
 *
 * @property string $id
 * @property string $nama_category
 * @property string $parent_category_id
 * @property string $keterangan
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property Item[] $items
 * @property Item[] $items0
 * @property User $userCreated
 * @property User $userUpdated
 * @property ItemCategory $parentCategory
 * @property ItemCategory[] $itemCategories
 */
class ItemCategory extends \sybase\SybaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'item_category';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'nama_category'], 'required'],
            [['keterangan'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['id', 'parent_category_id'], 'string', 'max' => 16],
            [['id'], 'unique'],
            [['nama_category', 'user_created', 'user_updated'], 'string', 'max' => 32],
            [['user_created'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_created' => 'id']],
            [['user_updated'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_updated' => 'id']],
            [['parent_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => ItemCategory::className(), 'targetAttribute' => ['parent_category_id' => 'id']]
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
            'keterangan' => 'Keterangan',
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
    public function getItems()
    {
        return $this->hasMany(Item::className(), ['item_category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems0()
    {
        return $this->hasMany(Item::className(), ['parent_item_category_id' => 'id']);
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
    public function getParentCategory()
    {
        return $this->hasOne(ItemCategory::className(), ['id' => 'parent_category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemCategories()
    {
        return $this->hasMany(ItemCategory::className(), ['parent_category_id' => 'id']);
    }
}