<?php

namespace restotech\standard\api\controllers\frontend;

use Yii;
use restotech\standard\backend\models\Mtable;
use restotech\standard\backend\models\MtableSession;
use restotech\standard\backend\models\SaleInvoice;
use restotech\standard\backend\models\PaymentMethod;
use restotech\standard\backend\models\Settings;
use yii\filters\VerbFilter;

/**
 * Home controller
 */
class HomeController extends \yii\rest\Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {

        return array_merge(
            [],
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'transaction' =>  ['post'],
                        'open-table' =>  ['post'],
                        'payment' =>  ['post'],
                        'reprint-invoice' =>  ['post'],
                        'reprint-invoice-submit' =>  ['post'],
                    ],
                ],
            ]);
    }

    public function actionTransaction() {

        $model = Mtable::find()
                ->joinWith(['mtableCategory'])
                ->andWhere(['mtable.id' => '9999'])
                ->andWhere(['mtable_category.id' => 9999])
                ->asArray()->all();

        if (!empty($model)) {

            return $this->runAction('open-table', ['id' => '9999', 'cid' => 9999, 'sessId' => null]);
        } else {

            throw new \yii\web\ForbiddenHttpException('Silakan lakukan inisialisasi data dahulu');
        }
    }

    public function actionOpenTable($id, $cid, $sessId = null, $isCorrection = false) {

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
                $modelMtableSession->user_opened = null; //Get token dari android, lalu di get identity by token

                if ($modelMtableSession->save()) {

                    $transaction->commit();
                } else {

                    return $this->render('@restotech/standard/frontend/views/home/_error', [
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
                ->asArray()->one();

        return [
            'modelMtableSession' => $modelMtableSession,
            'settingsArray' => Settings::getSettingsByName('struk_', true),
            'isCorrection' => $isCorrection,
        ];
    }

    public function actionPayment($id, $isCorrection = false) {

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
                ->asArray()->one();

         $modelPaymentMethod = PaymentMethod::find()
                ->andWhere(['type' => 'sale'])
                ->andWhere(['not_active' => 0])
                ->asArray()->all();

        return [
            'modelMtableSession' => $modelMtableSession,
            'modelPaymentMethod' => $modelPaymentMethod,
            'settingsArray' => Settings::getSettingsByName('struk_invoice_', true),
            'isCorrection' => $isCorrection,
        ];
    }

    public function actionReprintInvoice() {

        return [

        ];
    }

    public function actionReprintInvoiceSubmit() {

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
                ->andWhere(['sale_invoice.id' => $post['id']])
                ->asArray()->one();

        if (empty($modelSaleInvoice)) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }

        return [
            'modelSaleInvoice' => $modelSaleInvoice,
            'settingsArray' => Settings::getSettingsByName('struk_invoice_', true),
        ];
    }
}