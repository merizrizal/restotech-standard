<?php

namespace restotech\standard\backend\models;

use Yii;

/**
 * This is the model class for table "settings".
 *
 * @property string $setting_id
 * @property string $setting_name
 * @property string $setting_value
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property User $userCreated
 * @property User $userUpdated
 */
class Settings extends \synctech\SynctBaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'settings';
    }        
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['setting_name'], 'required'],
            [['setting_value'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['setting_name'], 'string', 'max' => 96],
            [['type'], 'string', 'max' => 16],
            [['user_created', 'user_updated'], 'string', 'max' => 32],
            [['setting_name'], 'unique'],
            [['setting_name'], 'restotech\standard\backend\components\String2Validator'],
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
            'setting_id' => 'Setting ID',
            'setting_name' => 'Setting Name',
            'setting_value' => 'Setting Value',
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
    
    public static function getTransNumber($trans, $dateFormat = 'ym', $character = null) {
        $models = Settings::find()->where(['setting_name' => $trans])->orWhere(['setting_name' => $trans . '_format'])->all();
        
        $number = '';
        $format = '';
        foreach ($models as $model) {
            if (stripos($model->setting_name, '_format') !== false)
                $format = explode(':', $model->setting_value);
            else
                $number = $model;            
        }       
        
        $index = '';
        $zero = '';        
        for ($i = 1; $i <= $format[1]; $i++) {
            $zero .= '0';
        }
        
        $index = substr($zero, 0, ($format[1] - strlen($number->setting_value))) . $number->setting_value;
        
        $noTrans = $format[0];
        $noTrans = str_replace('{date}', date($dateFormat), $noTrans);
        $noTrans = str_replace('{inc}', $index, $noTrans);
        
        if (!empty($character)) {
            $noTrans = str_replace('{AA}', strtoupper(substr($character, 0, 1)), $noTrans);
        }
        
        $number->setting_value = (string)($number->setting_value + 1);
        if ($number->save()) {                    
            return $noTrans;
        } else {                        
            return false;
        }                
    }
    
    public static function getSettings($params) {
        $settingName = '';
        foreach ($params as $value) {
            $settingName .= '"' . $value . '",';
        }
        $settingName = trim($settingName, ',');
        
        $query = Settings::find()
                ->andWhere('setting_name IN (' . $settingName . ')')
                ->asArray()->all();
        
        return $query;
    }
    
    public static function getSettingsByName($params, $isLike = false) {        
        
        $query = Settings::find();
        
        if ($isLike) {
            $query = $query->andWhere(['LIKE', 'setting_name', $params]);
        } else {
            $query = $query->andWhere(['setting_name' => $params]);
        }
                
        $query = $query->asArray()->all();
        
        $modelSettings = [];
        foreach ($query as $value) {
            $modelSettings[$value['setting_name']] = $value['setting_value'];
        }
        
        return $modelSettings;
    }
}
