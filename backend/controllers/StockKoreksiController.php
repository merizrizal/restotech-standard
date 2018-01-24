<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\StockKoreksi;
use restotech\standard\backend\models\search\StockKoreksiSearch;
use restotech\standard\backend\models\Stock;
use restotech\standard\backend\models\StockMovement;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;


/**
 * StockKoreksiController implements the CRUD actions for StockKoreksi model.
 */
class StockKoreksiController extends BackendController
{
    public function behaviors()
    {
        return array_merge(
            $this->getAccess(),
            [                
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['post'],
                    ],
                ],
            ]);
    }

    /**
     * Finds the StockKoreksi model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return StockKoreksi the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StockKoreksi::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
