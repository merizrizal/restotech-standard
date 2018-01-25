<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\TransactionAccount;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


/**
 * TransactionAccountController implements the CRUD actions for TransactionAccount model.
 */
class TransactionAccountController extends BackendController
{
    public function behaviors()
    {
        return array_merge(
            $this->getAccess(),
            [                
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        
                    ],
                ],
            ]);
    }

    /**
     * Finds the TransactionAccount model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return TransactionAccount the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TransactionAccount::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
