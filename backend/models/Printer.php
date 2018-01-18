<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "printer".
 *
 * @property string $printer
 * @property string $type
 * @property integer $is_autocut
 * @property integer $not_active
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property MenuCategoryPrinter[] $menuCategoryPrinters
 * @property User $userCreated
 * @property User $userUpdated
 */
class Printer extends \sybase\SybaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'printer';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['printer', 'type'], 'required'],
            [['type'], 'string'],
            [['is_autocut', 'not_active'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['printer'], 'string', 'max' => 128],
            [['user_created', 'user_updated'], 'string', 'max' => 32],
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
            'printer' => 'Printer',
            'type' => 'Type',
            'is_autocut' => 'Is Autocut',
            'not_active' => 'Non Aktif',
            'created_at' => 'Created At',
            'user_created' => 'User Created',
            'updated_at' => 'Updated At',
            'user_updated' => 'User Updated',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuCategoryPrinters()
    {
        return $this->hasMany(MenuCategoryPrinter::className(), ['printer' => 'printer']);
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
