<?php

namespace frontend\controllers\base;

use Yii;

use backend\models\Settings;
use backend\models\TransactionDay;

class PosBaseController extends \sybase\SybaseController {

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {            
        
        if (parent::beforeAction($action)) {                         
            
            $modelTransactionDay = TransactionDay::find()
                    ->andWhere(['IS', 'end', null])
                    ->andWhere(['IS NOT', 'start', null])
                    ->asArray()->one();                        
            
            $settingTransactionDay = [];
            
            $modelSettings = Settings::find()
                    ->orFilterWhere(['like', 'setting_name', 'transaction_day_'])
                    ->asArray()->all();                

            foreach ($modelSettings as $value) {
                $settingTransactionDay[$value['setting_name']] = $value['setting_value'];
            }
            
            if (!empty($modelTransactionDay)) {
                
                Yii::$app->formatter->timeZone = 'Asia/Jakarta';
            
                $timeEnd = strtotime(explode(' ', $modelTransactionDay['start'])[0] . ' ' . $settingTransactionDay['transaction_day_end']);
                $timeNow = strtotime(Yii::$app->formatter->asDatetime(time(), 'yyyy-MM-dd HH:mm:ss'));
                
                if ($timeNow > $timeEnd) {
                    $this->getView()->params['statusTransactionDay'] = 'over';
                }
                
                Yii::$app->formatter->timeZone = 'UTC';
                $this->getView()->params['transactionDay'] = Yii::$app->formatter->asDatetime($modelTransactionDay['start'], 'd LLLL yyyy HH:mm');
            } else {
                $this->getView()->params['statusTransactionDay'] = 'empty';
            }
    
            $this->getView()->params['isOverTransactionDay'] = $settingTransactionDay['transaction_day_is_over_24'];
            
            return true;  
        } else {
            return false;
        }
    }

}