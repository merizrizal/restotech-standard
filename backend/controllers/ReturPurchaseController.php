<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\ReturPurchase;
use restotech\standard\backend\models\search\ReturPurchaseSearch;
use restotech\standard\backend\models\ReturPurchaseTrx;
use restotech\standard\backend\models\Item;
use restotech\standard\backend\models\ItemSku;
use restotech\standard\backend\models\Stock;
use restotech\standard\backend\models\StockMovement;
use restotech\standard\backend\models\Settings;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\data\ActiveDataProvider;


/**
 * ReturPurchaseController implements the CRUD actions for ReturPurchase model.
 */
class ReturPurchaseController extends BackendController
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
     * Finds the ReturPurchase model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return ReturPurchase the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $detail = false)
    {
        $model = null;
        
        if (!$detail) {
            
            $model = ReturPurchase::findOne($id);
        } else {
            
            $model = ReturPurchase::find()
                    ->joinWith([
                        'returPurchaseTrxes',
                        'returPurchaseTrxes.item',
                        'returPurchaseTrxes.itemSku',
                        'returPurchaseTrxes.storage',
                        'returPurchaseTrxes.storageRack',
                    ])
                    ->andWhere(['retur_purchase.id' => $id])
                    ->one();
        }
        
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
