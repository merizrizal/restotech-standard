<?php

namespace frontend\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\data\ActiveDataProvider;
use yii\web\Response;
use frontend\controllers\base\PosBaseController;
use backend\models\User;
use backend\models\Employee;
use backend\models\MtableCategory;
use backend\models\Mtable;
use backend\models\MtableSession;
use backend\models\MtableJoin;
use backend\models\MtableOrder;
use backend\models\MenuCategory;
use backend\models\Menu;
use backend\models\MenuCondiment;
use backend\models\MenuReceipt;
use backend\models\PaymentMethod;
use backend\models\SaleInvoice;
use backend\models\SaleInvoiceDetail;
use backend\models\SaleInvoicePayment;
use backend\models\SaleInvoiceCorrection;
use backend\models\SaleInvoiceCorrectionDetail;
use backend\models\SaleInvoiceCorrectionPayment;
use backend\models\Stock;
use backend\models\StockMovement;
use backend\models\TransactionDay;
use backend\models\Settings;
use backend\models\Booking;
use backend\models\MenuQueue;
use backend\models\Voucher;

/**
 * Page controller
 */
class PageController extends PosBaseController {

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
                        'logout' => ['post'],
                        'get-menu-condiment' => ['post'],
                        'get-menu' => ['post'],
                        'get-menu-category' => ['post'],
                        'search-menu' => ['post'],
                        'split-menu' => ['post'],
                        'get-mtable' => ['post'],
                        'transfer-mtable' => ['post'],
                        'close-table' => ['post'],
                        'unlock-bill' => ['post'],
                        'booking' => ['post'],
                        'menu-queue-save' => ['post'],
                        'start-day' => ['post'],
                        'end-day' => ['post'],
                    ],
                ],
            ]);        
    }
   
    public function actionIndex($cid = null) {
        
        if ($cid == null) {
        
            $modelMtable = MtableCategory::find()   
                    ->andWhere(['mtable_category.not_active' => FALSE])
                    ->orderBy('nama_category')
                    ->asArray()->all();                    

            return $this->render('index_list_mtable_category', [
                'modelMtable' => $modelMtable
            ]);
        } else {
            $modelMtable = MtableCategory::find()
                    ->joinWith([
                        'mtables' => function($query) {
                            $query->andWhere(['mtable.status' => 'bebas']);
                        }, 
                        'mtables.mtableSessions' => function($query) {
                            $query->onCondition('mtable_session.is_closed = FALSE');
                        },
                        'mtables.mtableSessions.mtableJoin',
                        'mtables.mtableSessions.mtableJoin.activeMtableSession' => function($query) {
                            $query->from('mtable_session active_mtable_session');
                        },
                        'mtables.bookings' => function($query) {
                            $query->onCondition('booking.is_closed = FALSE');
                        },
                    ])
                    ->andWhere(['mtable_category.id' => $cid])        
                    ->andWhere(['mtable_category.not_active' => FALSE])
                    ->asArray()->one(); 
                        
            if (empty($modelMtable))
                throw new NotFoundHttpException();

            return $this->render('index_list_mtable', [
                'modelMtable' => $modelMtable,
                'modelBooking' => new Booking(),
                'cid' => $cid,
            ]);
        }
    }
    
    public function actionIndex2($catid = null) {
        
        $model = MtableCategory::find()
                ->joinWith([
                    'mtables'
                ])
                ->andWhere(['mtable_category.not_active' => FALSE])
                ->all();
        
        if (empty($catid)) {
            
            if (empty($model))
                throw new NotFoundHttpException('The requested page does not exist.');
            
            $catid = $model[0]->id;
        }
        
        return $this->render('index2', [
            'model' => $model,
            'modelBooking' => new Booking(),
            'catid' => $catid,
        ]);
    }
    
    public function actionListOpenTable() {
        
        $query = Mtable::find()
                ->joinWith([
                    'mtableCategory',
                    'mtableSessions' => function($query) {
                        $query->andWhere('mtable_session.is_closed = FALSE');
                    },
                    'mtableSessions.userOpened.kdKaryawan',
                ])
                ->andWhere(['mtable.status' => 'bebas'])
                ->orderBy('mtable.nama_meja');
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => array(
                'pageSize' => 20,
            ),
        ]); 
        
        return $this->render('list_open_table', [
            'dataProvider' => $dataProvider,            
        ]);
        
        /*$modelMtable = MtableCategory::find()
                ->joinWith([
                    'mtables' => function($query) {
                        $query->andWhere(['mtable.status' => 'bebas']);
                    }, 
                    'mtables.mtableSessions' => function($query) {
                        $query->onCondition('mtable_session.is_closed = FALSE');
                    },
                    'mtables.mtableSessions.mtableJoin',
                    'mtables.mtableSessions.mtableJoin.activeMtableSession' => function($query) {
                        $query->from('mtable_session active_mtable_session');
                    },
                    'mtables.bookings' => function($query) {
                        $query->onCondition('booking.is_closed = FALSE');
                    },
                ])
                ->andWhere(['mtable_category.id' => $cid])                    
                ->asArray()->one();                    

        return $this->render('index_list_mtable', [
            'modelMtable' => $modelMtable,
            'modelBooking' => new Booking(),
            'cid' => $cid,
        ]);*/
    }
    
    public function actionPayment($id) {
        $modelMtableSession = MtableSession::find()
                ->andWhere(['mtable_session.id' => $id])                
                ->one();                
                                        
        if ($modelMtableSession === null || $modelMtableSession->is_closed) {
            return $this->redirect(['index']);
        }        
        
        if ($modelMtableSession->is_join_mtable && ($modelMtableSession->id != $modelMtableSession->mtableJoin->active_mtable_session_id)) {
            return $this->redirect(['page/payment', 'id' => $modelMtableSession->mtableJoin->active_mtable_session_id]);
        }
             
        $modelMtableOrders = MtableOrder::find()
                ->joinWith([
                    'menu',
                    'mtableOrders' => function($query) {
                        $query->from('mtable_order a');
                    },
                ])
                ->andWhere(['mtable_order.mtable_session_id' => $modelMtableSession->id])
                ->andWhere(['mtable_order.parent_id' => null])
                ->orderBy('menu.nama_menu ASC')
                ->all();
        
        
        $modelPaymentMethod = PaymentMethod::find()
                ->andWhere(['type' => 'sale'])
                ->andWhere(['!=', 'method', 'account-receiveable'])
                ->asArray()->all();
        
        $modelSettings = Settings::find()
                ->andWhere(['like', 'setting_name', 'struk_'])
                ->asArray()->all();
        $settingsArray = [];
        foreach ($modelSettings as $value) {
            $settingsArray[$value['setting_name']] = $value['setting_value'];
        }
        
        if (($modelTable = $modelMtableSession->mtable) !== null) {
            
            if (count($modelMtableOrders) > 0) { 

                return $this->render('payment', [
                    'modelTable' => $modelTable,
                    'modelMtableSession' => $modelMtableSession,
                    'modelPaymentMethod' => $modelPaymentMethod,
                    'modelMtableOrders' => !empty($modelMtableOrders) ? $modelMtableOrders : [],
                    'settingsArray' => $settingsArray,
                    'modelSettings' => Settings::getSettings(['tax_amount', 'service_charge_amount']),
                ]);
            } else {
                Yii::$app->session->setFlash('status', 'warning');
                Yii::$app->session->setFlash('message1', 'Order Menu Sukses. Proses payment Gagal.');
                Yii::$app->session->setFlash('message2', 'Tidak ada item menu order yang bisa dijadikan faktur.');
                
                return $this->redirect(['view-table', 'id' => $id]);
            }
        } else {
            return $this->redirect(['index']);
        }
    }
    
    public function actionPaymentSubmit() {   
        
        if (!empty(($post = Yii::$app->request->post()))) {
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = true;
            
            $modelMtableSession = MtableSession::findOne($post['sessionId']);
            
            if ($modelMtableSession->is_join_mtable) {
                $modelMtableSessionTemp = MtableSession::find()->andWhere(['mtable_join_id' => $modelMtableSession->mtable_join_id])->all();
                
                foreach ($modelMtableSessionTemp as $dataMtableSessionTemp) {
                    $dataMtableSessionTemp->is_closed = 1;
                    $dataMtableSessionTemp->closed_at = date('Y-m-d H:i:s');
                    $dataMtableSessionTemp->user_closed = Yii::$app->user->identity->id;

                    if (!($flag = $dataMtableSessionTemp->save())) {
                        break;
                    }
                }
            } else {
                $modelMtableSession->is_closed = 1;
                $modelMtableSession->closed_at = date('Y-m-d H:i:s');
                $modelMtableSession->user_closed = Yii::$app->user->identity->id;
                
                $flag = $modelMtableSession->save();
            }
            
            if ($flag) {
                
                $modelSaleInvoice = new SaleInvoice();
                
                if (($modelSaleInvoice->id = Settings::getTransNumber('no_inv')) !== false) {                
                    
                    $modelSaleInvoice->date = date('Y-m-d H:i:s');
                    $modelSaleInvoice->mtable_session_id = $modelMtableSession->id;
                    $modelSaleInvoice->user_operator = Yii::$app->user->identity->id;
                    $modelSaleInvoice->jumlah_harga = $post['paymentJumlahHarga'];
                    $modelSaleInvoice->discount_type = $post['discBillType'];
                    $modelSaleInvoice->discount = $post['discBill'];
                    $modelSaleInvoice->pajak = $post['paymentTaxAmount'];
                    $modelSaleInvoice->service_charge = $post['paymentServiceChargeAmount'];
                    $modelSaleInvoice->jumlah_bayar = $post['paymentJumlahBayar'];
                    $modelSaleInvoice->jumlah_kembali = $post['paymentJumlahKembali'];
                    
                    if (($flag = $modelSaleInvoice->save())) {
                        
                        foreach ($post['menu'] as $menuOrder) {
                            $modelSaleInvoiceDetail = new SaleInvoiceDetail();
                            $modelSaleInvoiceDetail->sale_invoice_id = $modelSaleInvoice->id;
                            $modelSaleInvoiceDetail->menu_id = $menuOrder['inputMenuId'];
                            $modelSaleInvoiceDetail->catatan = $menuOrder['inputMenuCatatan'];
                            $modelSaleInvoiceDetail->jumlah = $menuOrder['inputMenuQty'];
                            $modelSaleInvoiceDetail->discount_type = $menuOrder['inputMenuDiscountType'];
                            $modelSaleInvoiceDetail->discount = $menuOrder['inputMenuDiscount'];
                            $modelSaleInvoiceDetail->harga = $menuOrder['inputMenuHarga'];
                            $modelSaleInvoiceDetail->is_void = $menuOrder['inputMenuVoid'];
                            
                            if ($modelSaleInvoiceDetail->is_void) {                            
                                $modelSaleInvoiceDetail->void_at = $menuOrder['inputMenuVoidAt'];
                                $modelSaleInvoiceDetail->user_void = $menuOrder['inputMenuUserVoid'];
                            }
                            
                            $modelSaleInvoiceDetail->is_free_menu = $menuOrder['inputMenuFreeMenu'];
                            
                            if ($menuOrder['inputMenuFreeMenu']) {
                                $modelSaleInvoiceDetail->free_menu_at = $menuOrder['inputMenuFreeMenuAt'];
                                $modelSaleInvoiceDetail->user_free_menu = $menuOrder['inputMenuUserFreeMenu'];                    
                            }
                            
                            if (($flag = $modelSaleInvoiceDetail->save())) {
                                $modelMenuReceipt = MenuReceipt::find()
                                        ->joinWith([
                                            'menu',
                                            'item',
                                            'itemSku',
                                        ])
                                        ->andWhere('menu_receipt.menu_id="' . $modelSaleInvoiceDetail->menu_id . '"')
                                        ->asArray()->all();
                                                                
                                
                                if (count($modelMenuReceipt) > 0) {                                    
                                    foreach ($modelMenuReceipt as $dataMenuReceipt) {
                                        if (empty($dataMenuReceipt['itemSku']['storage_id'])) {
                                            $flag = false;
                                            break 2;
                                        }
                                        
                                        $flag = Stock::setStock(
                                                $dataMenuReceipt['item_id'], 
                                                $dataMenuReceipt['item_sku_id'], 
                                                $dataMenuReceipt['itemSku']['storage_id'], 
                                                $dataMenuReceipt['itemSku']['storage_rack_id'], 
                                                -1 * $dataMenuReceipt['jumlah'] * $modelSaleInvoiceDetail->jumlah
                                        );
                                        
                                        if ($flag) {
                                            $modelStockMovement = new StockMovement();
                                            $modelStockMovement->type = 'outflow-menu';
                                            $modelStockMovement->item_id = $dataMenuReceipt['item_id'];
                                            $modelStockMovement->item_sku_id = $dataMenuReceipt['item_sku_id'];
                                            $modelStockMovement->storage_from = $dataMenuReceipt['itemSku']['storage_id'];
                                            $modelStockMovement->storage_rack_from = $dataMenuReceipt['itemSku']['storage_rack_id'];
                                            $modelStockMovement->jumlah = $dataMenuReceipt['jumlah'] * $modelSaleInvoiceDetail->jumlah;
                                            $modelStockMovement->tanggal = date('Y-m-d');
                                            
                                            if (!($flag = $modelStockMovement->save())) {
                                                break 2;
                                            }
                                        } else {
                                            break 2;
                                        }
                                    }
                                }
                            } else {
                                break;
                            }
                        }
                        
                        if ($flag) {
                            if (!empty($post['payment'])) {
                                foreach ($post['payment'] as $payment) {
                                    $modelSaleInvoicePayment = new SaleInvoicePayment();
                                    $modelSaleInvoicePayment->sale_invoice_id = $modelSaleInvoice->id;
                                    $modelSaleInvoicePayment->payment_method_id = $payment['paymentMethod'];
                                    $modelSaleInvoicePayment->jumlah_bayar = $payment['paymentValue'];
                                    $modelSaleInvoicePayment->keterangan = $payment['paymentKeterangan'];

                                    if ($payment['paymentMethod'] == 'XLIMIT') {
                                        $modelEmployee = Employee::findOne($payment['paymentKode']);
                                        $modelEmployee->sisa -= $payment['paymentValue'];

                                        if (!($flag = $modelEmployee->save())) {
                                            break;
                                        }
                                    } else if ($payment['paymentMethod'] == 'XVCHR') {
                                        $modelVoucher = Voucher::findOne($payment['paymentKode']);
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
                
                $inv['noFaktur'] = $modelSaleInvoice->id;
                $inv['flag'] = true;
                
                Yii::$app->response->format = Response::FORMAT_JSON;
                
                return $inv;
            } else {                   
                $transaction->rollBack();
                
                $inv['flag'] = false;
                
                Yii::$app->response->format = Response::FORMAT_JSON;
                
                return $inv;
            }
        } else {
            throw new ForbiddenHttpException('You are not allowed to perform this action.');
        }
    }
    
    public function actionReprintFaktur() {
        
        if (!empty(($post = Yii::$app->request->post())) && !empty($post['invoiceId'])) {
            $modelSaleInvoice = SaleInvoice::find()
                    ->joinWith([
                        'saleInvoicePayments' => function($query) {
                            $query->onCondition('sale_invoice_payment.parent_id IS NULL');
                        },
                        'saleInvoicePayments.paymentMethod',
                        'mtableSession',
                        'mtableSession.mtable',
                        'saleInvoiceDetails',
                        'saleInvoiceDetails.menu'
                    ])
                    ->andWhere(['sale_invoice.id' => $post['invoiceId']])->one();

            if ($modelSaleInvoice === null) {
                throw new NotFoundHttpException('Upss. Terjadi kesalahan dari apa yang Anda inputkan. Silakan ulangi lagi.');
            }                                

            $modelSettings = Settings::find()
                    ->orWhere(['like', 'setting_name', 'struk_'])
                    ->orWhere(['like', 'setting_name', 'printer_'])
                    ->asArray()->all();
            $settingsArray = [];
            foreach ($modelSettings as $value) {
                $settingsArray[$value['setting_name']] = $value['setting_value'];
            }

            return $this->render('reprint_faktur', [
                'modelSaleInvoice' => $modelSaleInvoice,
                'modelMtableSession' => $modelSaleInvoice->mtableSession,
                'modelTable' => $modelSaleInvoice->mtableSession->mtable,
                'modelSaleInvoiceDetails' => !empty($modelSaleInvoice->saleInvoiceDetails) ? $modelSaleInvoice->saleInvoiceDetails : [],
                'settingsArray' => $settingsArray,
            ]);
        }
        
        return $this->render('reprint_faktur_input');
    }
    
    public function actionOpenTable($id) {
                        
        $post = Yii::$app->request->post();
        
        if (!empty($post['menu']) || !empty($post['inputJumlahTamu'])) {
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = true;
            
            $modelMtableSession = new MtableSession();
            $modelMtableSession->mtable_id = $post['tableId'];
            $modelMtableSession->jumlah_harga = empty($post['total-harga-input']) ? 0 : $post['total-harga-input'];
            $modelMtableSession->discount_type = empty($post['discBillType']) ? 'percent' : $post['discBillType'];
            $modelMtableSession->discount = empty($post['discBill']) ? 0 : $post['discBill'];
            $modelMtableSession->service_charge = empty($post['serviceChargeAmount']) ? 0 : $post['serviceChargeAmount'];
            $modelMtableSession->pajak = empty($post['taxAmount']) ? 0 : $post['taxAmount'];
            $modelMtableSession->is_closed = 0;
            $modelMtableSession->opened_at = date('Y-m-d H:i:s');
            $modelMtableSession->user_opened = Yii::$app->user->identity->id;    
            
            if (!empty($post['inputJumlahTamu'])) {
                $modelMtableSession->jumlah_guest = $post['inputJumlahTamu'];
                $modelMtableSession->nama_tamu = $post['inputNamaTamu'];
            }
            
            if (($flag = $modelMtableSession->save())) {
                if (!empty($post['menu'])) {
                    $modelMtableOrders = [];
                    $i = 0;

                    foreach ($post['menu'] as $menuOrder) {
                        $modelMtableOrders[$i] = new MtableOrder();
                        $modelMtableOrders[$i]->mtable_session_id = $modelMtableSession->id;
                        $modelMtableOrders[$i]->menu_id = $menuOrder['inputMenuId'];
                        $modelMtableOrders[$i]->jumlah = $menuOrder['inputMenuQty'];
                        $modelMtableOrders[$i]->discount_type = $menuOrder['inputMenuDiscountType'];
                        $modelMtableOrders[$i]->discount = $menuOrder['inputMenuDiscount'];
                        $modelMtableOrders[$i]->harga_satuan = $menuOrder['inputMenuHarga'];
                        $modelMtableOrders[$i]->is_free_menu = $menuOrder['inputMenuFreeMenu'];
                        $modelMtableOrders[$i]->catatan = $menuOrder['inputMenuCatatan'];

                        if ($menuOrder['inputMenuVoid']) {
                            $modelMtableOrders[$i]->is_void = $menuOrder['inputMenuVoid'];
                            $modelMtableOrders[$i]->void_at = date('Y-m-d H:i:s');
                            $modelMtableOrders[$i]->user_void = Yii::$app->user->identity->id;                    
                        }
                        
                        if ($menuOrder['inputMenuFreeMenu']) {
                            $modelMtableOrders[$i]->is_free_menu = $menuOrder['inputMenuFreeMenu'];
                            $modelMtableOrders[$i]->free_menu_at = date('Y-m-d H:i:s');
                            $modelMtableOrders[$i]->user_free_menu = Yii::$app->user->identity->id;                    
                        }

                        if (!($flag = $modelMtableOrders[$i]->save())) {
                            break;
                        }

                        $i++;                                        
                    }
                }
            }
            
            if ($flag) {
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Order Menu Sukses');
                Yii::$app->session->setFlash('message2', 'Proses order menu sukses. Data telah berhasil disimpan.');
                
                $transaction->commit();
                
                if (!empty($post['inputPayment'])) {
                    return $this->redirect(['payment', 'id' => $modelMtableSession->id]);
                } else {
                    return $this->redirect(['view-table', 'id' => $modelMtableSession->id]);
                }
                
            } else {
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Order Menu Gagal');
                Yii::$app->session->setFlash('message2', 'Proses order menu gagal. Data gagal disimpan.');
                
                $transaction->rollBack();
            }
        }
        
        if (($modelTable = Mtable::findOne($id)) !== null) {    
        
            $modelMenuCategory = MenuCategory::find()
                    ->joinWith([
                        'menuCategories' => function($query) {
                            $query->from('menu_category child');
                        }
                    ])
                    ->andWhere(['IS', 'menu_category.parent_category_id', NULL])
                    ->asArray()->all();                    

            return $this->render('open_table', [
                'modelTable' => $modelTable,
                'modelMenuCategory' => $modelMenuCategory,
                'modelMtableOrders' => !empty($modelMtableOrders) ? $modelMtableOrders : [],
                'modelSettings' => Settings::getSettings(['tax_amount', 'service_charge_amount']),
            ]);
        } else {
            return $this->redirect(['index']);
        }
    }
    
    public function actionViewSession($id) {
        $modelMtableSession = MtableSession::find()
                ->andWhere([
                    'mtable_id' => $id,
                    'is_closed' => false
                ])->all();
        
        if (count($modelMtableSession) == 1) {
            return $this->redirect(['view-table', 'id' => $modelMtableSession[0]->id]);
        }
        
        return $this->render('view_session', [
            'modelTable' => $modelMtableSession[0]->mtable,        
            'modelMtableSession' => $modelMtableSession,
        ]);
    }
    
    public function actionViewTable($id) {  
        
        if (($modelMtableSession = MtableSession::findOne($id)) === null || $modelMtableSession->is_closed) {
            return $this->redirect(['index']);
        }
        
        $modelMtableOrders = MtableOrder::find()
                ->joinWith([
                    'menu',
                    'menu.menuCategory',
                    'menu.menuCategory.menuCategoryPrinters',
                    'menu.menuCategory.menuCategoryPrinters.printer0',
                    'mtableOrders' => function($query) {
                        $query->from('mtable_order a');
                    },
                ])
                ->andWhere(['mtable_order.mtable_session_id' => $modelMtableSession->id])
                ->andWhere(['mtable_order.parent_id' => null])
                ->orderBy('mtable_order.id ASC')
                ->all();
                        
        $post = Yii::$app->request->post();
        if (!empty($post['menu'])) {                     
            
            if (!$modelMtableSession->bill_printed) {
            
                $transaction = Yii::$app->db->beginTransaction();
                $flag = true;

                $modelMtableSession->mtable_id = $post['tableId'];
                $modelMtableSession->jumlah_harga = $post['total-harga-input'];
                $modelMtableSession->discount_type = $post['discBillType'];
                $modelMtableSession->discount = $post['discBill'];
                $modelMtableSession->service_charge = $post['serviceChargeAmount'];
                $modelMtableSession->pajak = $post['taxAmount'];
                $modelMtableSession->bill_printed = !empty($post['billPrinted']) ? $post['billPrinted'] : 0;

                if ($modelMtableSession->isNewRecord) {
                    $modelMtableSession->is_closed = false;
                    $modelMtableSession->opened_at = date('Y-m-d H:i:s');
                    $modelMtableSession->user_opened = Yii::$app->user->identity->id;                        
                }

                if (($flag = $modelMtableSession->save())) {

                    $modelMtableOrders = [];
                    $i = 0;
                    
                    $menuQueueList = '';

                    foreach ($post['menu'] as $menuOrder) {

                        if (empty($menuOrder['inputId'])) {
                            $modelMtableOrders[$i] = new MtableOrder();
                            $modelMtableOrders[$i]->mtable_session_id = $modelMtableSession->id;
                            $modelMtableOrders[$i]->menu_id = $menuOrder['inputMenuId'];                         
                        } else {
                            $modelMtableOrders[$i] = MtableOrder::findOne($menuOrder['inputId']);
                        }
                        
                        $menuQueue = $modelMtableOrders[$i]->menuQueue;
                        
                        if (empty($menuQueue) || $menuOrder['inputMenuVoid']) {

                            $modelMtableOrders[$i]->harga_satuan = $menuOrder['inputMenuHarga'];
                            $modelMtableOrders[$i]->jumlah = $menuOrder['inputMenuQty'];
                            $modelMtableOrders[$i]->catatan = $menuOrder['inputMenuCatatan'];   
                            
                            if (!empty($menuOrder['inputParentId']))
                                $modelMtableOrders[$i]->parent_id = $menuOrder['inputParentId'];   

                            if ($menuOrder['inputMenuVoid']) {
                                $modelMtableOrders[$i]->is_void = $menuOrder['inputMenuVoid'];
                                $modelMtableOrders[$i]->void_at = date('Y-m-d H:i:s');
                                $modelMtableOrders[$i]->user_void = Yii::$app->user->identity->id;

                                if (!empty($menuQueue))
                                    $menuQueue->delete();
                            }                                                                                                       
                        } else {                                                        
                            $menuQueueList .= '<br>Order <b>' . $menuQueue->menu->nama_menu . ' (' . $modelMtableOrders[$i]->jumlah . ') </b> tidak bisa di update karena menu order sudah diprint.';
                        }
                        
                        $modelMtableOrders[$i]->discount_type = $menuOrder['inputMenuDiscountType'];
                        $modelMtableOrders[$i]->discount = $menuOrder['inputMenuDiscount'];
                        $modelMtableOrders[$i]->is_free_menu = $menuOrder['inputMenuFreeMenu'];
                        
                        if ($menuOrder['inputMenuFreeMenu']) {
                            $modelMtableOrders[$i]->is_free_menu = $menuOrder['inputMenuFreeMenu'];
                            $modelMtableOrders[$i]->free_menu_at = date('Y-m-d H:i:s');
                            $modelMtableOrders[$i]->user_free_menu = Yii::$app->user->identity->id;                    
                        }
                        
                        if (!($flag = $modelMtableOrders[$i]->save())) {
                            break;
                        }

                        $i++;                                        
                    }
                }                
                
                if ($flag) {                    
                    $transaction->commit();

                    if (!empty($post['inputPayment'])) {
                        return $this->redirect(['payment', 'id' => $id]);
                    } else {
                        if ($menuQueueList != '')
                            $menuQueueList .= '<br><br>Hanya bisa update discount dan free menu.';
                            
                        Yii::$app->session->setFlash('status', 'success');
                        Yii::$app->session->setFlash('message1', 'Order Menu Sukses');
                        Yii::$app->session->setFlash('message2', 'Proses order menu sukses. Data telah berhasil disimpan.' . $menuQueueList);

                        return $this->redirect(['view-table', 'id' => $modelMtableSession->id]);
                    }

                } else {
                    Yii::$app->session->setFlash('status', 'danger');
                    Yii::$app->session->setFlash('message1', 'Order Menu Gagal');
                    Yii::$app->session->setFlash('message2', 'Proses order menu gagal. Data gagal disimpan.');

                    $transaction->rollBack();
                }
            } else {
                if (!empty($post['inputPayment'])) {
                    return $this->redirect(['payment', 'id' => $id]);
                }
                
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Tagihan sudah dicetak');
                Yii::$app->session->setFlash('message2', 'Proses edit dan simpan data tidak bisa dilakukan.');    
                
                return $this->redirect(['view-table', 'id' => $modelMtableSession->id]);
            }                        
        }
        
        if ($modelMtableSession->is_join_mtable && ($modelMtableSession->id != $modelMtableSession->mtableJoin->active_mtable_session_id)) {
            return $this->redirect(['page/view-table', 'id' => $modelMtableSession->mtableJoin->active_mtable_session_id]);
        }
        
        if (($modelTable = $modelMtableSession->mtable) !== null) {    
        
            $modelMenuCategory = MenuCategory::find()
                    ->joinWith([
                        'menuCategories' => function($query) {
                            $query->from('menu_category child');
                        }
                    ])
                    ->andWhere(['IS', 'menu_category.parent_category_id', NULL])
                    ->asArray()->all();
                    
            $modelSettings = Settings::find()
                    ->andWhere(['like', 'setting_name', 'struk_'])
                    ->asArray()->all();
            $settingsArray = [];
            foreach ($modelSettings as $value) {
                $settingsArray[$value['setting_name']] = $value['setting_value'];
            }

            return $this->render('open_table', [
                'modelTable' => $modelTable,
                'modelMtableSession' => $modelMtableSession,
                'modelMenuCategory' => $modelMenuCategory,
                'modelMtableOrders' => !empty($modelMtableOrders) ? $modelMtableOrders : [],
                'settingsArray' => $settingsArray,
                'modelSettings' => Settings::getSettings(['tax_amount', 'service_charge_amount']),
            ]);
        } else {
            return $this->redirect(['index']);
        }
    }
    
    public function actionKoreksiFakturInput() {
        
        return $this->render('koreksi-faktur-input', [
            
        ]);
    }
    
    public function actionKoreksiFaktur($id) {        
        
        $modelInvoice = SaleInvoice::findOne($id);

        if (($modelMtableSession = MtableSession::findOne(empty($modelInvoice->mtable_session_id) ? null : $modelInvoice->mtable_session_id)) === null) {
            throw new NotFoundHttpException('Upss. Terjadi kesalahan dari apa yang Anda inputkan. Silakan ulangi lagi.');
        }

        $modelMtableOrders = $modelMtableSession->mtableOrders;

        $post = Yii::$app->request->post();
        
        if (!empty($post['menu'])) {

            $transaction = Yii::$app->db->beginTransaction();
            $flag = true;

            $modelMtableSession->mtable_id = $post['tableId'];

            if ($modelMtableSession->isNewRecord) {
                $modelMtableSession->is_closed = false;
                $modelMtableSession->opened_at = date('Y-m-d H:i:s');
                $modelMtableSession->user_opened = Yii::$app->user->identity->id;                        
            }

            if (($flag = $modelMtableSession->save())) {

                $modelMtableOrders = [];
                $i = 0;

                foreach ($post['menu'] as $menuOrder) {

                    if (empty($menuOrder['inputId'])) {
                        $modelMtableOrders[$i] = new MtableOrder();
                        $modelMtableOrders[$i]->mtable_session_id = $modelMtableSession->id;
                        $modelMtableOrders[$i]->menu_id = $menuOrder['inputMenuId'];                         
                    } else {
                        $modelMtableOrders[$i] = MtableOrder::findOne($menuOrder['inputId']);
                    }

                    $modelMtableOrders[$i]->harga_satuan = $menuOrder['inputMenuHarga'];
                    $modelMtableOrders[$i]->discount_type = $menuOrder['inputMenuDiscountType'];
                    $modelMtableOrders[$i]->discount = $menuOrder['inputMenuDiscount'];
                    $modelMtableOrders[$i]->jumlah = $menuOrder['inputMenuQty'];
                    $modelMtableOrders[$i]->is_free_menu = $menuOrder['inputMenuFreeMenu'];
                    $modelMtableOrders[$i]->catatan = $menuOrder['inputMenuCatatan'];

                    if ($menuOrder['inputMenuVoid']) {
                        $modelMtableOrders[$i]->is_void = $menuOrder['inputMenuVoid'];
                        $modelMtableOrders[$i]->void_at = date('Y-m-d H:i:s');
                        $modelMtableOrders[$i]->user_void = Yii::$app->user->identity->id;                    
                    }

                    if (!($flag = $modelMtableOrders[$i]->save())) {
                        break;
                    }

                    $i++;                                        
                }
            }

            if ($flag) {
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Order Menu Sukses');
                Yii::$app->session->setFlash('message2', 'Proses order menu sukses. Data telah berhasil disimpan.');

                $transaction->commit();

                if (!empty($post['inputPayment'])) {
                    return $this->redirect(['koreksi-payment', 'id' => $id]);
                } else {
                    return $this->redirect(['koreksi-faktur', 'id' => $id]);
                }

            } else {
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Order Menu Gagal');
                Yii::$app->session->setFlash('message2', 'Proses order menu gagal. Data gagal disimpan.');

                $transaction->rollBack();
            }
        }

        if ($modelMtableSession->is_join_mtable && ($modelMtableSession->id != $modelMtableSession->mtableJoin->active_mtable_session_id)) {
            return $this->redirect(['page/koreksi-faktur', 'id' => $modelMtableSession->mtableJoin->active_mtable_session_id]);
        }

        if (($modelTable = $modelMtableSession->mtable) !== null) {    

            $modelMenuCategory = MenuCategory::find()
                    ->joinWith([
                        'menuCategories' => function($query) {
                            $query->from('menu_category child');
                        }
                    ])
                    ->andWhere(['IS', 'menu_category.parent_category_id', NULL])
                    ->asArray()->all();

            $modelSettings = Settings::find()
                    ->andWhere(['like', 'setting_name', 'struk_'])
                    ->asArray()->all();
            $settingsArray = [];
            foreach ($modelSettings as $value) {
                $settingsArray[$value['setting_name']] = $value['setting_value'];
            }

            return $this->render('open_table', [
                'modelTable' => $modelTable,
                'modelMtableSession' => $modelMtableSession,
                'modelMenuCategory' => $modelMenuCategory,
                'modelMtableOrders' => !empty($modelMtableOrders) ? $modelMtableOrders : [],
                'settingsArray' => $settingsArray,
                'modelSettings' => Settings::getSettings(['tax_amount', 'service_charge_amount']),
            ]);
        } else {
            return $this->redirect(['index']);
        }                
    }
    
    public function actionKoreksiPayment($id) {
        $modelInvoice = SaleInvoice::findOne($id);
        
        $modelMtableSession = MtableSession::find()
                ->joinWith([
                    'mtableOrders'
                ])
                ->andWhere(['mtable_session.id' => $modelInvoice->mtable_session_id])->one();                
                                        
        if ($modelMtableSession === null) {
            return $this->redirect(['index']);
        }        
        
        if ($modelMtableSession->is_join_mtable && ($modelMtableSession->id != $modelMtableSession->mtableJoin->active_mtable_session_id)) {
            return $this->redirect(['page/koreksi-payment', 'id' => $modelMtableSession->mtableJoin->active_mtable_session_id]);
        }
        
        $modelMtableOrders = $modelMtableSession->mtableOrders;
        $modelPaymentMethod = PaymentMethod::find()
                ->andWhere(['type' => 'sale'])
                ->andWhere(['!=', 'method', 'account-receiveable'])
                ->asArray()->all();
        
        $modelSettings = Settings::find()
                ->andWhere(['like', 'setting_name', 'struk_'])
                ->asArray()->all();
        $settingsArray = [];
        foreach ($modelSettings as $value) {
            $settingsArray[$value['setting_name']] = $value['setting_value'];
        }
        
        if (($modelTable = $modelMtableSession->mtable) !== null) {
            
            if (count($modelMtableOrders) > 0) { 

                return $this->render('payment', [
                    'modelTable' => $modelTable,
                    'modelInvoice' => $modelInvoice,
                    'modelMtableSession' => $modelMtableSession,
                    'modelPaymentMethod' => $modelPaymentMethod,
                    'modelMtableOrders' => !empty($modelMtableOrders) ? $modelMtableOrders : [],
                    'settingsArray' => $settingsArray,
                    'modelSettings' => Settings::getSettings(['tax_amount', 'service_charge_amount']),
                    'isKoreksi' => true,
                ]);
            } else {
                Yii::$app->session->setFlash('status', 'warning');
                Yii::$app->session->setFlash('message1', 'Order Menu Sukses. Proses payment Gagal.');
                Yii::$app->session->setFlash('message2', 'Tidak ada item menu order yang bisa dijadikan faktur.');
                
                return $this->redirect(['koreksi-faktur', 'id' => $id]);
            }
        } else {
            return $this->redirect(['index']);
        }
    }
    
    public function actionKoreksiPaymentSubmit() {              
        if (!empty(($post = Yii::$app->request->post()))) {
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = true;                        
            
            $modelSaleInvoice = SaleInvoice::findOne($post['oldInvoiceId']);
            
            $modelSaleInvoiceCorrection = new SaleInvoiceCorrection();
            $modelSaleInvoiceCorrection->sale_invoice_id = $modelSaleInvoice->id;
            $modelSaleInvoiceCorrection->date = $modelSaleInvoice->date;
            $modelSaleInvoiceCorrection->mtable_session_id = $modelSaleInvoice->mtable_session_id;
            $modelSaleInvoiceCorrection->user_operator = $modelSaleInvoice->user_operator;
            $modelSaleInvoiceCorrection->jumlah_harga = $modelSaleInvoice->jumlah_harga;
            $modelSaleInvoiceCorrection->pajak = $modelSaleInvoice->pajak;
            $modelSaleInvoiceCorrection->service_charge = $modelSaleInvoice->service_charge;
            $modelSaleInvoiceCorrection->jumlah_bayar = $modelSaleInvoice->jumlah_bayar;
            $modelSaleInvoiceCorrection->jumlah_kembali = $modelSaleInvoice->jumlah_kembali;
            $modelSaleInvoiceCorrection->keterangan = $post['keterangan'];
            
            if (($flag = $modelSaleInvoiceCorrection->save())) {                
                
                foreach ($modelSaleInvoice->saleInvoiceDetails as $dataSaleInvoiceDetail) {
                    $modelSaleInvoiceCorrectionDetail = new SaleInvoiceCorrectionDetail();
                    $modelSaleInvoiceCorrectionDetail->sale_invoice_correction_id = $modelSaleInvoiceCorrection->id;
                    $modelSaleInvoiceCorrectionDetail->menu_id = $dataSaleInvoiceDetail->menu_id;
                    $modelSaleInvoiceCorrectionDetail->catatan = $dataSaleInvoiceDetail->catatan;
                    $modelSaleInvoiceCorrectionDetail->jumlah = $dataSaleInvoiceDetail->jumlah;
                    $modelSaleInvoiceCorrectionDetail->discount_type = $dataSaleInvoiceDetail->discount_type;
                    $modelSaleInvoiceCorrectionDetail->discount = $dataSaleInvoiceDetail->discount;
                    $modelSaleInvoiceCorrectionDetail->harga = $dataSaleInvoiceDetail->harga;
                    $modelSaleInvoiceCorrectionDetail->is_void = $dataSaleInvoiceDetail->is_void;
                    
                    if ($modelSaleInvoiceCorrectionDetail->is_void) {                            
                        $modelSaleInvoiceCorrectionDetail->void_at = $dataSaleInvoiceDetail->void_at;
                        $modelSaleInvoiceCorrectionDetail->user_void = $dataSaleInvoiceDetail->user_void;
                    }                    
                    
                    $modelSaleInvoiceCorrectionDetail->is_free_menu = $dataSaleInvoiceDetail->is_free_menu;
                    
                    if ($modelSaleInvoiceCorrectionDetail->is_free_menu) {
                        $modelSaleInvoiceCorrectionDetail->free_menu_at = $dataSaleInvoiceDetail->free_menu_at;
                        $modelSaleInvoiceCorrectionDetail->user_free_menu = $dataSaleInvoiceDetail->user_free_menu;          
                    }
                    
                    if (($flag = ($modelSaleInvoiceCorrectionDetail->save() && $dataSaleInvoiceDetail->delete()))) {
                        
                        $modelMenuReceipt = MenuReceipt::find()
                            ->joinWith([
                                'menu',
                                'item',
                                'itemSku',
                            ])
                            ->andWhere('menu_receipt.menu_id="' . $modelSaleInvoiceCorrectionDetail->menu_id . '"')
                            ->asArray()->all();                                                
                        
                        if (count($modelMenuReceipt) > 0) {                               
                            foreach ($modelMenuReceipt as $dataMenuReceipt) {
                                if (empty($dataMenuReceipt['itemSku']['storage_id'])) {
                                    $flag = false;
                                    break 2;
                                }

                                $flag = Stock::setStock(
                                        $dataMenuReceipt['item_id'], 
                                        $dataMenuReceipt['item_sku_id'], 
                                        $dataMenuReceipt['itemSku']['storage_id'], 
                                        $dataMenuReceipt['itemSku']['storage_rack_id'], 
                                        $dataMenuReceipt['jumlah'] * $modelSaleInvoiceCorrectionDetail->jumlah
                                );

                                if ($flag) {
                                    $modelStockMovement = new StockMovement();
                                    $modelStockMovement->type = 'outflow';
                                    $modelStockMovement->item_id = $dataMenuReceipt['item_id'];
                                    $modelStockMovement->item_sku_id = $dataMenuReceipt['item_sku_id'];
                                    $modelStockMovement->storage_to = $dataMenuReceipt['itemSku']['storage_id'];
                                    $modelStockMovement->storage_rack_to = $dataMenuReceipt['itemSku']['storage_rack_id'];
                                    $modelStockMovement->jumlah = $dataMenuReceipt['jumlah'] * $modelSaleInvoiceCorrectionDetail->jumlah;
                                    $modelStockMovement->tanggal = date('Y-m-d');

                                    if (!($flag = $modelStockMovement->save())) {
                                        break 2;
                                    }
                                } else {
                                    break 2;
                                }
                            }
                        }
                    } else {
                        break;
                    }
                }
                
                if ($flag) {
                    if (!empty($modelSaleInvoice->saleInvoicePayments)) {
                        foreach ($modelSaleInvoice->saleInvoicePayments as $payment) {
                            $modelSaleInvoiceCorrectionPayment = new SaleInvoiceCorrectionPayment();
                            $modelSaleInvoiceCorrectionPayment->sale_invoice_correction_id = $modelSaleInvoiceCorrection->id;
                            $modelSaleInvoiceCorrectionPayment->payment_method_id = $payment->payment_method_id;
                            $modelSaleInvoiceCorrectionPayment->jumlah_bayar = $payment->jumlah_bayar;
                            $modelSaleInvoiceCorrectionPayment->keterangan = $payment->keterangan;

                            if (!($flag = ($modelSaleInvoiceCorrectionPayment->save() && $payment->delete()))) {
                                break;
                            }
                        }
                    }
                }
                
                if ($flag) {
                                
                    $modelSaleInvoice->user_operator = Yii::$app->user->identity->id;
                    $modelSaleInvoice->jumlah_harga = $post['paymentJumlahHarga'];
                    $modelSaleInvoice->pajak = $post['paymentTaxAmount'];
                    $modelSaleInvoice->service_charge = $post['paymentServiceChargeAmount'];
                    $modelSaleInvoice->jumlah_bayar = $post['paymentJumlahBayar'];
                    $modelSaleInvoice->jumlah_kembali = $post['paymentJumlahKembali'];

                    if (($flag = $modelSaleInvoice->save())) {

                        foreach ($post['menu'] as $menuOrder) {
                            $modelSaleInvoiceDetail = new SaleInvoiceDetail();
                            $modelSaleInvoiceDetail->sale_invoice_id = $modelSaleInvoice->id;
                            $modelSaleInvoiceDetail->menu_id = $menuOrder['inputMenuId'];
                            $modelSaleInvoiceDetail->jumlah = $menuOrder['inputMenuQty'];
                            $modelSaleInvoiceDetail->discount_type = $menuOrder['inputMenuDiscountType'];
                            $modelSaleInvoiceDetail->discount = $menuOrder['inputMenuDiscount'];
                            $modelSaleInvoiceDetail->harga = $menuOrder['inputMenuHarga'];
                            $modelSaleInvoiceDetail->is_void = $menuOrder['inputMenuVoid'];
                            $modelSaleInvoiceDetail->is_free_menu = $menuOrder['inputMenuFreeMenu'];
                            
                            if ($modelSaleInvoiceDetail->is_void) {                            
                                $modelSaleInvoiceDetail->void_at = $menuOrder['inputMenuVoidAt'];
                                $modelSaleInvoiceDetail->user_void = $menuOrder['inputMenuUserVoid'];
                            }                            
                            
                            if ($menuOrder['inputMenuFreeMenu']) {
                                $modelSaleInvoiceDetail->free_menu_at = $menuOrder['inputMenuFreeMenuAt'];
                                $modelSaleInvoiceDetail->user_free_menu = $menuOrder['inputMenuUserFreeMenu'];                    
                            }

                            if (($flag = $modelSaleInvoiceDetail->save())) {
                                $modelMenuReceipt = MenuReceipt::find()
                                        ->joinWith([
                                            'menu',
                                            'item',
                                            'itemSku',
                                        ])
                                        ->andWhere('menu_receipt.menu_id="' . $modelSaleInvoiceDetail->menu_id . '"')
                                        ->asArray()->all();


                                if (count($modelMenuReceipt) > 0) {                                    
                                    foreach ($modelMenuReceipt as $dataMenuReceipt) {
                                        if (empty($dataMenuReceipt['itemSku']['storage_id'])) {
                                            $flag = false;
                                            break 2;
                                        }

                                        $flag = Stock::setStock(
                                                $dataMenuReceipt['item_id'], 
                                                $dataMenuReceipt['item_sku_id'], 
                                                $dataMenuReceipt['itemSku']['storage_id'], 
                                                $dataMenuReceipt['itemSku']['storage_rack_id'], 
                                                -1 * $dataMenuReceipt['jumlah'] * $modelSaleInvoiceDetail->jumlah
                                        );

                                        if ($flag) {
                                            $modelStockMovement = new StockMovement();
                                            $modelStockMovement->type = 'outflow-menu';
                                            $modelStockMovement->item_id = $dataMenuReceipt['item_id'];
                                            $modelStockMovement->item_sku_id = $dataMenuReceipt['item_sku_id'];
                                            $modelStockMovement->storage_from = $dataMenuReceipt['itemSku']['storage_id'];
                                            $modelStockMovement->storage_rack_from = $dataMenuReceipt['itemSku']['storage_rack_id'];
                                            $modelStockMovement->jumlah = $dataMenuReceipt['jumlah'] * $modelSaleInvoiceDetail->jumlah;
                                            $modelStockMovement->tanggal = date('Y-m-d');

                                            if (!($flag = $modelStockMovement->save())) {
                                                break 2;
                                            }
                                        } else {
                                            break 2;
                                        }
                                    }
                                }
                            } else {
                                break;
                            }
                        }

                        if ($flag) {
                            if (!empty($post['payment'])) {
                                foreach ($post['payment'] as $payment) {
                                    $modelSaleInvoicePayment = new SaleInvoicePayment();
                                    $modelSaleInvoicePayment->sale_invoice_id = $modelSaleInvoice->id;
                                    $modelSaleInvoicePayment->payment_method_id = $payment['paymentMethod'];
                                    $modelSaleInvoicePayment->jumlah_bayar = $payment['paymentValue'];
                                    $modelSaleInvoicePayment->keterangan = $payment['paymentKeterangan'];

                                    if ($payment['paymentMethod'] == 'XLIMIT') {
                                        $modelEmployee = Employee::findOne($payment['paymentKode']);
                                        $modelEmployee->sisa -= $payment['paymentValue'];

                                        if (!($flag = $modelEmployee->save())) {
                                            break;
                                        }
                                    } else if ($payment['paymentMethod'] == 'XVCHR') {
                                        $modelVoucher = Voucher::findOne($payment['paymentKode']);
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
                
                $inv['noFaktur'] = $modelSaleInvoice->id;
                $inv['flag'] = true;
                
                Yii::$app->response->format = Response::FORMAT_JSON;
                
                return $inv;
            } else {                   
                $transaction->rollBack();
                
                $inv['flag'] = false;
                
                Yii::$app->response->format = Response::FORMAT_JSON;
                
                return $inv;
            }
        } else {
            throw new ForbiddenHttpException('You are not allowed to perform this action.');
        }
    }
    
    public function actionSplitMenu() {  
        
        $post = Yii::$app->request->post();
        
        if (!empty($post['menu'])) {
            if (!$post['billPrinted']) {

                $transaction = Yii::$app->db->beginTransaction();
                $flag = true;

                $modelMtableSession = new MtableSession();
                $modelMtableSession->mtable_id = $post['tableId'];
                $modelMtableSession->nama_tamu = $post['inputNamaTamu'];
                $modelMtableSession->jumlah_guest = $post['inputJumlahTamu'];
                $modelMtableSession->is_closed = 0;
                $modelMtableSession->opened_at = date('Y-m-d H:i:s');
                $modelMtableSession->user_opened = Yii::$app->user->identity->id;                        

                if (($flag = $modelMtableSession->save())) {

                    foreach (Yii::$app->request->post()['menu'] as $menuOrder) {
                        $modelMtableOrders = MtableOrder::findOne($menuOrder['inputId']);
                        $modelMtableOrders->mtable_session_id = $modelMtableSession->id;
                        $modelMtableOrders->menu_id = $menuOrder['inputMenuId'];
                        $modelMtableOrders->jumlah = $menuOrder['inputMenuQty'];
                        $modelMtableOrders->discount = $menuOrder['inputMenuDiscount'];
                        $modelMtableOrders->harga_satuan = $menuOrder['inputMenuHarga'];

                        if (!($flag = $modelMtableOrders->save())) {
                            break;
                        }                              
                    }
                }            

                if ($flag) {
                    Yii::$app->session->setFlash('status', 'success');
                    Yii::$app->session->setFlash('message1', 'Split Menu Sukses');
                    Yii::$app->session->setFlash('message2', 'Proses split menu sukses. Data berhasil disimpan.');

                    $transaction->commit();                
                } else {
                    Yii::$app->session->setFlash('status', 'danger');
                    Yii::$app->session->setFlash('message1', 'Split Menu Gagal');
                    Yii::$app->session->setFlash('message2', 'Proses split menu gagal. Data gagal disimpan.');               

                    $transaction->rollBack();
                }
                
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Tagihan sudah dicetak');
                Yii::$app->session->setFlash('message2', 'Proses split tidak bisa dilakukan.');
                
                return $this->redirect(['view-table', 'id' => $post['sessionMtable']]);
            }                       
        }
    }
    
    public function actionTransferMtable() {
        $post = Yii::$app->request->post();        
        
        if (!empty($post)) {
            if (!$post['billPrinted']) {
                $modelMtableSession = MtableSession::findOne($post['sessionMtableId']);
                $modelMtableSession->mtable_id = $post['mtableId'];

                if ($modelMtableSession->save()) {
                    Yii::$app->session->setFlash('status', 'success');
                    Yii::$app->session->setFlash('message1', 'Transfer Table Sukses');
                    Yii::$app->session->setFlash('message2', 'Proses transfer table sukses. Data telah berhasil disimpan.');                
                } else {
                    Yii::$app->session->setFlash('status', 'danger');
                    Yii::$app->session->setFlash('message1', 'Transfer Table Gagal');
                    Yii::$app->session->setFlash('message2', 'Proses transfer table gagal. Data gagal disimpan.');                
                } 
                            
                return $this->redirect(['page/view-table', 'id' => $modelMtableSession->id]); 
            } else {
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Tagihan sudah dicetak');
                Yii::$app->session->setFlash('message2', 'Proses transfer meja tidak bisa dilakukan.');
                
                return $this->redirect(['view-table', 'id' => $post['sessionMtableId']]);
            }   
        }
    }
    
    public function actionSubmitJumlahGuest() {
        $post = Yii::$app->request->post();
        
        if (!empty($post)) {
            $modelMtableSession = MtableSession::findOne($post['sessionMtable']);
            $modelMtableSession->jumlah_guest = $post['inputJumlahTamu'];
            $modelMtableSession->nama_tamu = $post['inputNamaTamu'];
            
            if ($modelMtableSession->save()) {
                return true;
            } else {
                return false;       
            }            
        }
        
        return false;
    }
    
    public function actionTransferMenu() {
        $post = Yii::$app->request->post();
                
        if (!empty($post['menu'])) {
            if (!$post['billPrinted']) {
            
                $transaction = Yii::$app->db->beginTransaction();
                $flag = true;                                 

                foreach ($post['menu'] as $menuOrder) {
                    $modelMtableOrders = MtableOrder::findOne($menuOrder['inputId']);
                    $modelMtableOrders->mtable_session_id = $post['mtableSessionId'];
                    $modelMtableOrders->menu_id = $menuOrder['inputMenuId'];
                    $modelMtableOrders->jumlah = $menuOrder['inputMenuQty'];
                    $modelMtableOrders->discount = $menuOrder['inputMenuDiscount'];
                    $modelMtableOrders->harga_satuan = $menuOrder['inputMenuHarga'];

                    if (!($flag = $modelMtableOrders->save())) {
                        break;
                    }                              
                }

                if ($flag) {
                    $transaction->commit();                
                } else {
                    $transaction->rollBack();
                }

                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Tagihan sudah dicetak');
                Yii::$app->session->setFlash('message2', 'Proses transfer menu tidak bisa dilakukan.');
                
                return $this->redirect(['view-table', 'id' => $post['activeMtableSessionId']]);
            }   
        }
    }
    
    public function actionJoinMtable() {
        $post = Yii::$app->request->post();                
        
        if (!empty($post)) {
            if (!$post['billPrinted']) {

                $transaction = Yii::$app->db->beginTransaction();
                $flag = true;                                                         
                $isJoined = false;

                $modelMtableSessionActive = MtableSession::findOne($post['activeMtableSessionId']);            

                if (!$modelMtableSessionActive->is_join_mtable) {

                    $modelMtableSession = MtableSession::findOne($post['mtableSessionId']);

                    $modelMTableJoin = null;

                    if ($modelMtableSession->is_join_mtable) {
                        $modelMTableJoin = MtableJoin::findOne($modelMtableSession->mtable_join_id);
                        $modelMtableSessionActive->mtable_join_id = $modelMTableJoin->id;
                    } else {
                        $modelMTableJoin = new MtableJoin();
                        $modelMTableJoin->active_mtable_session_id = $modelMtableSession->id;

                        if (($flag = $modelMTableJoin->save())) {
                            $modelMtableSession->is_join_mtable = true;
                            $modelMtableSession->mtable_join_id = $modelMTableJoin->id;

                            $flag = $modelMtableSession->save();
                        }

                        $modelMtableSessionActive->mtable_join_id = $modelMTableJoin->id;
                    }                    

                    $modelMtableSessionActive->is_join_mtable = true;

                    if ($flag) {
                        if (($flag = $modelMtableSessionActive->save())) {
                            if (!empty($post['menu'])) {
                                foreach ($post['menu'] as $menuOrder) {
                                    $modelMtableOrders = MtableOrder::findOne($menuOrder['inputId']);
                                    $modelMtableOrders->mtable_session_id = $modelMTableJoin->active_mtable_session_id;

                                    if (!($flag = $modelMtableOrders->save())) {
                                        break;
                                    }                              
                                }
                            }
                        }
                    }
                } else {
                    $flag = false;
                    $isJoined = true;
                }

                if ($flag) {
                    Yii::$app->session->setFlash('status', 'success');
                    Yii::$app->session->setFlash('message1', 'Gabung Meja Sukses');
                    Yii::$app->session->setFlash('message2', 'Proses gabung meja sukses. Data telah berhasil disimpan.');   

                    $transaction->commit();     

                    return $this->redirect(['page/view-table', 'id' => $modelMtableSession->id]);
                } else {
                    Yii::$app->session->setFlash('status', 'danger');
                    Yii::$app->session->setFlash('message1', 'Gabung Meja Gagal');
                    Yii::$app->session->setFlash('message2', 'Proses gabung meja gagal. Data gagal disimpan.');    

                    if ($isJoined)
                        Yii::$app->session->setFlash('message2', 'Proses gabung meja gagal. Meja ini sudah tergabung dalam meja lain.');    

                    $transaction->rollBack();

                    return $this->redirect(['page/view-table', 'id' => $modelMtableSessionActive->id]);
                }  
            } else {
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Tagihan sudah dicetak');
                Yii::$app->session->setFlash('message2', 'Proses transfer menu tidak bisa dilakukan.');
                
                return $this->redirect(['view-table', 'id' => $post['activeMtableSessionId']]);
            }  
        }
    }
    
    public function actionCloseTable() {
        $post = Yii::$app->request->post();        
        
        if (!empty($post)) {
            if (!$post['billPrinted']) {

                $transaction = Yii::$app->db->beginTransaction();
                $flag = true;

                $modelMtableSession = MtableSession::findOne($post['sessionMtable']);            

                if ($modelMtableSession->is_join_mtable) {
                    $modelMtableSessionTemp = MtableSession::find()->andWhere(['mtable_join_id' => $modelMtableSession->mtable_join_id])->all();

                    foreach ($modelMtableSessionTemp as $dataMtableSessionTemp) {
                        $dataMtableSessionTemp->is_closed = 1;
                        $dataMtableSessionTemp->closed_at = date('Y-m-d H:i:s');
                        $dataMtableSessionTemp->user_closed = Yii::$app->user->identity->id;                                        

                        if (!($flag = $dataMtableSessionTemp->save())) {
                            break;
                        }
                    }                                
                } else {
                    $modelMtableSession->is_closed = 1;
                    $modelMtableSession->closed_at = date('Y-m-d H:i:s');
                    $modelMtableSession->user_closed = Yii::$app->user->identity->id;

                    $flag = $modelMtableSession->save();
                }

                if ($flag &&!empty($post['menu']) && count($post['menu']) > 0) {
                    foreach ($post['menu'] as $menuOrder) {
                        $modelMtableOrder = MtableOrder::findOne($menuOrder['inputId']);
                        $modelMtableOrder->discount_type = $menuOrder['inputMenuDiscountType'];
                        $modelMtableOrder->discount = $menuOrder['inputMenuDiscount'];
                        $modelMtableOrder->catatan = $menuOrder['inputMenuCatatan'];
                        $modelMtableOrder->is_void = $menuOrder['inputMenuVoid'];
                        $modelMtableOrder->void_at = date('Y-m-d H:i:s');
                        $modelMtableOrder->user_void = Yii::$app->user->identity->id;    

                        if (!($flag = $modelMtableOrder->save())) {
                            break;
                        }
                    }
                }

                if ($flag) {                
                    $transaction->commit();

                    Yii::$app->session->setFlash('status', 'success');
                    Yii::$app->session->setFlash('message1', 'Close Table Sukses');
                    Yii::$app->session->setFlash('message2', 'Proses close table sukses. Data telah berhasil disimpan.');   

                    return $this->redirect(['page/index']);
                } else {                
                    $transaction->rollBack();

                    Yii::$app->session->setFlash('status', 'danger');
                    Yii::$app->session->setFlash('message1', 'Close Table Gagal');
                    Yii::$app->session->setFlash('message2', 'Proses close table gagal. Data gagal disimpan.');    

                    return $this->redirect(['page/view-table', 'id' => $post['sessionMtable']]);
                }
            } else {
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Tagihan sudah dicetak');
                Yii::$app->session->setFlash('message2', 'Proses close table tidak bisa dilakukan.');
                
                return $this->redirect(['view-table', 'id' => $post['sessionMtable']]);
            }
        }
    }
    
    public function actionUnlockBill() {
        $post = Yii::$app->request->post();
        if (!empty($post)) {
            $modelMtableSession = MtableSession::findOne($post['sessionMtable']);
            $modelMtableSession->bill_printed = 0;
            
            if ($modelMtableSession->save()) {
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Unlock Bill Sukses');
                Yii::$app->session->setFlash('message2', 'Proses unlock bill sukses. Data telah berhasil disimpan.');
            } else {
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Unlock Bill Gagal');
                Yii::$app->session->setFlash('message2', 'Proses unlock bill gagal. Data gagal disimpan.');
            }
            
            return $this->redirect(['view-table', 'id' => $post['sessionMtable']]);
        }
    }
    
    public function actionGetMenuCondiment() {
        $post = Yii::$app->request->post();
        
        $modelMenu = MenuCondiment::find()
                ->joinWith([
                    'menu',
                    'menu.menuReceipts',
                    'menu.menuReceipts.itemSku',
                    'menu.menuReceipts.itemSku.stocks',
                    'menu.menuCategory',
                    'menu.menuCategory.printer0',
                    'menu.menuCategory.parentCategory' => function($query) {
                        $query->from('menu_category parent_menu_category');
                    },
                    'menu.menuCategory.parentCategory.printer0' => function($query) {
                        $query->from('printer parent_printer');
                    },                    
                ])
                ->andWhere(['menu_condiment.parent_menu_id' => $post['parent_menu_id']])
                ->andWhere(['IS NOT', 'menu.harga_jual', NULL])
                ->andWhere(['menu.not_active' => false])
                ->asArray()->all(); 
                
        return $this->renderPartial('get_menu_condiment', [
            'modelMenu' => $modelMenu,
        ]);
    }
    
    public function actionGetMenu() {
        $post = Yii::$app->request->post();
        
        $modelMenu = Menu::find()
                ->joinWith([
                    'menuReceipts',
                    'menuReceipts.itemSku',
                    'menuReceipts.itemSku.stocks',
                    'menuCategory',
                    'menuCategory.printer0',
                    'menuCategory.parentCategory' => function($query) {
                        $query->from('menu_category parent_menu_category');
                    },
                    'menuCategory.parentCategory.printer0' => function($query) {
                        $query->from('printer parent_printer');
                    },                    
                ])
                ->andWhere(['menu.menu_category_id' => $post['id']])
                ->andWhere(['IS NOT', 'menu.harga_jual', NULL])
                ->andWhere(['menu.not_active' => false])
                ->asArray()->all();                    
                
        return $this->renderPartial('get_menu', [
            'modelMenu' => $modelMenu,
            'cid' => MenuCategory::find()->andWhere(['id' => $post['id']])->asArray()->one()['parent_category_id'],
        ]);
    }
        
    public function actionGetMenuCategory() {
        $post = Yii::$app->request->post();
        
        $modelMenuCategory = MenuCategory::find();
        
        if (!empty($post['id']))
            $modelMenuCategory = $modelMenuCategory->andWhere(['menu_category.parent_category_id' => $post['id']]);
        else
            $modelMenuCategory = $modelMenuCategory->andWhere(['IS', 'menu_category.parent_category_id', NULL]);
                
        $modelMenuCategory = $modelMenuCategory->andWhere(['menu_category.not_active' => false])->asArray()->all();
            
        return $this->renderPartial('get_menu_category', [
            'modelMenuCategory' => $modelMenuCategory,
            'pid' => !empty($post['id']) ? $post['id'] : null,
        ]);
    }           
    
    public function actionSearchMenu() {
        $post = Yii::$app->request->post();
        
        $modelMenu = Menu::find()
                ->joinWith([
                    'menuReceipts',
                    'menuReceipts.itemSku',
                    'menuReceipts.itemSku.stocks',
                    'menuCategory',
                    'menuCategory.printer0',
                    'menuCategory.parentCategory' => function($query) {
                        $query->from('menu_category parent_menu_category');
                    },
                    'menuCategory.parentCategory.printer0' => function($query) {
                        $query->from('printer parent_printer');
                    },                    
                ])
                ->andFilterWhere(['like', 'menu.nama_menu', $post['namaMenu']])
                ->andWhere(['IS NOT', 'menu.harga_jual', NULL])
                ->andWhere(['menu.not_active' => false])
                ->asArray()->all();                    
                
        return $this->renderPartial('search_menu', [
            'modelMenu' => $modelMenu,
        ]);
    }
            
    public function actionGetMtable() {
        $post = Yii::$app->request->post();
        
        $modelMtable = Mtable::find()
                ->joinWith([
                    'mtableSessions' => function($query) {
                        $query->onCondition('mtable_session.is_closed = FALSE');
                    },
                    'mtableCategory',
                ])
                ->andWhere(['!=', 'mtable_category.not_active', 1])
                ->asArray()->all();
        
        return $this->renderPartial('get_mtable', [
            'modelMtable' => $modelMtable,
            'type' => !empty($post['type']) ? $post['type'] : '',
            'mtableId' => !empty($post['table']) ? $post['table'] : '',
            'row' => !empty($post['row']) ? $post['row'] : '',
        ]);
    }
    
    public function actionGetInfoMtable() {
        
        if (($post = Yii::$app->request->post())) {
            
            $modelMtable = Mtable::find()
                    ->joinWith([
                        'mtableCategory',
                        'mtableSessions' => function($query) {
                            $query->onCondition('mtable_session.is_closed = FALSE');
                        },
                        'mtableSessions.mtableJoin',
                        'mtableSessions.mtableJoin.activeMtableSession' => function($query) {
                            $query->from('mtable_session active_mtable_session');
                        },
                        'bookings' => function($query) {
                            $query->onCondition('booking.is_closed = FALSE');
                        },
                    ])
                    ->andWhere(['mtable.id' => $post['idTable']])
                    ->asArray()->one();        
                           
            return $this->renderPartial('get_info_mtable', [
                'modelMtable' => $modelMtable,
            ]);
        }                
    }
    
    public function actionBooking($id) {
        
        $model = new Booking();
        $post = Yii::$app->request->post();
        
        if ($model->load($post)) {
            $flag = false;
            if (($model->id = Settings::getTransNumber('no_booking')) !== false) {
                $flag = $model->save();
            }
            
            if ($flag) {
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Booking Sukses');
                Yii::$app->session->setFlash('message2', 'Proses booking sukses. Data telah berhasil disimpan.');                                
            } else {
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Booking Gagal');
                Yii::$app->session->setFlash('message2', 'Proses booking gagal. Data gagal disimpan.');                
            }                        
        }
        
        if (!empty($post['layout'])) 
            return $this->redirect(['index2']);
        else
            return $this->redirect(['index', 'cid' => $post['cid']]);
    }
    
    public function actionListBooking($id) {
        $query = Booking::find()
                ->joinWith([
                    'mtable', 
                    'mtable.mtableSessions' => function($query) {
                        $query->onCondition('mtable_session.is_closed = FALSE');
                    },
                ])
                ->andWhere(['booking.mtable_id' => $id])
                ->andWhere(['booking.is_closed' => false]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => array(
                'pageSize' => 10,
            ),
        ]); 
        
        return $this->renderPartial('list_booking', [
            'dataProvider' => $dataProvider,            
        ]);
    }
    
    public function actionConfirmBooking($id) {
        $modelBooking = Booking::findOne($id);
        $modelBooking->is_closed = true;
        
        if ($modelBooking->save()) {
            Yii::$app->session->setFlash('status', 'success');
            Yii::$app->session->setFlash('message1', 'Confirm Booking Sukses');
            Yii::$app->session->setFlash('message2', 'Proses confirm booking sukses. Data telah berhasil disimpan.'); 
            
            return $this->redirect(['page/open-table', 'id' => $modelBooking->mtable_id]);
        } else {
            Yii::$app->session->setFlash('status', 'danger');
            Yii::$app->session->setFlash('message1', 'Confirm Booking Gagal');
            Yii::$app->session->setFlash('message2', 'Proses confirm booking gagal. Data gagal disimpan.');
            
            return $this->redirect(['index']);
        }          
    }
    
    public function actionMenuQueueSave() {                
        
        if (!empty(($post = Yii::$app->request->post()))) {
        
            if (!empty($post['menu'])) {  
                if (!$post['billPrinted']) {
                    $transaction = Yii::$app->db->beginTransaction();
                    $flag = true;                 

                    $modelMenuQueue = [];                    

                    foreach ($post['menu'] as $key => $menuOrder) {
                        $modelMenuQueue[$key] = new MenuQueue();
                        $modelMenuQueue[$key]->mtable_order_id = $menuOrder['inputId'];
                        $modelMenuQueue[$key]->menu_id = $menuOrder['inputMenuId'];
                        $modelMenuQueue[$key]->jumlah = $menuOrder['inputMenuQty'];
                        $modelMenuQueue[$key]->keterangan = $menuOrder['inputMenuCatatan']; 
                    }

                    $error = '';
                    $orderQueue = [];
                    
                    foreach ($modelMenuQueue as $key => $menuOrder) {
                        if (!$menuOrder->save()) {
                            if (count($menuOrder->getErrors('mtable_order_id')) > 0) {
                                foreach ($menuOrder->getErrors('mtable_order_id') as $err) {
                                    if (stripos($err, 'has already been taken') !== false )
                                        $error .= '<br>Menu: <b>' . $menuOrder->menu->nama_menu . '</b> (' . $menuOrder->jumlah . ') sudah ada dalam antrian dan sudah diprint.';
                                }
                            } else {
                                $flag = false;
                                break;
                            }
                        }
                        
                        $orderQueue[$key]['menu'] = (!empty($menuOrder->mtableOrder->parent_id) ? '(+) ' : '') .  $menuOrder->menu->nama_menu;
                        $orderQueue[$key]['menuCategory'] = $menuOrder->menu->menuCategory->id . '.' . $menuOrder->menu->menuCategory->nama_category;
                        $orderQueue[$key]['jumlah'] = $menuOrder->jumlah;
                        $orderQueue[$key]['catatan'] = $menuOrder->keterangan;

                        $printer = [];
                        foreach ($menuOrder->menu->menuCategory->menuCategoryPrinters as $value) {
                            $printer[] = $value['printer'];
                        }

                        $orderQueue[$key]['printer'] = $printer;
                    }                    
                    
                    if ($flag) {
                        if ($error != '') {
                            Yii::$app->session->setFlash('errorOrderQueue', 'Proses print tidak bisa dilakukan.' . $error);
                            Yii::$app->session->setFlash('orderQueueStatus', false);                                                
                        } else {
                            Yii::$app->session->setFlash('orderQueueStatus', true);                        
                        }

                        Yii::$app->session->setFlash('orderQueue', $orderQueue);

                        $transaction->commit();                                               
                    } else {
                        Yii::$app->session->setFlash('status', 'danger');
                        Yii::$app->session->setFlash('message1', 'Antrian Menu Gagal');
                        Yii::$app->session->setFlash('message2', 'Proses antrian menu gagal. Data gagal disimpan.');

                        $transaction->rollBack();
                    }
                } else {
                    Yii::$app->session->setFlash('status', 'danger');
                    Yii::$app->session->setFlash('message1', 'Tagihan sudah dicetak');
                    Yii::$app->session->setFlash('message2', 'Proses antrian menu tidak bisa dilakukan.');
                }   
            }

            return $this->redirect(['page/view-table', 'id' => $post['sessionMtable']]);
        }                        
    }
    
    public function actionMenuQueue() {                
        
        if (!empty(($post = Yii::$app->request->post()))) {
            $modelMenuQueue = MenuQueue::findOne($post['queueId']);
            $modelMenuQueue->is_finish = true;
            
            if ($modelMenuQueue->save()) {
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Finishing Menu Antrian Sukses');
                Yii::$app->session->setFlash('message2', 'Proses finishing menu antrian sukses. Data telah berhasil disimpan.'); 
            } else {
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Finishing Menu Antrian Gagal');
                Yii::$app->session->setFlash('message2', 'Proses finishing menu antrian gagal. Data gagal disimpan.' . $error);
            }
            
            return $this->redirect(['page/menu-queue']);
        }
        
        $query = MenuQueue::find()
                ->joinWith([
                    'menu', 
                    'mtableOrder',
                    'mtableOrder.mtableSession',
                ])
                ->andWhere(['menu_queue.is_finish' => false])
                ->andWhere(['mtable_session.is_closed' => false])
                ->andWhere(['>', 'menu_queue.jumlah', 0]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => array(
                'pageSize' => 20,
            ),
        ]); 
        
        return $this->render('menu_queue', [
            'dataProvider' => $dataProvider,            
        ]);
    }
    
    public function actionMenuQueueFinished() {                
        
        if (!empty(($post = Yii::$app->request->post()))) {
            $modelMenuQueue = MenuQueue::findOne($post['queueId']);
            $modelMenuQueue->is_send = true;
            
            if ($modelMenuQueue->save()) {
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Sendind Menu Antrian Sukses');
                Yii::$app->session->setFlash('message2', 'Proses sending menu antrian sukses. Data telah berhasil disimpan.'); 
            } else {
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Sendind Menu Antrian Gagal');
                Yii::$app->session->setFlash('message2', 'Proses sending menu antrian gagal. Data gagal disimpan.' . $error);
            }
            
            return $this->redirect(['page/menu-queue-finished']);
        }
        
        $query = MenuQueue::find()
                ->joinWith([
                    'menu', 
                    'mtableOrder',
                    'mtableOrder.mtableSession',
                ])
                ->andWhere(['menu_queue.is_finish' => true])
                ->andWhere(['menu_queue.is_send' => false])
                ->andWhere(['mtable_session.is_closed' => false])
                ->andWhere(['>', 'menu_queue.jumlah', 0]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => array(
                'pageSize' => 20,
            ),
        ]); 
        
        return $this->render('menu_queue_finished', [
            'dataProvider' => $dataProvider,            
        ]);
    }        
    
    public function actionVerifyEmployee() {
        if (!empty(($post = Yii::$app->request->post()))) {
            if (($model = Employee::findOne($post['kdKaryawan'])) !== null) {
                if ($post['jmlBayar'] > $model->sisa) {
                    return 'jmlBayar';
                } else {
                    return true;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
                
    }
    
    public function actionVerifyVoucher() {
        if (!empty(($post = Yii::$app->request->post()))) {
            if (($model = Voucher::findOne($post['kdVoucher'])) !== null) {     
                $date = strtotime(date('Y-m-d'));
                $from = strtotime($model->start_date);
                $to = strtotime($model->end_date);
                
                if ($model->not_active)
                    return 'not_active';
                
                if ($post['jmlBayar'] > $model->jumlah_voucher)
                    return 'exceed';
                
                if (($date >= $from) && ($date <= $to))
                    return true;
                else
                    return 'date';
            } else {
                return false;
            }
        } else {
            return false;
        }                
    }
    
    public function actionDiscount() {
        return true;
    }
    
    public function actionDiscountbill() {
        return true;
    }
    
    public function actionFreeMenu() {
        return true;
    }
    
    public function actionVoidMenu() {
        return true;
    }
    
    public function actionOpenCashdrawer() {
        return true;
    }
    
    public function actionPrintInvoice() {
        return true;
    }
    
    public function actionPrintOrder() {
        return true;
    }
    
    public function actionAuthorize() {
        if (!empty($post = Yii::$app->request->post())) {
            if (($modelUser = User::findOne($post['userId'])) !== null) {
            
                if ($modelUser->validatePassword($post['password']) && !$modelUser->not_active) {
                    if ($modelUser->userLevel->is_super_admin) {
                        return true;
                    } else {
                        $userAkses = \backend\models\UserAkses::find()
                            ->joinWith(['userLevel', 'userAppModule'])
                            ->andWhere(['user_akses.user_level_id' => $modelUser->userLevel->id])
                            ->andWhere(['user_akses.is_active' => true])
                            ->asArray()->all();
                        
                        foreach ($userAkses as $value) {
                            if (
                                    ($value['userAppModule']['nama_module'] === 'page' 
                                    && $value['userAppModule']['module_action'] === $post['type']
                                    && $value['userAppModule']['sub_program'] === 'front') 
                                ) {

                                return true;
                            }
                        }
                                                
                        return 'errorAccess';
                    }
                } else {
                    return 'errorPass';
                }            
            } else {
                return 'errorUser';
            }
        } else {
            throw new ForbiddenHttpException('You are not allowed to perform this action.');
        }
    }
}
