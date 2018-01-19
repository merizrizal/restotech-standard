<?php

namespace restotech\standard\backend\controllers;

use Yii;

class BackendController extends \sybase\SybaseController
{
    
    public function beforeAction($action) {
        
        if (parent::beforeAction($action)) {
            
            $this->getView()->params['assetCommon'] = \common\assets\AppAsset::register($this->getView());
            
            if (Yii::$app->session->get('company_settings_profile') === null) {
                
                $settings = \restotech\standard\backend\models\Settings::find()->andWhere('setting_name LIKE "company%"')->all();
                foreach ($settings as $value) {
                    $data[$value->setting_name] = $value->setting_value;
                }

                Yii::$app->session->set('company_settings_profile', $data);
            }
            
            return true;
        } else {
            return false;
        }
    }
}