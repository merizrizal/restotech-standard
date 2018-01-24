<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\SupplierDeliveryInvoice;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SupplierDeliveryInvoiceController implements the CRUD actions for SupplierDeliveryInvoice model.
 */
class SupplierDeliveryInvoiceController extends BackendController
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
     * Finds the SupplierDeliveryInvoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return SupplierDeliveryInvoice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SupplierDeliveryInvoice::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
