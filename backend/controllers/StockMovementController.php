<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\StockMovement;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


/**
 * StockMovementController implements the CRUD actions for StockMovement model.
 */
class StockMovementController extends BackendController
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
     * Finds the StockMovement model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return StockMovement the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StockMovement::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
