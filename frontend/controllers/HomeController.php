<?php

namespace restotech\standard\frontend\controllers;

use Yii;
use restotech\standard\backend\models\MtableSession;
use restotech\standard\backend\models\SaleInvoice;
use restotech\standard\backend\models\PaymentMethod;
use restotech\standard\backend\models\Settings;
use yii\filters\VerbFilter;

/**
 * Home controller
 */
class HomeController extends FrontendController {

    /**
     * @inheritdoc
     */
    public function behaviors() {

        return array_merge(
            $this->getAccess(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'load-menu' => ['post'],
                        'payment' =>  ['post'],
                        'reprint-invoice' =>  ['post'],
                        'reprint-invoice-submit' =>  ['post'],
                    ],
                ],
            ]);
    }

    public function actionIndex() {

        return $this->render('index', [

        ]);
    }

    public function actionLoadMenu() {

        $this->layout = 'ajax';

        return $this->render('_menu', [

        ]);
    }

    public function actionPayment($id, $isCorrection = false) {

        $this->layout = 'ajax';

        $modelMtableSession = MtableSession::find()
                ->joinWith([
                    'mtable',
                    'mtableOrders' => function($query) {
                        $query->andOnCondition(['mtable_order.parent_id' => null]);
                    },
                    'mtableOrders.menu',
                    'mtableOrders.menu.menuCategory',
                    'mtableOrders.menu.menuCategory.menuCategoryPrinters',
                    'mtableOrders.menu.menuCategory.menuCategoryPrinters.printer0',
                    'mtableOrders.mtableOrders' => function($query) {
                        $query->from('mtable_order a');
                    },
                    'mtableOrders.mtableOrderQueue',
                ])
                ->andWhere(['mtable_session.id' => $id])
                ->orderBy('mtable_order.id ASC')
                ->one();

         $modelPaymentMethod = PaymentMethod::find()
                ->andWhere(['type' => 'sale'])
                 ->andWhere(['not_active' => 0])
                ->asArray()->all();

        return $this->render('_payment', [
            'modelMtableSession' => $modelMtableSession,
            'modelPaymentMethod' => $modelPaymentMethod,
            'settingsArray' => Settings::getSettingsByName('struk_invoice_', true),
            'isCorrection' => $isCorrection,
        ]);
    }

    public function actionReprintInvoice() {

        $this->layout = 'ajax';

        return $this->render('_input_invoice', [
            'type' => 'reprint',
        ]);
    }

    public function actionReprintInvoiceSubmit() {

        $this->layout = 'ajax';

        $post = Yii::$app->request->post();

        $modelSaleInvoice = SaleInvoice::find()
                ->joinWith([
                    'mtableSession',
                    'mtableSession.mtable',
                    'mtableSession.mtableOrders',
                    'mtableSession.mtableOrders.menu',
                    'saleInvoicePayments',
                    'saleInvoicePayments.paymentMethod',
                ])
                ->andWhere(['sale_invoice.id' => $post['id']])->one();

        if (empty($modelSaleInvoice)) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }

        return $this->render('_reprint_invoice_submit', [
            'modelSaleInvoice' => $modelSaleInvoice,
            'modelMtableSession' => $modelSaleInvoice->mtableSession,
            'settingsArray' => Settings::getSettingsByName('struk_invoice_', true),
        ]);
    }
}