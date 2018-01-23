<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\Mtable;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


/**
 * MtableController implements the CRUD actions for Mtable model.
 */
class MtableController extends BackendController
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
     * Finds the Mtable model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Mtable the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Mtable::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
