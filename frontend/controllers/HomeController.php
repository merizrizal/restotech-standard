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
                        'transaction' =>  ['post'],
                        'open-table' =>  ['post'],
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
    
    public function actionTransaction() {
        
        return $this->runAction('open-table', ['id' => '9999', 'cid' => 9999, 'sessId' => null]);
    }
    
    public function actionOpenTable($id, $cid, $sessId = null, $isCorrection = false) {

        $this->layout = 'ajax';

        $modelSettings = Settings::getSettingsByName(['tax_amount', 'service_charge_amount']);

        $modelMtableSession = null;

        if (empty($sessId)) {

            $transaction = Yii::$app->db->beginTransaction();

            $modelMtableSession = MtableSession::find()
                    ->andWhere([
                        'mtable_id' => $id,
                        'is_closed' => 0
                    ])->asArray()->one();

            if (empty($modelMtableSession)) {

                $modelMtableSession = new MtableSession();
                $modelMtableSession->mtable_id = $id;
                $modelMtableSession->nama_tamu = '';
                $modelMtableSession->catatan = '';
                $modelMtableSession->pajak = $modelMtableSession->mtable->not_ppn ? 0 :$modelSettings['tax_amount'] ;
                $modelMtableSession->service_charge = $modelMtableSession->mtable->not_service_charge ? 0 : $modelSettings['service_charge_amount'];
                $modelMtableSession->opened_at = Yii::$app->formatter->asDatetime(time());
                $modelMtableSession->user_opened = Yii::$app->user->identity->id;

                if ($modelMtableSession->save()) {

                    $transaction->commit();
                } else {                    

                    return $this->render('_error', [
                        'tableCategoryId' => $cid,
                        'title' => 'Error open table',
                        'message' => 'Telah terjadi kesalahan saat proses open table.'
                    ]);

                    $transaction->rollBack();
                }
            } else {
                $sessId = $modelMtableSession['id'];

                $transaction->rollBack();
            }
        }

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
                ->andWhere(['mtable_session.id' => !empty($sessId) ? $sessId : $modelMtableSession->id])
                ->orderBy('mtable_order.id ASC')
                ->one();

        return $this->render('_open_table', [
            'modelMtableSession' => $modelMtableSession,
            'settingsArray' => Settings::getSettingsByName('struk_', true),
            'isCorrection' => $isCorrection,
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
            'version' => 'standard',
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