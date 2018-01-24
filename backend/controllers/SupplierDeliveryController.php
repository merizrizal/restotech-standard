<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\SupplierDelivery;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SupplierDeliveryController implements the CRUD actions for SupplierDelivery model.
 */
class SupplierDeliveryController extends BackendController
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
     * Finds the SupplierDelivery model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return SupplierDelivery the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $detail = false)
    {
        $model = null;

        if (!$detail) {

            $model = SupplierDelivery::findOne($id);
        } else {

            $model = SupplierDelivery::find()
                    ->joinWith([
                        'supplierDeliveryTrxes',
                        'supplierDeliveryTrxes.item',
                        'supplierDeliveryTrxes.itemSku',
                        'supplierDeliveryTrxes.storage',
                        'supplierDeliveryTrxes.storageRack',
                    ])
                    ->andWhere(['supplier_delivery.id' => $id])
                    ->one();
        }

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
