<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\SupplierDeliveryInvoicePayment;
use restotech\standard\backend\models\SupplierDeliveryInvoice;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\data\ActiveDataProvider;
use kartik\mpdf\Pdf;

/**
 * SupplierDeliveryInvoicePaymentController implements the CRUD actions for SupplierDeliveryInvoicePayment model.
 */
class SupplierDeliveryInvoicePaymentController extends BackendController
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
     * Finds the SupplierDeliveryInvoicePayment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return SupplierDeliveryInvoicePayment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SupplierDeliveryInvoicePayment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
