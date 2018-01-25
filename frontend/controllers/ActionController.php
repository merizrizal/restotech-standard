<?php

namespace restotech\standard\frontend\controllers;

use Yii;
use restotech\standard\backend\models\MtableSession;
use restotech\standard\backend\models\MtableOrder;
use restotech\standard\backend\models\SaleInvoice;
use restotech\standard\backend\models\SaleInvoiceTrx;
use restotech\standard\backend\models\SaleInvoicePayment;
use restotech\standard\backend\models\MenuRecipe;
use restotech\standard\backend\models\Stock;
use restotech\standard\backend\models\StockMovement;
use restotech\standard\backend\models\Employee;
use restotech\standard\backend\models\Voucher;
use restotech\standard\backend\models\Settings;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * Action controller
 */
class ActionController extends FrontendController {

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
                        'save-order' => ['post'],
                        'info-tamu' => ['post'],
                        'catatan' => ['post'],
                        'free-menu' => ['post'],
                        'void-menu' => ['post'],
                        'discount-bill' => ['post'],
                        'discount-menu' => ['post'],
                        'close-table' => ['post'],
                        'print-bill' => ['post'],
                        'unlock-bill' => ['post'],
                        'change-qty' => ['post'],
                        'payment' => ['post'],
                    ],
                ],
            ]);
    }

    public function actionSaveOrder() {

        $post = Yii::$app->request->post();

        $transaction = Yii::$app->db->beginTransaction();
        $flag = true;

        if (($flag = !empty(($modelMtableSession = MtableSession::findOne($post['sess_id']))))) {

            $modelMtableSession->jumlah_harga = $post['jumlah_harga'];

            if (($flag = $modelMtableSession->save())) {

                $modelMtableOrder = new MtableOrder();
                $modelMtableOrder->parent_id = empty($post['parent_id']) ? null : $post['parent_id'];
                $modelMtableOrder->mtable_session_id = $modelMtableSession->id;
                $modelMtableOrder->menu_id = $post['menu_id'];
                $modelMtableOrder->harga_satuan = $post['harga_satuan'];
                $modelMtableOrder->jumlah = 1;

                $flag = $modelMtableOrder->save();
            }
        }

        $return = [];

        if ($flag) {

            $transaction->commit();

            $return['success'] = true;
            $return['order_id'] = $modelMtableOrder->id;
        } else {

            $transaction->rollBack();

            $return['success'] = false;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    public function actionInfoTamu() {

        $post = Yii::$app->request->post();

        $flag = true;

        if (($flag = !empty(($modelMtableSession = MtableSession::findOne($post['sess_id']))))) {

            $modelMtableSession->jumlah_tamu = $post['jumlah_tamu'];
            $modelMtableSession->nama_tamu = $post['nama_tamu'];
            $modelMtableSession->catatan = $post['catatan'];

            $flag = $modelMtableSession->save();
        }

        $return = [];

        if ($flag) {

            $return['success'] = true;
            $return['jumlah_tamu'] = $modelMtableSession->jumlah_tamu;
            $return['nama_tamu'] = $modelMtableSession->nama_tamu;
            $return['catatan'] = $modelMtableSession->catatan;
        } else {

            $return['success'] = false;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    public function actionCatatan() {

        $post = Yii::$app->request->post();

        $flag = true;

        if (($flag = !empty(($modelMtableOrder = MtableOrder::findOne($post['order_id']))))) {

            $modelMtableOrder->catatan = $post['catatan'];

            $flag = $modelMtableOrder->save();
        }

        $return = [];

        if ($flag) {

            $return['success'] = true;
            $return['catatan'] = $modelMtableOrder->catatan;
        } else {

            $return['success'] = false;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    public function actionFreeMenu() {

        $post = Yii::$app->request->post();

        $transaction = Yii::$app->db->beginTransaction();
        $flag = true;

        if (($flag = !empty(($modelMtableSession = MtableSession::findOne($post['sess_id']))))) {

            $modelMtableSession->jumlah_harga = $post['jumlah_harga'];

            if (($flag = $modelMtableSession->save())) {

                $flag = MtableOrder::updateAll(
                        [
                            'is_free_menu' => 1,
                            'free_menu_at' => Yii::$app->formatter->asDatetime(time()),
                            'user_free_menu' => Yii::$app->user->identity->id,
                            'discount_type' => 'Percent',
                            'discount' => 0
                        ],
                        [
                            'id' => $post['order_id']
                        ]) > 0;
            }
        }

        $return = [];

        if ($flag) {

            $transaction->commit();

            $return['success'] = true;
        } else {

            $transaction->rollBack();

            $return['success'] = false;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    public function actionVoidMenu() {

        $post = Yii::$app->request->post();

        $transaction = Yii::$app->db->beginTransaction();
        $flag = true;

        if (($flag = !empty(($modelMtableSession = MtableSession::findOne($post['sess_id']))))) {

            $modelMtableSession->jumlah_harga = $post['jumlah_harga'];

            if (($flag = $modelMtableSession->save())) {

                $flag = MtableOrder::updateAll(
                        [
                            'is_void' => 1,
                            'void_at' => Yii::$app->formatter->asDatetime(time()),
                            'user_void' => Yii::$app->user->identity->id,
                            'discount_type' => 'Percent',
                            'discount' => 0
                        ],
                        [
                            'id' => $post['order_id']
                        ]) > 0;
            }
        }

        $return = [];

        if ($flag) {

            $transaction->commit();

            $return['success'] = true;
        } else {

            $transaction->rollBack();

            $return['success'] = false;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    public function actionDiscountBill() {

        $post = Yii::$app->request->post();

        $flag = true;

        if (($flag = !empty(($modelMtableSession = MtableSession::findOne($post['sess_id']))))) {

            $modelMtableSession->discount_type = $post['discount_type'];
            $modelMtableSession->discount = $post['discount'];

            $flag = $modelMtableSession->save();
        }

        $return = [];

        if ($flag) {

            $return['success'] = true;
            $return['discount_type'] = $modelMtableSession->discount_type;
            $return['discount'] = $modelMtableSession->discount;
        } else {

            $return['success'] = false;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    public function actionDiscountMenu() {

        $post = Yii::$app->request->post();

        $transaction = Yii::$app->db->beginTransaction();
        $flag = true;

        if (($flag = !empty(($modelMtableSession = MtableSession::findOne($post['sess_id']))))) {

            $modelMtableSession->jumlah_harga = $post['jumlah_harga'];

            if (($flag = $modelMtableSession->save())) {

                $flag = MtableOrder::updateAll(['discount_type' => $post['discount_type'], 'discount' => $post['discount']], ['id' => $post['order_id']]) > 0;
            }
        }

        $return = [];

        if ($flag) {

            $transaction->commit();

            $return['success'] = true;
            $return['jumlah_harga'] = $modelMtableSession->jumlah_harga;
            $return['discount_type'] = $post['discount_type'];
            $return['discount'] = $post['discount'];
        } else {

            $transaction->rollBack();

            $return['success'] = false;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    public function actionCloseTable() {

        $post = Yii::$app->request->post();

        $transaction = Yii::$app->db->beginTransaction();
        $flag = true;

        if (($flag = !empty(($modelMtableSession = MtableSession::findOne($post['sess_id']))))) {

            if (!$modelMtableSession->is_join_mtable) {

                $modelMtableSession->is_closed = 1;
                $modelMtableSession->closed_at = Yii::$app->formatter->asDatetime(time());
                $modelMtableSession->user_closed = Yii::$app->user->identity->id;
                $modelMtableSession->catatan = $post['catatan'];

                $flag = $modelMtableSession->save();
            } else {

                $modelMtableSessionJoins = $modelMtableSession->mtableSessionJoin->mtableJoin->mtableSessionJoins;

                foreach ($modelMtableSessionJoins as $modelMtableSessionJoin) {

                    $modelMtableSession = $modelMtableSessionJoin->mtableSession;

                    $modelMtableSession->is_closed = 1;
                    $modelMtableSession->closed_at = Yii::$app->formatter->asDatetime(time());
                    $modelMtableSession->user_closed = Yii::$app->user->identity->id;
                    $modelMtableSession->catatan = $post['catatan'];

                    if (!($flag = $modelMtableSession->save())) {
                        break;
                    }
                }
            }
        }

        $return = [];

        if ($flag) {

            $transaction->commit();

            $return['success'] = true;
        } else {

            $transaction->rollBack();

            $return['success'] = false;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    public function actionPrintBill() {

        $post = Yii::$app->request->post();

        $flag = true;

        if (($flag = !empty(($modelMtableSession = MtableSession::findOne($post['sess_id']))))) {

            $modelMtableSession->bill_printed = 1;

            $flag = $modelMtableSession->save();
        }

        $return = [];

        if ($flag) {

            $return['success'] = true;
        } else {

            $return['success'] = false;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    public function actionUnlockBill() {

        $post = Yii::$app->request->post();

        $flag = true;

        if (($flag = !empty(($modelMtableSession = MtableSession::findOne($post['sess_id']))))) {

            $modelMtableSession->bill_printed = 0;

            $flag = $modelMtableSession->save();
        }

        $return = [];

        if ($flag) {

            $return['success'] = true;
        } else {

            $return['success'] = false;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    public function actionChangeQty() {

        $post = Yii::$app->request->post();

        $transaction = Yii::$app->db->beginTransaction();
        $flag = true;

        $message = '';

        if (($flag = !empty(($modelMtableSession = MtableSession::findOne($post['sess_id']))))) {

            $modelMtableSession->jumlah_harga = $post['jumlah_harga'];

            if (($flag = $modelMtableSession->save())) {

                $order = [];

                foreach ($post['order_id'] as $i => $dataOrder) {

                    if ($dataOrder['jumlah'] > 0) {

                        $modelMtableOrder = MtableOrder::findOne($dataOrder['order_id']);

                        if (!$modelMtableOrder->is_free_menu) {

                            $modelMtableOrder->jumlah = $dataOrder['jumlah'];

                            $order[$i]['id'] = $modelMtableOrder->id;
                            $order[$i]['jumlah'] = $modelMtableOrder->jumlah;

                            $subtotal = $modelMtableOrder->harga_satuan * $modelMtableOrder->jumlah;
                            $disc = 0;

                            if ($modelMtableOrder->discount_type == 'Percent') {

                                $disc = $modelMtableOrder->discount * 0.01 * $subtotal;
                            } else if ($modelMtableOrder->discount_type == 'Value') {

                                $disc = $modelMtableOrder->jumlah * $modelMtableOrder->discount;
                            }

                            $order[$i]['jumlah_harga'] = $subtotal - $disc;

                            if (!($flag = $modelMtableOrder->save())) {
                                break;
                            }
                        } else {

                            $message .= ' Apakah terdapat free menu atau tidak.';
                            $flag = false;
                            break;
                        }
                    } else {
                        $message .= ' Order yang berjumlah 1 tidak bisa dikurangi.';
                        $flag = false;
                        break;
                    }
                }
            }
        }

        $return = [];

        if ($flag) {

            $transaction->commit();

            $return['jumlah_harga'] = $modelMtableSession->jumlah_harga;
            $return['order'] = $order;
            $return['success'] = true;
        } else {

            $transaction->rollBack();

            $return['message'] = $message;
            $return['success'] = false;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    public function actionPayment() {

        $post = Yii::$app->request->post();

        $transaction = Yii::$app->db->beginTransaction();
        $flag = true;

        $return = [];

        if (($flag = !empty(($modelMtableSession = MtableSession::findOne($post['sess_id']))))) {

            if ($modelMtableSession->is_join_mtable) {

                foreach ($modelMtableSession->mtableSessionJoin->mtableJoin->mtableSessionJoins as $mtableSessionJoin) {

                    $mtableSession = $mtableSessionJoin->mtableSession;

                    $mtableSession->is_closed = 1;
                    $mtableSession->closed_at = Yii::$app->formatter->asDatetime(time());
                    $mtableSession->user_closed = Yii::$app->user->identity->id;
                    $mtableSession->bill_printed = 1;
                    $mtableSession->is_paid = 1;

                    if (!($flag = $mtableSession->save())) {
                        break;
                    }
                }
            } else {
                $modelMtableSession->is_closed = 1;
                $modelMtableSession->closed_at = Yii::$app->formatter->asDatetime(time());
                $modelMtableSession->user_closed = Yii::$app->user->identity->id;
                $modelMtableSession->is_paid = 1;

                $flag = $modelMtableSession->save();
            }

            if ($flag) {

                $modelSaleInvoice = new SaleInvoice();

                if (($modelSaleInvoice->id = Settings::getTransNumber('no_inv')) !== false) {

                    $modelSaleInvoice->date = Yii::$app->formatter->asDatetime(time());
                    $modelSaleInvoice->mtable_session_id = $modelMtableSession->id;
                    $modelSaleInvoice->user_operator = Yii::$app->user->identity->id;
                    $modelSaleInvoice->jumlah_harga = $post['jumlah_harga'];
                    $modelSaleInvoice->discount_type = $post['discount_type'];
                    $modelSaleInvoice->discount = $post['discount'];
                    $modelSaleInvoice->pajak = $post['pajak'];
                    $modelSaleInvoice->service_charge = $post['service_charge'];
                    $modelSaleInvoice->jumlah_bayar = $post['jumlah_bayar'];
                    $modelSaleInvoice->jumlah_kembali = $post['jumlah_kembali'];

                    if (($flag = $modelSaleInvoice->save())) {

                        foreach ($post['order_id'] as $order) {

                            $modelSaleInvoiceTrx = new SaleInvoiceTrx();
                            $modelSaleInvoiceTrx->sale_invoice_id = $modelSaleInvoice->id;
                            $modelSaleInvoiceTrx->menu_id = $order['menu_id'];
                            $modelSaleInvoiceTrx->catatan = $order['catatan'];
                            $modelSaleInvoiceTrx->jumlah = $order['jumlah'];
                            $modelSaleInvoiceTrx->discount_type = $order['discount_type'];
                            $modelSaleInvoiceTrx->discount = $order['discount'];
                            $modelSaleInvoiceTrx->harga_satuan = $order['harga_satuan'];
                            $modelSaleInvoiceTrx->is_free_menu = $order['is_free_menu'];

                            if (($flag = $modelSaleInvoiceTrx->save())) {

                                $modelMenuRecipe = MenuRecipe::find()
                                        ->joinWith([
                                            'menu',
                                            'item',
                                            'itemSku',
                                        ])
                                        ->andWhere(['menu_recipe.menu_id' => $order['menu_id']])
                                        ->asArray()->all();


                                if (count($modelMenuRecipe) > 0) {

                                    foreach ($modelMenuRecipe as $dataMenuRecipe) {

                                        if (empty($dataMenuRecipe['itemSku']['storage_id'])) {

                                            $return['message'] = 'Item pada resep salah satu menu, belum disetting storagenya untuk pengurangan item.';
                                            $flag = false;
                                            break;
                                        }

                                        $flag = Stock::setStock(
                                                $dataMenuRecipe['item_id'],
                                                $dataMenuRecipe['item_sku_id'],
                                                $dataMenuRecipe['itemSku']['storage_id'],
                                                $dataMenuRecipe['itemSku']['storage_rack_id'],
                                                -1 * $dataMenuRecipe['jumlah'] * $order['jumlah']
                                        );

                                        if ($flag) {

                                            $modelStockMovement = new StockMovement();
                                            $modelStockMovement->type = 'Outflow-Menu';
                                            $modelStockMovement->item_id = $dataMenuRecipe['item_id'];
                                            $modelStockMovement->item_sku_id = $dataMenuRecipe['item_sku_id'];
                                            $modelStockMovement->storage_from = $dataMenuRecipe['itemSku']['storage_id'];
                                            $modelStockMovement->storage_rack_from = $dataMenuRecipe['itemSku']['storage_rack_id'];
                                            $modelStockMovement->jumlah = $dataMenuRecipe['jumlah'] * $order['jumlah'];

                                            Yii::$app->formatter->timeZone = 'Asia/Jakarta';
                                            $modelStockMovement->tanggal = Yii::$app->formatter->asDate(time());
                                            Yii::$app->formatter->timeZone = 'UTC';

                                            if (!($flag = $modelStockMovement->save())) {
                                                break;
                                            }
                                        } else {
                                            break;
                                        }
                                    }
                                }
                            }

                            if (!$flag) {
                                break;
                            }
                        }

                        if ($flag && !empty($post['payment'])) {

                            foreach ($post['payment'] as $payment) {

                                $modelSaleInvoicePayment = new SaleInvoicePayment();
                                $modelSaleInvoicePayment->sale_invoice_id = $modelSaleInvoice->id;
                                $modelSaleInvoicePayment->payment_method_id = $payment['payment_method_id'];
                                $modelSaleInvoicePayment->jumlah_bayar = $payment['jumlah_bayar'];
                                $modelSaleInvoicePayment->keterangan = $payment['keterangan'];

                                if ($payment['payment_method_id'] == 'XLIMIT') {
                                    $modelEmployee = Employee::findOne($payment['kode']);
                                    $modelEmployee->sisa -= $payment['jumlah_bayar'];

                                    if (!($flag = $modelEmployee->save())) {
                                        break;
                                    }
                                } else if ($payment['payment_method_id'] == 'XVCHR') {
                                    $modelVoucher = Voucher::findOne($payment['kode']);
                                    $modelVoucher->not_active = true;

                                    if (!($flag = $modelVoucher->save())) {
                                        break;
                                    }
                                }

                                if (!($flag = $modelSaleInvoicePayment->save())) {
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($flag) {

            $transaction->commit();

            $return['id'] = $modelSaleInvoice->id;
            $return['table'] = Yii::$app->urlManager->createUrl([Yii::$app->params['posModule']['full'] . 'home/table', 'id' => $modelMtableSession->mtable->mtable_category_id]);
            $return['success'] = true;
        } else {

            $transaction->rollBack();

            $return['success'] = false;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }
}
