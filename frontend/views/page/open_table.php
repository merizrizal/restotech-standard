<?php
use yii\helpers\Html; 
use kartik\money\MaskMoney;
use backend\components\Tools;
use frontend\components\NotificationDialog; 
use backend\components\PrinterDialog;
use backend\components\VirtualKeyboard;

Yii::$app->formatter->timeZone = 'Asia/Jakarta';

Tools::loadIsIncludeScp();

$status = Yii::$app->session->getFlash('status');
$message1 = Yii::$app->session->getFlash('message1');
$message2 = Yii::$app->session->getFlash('message2');

if ($status !== null) : 
    $notif = new NotificationDialog([
        'status' => $status,
        'message1' => $message1,
        'message2' => $message2,
    ]);

    $notif->theScript();
    echo $notif->renderDialog();

endif;

if (!empty(($orderQueue = Yii::$app->session->getFlash('orderQueue')))): ?>
    
    <table id="tempOrderQueue" style="display: none">
        
        <?php
        foreach ($orderQueue as $value): ?>                     
        
            <tr id="queueRow">
                <td id="menu"><?= $value['menu'] ?></td>
                <td id="jumlah"><?= $value['jumlah'] ?></td>
                <td id="catatan"><?= $value['catatan'] ?></td>
                <td>
                    <?php
                    
                    echo Html::hiddenInput('inputMenuCategoryId', $value['menuCategory'], ['id' => 'inputMenuCategoryId']);
                    
                    foreach ($value['printer'] as $printer) {
                        echo Html::hiddenInput('inputMenuCategoryPrinter', $printer, ['id' => 'inputMenuCategoryPrinter', 'class' => 'inputMenuCategoryPrinter']);
                    } ?>
                </td>
            </tr>
        
        <?php
        endforeach; ?>
            
    </table>

<?php
endif;

if (($orderQueueStatus = Yii::$app->session->getFlash('orderQueueStatus'))) {
    echo Html::hiddenInput('orderQueueStatus', '1', ['id' => 'orderQueueStatus']);
}

$this->title = 'Open Table';

$temp = $modelSettings;
$modelSettings = [];
foreach ($temp as $value) {
    $modelSettings[$value['setting_name']] = $value['setting_value'];
}

if ($modelTable->not_ppn)
    $modelSettings['tax_amount'] = 0;

if ($modelTable->not_service_charge)
    $modelSettings['service_charge_amount'] = 0;

$this->params['tableInfo'] = '
    <div class="weather-2 pn">
        <div class="weather-2-header">
            <div class="row data">
                <div class="col-sm-6 col-xs-6 goleft">
                    <span class="badge" style="margin: 0 0 10px 5px; font-size: 20px">' . $modelTable->id . '</span>                                               
                </div>
            </div>
        </div><!-- /weather-2 header -->
        <div class="row data centered">
            <div class="col-sm-12 col-xs-12">
                <img src="' . Yii::getAlias('@backend-web') . '/img/mtable/thumb120x120' . $modelTable->image . '" class="img-circle" width="120">	
            </div>
        </div>
        <div class="row data">
            <div class="col-sm-6 col-xs-6 goleft">
                <h4><b>' . $modelTable->nama_meja . '</b></h4>
                <h6>' . $modelTable->kapasitas . ' chair</h6>
            </div>
        </div>
    </div>                           
' ; 

if (!empty($settingsArray)) {
    echo Html::textarea('strukInvoiceHeader', $settingsArray['struk_invoice_header'], ['id' => 'strukInvoiceHeader', 'style' => 'display:none']);
    echo Html::textarea('strukInvoiceFooter', $settingsArray['struk_invoice_footer'], ['id' => 'strukInvoiceFooter', 'style' => 'display:none']);
    echo Html::textarea('strukOrderHeader', $settingsArray['struk_order_header'], ['id' => 'strukOrderHeader', 'style' => 'display:none']);
    echo Html::textarea('strukOrderFooter', $settingsArray['struk_order_footer'], ['id' => 'strukOrderFooter', 'style' => 'display:none']); 
} 

$guestFormJml = '';
$guestFormNamaTamu = '';
$guestFormAction = '';

$unlockBill = false;

if (!empty($modelMtableSession)) {
    echo Html::hiddenInput('sessionMtable', $modelMtableSession->id, ['id' => 'sessionMtable', 'class' => 'sessionMtable']);  
    
    echo Html::hiddenInput('billPrinted', $modelMtableSession->bill_printed, ['id' => 'billPrinted']);
    
    $guestFormJml = $modelMtableSession->jumlah_guest;
    $guestFormNamaTamu = $modelMtableSession->nama_tamu;
    $guestFormAction = Yii::$app->urlManager->createUrl(['page/submit-jumlah-guest']);
    
    if ($modelMtableSession->bill_printed) 
        $unlockBill = true;
}

echo Html::beginForm($guestFormAction, 'post', ['id' => 'formJumlahGuest', 'style' => 'display:none']);
echo Html::hiddenInput('inputJumlahTamu', $guestFormJml, ['id' => 'inputJumlahTamu']);
echo Html::hiddenInput('inputNamaTamu', $guestFormNamaTamu, ['id' => 'inputNamaTamu']);
echo Html::endForm();    



echo Html::beginForm(Yii::$app->urlManager->createUrl(['page/split-menu']), 'post', ['id' => 'formSplit', 'style' => 'display:none']);
echo Html::hiddenInput('tableId', $modelTable->id);
echo Html::endForm();

echo Html::beginForm(Yii::$app->urlManager->createUrl(['page/close-table']), 'post', ['id' => 'formCloseTable', 'style' => 'display:none']); 
echo Html::endForm();

echo Html::beginForm(Yii::$app->urlManager->createUrl(['page/menu-queue-save']), 'post', ['id' => 'formMenuQueue', 'style' => 'display:none']); 
echo Html::endForm(); ?>


<table id="temp" style="display: none">
    <tr id="menuRow" style="cursor: pointer">
        <td id="menu" class="goleft">
            <span></span>
        </td>
        <td id="qty" class="centered">
            <span></span>
        </td>
        <td id="subtotal" class="goright">
            <span id="spanDiscount">Disc: <span id="valDiscount">0</span></span>
            <br>
            <span id="spanSubtotal"></span>
        </td>
    </tr>
</table>


<div class="col-lg-12">

    <div class="row mt">
        <div class="col-md-12 col-sm-12 mb">
            <div class="white-panel pn" style="height: auto">
                <div class="white-header"></div>
                <div style="padding: 0 10px 15px 10px">
                    <div class="row goleft">
                        <div class="col-md-12 col-sm-12 btnMenu">
                            <button id="btnCatatanMenu" class="btn btn-primary btn-lg" type="button"><i class="ion ion-ios-list-outline" style="font-size: 16px; color: white"></i> Catatan Menu</button>                            
                            <button id="btnJumlahTamu" class="btn btn-primary btn-lg" type="button"><i class="ion ion-ios-people" style="font-size: 16px; color: white"></i> Jumlah Tamu</button>
                            <a id="btnCashdrawer" class="btn btn-primary btn-lg" href="<?= Yii::$app->urlManager->createUrl(['page/open-cashdrawer']) ?>"><i class="ion ion-eject" style="font-size: 16px; color: white"></i> Cashdrawer</a>                                                        
                            <a id="btnPrintInvoice" class="btn btn-primary btn-lg" href="<?= Yii::$app->urlManager->createUrl(['page/print-invoice']) ?>"><i class="ion ion-ios-printer" style="font-size: 16px; color: white"></i> Cetak Tagihan</a>                          
                            <button id="btnAntrianMenu" class="btn btn-primary btn-lg" type="button"><i class="ion ion-ios-paper-outline" style="font-size: 16px; color: white"></i> Antrian Menu</button>                            
                            <button id="btnCloseTable" class="btn btn-danger btn-lg" type="button"><i class="ion ion-ios-upload" style="font-size: 16px; color: white"></i> Close Table</button>
                        </div>                                                   
                    </div>
                    
                    <div class="row goleft" style="margin-top: 10px">
                        <div class="col-md-12 col-sm-12 btnMenu">
                            <a id="btnVoid" class="btn btn-danger btn-lg" href="<?= Yii::$app->urlManager->createUrl(['page/void-menu']) ?>"><i class="ion ion-backspace" style="font-size: 16px; color: white"></i> Void Menu</a>                            
                            <button id="btnSplit" class="btn btn-primary btn-lg" type="button"><i class="ion ion-android-done-all" style="font-size: 16px; color: white"></i> Split</button>
                            <a id="btnTransMeja" class="btn btn-primary btn-lg" href="<?= Yii::$app->urlManager->createUrl(['page/get-mtable']) ?>"><i class="ion ion-arrow-return-right" style="font-size: 16px; color: white"></i> Transfer Meja</a>
                            <a id="btnTransMenu" class="btn btn-primary btn-lg" href="<?= Yii::$app->urlManager->createUrl(['page/get-mtable']) ?>"><i class="ion ion-arrow-swap" style="font-size: 16px; color: white"></i> Transfer Menu</a>
                            <a id="btnJoinTable" class="btn btn-primary btn-lg" href="<?= Yii::$app->urlManager->createUrl(['page/get-mtable']) ?>"><i class="ion ion-arrow-shrink" style="font-size: 16px; color: white"></i> Gabung Meja</a>
                            <a id="btnFreeMenu" class="btn btn-primary btn-lg" href="<?= Yii::$app->urlManager->createUrl(['page/free-menu']) ?>"><i class="ion ion-bag" style="font-size: 16px; color: white"></i> Free Menu</a>                            
                        </div>                                                    
                    </div>
                    
                    <div class="row goleft" style="margin-top: 10px">
                        <div class="col-md-12 col-sm-12 btnMenu">
                            <a id="btnDiscount" class="btn btn-primary btn-lg" href="<?= Yii::$app->urlManager->createUrl(['page/discount']) ?>"><i class="ion ion-ios-pricetags" style="font-size: 16px; color: white"></i> Discount Menu</a>                            
                            <a id="btnDiscountBill" class="btn btn-primary btn-lg" href="<?= Yii::$app->urlManager->createUrl(['page/discountbill']) ?>"><i class="ion ion-ios-pricetags" style="font-size: 16px; color: white"></i> Discount Bill</a>                                                        
                            
                            <?php
                            if ($unlockBill) 
                                echo Html::a('<i class="ion ion-unlocked" style="font-size: 16px; color: white"></i> Unlock Bill', Yii::$app->urlManager->createUrl(['page/unlock-bill']), ['id' => 'btnUnlockBill', 'class' => 'btn btn-danger btn-lg']) ?>                                
                        </div>                                                    
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="weather-2 pn" style="height: auto; padding: 0 0 20px 0">
                <div class="weather-2-header">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">                            
                            <p>
                                <a id="btnPayment" class="btn btn-danger btn-lg" href=""><i class="ion ion-cash" style="font-size: 16px; color: white"></i> Payment</a>
                                &nbsp; &nbsp; &nbsp;
                                Table: <?= '(' . $modelTable->id . ') ' . $modelTable->nama_meja ?>
                            </p>
                        </div>
                    </div>                    
                </div>
                
                
                <div class="row data mt">
                    <?= Html::beginForm('', 'post', ['id' => 'formMenuOrder']); ?>
                    
                    <?php 
                    echo Html::hiddenInput('tableId', $modelTable->id, ['id' => 'tableId']);
                    echo Html::hiddenInput('userActive', Yii::$app->session->get('user_data')['employee']['nama'], ['id' => 'userActive']);                                       
                    
                    $discBill = 0;
                    $discBillType = '';
                    $discBillValue = 0;
                    
                    if (!empty($modelMtableSession)) {
                        echo Html::hiddenInput('tglJamPesan', Yii::$app->formatter->asDatetime($modelMtableSession->opened_at), ['id' => 'tglJam']); 
                        
                        $discBill = empty($modelMtableSession->discount) ? 0 : $modelMtableSession->discount;
                        $discBillType = $modelMtableSession->discount_type;                        

                        if ($discBillType == 'percent') {                                                    
                            $discBillValue = $discBill * 0.01 * $modelMtableSession->jumlah_harga; 
                        } else if ($discBillType == 'value') {
                            $discBillValue = $discBill;                                                
                        }                                                
                    } ?>
                    
                    
                    <div class="col-lg-4">
                        <div class="white-panel pn" style="height: auto; color: #000">
                            <div class="white-header" style="padding: 10px">
                                <div class="row">
                                    <div class="col-md-7 goleft">
                                        <button id="btnQtyPlus" class="btn btn-primary btn-sm btnQty" type="button"><i class="fa fa-plus" style="font-size: 12px; color: white"></i></button>
                                        <button id="btnQtyMinus" class="btn btn-danger btn-sm btnQty" type="button"><i class="fa fa-minus" style="font-size: 12px; color: white"></i></button>
                                        <button id="btnDeleteOrder" class="btn btn-danger btn-sm" type="button"><i class="fa fa-trash" style="font-size: 12px; color: white"></i>Delete</button>
                                    </div>
                                    <div class="col-md-5 goright">
                                        <button id="btnSelectAll" class="btn btn-primary btn-sm" type="button">
                                            <i class="fa fa-check-square-o" style="font-size: 12px; color: white"></i>All
                                            
                                        </button>
                                        <button id="btnUnselectAll" class="btn btn-primary btn-sm" type="button">
                                            <i class="fa fa-square-o" style="font-size: 12px; color: white"></i>All
                                        </button>
                                    </div>
                                </div>                                
                            </div>
                            
                            
                            
                            <div class="table-responsive">
                                <div class="goleft" style="margin: 10px">
                                    <?= Html::submitButton('<i class="fa fa-check" style="color: white"></i> Submit', ['class' => 'btn btn-success']) ?>
                                    &nbsp; &nbsp;
                                    <?= Html::a('<i class="fa fa-undo" style="color: white"></i> Back', Yii::$app->urlManager->createUrl(['page/index', 'cid' => $modelTable->mtable_category_id]), ['class' => 'btn btn-danger']) ?>
                                </div> 
                                
                                <table class="table table-advance table-hover">
                                    <thead>
                                        <tr>
                                            <th class="goleft">Menu</th>
                                            <th class="centered" style="width: 60px">Qty</th>
                                            <th class="goright" style="width: 35%">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyOrderMenu">
                                        <?php                                        
                                        
                                        $i = 0;

                                        $jumlah_harga = 0;
                                        $serviceCharge = 0;
                                        $pajak = 0;
                                        $grandTotal = 0;                                                                                
                                        
                                        $totalDisc = 0;

                                        $totalFreeMenu = 0;
                                        $totalVoid = 0;

                                        if (count($modelMtableOrders) > 0): 
                                            
                                            foreach ($modelMtableOrders as $mtableOrderData):          
                                                $data = [];
                                                
                                                if (!empty($mtableOrderData['mtableOrders'])) {
                                                    $data[] = $mtableOrderData;
                                                    $data = array_merge($data, $mtableOrderData['mtableOrders']);
                                                } else {
                                                    $data[] = $mtableOrderData;
                                                }
                                                
                                                foreach ($data as $mtableOrderData): 

                                                    $freeMenu = $mtableOrderData->is_free_menu ? 'FreeMenu' : ''; 

                                                    $subtotal = $mtableOrderData->jumlah * $mtableOrderData->harga_satuan;

                                                    if ($mtableOrderData->is_free_menu) 
                                                        $totalFreeMenu += $subtotal;

                                                    if ($mtableOrderData->is_void) 
                                                        $totalVoid += $subtotal;    

                                                    if ($mtableOrderData->discount_type == 'percent') {                                                    
                                                        $disc = $mtableOrderData->discount * 0.01 * $subtotal;
                                                        $subtotal = $subtotal - $disc; 
                                                        $totalDisc += $disc;
                                                    } else if ($mtableOrderData->discount_type == 'value') {
                                                        $disc = $mtableOrderData->jumlah * $mtableOrderData->discount;
                                                        $subtotal = $subtotal - $disc;                                                
                                                        $totalDisc += $disc;
                                                    }                                                                                                

                                                    if (!$mtableOrderData->is_free_menu && !$mtableOrderData->is_void) 
                                                        $jumlah_harga += $subtotal; ?>

                                                    <tr id="menuRow" class="<?= ($mtableOrderData->is_void ? 'voided' : '') . ' ' .  ($mtableOrderData->is_free_menu ? 'free-menu' : '') ?>" style="cursor: pointer">
                                                        <td id="menu" class="goleft">
                                                            <span><?= (!empty($mtableOrderData->parent_id) ? '<i class="fa fa-plus" style="color:green"></i>' : '') . $mtableOrderData->menu->nama_menu ?></span>
                                                            <?= Html::hiddenInput('menu[' . $i .'][inputMenuId]', $mtableOrderData->menu_id, ['id' => 'inputMenuId']) ?>
                                                            <?= Html::hiddenInput('menu[' . $i .'][inputId]', $mtableOrderData->id, ['id' => 'inputId', 'class' => 'inputId']) ?>
                                                            <?= Html::hiddenInput('menu[' . $i .'][inputMenuCatatan]', $mtableOrderData->catatan, ['id' => 'inputMenuCatatan', 'class' => 'inputMenuCatatan']) ?>
                                                            <?= Html::hiddenInput('menu[' . $i .'][inputParentId]', $mtableOrderData->parent_id, ['id' => 'inputParentId', 'class' => 'inputParentId']) ?>

                                                            <?php
                                                            if (!empty($mtableOrderData->menu->menuCategory->menuCategoryPrinters)) {
                                                                foreach ($mtableOrderData->menu->menuCategory->menuCategoryPrinters as $value) {
                                                                    if (!$value['printer0']['not_active'])
                                                                        echo Html::hiddenInput('menu[' . $i .'][inputMenuCategoryPrinter]', $value['printer'], ['id' => 'inputMenuCategoryPrinter', 'class' => 'inputMenuCategoryPrinter']);
                                                                }
                                                            } ?>                                                        

                                                            <?php
                                                            $badgeMenuQueue = '';

                                                            if (!empty($mtableOrderData->menuQueue)) {
                                                                echo Html::hiddenInput('menu[' . $i .'][inputMenuQueueIsFinish]', $mtableOrderData->menuQueue->is_finish, ['id' => 'inputMenuQueueIsFinish', 'class' => 'inputMenuQueueIsFinish']);

                                                                if ($mtableOrderData->menuQueue->is_finish)
                                                                    $badgeMenuQueue = '<div class="badge bg-success"><i class="fa fa-thumbs-up" style="color:#000"></i></div>';
                                                                else
                                                                    $badgeMenuQueue = '<div class="badge bg-important"><i class="fa fa-thumbs-o-up" style="color:#FFF"></i></div>';
                                                            } ?>

                                                        </td>
                                                        <td id="qty" class="centered">
                                                            <?= Html::hiddenInput('menu[' . $i .'][inputMenuQty]', $mtableOrderData->jumlah, ['id' => 'inputMenuQty', 'class' => 'inputMenuQty']) ?>
                                                            <?= Html::hiddenInput('menu[' . $i .'][inputMenuHarga]', $mtableOrderData->harga_satuan, ['id' => 'inputMenuHarga', 'class' => 'inputMenuHarga']) ?>
                                                            <?= Html::hiddenInput('menu[' . $i .'][inputMenuDiscountType]', $mtableOrderData->discount_type, ['id' => 'inputMenuDiscountType', 'class' => 'inputMenuDiscountType']) ?>
                                                            <?= Html::hiddenInput('menu[' . $i .'][inputMenuDiscount]', $mtableOrderData->discount, ['id' => 'inputMenuDiscount', 'class' => 'inputMenuDiscount']) ?>
                                                            <?= Html::hiddenInput('menu[' . $i .'][inputMenuVoid]', $mtableOrderData->is_void, ['id' => 'inputMenuVoid', 'class' => 'inputMenuVoid']) ?>
                                                            <?= Html::hiddenInput('menu[' . $i .'][inputMenuFreeMenu]', $mtableOrderData->is_free_menu, ['id' => 'inputMenuFreeMenu', 'class' => 'inputMenuFreeMenu']) ?>
                                                            <span><?= $mtableOrderData->jumlah ?></span>
                                                            <br>
                                                            <?= $badgeMenuQueue ?>                                                        
                                                        </td>
                                                        <td id="subtotal" class="goright">
                                                            <span id="spanDiscount">Disc: <span id="valDiscount"><?= $mtableOrderData->discount ?></span></span>
                                                            <br>
                                                            <span id="spanSubtotal"><?= $subtotal ?></span>
                                                        </td>
                                                    </tr>

                                                    <?php
                                                    $i++;

                                                endforeach;                                                    
                                                
                                            endforeach;

                                            $scp = Tools::hitungServiceChargePajak($jumlah_harga, $modelSettings['service_charge_amount'], $modelSettings['tax_amount']);                                        
                                            $serviceCharge = $scp['serviceCharge'];
                                            $pajak = $scp['pajak']; 
                                            $grandTotal = $jumlah_harga + $serviceCharge + $pajak - $discBillValue;

                                        endif; ?>

                                    </tbody>
                                    <tfoot>
                                        <tr id="freeMenuRow">
                                            <td class="goleft">Total Free Menu</td>
                                            <td colspan="2" class="goright">
                                                <span>(</span><span id="total-free-menu"><?= $totalFreeMenu ?></span><span>)</span>
                                            </td>
                                            <?= Html::hiddenInput('total-free-menu-input', $totalFreeMenu, ['id' => 'total-free-menu-input']) ?>
                                        </tr>
                                        <tr id="freeMenuRow">
                                            <td class="goleft">Total Void</td>
                                            <td colspan="2" class="goright">
                                                <span>(</span><span id="total-void"><?= $totalVoid ?></span><span>)</span>
                                            </td>
                                            <?= Html::hiddenInput('total-void-input', $totalVoid, ['id' => 'total-void-input']) ?>
                                        </tr>
                                        <tr style="font-weight: bold; font-size: 16px">
                                            <td class="goleft">Total</td>
                                            <td colspan="2" id="total-harga" class="goright"><?= $jumlah_harga ?></td>
                                            <?= Html::hiddenInput('total-harga-input', $jumlah_harga, ['id' => 'total-harga-input']) ?>                                            
                                        </tr>                                        
                                        <tr>
                                            <td class="goleft">Service (<?= $modelSettings['service_charge_amount'] ?> %)</td>
                                            <td colspan="2" id="service-charge-amount" class="goright"><?= $serviceCharge ?></td>
                                            <?= Html::hiddenInput('serviceChargeAmount', $modelSettings['service_charge_amount'], ['id' => 'serviceChargeAmount']) ?>
                                        </tr>
                                        <tr>
                                            <td class="goleft">Ppn (<?= $modelSettings['tax_amount'] ?> %)</td>
                                            <td colspan="2" id="tax-amount" class="goright"><?= $pajak ?></td>
                                            <?= Html::hiddenInput('taxAmount', $modelSettings['tax_amount'], ['id' => 'taxAmount']) ?>
                                        </tr>  
                                        <tr>
                                            <?php
                                            $discBillText = '';
                                            if ($discBillType === 'percent')
                                                $discBillText = '(' . $discBill . '%)'; ?>
                                            
                                            <td class="goleft">Discount Bill <span id="discBillText"><?= $discBillText ?></span></td>
                                            <td colspan="2" class="goright">
                                                (<span id="discbill"><?= $discBillValue ?></span>)
                                            </td>
                                            <?= Html::hiddenInput('discBill', $discBill, ['id' => 'discBill']) ?>
                                            <?= Html::hiddenInput('discBillType', $discBillType, ['id' => 'discBillType']) ?>
                                        </tr>
                                        <tr style="font-weight: bold; font-size: 16px">
                                            <td class="goleft">Grand Total</td>
                                            <td colspan="2" id="grand-harga" class="goright"><?= $grandTotal ?></td>
                                            <?= Html::hiddenInput('grand-harga-input', $grandTotal, ['id' => 'grand-harga-input']) ?>
                                            <?= Html::hiddenInput('total-disc-input', $totalDisc, ['id' => 'total-disc-input']) ?>                                            
                                        </tr>
                                    </tfoot>
                                </table>                            
                            
                                <div class="goleft" style="margin: 10px">
                                    <?= Html::submitButton('<i class="fa fa-check" style="color: white"></i> Submit', ['class' => 'btn btn-success']) ?>
                                    &nbsp; &nbsp;
                                    <?= Html::a('<i class="fa fa-undo" style="color: white"></i> Back', Yii::$app->urlManager->createUrl(['page/index', 'cid' => $modelTable->mtable_category_id]), ['class' => 'btn btn-danger']) ?>
                                </div>                                
                            </div>
                        </div>
                    </div>
                    
                    <?= Html::hiddenInput('indexMenu', $i, ['id' => 'indexMenu']) ?>
                    
                    <?= Html::endForm(); ?>                    

                    <div class="col-lg-8">
                        <div class="darkblue-panel pn" style="height: auto; padding: 0 10px 10px 10px">
                            <div class="darkblue-header" style="padding-top: 10px; text-align: left">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-lg-3 text-right">
                                            <?= Html::label('Search Menu', 'searchMenu', ['class' => 'control-label', 'style' => 'color:white;font-size:18px']) ?>
                                        </div>                                    
                                        <div class="col-lg-9">
                                            <div class="input-group">
                                                <?= Html::textInput('searchMenu', null, ['class' => 'form-control', 'id' => 'searchMenu']) ?>
                                                <span class="input-group-btn">
                                                    <button class="btn btn-success" type="button"><i class="glyphicon glyphicon-search"></i></button>
                                                    <button id="cancelSearchMenu" class="btn btn-danger" type="button"><i class="glyphicon glyphicon-remove"></i></button>
                                                </span>
                                            </div>
                                        </div>                                                                        
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-lg-12">
                                        <button id="add" class="addCondiment btn btn-success btn-lg btn-block" type="button"><i class="glyphicon glyphicon-plus"></i> Add Condiment</button>
                                        <button id="cancel" class="addCondiment btn btn-danger btn-lg btn-block" type="button" style="display: none"><i class="glyphicon glyphicon-remove"></i> Cancel Condiment</button>
                                        <?= Html::hiddenInput('valAddCondiment', "", ['id' => 'valAddCondiment']) ?>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="darkblue-header" style="padding-top: 10px; text-align: left">
                                <?= Html::a('<i class="fa fa-chevron-circle-left"></i> Back', "#", ['class' => 'btn btn-primary', 'id' => 'btnMenuBack', 'style' => 'display:none']) ?>                                
                            </div>
                            <div id="menu-container" class="row" style="height: auto; max-height: 400px; overflow-x: hidden">
                                <br><br><br><br><br><br><br><br><br><br>
                            </div>
                            
                            <div class="overlay"></div>
                            <div class="loading-img"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal Verify User Pass -->
<div class="modal fade" id="modalUserPass" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Verify Access</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-4 col-sm-4"><?= Html::label('User ID', 'userId', ['class' => 'control-label']) ?></div>
                        <div class="col-md-8 col-sm-8">
                            <?= Html::textInput('userId', null, [
                                'id' => 'userId',
                                'class' => 'form-control keyboardUserPass showKeyboard',
                            ]); ?>                     
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-4 col-sm-4"><?= Html::label('Password', 'password', ['class' => 'control-label']) ?></div>
                        <div class="col-md-8 col-sm-8">                           
                            <?= Html::passwordInput('passsword', null, [
                                'id' => 'password',
                                'class' => 'form-control keyboardUserPass showKeyboard',
                            ]); ?>                           
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> &nbsp; Close</button>
                <button id="submitUserPass" type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-check"></i> &nbsp;Submit</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Discount -->
<div class="modal fade" id="modalDiscount" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Input Discount</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 col-sm-4"></div>
                    <div class="col-md-8 col-sm-8">
                        <?= Html::radio('discountType', true, [
                                'label' => 'Percent',
                                'value' => 'percent',
                                'id' => 'discountTypePercent'
                            ]); ?>
                        
                        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
                        
                        <?= Html::radio('discountType', false, [
                                'label' => 'Value',
                                'value' => 'value',
                                'id' => 'discountTypeValue'
                            ]); ?>                        
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 col-sm-4"><?= Html::label('Discount', 'discount', ['class' => 'control-label']) ?></div>
                    <div class="col-md-8 col-sm-8">
                        <?= MaskMoney::widget(['name' => 'discount', 'value' => 0, 'options' => [
                                'id' => 'discount',
                                'class' => 'form-control keyboardDisc showKeyboard',
                            ]]) ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> &nbsp; Close</button>
                <button id="submitDiscount" type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-check"></i> &nbsp;Submit</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmation -->
<div class="modal fade" id="modalConfirmation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalConfirmationTitle"></h4>
            </div>
            <div class="modal-body" id="modalConfirmationBody">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> &nbsp; Close</button>
                <button id="submitConfirmation" type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-check"></i> &nbsp;Ok</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmation Danger -->
<div class="modal fade" id="modalConfirmationDanger" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalConfirmationTitle"></h4>
            </div>
            <div class="modal-body" id="modalConfirmationBody">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> &nbsp; Close</button>
                <button id="submitConfirmation" type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-check"></i> &nbsp;Ok</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Info -->
<div class="modal fade" id="modalInfo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Info</h4>
            </div>
            <div class="modal-body" id="modalInfoBody">                
                Tidak ada yang dipilih
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> &nbsp; Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Alert -->
<div class="modal fade" id="modalAlert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Warning</h4>
            </div>
            <div class="modal-body" id="modalAlertBody">                
                Tidak ada yang dipilih
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> &nbsp; Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Custom -->
<div class="modal fade" id="modalCustom" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalCustomTitle">Warning</h4>
            </div>
            <div class="modal-body" id="modelCustomBody">                                
                <div id="content"><br><br><br><br><br><br><br><br><br></div>
                <div id="overlayModalCustom" class="overlay"></div>
                <div id="loadingModalCustom" class="loading-img"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> &nbsp; Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Custom without close Button -->
<div class="modal fade" id="modalCustomNoClose" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-inverse">                
                <h4 class="modal-title" id="modalCustomTitle">Warning</h4>
            </div>
            <div class="modal-body" id="modelCustomBody">                                
                <div id="content"><br><br><br><br><br><br><br><br><br></div>
                <div id="overlayModalCustom" class="overlay"></div>
                <div id="loadingModalCustom" class="loading-img"></div>
            </div>
            <div class="modal-footer">
                
            </div>
        </div>
    </div>
</div>
<!-- BUTTON SCROLL
<div style="position: fixed; z-index: 133; bottom: 200px; right: 20px">
    <a href="" id="scrollUp" class="btn btn-lg btn-primary"><i class="fa fa-arrow-up"></i></a>
</div>

<div style="position: fixed; z-index: 133; bottom: 100px; right: 20px">
    <a href="" id="scrollDown" class="btn btn-lg btn-primary"><i class="fa fa-arrow-down"></i></a>
</div>
-->

<?php
$printerDialog = new PrinterDialog();
$printerDialog->theScript();
echo $printerDialog->renderDialog('pos');

$virtualKeyboard = new VirtualKeyboard();
$virtualKeyboard->registerCssFile();
$virtualKeyboard->registerJsFile();

$this->params['regCssFile'][] = function() {
    $this->registerCssFile(Yii::getAlias('@common-web') . '/css/keyboard/keyboard.min.css');
}; 

$this->params['regJsFile'][] = function() {
    $this->registerJsFile(Yii::getAlias('@common-web') . '/js/plugins/keyboard/js/jquery.keyboard.min.js');
    $this->registerJsFile(Yii::getAlias('@common-web') . '/js/plugins/keyboard/js/jquery.keyboard.extension-typing.min.js');
    $this->registerJsFile(Yii::getAlias('@common-web') . '/js/plugins/iCheck/icheck.min.js');
    $this->registerJsFile(Yii::getAlias('@common-web') . '/js/plugins/jquery-currency/jquery.currency.js');
};

$jscriptOrderQueue = '';
if (!empty($orderQueue)) {
    $jscriptOrderQueue = '
        var printOrder = function() {
            getDateTime();

            var header = "";
            header += "\n" + $("textarea#strukOrderHeader").val() + "\n";
            header += separatorPrint(40, "-") + "\n";
            header += "Tanggal/Jam Print" + separatorPrint(spaceLength - "Tanggal/Jam Print".length) + ": " + datetime + "\n";
            header += separatorPrint(40, "-") + "\n";
            header += "Meja" + separatorPrint(spaceLength - "Meja".length) + ": " + $("input#tableId").val() + "\n";
            header += "Tanggal/Jam Open" + separatorPrint(spaceLength - "Tanggal/Jam Open".length) + ": " + $("input#tglJam").val() + "\n";
            header += "Kasir" + separatorPrint(spaceLength - "Kasir".length) + ": " + $("input#userActive").val() + "\n";                                

            header += separatorPrint(40, "-") + "\n";
            header += separatorPrint(14) + "Menu Pesanan \n";                        
            header += separatorPrint(40, "-") + "\n";           


            var content = [];

            $("table#tempOrderQueue tr#queueRow").each(function() {

                var thisObj = $(this);

                $(this).find("input.inputMenuCategoryPrinter").each(function() {
                    var printer = $(this).val();
                    
                    if (content[printer] === undefined)
                        content[printer] = "";

                    var menu = thisObj.find("td#menu").html();
                    var qty = thisObj.find("td#jumlah").html();
                    var separatorLength = 40 - (menu.length + qty.length);                                        

                    content[printer] += menu + separatorPrint(separatorLength) + qty + "\n";

                    var catatan = thisObj.find("td#catatan").html();                                     
                    content[printer] += catatan + "\n";                      
                });
            });

            var footer = "";
            footer += separatorPrint(40, "-") + "\n";
            footer += $("textarea#strukOrderFooter").val() + "\n";
            
            printContentToServer(header, footer, content);            
        };
        
        var reprintOrder = function() {
            $.ajax({
                cache: false,
                type: "POST",
                url: "' . Yii::$app->urlManager->createUrl(['page/print-order']) . '",
                success: function(response) {
                    printOrder();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    if (xhr.status == "403") {                
                        showModalUserPass(printOrder, "print-order");
                    }
                }
            });
        };
        
        if ($("input#orderQueueStatus").val() == "1") {
            printOrder();
        } else {
            $("#modalConfirmationDanger #modalConfirmationTitle").html("Order Sudah Diprint. Lanjutkan print ?");
            $("#modalConfirmationDanger #modalConfirmationBody").html("' . Yii::$app->session->getFlash('errorOrderQueue') . '");
            $("#modalConfirmationDanger").modal();
            
            $("#modalConfirmationDanger #submitConfirmation").on("click", function(e) {
                reprintOrder();
                
                $(this).off("click");
            });
        }
    ';
}

$jscript = '           
    
    var datetime;
    var getDateTime = function() {  
        datetime = 0;
        $.when(
            $.ajax({
                async: false,
                type: "GET",
                url: "' . Yii::$app->urlManager->createUrl(['page/get-datetime']) . '",            
                success: function(data) {
                    datetime = data.datetime;
                }
            })
        ).done(function() {
            return datetime;
        });
    };
    
    ' . $virtualKeyboard->keyboardQwerty('#searchMenu') . '
        
    var searchMenu = function(namaMenu) {
        var csrfToken = $(\'meta[name="csrf-token"]\').attr("content");
        
        $.ajax({
            cache: false,
            type: "POST",
            data: {"_csrf" : csrfToken, "namaMenu" : namaMenu},
            url: "' . Yii::$app->urlManager->createUrl(['page/search-menu']) . '",
            beforeSend: function(xhr) {
                $(".overlay").show();
                $(".loading-img").show();
            },
            success: function(response) {
                $("#menu-container").html(response);
                $(".overlay").hide();
                $(".loading-img").hide();                                        
            }
        });
    };
    
    var hitungDiscBill = function() {
        var discountType = $("input#discBillType").val();
        var discount = $("input#discBill");                            
        var harga = parseFloat($("#total-harga-input").val());
        
        var hargaDisc = 0; 
        
        if (discountType == "percent") {
            hargaDisc = parseFloat(discount.val()) * 0.01 * harga 
        } else if (discountType == "value") {
            hargaDisc = parseFloat(discount.val()); 
        }
        
        return hargaDisc;
    };            
    
    var showModalUserPass = function(execute, type) {
        $("#modalUserPass input#userId").val("");
        $("#modalUserPass input#password").val("");
        $("#modalUserPass").modal();
        
        $("#modalUserPass #submitUserPass").on("click", function(event) {
            $.ajax({
                cache: false,
                type: "POST",
                data: {
                    "userId": $("#modalUserPass input#userId").val(),
                    "password": $("#modalUserPass input#password").val(),
                    "type": type,
                },
                url: "' . Yii::$app->urlManager->createUrl(['page/authorize']) . '",
                success: function(response) {
                    if (response == true) {
                        execute();
                    } else if (response == "errorUser") {
                        $("#modalAlert #modalAlertBody").html("Error.<br>Unregistered User ID.");
                        $("#modalAlert").modal();
                    } else if (response == "errorPass") {
                        $("#modalAlert #modalAlertBody").html("Error.<br>Incorrect Password.");
                        $("#modalAlert").modal();
                    } else if (response == "errorAccess") {
                        $("#modalAlert #modalAlertBody").html("Error.<br>You are not allowed to perform this action.");
                        $("#modalAlert").modal();
                    } 
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $("#modalAlert #modalAlertBody").html("Error");
                    $("#modalAlert").modal();
                }
            });
            
            $(this).off("click");
        });
    };
    
    var catatanMenuModal = function(thisObj, oldValue, title, theFunction) {        
        
        var catatan = $("<input>").attr("class", "form-control keyboard").val(oldValue);
        ' . $virtualKeyboard->keyboardQwerty('catatan', true) . '

        var submit = $("<button>").on("click", function(event) {
            var inputCatatan = $(this).parent().find("input");
            thisObj.each(function() {
                $(this).val(inputCatatan.val());
            });           
            
            $("#modalCustom").modal("hide");
            
            if (theFunction !== undefined)
                theFunction(thisObj);
        })
        .attr("class", "btn btn-primary").append("<i class=\"fa fa-check\"></i>&nbsp; Submit");                                                

        $("#modalCustom #modalCustomTitle").text(title);
        $("#modalCustom #modelCustomBody #content").html("").append(catatan).append(submit);
        $("#modalCustom").modal(); 
    };
        
    var loadMenuCategory = function() {
        $.ajax({
            cache: false,
            type: "POST",
            url: "' . Yii::$app->urlManager->createUrl(['page/get-menu-category']) . '",
            beforeSend: function(xhr) {
                $(".overlay").show();
                $(".loading-img").show();
            },
            success: function(response) {
                $("#menu-container").html(response);
                $("a#btnMenuBack").css("display", "none");
                $(".overlay").hide();
                $(".loading-img").hide();
            }
        });
    };   

    loadMenuCategory();    
    
    var openTable = function() {
        if ($("input#sessionMtable").length == 0) {
            
            var jmlTamu = $("<input>").attr("class", "form-control keyboard jmlTamu").val($("input#inputJumlahTamu").val());
            ' . $virtualKeyboard->keyboardNumeric('jmlTamu', true) . '
            
            var namaTamu = $("<input>").attr("class", "form-control keyboard namaTamu").val($("input#inputNamaTamu").val());
            ' . $virtualKeyboard->keyboardQwerty('namaTamu', true) . '
            
            var label = $("<label>").html("Jumlah Tamu");            
            var label2 = $("<label>").html("Nama Tamu");

            var submit = $("<button>").on("click", function(event) {
                $("input#inputJumlahTamu").val($(this).parent().find("input.jmlTamu").val());
                $("input#inputNamaTamu").val($(this).parent().find("input.namaTamu").val());
                $("#modalCustomNoClose").modal("hide");
                $("form#formJumlahGuest").append($("input#tableId"));
                $("form#formJumlahGuest").submit();
            })
            .attr("class", "btn btn-primary").append("<i class=\"fa fa-check\"></i>&nbsp; Submit");
            
            var back =  $("<a>").attr("class", "btn btn-danger").attr("href", "' . Yii::$app->urlManager->createUrl(['page/index', 'cid' => $modelTable->mtable_category_id]) . '").append("<i class=\"fa fa-undo\"></i>&nbsp; Back");

            $("#modalCustomNoClose #modalCustomTitle").text("Informasi Tamu");
            $("#modalCustomNoClose #modelCustomBody #content").html("").append(label).append(jmlTamu).append("<br>").append(label2).append(namaTamu).append("<br>").append(submit).append("&nbsp;&nbsp;").append(back);
            $("#modalCustomNoClose").modal();
        }
    };
    
    openTable();
    
    $("#searchMenu").on("change", function(event) {
        searchMenu($(this).val());
    });
    
    $("button.addCondiment").on("click", function(event) {    
        if ($(this).attr("id") == "add") {
            if ($("tbody#tbodyOrderMenu").children("tr#menuRow").hasClass("highlight")) {
                var row = $("tbody#tbodyOrderMenu").children("tr#menuRow.highlight");
                if (row.length == 1) {
                    if (row.find("input.inputId").length == 0) {
                        $("#modalAlert #modalAlertBody").html("Harap disubmit dahulu sebelum menambahkan condiment");
                        $("#modalAlert").modal();                          
                    } else if (row.find("input.inputId").length == 1) {
                        if (row.find("input.inputParentId").val() == "") {   
                            
                            $.ajax({
                                cache: false,
                                type: "POST",
                                data: {
                                    "parent_menu_id": row.find("input#inputMenuId").val()
                                },
                                url: "' . Yii::$app->urlManager->createUrl(['page/get-menu-condiment']) . '",
                                beforeSend: function(xhr) {
                                    $(".overlay").show();
                                    $(".loading-img").show();
                                },
                                success: function(response) {
                                    $("#menu-container").html(response);
                                    $("a#btnMenuBack").css("display", "none");
                                    $(".overlay").hide();
                                    $(".loading-img").hide();
                                }
                            });
                            
                            $("input#valAddCondiment").val(row.find("input.inputId").val());
                            $("button.addCondiment").toggle();
                        } else {
                            $("#modalAlert #modalAlertBody").html("Tidak bisa menambahkan condiment ke dalam condiment");
                            $("#modalAlert").modal();
                        }
                    }                    
                } else {
                    $("#modalAlert #modalAlertBody").html("Hanya boleh memilih satu menu order untuk ditambahkan condiment");
                    $("#modalAlert").modal();
                }
            } else {
                $("#modalAlert #modalAlertBody").html("Tidak ada yang dipilih");
                $("#modalAlert").modal();
            }
        } else if ($(this).attr("id") == "cancel") {  
            loadMenuCategory();
            $("input#valAddCondiment").val("");
            $("tbody#tbodyOrderMenu").children("tr#menuRow.highlight").removeClass("highlight");
            $("button.addCondiment").toggle();
        }
    });
    
    $("#cancelSearchMenu").on("click", function(event) {
        loadMenuCategory();
    });

    $("input.inputMenuDiscountType").each(function() {
        if ($(this).val() == "percent") {

        } else if ($(this).val() == "value") {
            $(this).parent().parent().find("td#subtotal #spanDiscount #valDiscount").currency({' . Yii::$app->params['currencyOptions'] . '});
        }
    });

    $("tbody#tbodyOrderMenu").children("tr#menuRow").children("td#subtotal").children("span#spanSubtotal").currency({' . Yii::$app->params['currencyOptions'] . '});    
    $("#total-free-menu").currency({' . Yii::$app->params['currencyOptions'] . '});
    $("#total-void").currency({' . Yii::$app->params['currencyOptions'] . '});
    $("#total-harga").currency({' . Yii::$app->params['currencyOptions'] . '});
    $("#discbill").currency({' . Yii::$app->params['currencyOptions'] . '});
    $("#service-charge-amount").currency({' . Yii::$app->params['currencyOptions'] . '});
    $("#tax-amount").currency({' . Yii::$app->params['currencyOptions'] . '});
    $("#grand-harga").currency({' . Yii::$app->params['currencyOptions'] . '});

    $("#modalDiscount").on("shown.bs.modal", function () {
        $("input#discount").focus();
    });           

    $(\'input[name="discountType"]\').on("ifChecked", function() {
        var val = parseFloat($("input#discount").val()); 

        if ($(this).val() == "percent") {                    
            $("input#discount-disp").maskMoney({prefix: "", suffix: ""}, val);                
        } else if ($(this).val() == "value") {                    
            $("input#discount-disp").maskMoney({prefix: "Rp. ", suffix: ""}, val);
        }                                

        $("input#discount-disp").maskMoney("mask");
    });        

    $("a#btnDiscount").on("click", function(event) {
        var thisObj = $(this);
        var discountFunction = function() {
            if ($("tbody#tbodyOrderMenu").children("tr#menuRow").hasClass("highlight")) {
                var menu = "";

                $("tbody#tbodyOrderMenu").children("tr#menuRow.highlight").each(function() {  
                    var thisRow = $(this);
                
                    if (thisRow.find("input.inputMenuFreeMenu").val() == 1) {
                        menu += "(" + thisRow.find("td#menu span").text() + ") ";  
                    } else if (thisRow.find("input.inputMenuFreeMenu").val() == 0) {

                        var discountType = thisRow.find("input.inputMenuDiscountType").val();
                        var discount = thisRow.find("input.inputMenuDiscount");                            
                        var qty = parseFloat(thisRow.find("input.inputMenuQty").val());
                        var harga = parseFloat(thisRow.find("input.inputMenuHarga").val());  

                        if (discountType == "percent") {
                            $("input#discountTypePercent").iCheck("check");
                        } else if (discountType == "value") {
                            $("input#discountTypeValue").iCheck("check");
                        }

                        var hargaTemp = 0;

                        if ($("input#discountTypePercent:checked").length === 1) {
                            hargaTemp = harga - (parseFloat(discount.val()) * 0.01 * harga);
                        } else if ($("input#discountTypeValue:checked").length === 1) {                                
                            hargaTemp = harga - parseFloat(discount.val());
                        }                            

                        var jmlHargaTemp = hargaTemp * qty;                                                        

                        $("input#discount").val(discount.val());  
                        $("input#discount-disp").maskMoney("mask", parseFloat(discount.val()));

                        $("#modalDiscount").modal();

                        $("#submitDiscount").on("click", function() {        
                            discount.val(parseFloat($("input#discount").val()));
                            var hargaTemp2 = 0; 

                            if ($("input#discountTypePercent:checked").length === 1) {
                                hargaTemp2 = harga - (parseFloat(discount.val()) * 0.01 * harga); 
                            } else if ($("input#discountTypeValue:checked").length === 1) {
                                hargaTemp2 = harga - parseFloat(discount.val()); 
                            }

                            var jmlHarga = hargaTemp2 * qty;

                            thisRow.find("#subtotal span#spanDiscount span#valDiscount").html(discount.val());

                            if ($("input#discountTypePercent:checked").length === 1) {
                                thisRow.find("input.inputMenuDiscountType").val("percent");
                            } else if ($("input#discountTypeValue:checked").length === 1) {
                                thisRow.find("td#subtotal #spanDiscount #valDiscount").currency({' . Yii::$app->params['currencyOptions'] . '});
                                thisRow.find("input.inputMenuDiscountType").val("value");
                            }                                                                

                            thisRow.find("#subtotal span#spanSubtotal").html(jmlHarga);
                            thisRow.find("#subtotal span#spanSubtotal").currency({' . Yii::$app->params['currencyOptions'] . '});

                            $("#total-harga-input").val(jmlHarga + (parseFloat($("#total-harga-input").val()) - jmlHargaTemp));                                
                            $("#total-harga").html($("#total-harga-input").val());
                            $("#total-harga").currency({' . Yii::$app->params['currencyOptions'] . '});   
                                
                            var scp = hitungServiceChargePajak($("#total-harga-input").val(), $("#serviceChargeAmount").val(), $("#taxAmount").val());

                            var serviceCharge = scp["serviceCharge"];
                            $("#service-charge-amount").html(serviceCharge);
                            $("#service-charge-amount").currency({' . Yii::$app->params['currencyOptions'] . '});

                            var pajak = scp["pajak"];
                            $("#tax-amount").html(pajak);
                            $("#tax-amount").currency({' . Yii::$app->params['currencyOptions'] . '});

                            var grandTotal = parseFloat($("#total-harga-input").val()) + serviceCharge + pajak - hitungDiscBill();
                            $("#grand-harga").html(grandTotal);
                            $("#grand-harga").currency({' . Yii::$app->params['currencyOptions'] . '});

                            $(this).off("click");
                        });
                    }
                });

                if (menu != "") {
                    $("#modalAlert #modalAlertBody").html("<b>" + menu + "</b><br>Tidak bisa melakukan diskon harga pada free menu");
                    $("#modalAlert").modal();
                }
            } else {
                $("#modalAlert #modalAlertBody").html("Tidak ada yang dipilih");
                $("#modalAlert").modal();
            }
        };
        
        $.ajax({
            cache: false,
            type: "POST",
            url: thisObj.attr("href"),
            success: function(response) {
                discountFunction();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                if (xhr.status == "403") {                
                    showModalUserPass(discountFunction, "discount");
                }
            }
        });  
        
        return false;
    });
    
    $("a#btnDiscountBill").on("click", function(event) {
        var thisObj = $(this);
        var discountFunction = function() {
            var discountType = $("input#discBillType").val();
            var discount = $("input#discBill");                            
            var harga = parseFloat($("#total-harga-input").val());  
            
            if (discountType == "percent") {
                $("input#discountTypePercent").iCheck("check");
            } else if (discountType == "value") {
                $("input#discountTypeValue").iCheck("check");
            }                          
            
            $("input#discount").val(discount.val());  
            $("input#discount-disp").maskMoney("mask", parseFloat(discount.val()));
            
            $("#modalDiscount").modal();

            $("#submitDiscount").on("click", function() {        
                discount.val($("input#discount").val());
                
                var hargaDisc = 0; 
                                                            
                if ($("input#discountTypePercent:checked").length === 1) {
                    hargaDisc = parseFloat(discount.val()) * 0.01 * harga 
                    $("#discBillText").html("(" + $("input#discount").val() + "%)");
                    $("input#discBillType").val("percent");
                } else if ($("input#discountTypeValue:checked").length === 1) {
                    hargaDisc = parseFloat(discount.val()); 
                    $("#discBillText").html("");
                    $("input#discBillType").val("value");
                }                                                                   
                
                $("#discbill").html(hargaDisc);
                $("#discbill").currency({' . Yii::$app->params['currencyOptions'] . '});
                    
                var scp = hitungServiceChargePajak(harga, $("#serviceChargeAmount").val(), $("#taxAmount").val());

                var serviceCharge = scp["serviceCharge"];

                var pajak = scp["pajak"];

                var grandTotal = harga + serviceCharge + pajak - hargaDisc;
                $("#grand-harga").html(grandTotal);
                $("#grand-harga").currency({' . Yii::$app->params['currencyOptions'] . '});

                $(this).off("click");
            });
        };
        
        $.ajax({
            cache: false,
            type: "POST",
            url: thisObj.attr("href"),
            success: function(response) {
                discountFunction();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                if (xhr.status == "403") {                
                    showModalUserPass(discountFunction, "discount");
                }
            }
        });  
        
        return false;
    });

    $("a#btnVoid").on("click", function(event) {
        
        var voidMenuFunction = function() {
        
            if ($("tbody#tbodyOrderMenu").children("tr#menuRow").hasClass("highlight")) {            
                var valJmlVoid = 0;
                
                var theFunction = function(thisObj) {
                    
                    var menu = "";
                    var qtyFailed = ""

                    $("tbody#tbodyOrderMenu").children("tr#menuRow.highlight").each(function() {                              

                        if ($(this).find("input.inputId").length == 0 || (parseFloat($(this).find("input.inputMenuQty").val()) - parseFloat(valJmlVoid)) < 0){
                            if ($(this).find("input.inputId").length == 0)
                                menu += "(" + $(this).find("td#menu span").text() + ") ";
                                
                            if ((parseFloat($(this).find("input.inputMenuQty").val()) - parseFloat(valJmlVoid)) < 0)    
                                qtyFailed += "(" + $(this).find("td#menu span").text() + ") ";
                        } else {
                            var discount = $(this).find("input.inputMenuDiscount");
                            var qty = parseFloat(valJmlVoid);
                            var harga = parseFloat($(this).find("input.inputMenuHarga").val());      

                            var hargaTemp = 0;

                            if ($(this).find("input.inputMenuDiscountType").val() == "percent") {
                                hargaTemp = harga - (parseFloat(discount.val()) * 0.01 * harga);
                            } else if ($(this).find("input.inputMenuDiscountType").val() == "value") {
                                hargaTemp = harga - parseFloat(discount.val());
                            }

                            var jmlHargaTemp = hargaTemp * qty;                         
                            var jmlHarga = harga * qty;

                            var totalVoid = parseFloat($("input#total-void-input").val()) + jmlHarga;                                                     

                            $("input#total-void-input").val(totalVoid);
                            $("#total-void").html($("input#total-void-input").val());
                            $("#total-void").currency({' . Yii::$app->params['currencyOptions'] . '});

                            $("#total-harga-input").val(parseFloat($("#total-harga-input").val()) - jmlHargaTemp);
                            $("#total-harga").html($("#total-harga-input").val());
                            $("#total-harga").currency({' . Yii::$app->params['currencyOptions'] . '}); 

                            $(this).find("#subtotal span#spanSubtotal").html(jmlHarga);
                            $(this).find("#subtotal span#spanSubtotal").currency({' . Yii::$app->params['currencyOptions'] . '});
                            $(this).find("#subtotal span#spanDiscount span#valDiscount").html(0);

                            discount.val(0);                            
                            $(this).find("input.inputMenuDiscountType").val("percent");

                            var scp = hitungServiceChargePajak($("#total-harga-input").val(), $("#serviceChargeAmount").val(), $("#taxAmount").val());           

                            var serviceCharge = scp["serviceCharge"];
                            $("#service-charge-amount").html(serviceCharge);
                            $("#service-charge-amount").currency({' . Yii::$app->params['currencyOptions'] . '});

                            var pajak = scp["pajak"];
                            $("#tax-amount").html(pajak);
                            $("#tax-amount").currency({' . Yii::$app->params['currencyOptions'] . '});

                            var grandTotal = parseFloat($("#total-harga-input").val()) + serviceCharge + pajak - hitungDiscBill();
                            $("#grand-harga").html(grandTotal);
                            $("#grand-harga").currency({' . Yii::$app->params['currencyOptions'] . '});

                            $(this).removeClass().addClass("voided");
                            $(this).find("input.inputMenuVoid").val(1);  
                            
                            if ((parseFloat($(this).find("input.inputMenuQty").val()) - parseFloat(valJmlVoid)) > 0) {
                                var indexMenu = parseFloat($("input#indexMenu").val());

                                var menuId = $(this).find("input#inputMenuId");
                                var menuNama = $(this).find("td#menu span");
                                var menuQty = parseFloat($(this).find("input.inputMenuQty").val()) - parseFloat(valJmlVoid);
                                var menuHarga = $(this).find("input#inputMenuHarga");                                               
                                var menuCategoryPrinter = $(this).find("input#inputMenuCategoryPrinter");

                                var subtotalHarga = menuQty * parseFloat(menuHarga.val());                          

                                var scp = hitungServiceChargePajak($("#total-harga-input").val(), $("#serviceChargeAmount").val(), $("#taxAmount").val());                                   

                                var serviceCharge = scp["serviceCharge"];
                                $("#service-charge-amount").html(serviceCharge);
                                $("#service-charge-amount").currency({' . Yii::$app->params['currencyOptions'] . '});

                                var pajak = scp["pajak"];
                                $("#tax-amount").html(pajak);
                                $("#tax-amount").currency({' . Yii::$app->params['currencyOptions'] . '});

                                var grandTotal = parseFloat($("#total-harga-input").val()) + serviceCharge + pajak - hitungDiscBill();
                                $("#grand-harga").html(grandTotal);
                                $("#grand-harga").currency({' . Yii::$app->params['currencyOptions'] . '});

                                var inputMenuQty = $("<input>").attr("type", "hidden").attr("id", "inputMenuQty").attr("class", "inputMenuQty").attr("name", "menu[" + indexMenu + "][inputMenuQty]").attr("value", menuQty);
                                var inputMenuId = $("<input>").attr("type", "hidden").attr("id", "inputMenuId").attr("name", "menu[" + indexMenu + "][inputMenuId]").attr("value", menuId.val());
                                var inputHarga = $("<input>").attr("type", "hidden").attr("id", "inputMenuHarga").attr("class", "inputMenuHarga").attr("name", "menu[" + indexMenu + "][inputMenuHarga]").attr("value", menuHarga.val());
                                var inputDiscountType = $("<input>").attr("type", "hidden").attr("id", "inputMenuDiscountType").attr("class", "inputMenuDiscountType").attr("name", "menu[" + indexMenu + "][inputMenuDiscountType]").attr("value", "percent");  
                                var inputDiscount = $("<input>").attr("type", "hidden").attr("id", "inputMenuDiscount").attr("class", "inputMenuDiscount").attr("name", "menu[" + indexMenu + "][inputMenuDiscount]").attr("value", 0);                
                                var inputVoid = $("<input>").attr("type", "hidden").attr("id", "inputMenuVoid").attr("class", "inputMenuVoid").attr("name", "menu[" + indexMenu + "][inputMenuVoid]").attr("value", 0);                
                                var inputFreeMenu = $("<input>").attr("type", "hidden").attr("id", "inputMenuFreeMenu").attr("class", "inputMenuFreeMenu").attr("name", "menu[" + indexMenu + "][inputMenuFreeMenu]").attr("value", 0);                
                                var inputCatatan = $("<input>").attr("type", "hidden").attr("id", "inputMenuCatatan").attr("class", "inputMenuCatatan").attr("name", "menu[" + indexMenu + "][inputMenuCatatan]").attr("value", "");
                                var inputCategoryPrinter = $("<input>").attr("type", "hidden").attr("id", "inputMenuCategoryPrinter").attr("class", "inputMenuCategoryPrinter").attr("name", "menu[" + indexMenu + "][inputMenuCategoryPrinter]").attr("value", menuCategoryPrinter.val());

                                $("input#indexMenu").val(indexMenu + 1);

                                var comp = $("#temp").clone();
                                comp.children().find("#menu span").html(menuNama.html());
                                comp.children().find("#menu").append(inputMenuId).append(inputCatatan).append(inputCategoryPrinter);
                                comp.children().find("#qty").append(inputMenuQty).append(inputHarga).append(inputDiscount).append(inputVoid).append(inputFreeMenu).append(inputDiscountType);
                                comp.children().find("#qty").find("span").html(menuQty);

                                comp.children().find("#subtotal span#spanSubtotal").append(subtotalHarga);
                                comp.children().find("#subtotal span#spanSubtotal").currency({' . Yii::$app->params['currencyOptions'] . '});

                                $("tbody#tbodyOrderMenu").append(comp.children().html());
                            }
                            
                            $(this).find("input.inputMenuQty").val(qty);
                            $(this).find("td#qty span").html(qty);
                        }                                            
                    });

                    if (menu != "" || qtyFailed != "") {
                        var msg = "";
                        
                        if (menu != "")
                            msg = "<b>" + menu + "</b><br>Tidak bisa melakukan void menu karena data belum disave.<br>Pakai fungsi delete jika ingin mengcancel item order<br>";
                        
                        if (qtyFailed != "")
                            msg += "<b>" + qtyFailed + "</b><br>Tidak bisa melakukan void menu karena jumlah void melebihi jumlah order<br>";

                        $("#modalAlert #modalAlertBody").html(msg);
                        $("#modalAlert").modal();
                    }                                        
                };                                
                
                var jmlVoid = $("<input>").attr("class", "form-control keyboard jmlVoid");
                ' . $virtualKeyboard->keyboardNumeric('jmlVoid', true) . '

                var submit = $("<button>").on("click", function(event) {
                    valJmlVoid = $(this).parent().find("input").val();       

                    $("#modalCustom").modal("hide");
                    $("#modalCustom").on("hidden.bs.modal", function (e) {
                        catatanMenuModal($("tbody#tbodyOrderMenu").children("tr#menuRow.highlight").find("input.inputMenuCatatan"), "", "Alasan Void", theFunction);
                        $("#modalCustom").off("hidden.bs.modal");
                    });                                        
                })
                .attr("class", "btn btn-primary").append("<i class=\"fa fa-check\"></i>&nbsp; Submit");                                                

                $("#modalCustom #modalCustomTitle").text("Jumlah Void");
                $("#modalCustom #modelCustomBody #content").html("").append(jmlVoid).append(submit);
                $("#modalCustom").modal(); 
                                
            } else {
                $("#modalAlert #modalAlertBody").html("Tidak ada yang dipilih");
                $("#modalAlert").modal();
            }
        };
        
        var thisObj = $(this);
        
        $.ajax({
            cache: false,
            type: "POST",
            url: thisObj.attr("href"),
            success: function(response) {
                voidMenuFunction();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                if (xhr.status == "403") {                
                    showModalUserPass(voidMenuFunction, "void-menu");
                }
            }
        });  
        
        return false;
    });

    $("button#btnSplit").on("click", function(event) {
        if ($(document).find("input#sessionMtable").hasClass("sessionMtable")) {
            if ($("tbody#tbodyOrderMenu").children("tr#menuRow").hasClass("highlight")) {

                var menu = "";
                $("tbody#tbodyOrderMenu").children("tr#menuRow.highlight").each(function() {
                    if ($(this).find("input.inputId").length == 0) {
                        menu += "(" + $(this).find("td#menu span").text() + ") ";                            
                    } else {
                        $("form#formSplit").append($(this).html());  
                        $("form#formSplit").append($("input#sessionMtable"));  
                        $("form#formSplit").append($("input#billPrinted"));                          
                        $("form#formSplit").append($("form#formJumlahGuest").html());  
                    }
                });

                if (menu != "") {
                    $("#modalAlert #modalAlertBody").html("<b>" + menu + "</b><br>Tidak bisa melakukan split menu karena data belum disave");
                    $("#modalAlert").modal();
                } else {
                    $("form#formSplit").submit();
                }
            } else {
                $("#modalAlert #modalAlertBody").html("Tidak ada yang dipilih");
                $("#modalAlert").modal();
            }
        } else {
            $("#modalAlert #modalAlertBody").html("Tidak bisa melakukan split karena data belum disave");
            $("#modalAlert").modal();
        }
    });

    $("a#btnTransMeja").on("click", function(event) {
        if ($(document).find("input#sessionMtable").hasClass("sessionMtable")) {
            var menu = "";
            $("tbody#tbodyOrderMenu").children("tr#menuRow").each(function() {
                if ($(this).find("input.inputId").length == 0) {
                    menu += "(" + $(this).find("td#menu span").text() + ") ";                            
                }
            });

            if (menu != "") {
                $("#modalAlert #modalAlertBody").html("<b>" + menu + "</b><br>Tidak bisa melakukan transfer meja karena data belum disave");
                $("#modalAlert").modal();
            } else {
                $("#modalCustom #modalCustomTitle").text("Select Table");
                $("#modalCustom").modal();
                $("#overlayModalCustom").show();
                $("#loadingModalCustom").show();

                var thisObj = $(this);

                $.ajax({
                    cache: false,
                    type: "POST",
                    data: {
                        "type": "close"
                    },
                    url: thisObj.attr("href"),
                    beforeSend: function(xhr) {

                    },
                    success: function(response) {
                        $("#modalCustom #modelCustomBody #content").html(response);   
                        $("#overlayModalCustom").hide();
                        $("#loadingModalCustom").hide();
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        $("#modalCustom").modal("hide");
                        $("#overlayModalCustom").hide();
                        $("#loadingModalCustom").hide();
                        
                        if (xhr.status == "403") {
                            $("#modalAlert #modalAlertBody").html("Error.<br>Forbidden (#403): You are not allowed to perform this action.");
                            $("#modalAlert").modal();
                        }
                    }
                });
            }
        } else {
            $("#modalAlert #modalAlertBody").html("Tidak bisa melakukan transfer table karena data belum disave");
            $("#modalAlert").modal();
        }

        return false;
    });

    $("a#btnTransMenu").on("click", function(event) {
        if ($(document).find("input#sessionMtable").hasClass("sessionMtable")) {
            if ($("tbody#tbodyOrderMenu").children("tr#menuRow").hasClass("highlight")) {
                var menu = "";
                var row = "";
                $("tbody#tbodyOrderMenu").children("tr#menuRow.highlight").each(function() {
                    if ($(this).find("input.inputId").length == 0) {
                        menu += "(" + $(this).find("td#menu span").text() + ") ";                            
                    } else {
                        row += $(this).html();
                    }
                });

                if (menu != "") {
                    $("#modalAlert #modalAlertBody").html("<b>" + menu + "</b><br>Tidak bisa melakukan transfer menu karena data belum disave");
                    $("#modalAlert").modal();
                } else {
                    $("#modalCustom #modalCustomTitle").text("Select Table");
                    $("#modalCustom").modal();
                    $("#overlayModalCustom").show();
                    $("#loadingModalCustom").show();

                    var thisObj = $(this);

                    $.ajax({
                        cache: false,
                        type: "POST",
                        data: {
                            "type": "open",
                            "table": "' . $modelTable->id . '",
                            "row": row,
                        },
                        url: thisObj.attr("href"),
                        beforeSend: function(xhr) {

                        },
                        success: function(response) {
                            $("#modalCustom #modelCustomBody #content").html(response);   
                            $("#overlayModalCustom").hide();
                            $("#loadingModalCustom").hide();
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            $("#modalCustom").modal("hide");
                            $("#overlayModalCustom").hide();
                            $("#loadingModalCustom").hide();
                            
                            if (xhr.status == "403") {
                                $("#modalAlert #modalAlertBody").html("Error.<br>Forbidden (#403): You are not allowed to perform this action.");
                                $("#modalAlert").modal();
                            }
                        }
                    });
                }
            } else {
                $("#modalAlert #modalAlertBody").html("Tidak ada yang dipilih");
                $("#modalAlert").modal();
            }
        } else {
            $("#modalAlert #modalAlertBody").html("Tidak bisa melakukan transfer menu karena data belum disave");
            $("#modalAlert").modal();
        }

        return false;
    });

    $("a#btnJoinTable").on("click", function(event) {
        if ($(document).find("input#sessionMtable").hasClass("sessionMtable")) {
            var menu = "";
            var row = "";
            $("tbody#tbodyOrderMenu").children("tr#menuRow").each(function() {
                if ($(this).find("input.inputId").length == 0) {
                    menu += "(" + $(this).find("td#menu span").text() + ") ";  
                } else {
                    row += $(this).html();
                }
            });

            if (menu != "") {
                $("#modalAlert #modalAlertBody").html("<b>" + menu + "</b><br>Tidak bisa melakukan transfer meja karena data belum disave");
                $("#modalAlert").modal();
            } else {
                $("#modalCustom #modalCustomTitle").text("Select Table");
                $("#modalCustom").modal();
                $("#overlayModalCustom").show();
                $("#loadingModalCustom").show();

                var thisObj = $(this);

                $.ajax({
                    cache: false,
                    type: "POST",
                    data: {
                        "type": "open-join",
                        "table": "' . $modelTable->id . '",
                        "row": row,
                    },
                    url: thisObj.attr("href"),
                    beforeSend: function(xhr) {

                    },
                    success: function(response) {
                        $("#modalCustom #modelCustomBody #content").html(response);   
                        $("#overlayModalCustom").hide();
                        $("#loadingModalCustom").hide();
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        $("#modalCustom").modal("hide");
                        $("#overlayModalCustom").hide();
                        $("#loadingModalCustom").hide();
                        
                        if (xhr.status == "403") {
                            $("#modalAlert #modalAlertBody").html("Error.<br>Forbidden (#403): You are not allowed to perform this action.");
                            $("#modalAlert").modal();
                        }
                    }
                });
            }
        } else {
            $("#modalAlert #modalAlertBody").html("Tidak bisa melakukan transfer table karena data belum disave");
            $("#modalAlert").modal();
        }

        return false;
    });

    $("a#btnFreeMenu").on("click", function(event) {
        var thisObj = $(this);
        var freeMenuFunction = function() {
            if ($("tbody#tbodyOrderMenu").children("tr#menuRow").hasClass("highlight")) {                            
                
                $("#modalConfirmation #modalConfirmationTitle").html("Free Menu");
                $("#modalConfirmation #modalConfirmationBody").html("Free untuk menu ini?");
                $("#modalConfirmation").modal();
                
                $("#modalConfirmation #submitConfirmation").on("click", function() {
                    var theFunction = function(thisObj) {
                        var isFree = false;

                        $("tbody#tbodyOrderMenu").children("tr#menuRow.highlight").each(function() { 
                            if (parseFloat($(this).find("input.inputMenuFreeMenu").val()) == 0) {
                                var discount = $(this).find("input.inputMenuDiscount");
                                var inputQty = $(this).find("input.inputMenuQty");
                                var qty = parseFloat(inputQty.val());
                                var harga = parseFloat($(this).find("input.inputMenuHarga").val());      
                                var inputFreeMenu = $(this).find("input.inputMenuFreeMenu");

                                var hargaTemp = 0;

                                if ($(this).find("input.inputMenuDiscountType").val() == "percent") {
                                    hargaTemp = harga - (parseFloat(discount.val()) * 0.01 * harga);
                                } else if ($(this).find("input.inputMenuDiscountType").val() == "value") {
                                    hargaTemp = harga - parseFloat(discount.val());
                                }

                                var jmlHargaTemp = hargaTemp * qty;   
                                var jmlHarga = harga * qty;

                                var totalFreeMenu = parseFloat($("input#total-free-menu-input").val()) + jmlHarga;                         

                                $(this).attr("class", "free-menu");

                                discount.val(0);
                                $(this).find("input.inputMenuDiscountType").val("percent");

                                inputFreeMenu.val(1);

                                $("input#total-free-menu-input").val(totalFreeMenu);
                                $("#total-free-menu").html($("input#total-free-menu-input").val());
                                $("#total-free-menu").currency({' . Yii::$app->params['currencyOptions'] . '}); 

                                $(this).find("#subtotal span#spanSubtotal").html(jmlHarga);
                                $(this).find("#subtotal span#spanSubtotal").currency({' . Yii::$app->params['currencyOptions'] . '});
                                $(this).find("#subtotal span#spanDiscount span#valDiscount").html(0);

                                $("#total-harga-input").val(parseFloat($("#total-harga-input").val()) - jmlHargaTemp);
                                $("#total-harga").html($("#total-harga-input").val());
                                $("#total-harga").currency({' . Yii::$app->params['currencyOptions'] . '}); 

                                var scp = hitungServiceChargePajak($("#total-harga-input").val(), $("#serviceChargeAmount").val(), $("#taxAmount").val());           

                                var serviceCharge = scp["serviceCharge"];
                                $("#service-charge-amount").html(serviceCharge);
                                $("#service-charge-amount").currency({' . Yii::$app->params['currencyOptions'] . '});

                                var pajak = scp["pajak"];
                                $("#tax-amount").html(pajak);
                                $("#tax-amount").currency({' . Yii::$app->params['currencyOptions'] . '});

                                var grandTotal = parseFloat($("#total-harga-input").val()) + serviceCharge + pajak - hitungDiscBill();
                                $("#grand-harga").html(grandTotal);
                                $("#grand-harga").currency({' . Yii::$app->params['currencyOptions'] . '});

                                discount.parent().parent().find("input").each(function() {
                                    $(this).attr("id", $(this).attr("id") + "FreeMenu");
                                });
                            } else if (parseFloat($(this).find("input.inputMenuFreeMenu").val()) == 1) {
                                isFree = true;
                            }
                        });

                        if (isFree) {
                            $("#modalAlert #modalAlertBody").html("Salah salah menu atau lebih yang Anda pilih sudah dalam free menu");
                            $("#modalAlert").modal();
                        }
                    };
                    
                    catatanMenuModal($("tbody#tbodyOrderMenu").children("tr#menuRow.highlight").find("input.inputMenuCatatan"), "", "Alasan Free Menu", theFunction);
                    
                    $(this).off("click");
                });
            } else {
                $("#modalAlert #modalAlertBody").html("Tidak ada yang dipilih");
                $("#modalAlert").modal();
            }
        };
        
        $.ajax({
            cache: false,
            type: "POST",
            url: thisObj.attr("href"),
            success: function(response) {
                freeMenuFunction();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                if (xhr.status == "403") {
                    showModalUserPass(freeMenuFunction, "free-menu");
                }
            }
        });

        return false;
    });

    $("button#btnCloseTable").on("click", function(event) {
    
        if ($(document).find("input#sessionMtable").hasClass("sessionMtable")) {
                var menu = "";
                $("tbody#tbodyOrderMenu").children("tr#menuRow").each(function() {                        
                    if ($(this).find("input.inputId").length == 0) {
                        menu += "(" + $(this).find("td#menu span").text() + ") ";
                    }
                });
                
                if (menu != "") {
                    var msg = "<b>" + menu + "</b><br>Tidak bisa melakukan close table karena data belum disave.<br>";

                    $("#modalAlert #modalAlertBody").html(msg);
                    $("#modalAlert").modal();
                } else {
                    $("#modalConfirmation #modalConfirmationTitle").html("Close Table");
                    $("#modalConfirmation #modalConfirmationBody").html("Close table ini?");
                    $("#modalConfirmation").modal();

                    $("#modalConfirmation #submitConfirmation").on("click", function() {
                        var theFunction = function() {
                            $("tbody#tbodyOrderMenu").children("tr#menuRow").each(function() {                        
                                $(this).find("input.inputMenuDiscount").val(0);
                                $(this).find("input.inputMenuDiscountType").val("percent");
                                $(this).find("input.inputMenuVoid").val(1);
                            });
                            
                            $("form#formCloseTable").append($("input#sessionMtable")).append($("#tbodyOrderMenu").html());
                            $("form#formCloseTable").append($("input#billPrinted"))
                            $("form#formCloseTable").submit();
                        };

                        catatanMenuModal($("#tbodyOrderMenu").find("input.inputMenuCatatan"), "", "Alasan Close Table", theFunction);

                        $(this).off("click");
                    });
                }
        } else {
            $("#modalAlert #modalAlertBody").html("Tidak bisa melakukan close table karena tidak ada data");
            $("#modalAlert").modal();
        }
    });
    
    $("button#btnCatatanMenu").on("click", function(event) {
        if ($("tbody#tbodyOrderMenu").children("tr#menuRow").hasClass("highlight")) {
            var obj = $("tbody#tbodyOrderMenu").children("tr#menuRow.highlight");                   
            catatanMenuModal(obj.find("input.inputMenuCatatan"), obj.find("input.inputMenuCatatan").val(), "Catatan Menu");
        } else {
            $("#modalAlert #modalAlertBody").html("Tidak ada yang dipilih");
            $("#modalAlert").modal();
        }
    });
    
    $("a#btnCashdrawer").on("click", function(event) {
        var thisObj = $(this);
        var cashdrawerFunction = function() {
            content = [];
            $("input#printerKasir").each(function() {
                content[$(this).val()] = "";
            });
            
            printContentToServer("", "", content, true);
        };
        
        $.ajax({
            cache: false,
            type: "POST",
            url: thisObj.attr("href"),
            success: function(response) {
                cashdrawerFunction();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                if (xhr.status == "403") {                
                    showModalUserPass(cashdrawerFunction, "open-cashdrawer");
                }
            }
        });  
        
        return false;
    });    

    $("a#btnPrintInvoice").on("click", function(event) {
        var thisObj = $(this);
        var functionPrintInvoice = function() {
            if ($(document).find("input#sessionMtable").hasClass("sessionMtable")) {                

                getDateTime();

                var text = "";
                var totalQty = 0;
                var totalSubtotal = 0;

                text += "\n" + $("textarea#strukInvoiceHeader").val() + "\n";
                text += separatorPrint(40, "-") + "\n";            
                text += "Tanggal/Jam Print" + separatorPrint(spaceLength - "Tanggal/Jam Print".length) + ": " + datetime + "\n";
                text += separatorPrint(40, "-") + "\n";
                text += "Meja" + separatorPrint(spaceLength - "Meja".length) + ": " + $("input#tableId").val() + "\n";
                text += "Tanggal/Jam Open" + separatorPrint(spaceLength - "Tanggal/Jam Open".length) + ": " + $("input#tglJam").val() + "\n";
                text += "Kasir" + separatorPrint(spaceLength - "Kasir".length) + ": " + $("input#userActive").val() + "\n";

                text += separatorPrint(40, "-") + "\n"
                text += separatorPrint(16) + "Tagihan \n";                        
                text += separatorPrint(40, "-") + "\n"                        
                
                var arrayMenu = [];
                
                $("tbody#tbodyOrderMenu").children("tr#menuRow").each(function() {
                    
                    //alert($(this).find("input#inputMenuId").val());

                    var discountType = $(this).find("input.inputMenuDiscountType").val();
                    var discount = parseFloat($(this).find("input.inputMenuDiscount").val());
                    var harga = parseFloat($(this).find("input.inputMenuHarga").val());

                    var menu = $(this).find("td#menu span").html().replace("<i class=\"fa fa-plus\" style=\"color:green\"></i>", "(+) ");
                    var qty = $(this).find("td#qty input.inputMenuQty").val();

                    var textDisc = "";

                    if ($(this).find("input.inputMenuVoid").val() == 1) {
                        textDisc = "Void";
                    } else if ($(this).find("input.inputMenuFreeMenu").val() == 1) {
                        textDisc = "Free";
                    } else {
                        if (discount > 0) {
                            if (discountType == "percent") {
                                harga = harga - (discount * 0.01 * harga);

                                textDisc = "Disc: " + discount + "%";
                            } else if (discountType == "value") {
                                harga = harga - discount; 

                                var discSpan = $("<span>").html(discount);
                                discSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                                textDisc = "Disc: " + discSpan.html();
                            }
                        }
                    }

                    var jmlHarga = harga * qty;                                            

                    var hargaItem = $(this).find("td#qty input.inputMenuHarga").val();
                    var hargaSpan = $("<span>").html(hargaItem);
                    hargaSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});

                    var subtotal = jmlHarga;
                    var subtotalSpan = $("<span>").html(subtotal);
                    subtotalSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});

                    totalQty += parseFloat(qty);
                    totalSubtotal += subtotal;

                    var line2 = qty + " X " + hargaSpan.html();                        

                    text += menu + separatorPrint(40 - (menu + textDisc).length) + textDisc + "\n";                    
                    text += line2 + separatorPrint(40 - (line2 + subtotalSpan.html()).length) + subtotalSpan.html() + "\n";
                });

                text += separatorPrint(40, "-") + "\n";

                var totalFreeMenu = parseFloat($("input#total-free-menu-input").val());
                var totalFreeMenuSpan = $("<span>").html(totalFreeMenu);
                totalFreeMenuSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                text += "Free Menu" + separatorPrint(40 - ("Free Menu" + "(" + totalFreeMenuSpan.html() + ")").length) + "(" + totalFreeMenuSpan.html() + ")" + "\n";

                var totalVoid = parseFloat($("input#total-void-input").val());
                var totalVoidSpan = $("<span>").html(totalVoid);
                totalVoidSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                text += "Void Menu" + separatorPrint(40 - ("Void Menu" + "(" + totalVoidSpan.html() + ")").length) + "(" + totalVoidSpan.html() + ")" + "\n";

                totalSubtotal -= (totalFreeMenu + totalVoid);

                var totalSubtotalSpan = $("<span>").html(totalSubtotal);
                totalSubtotalSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});

                var scp = hitungServiceChargePajak(totalSubtotal, $("#serviceChargeAmount").val(), $("#taxAmount").val());

                var scText = "";
                var serviceCharge = 0;
                if (parseFloat($("#serviceChargeAmount").val()) > 0) {
                    serviceCharge = scp["serviceCharge"];
                    var serviceChargeSpan = $("<span>").html(serviceCharge);
                    serviceChargeSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                    var sc = "Service Charge (" + $("#serviceChargeAmount").val() + "%)";
                    
                    scText = sc + separatorPrint(40 - (sc + serviceChargeSpan.html()).length) + serviceChargeSpan.html() +"\n";
                }

                var pjkText = "";
                var pajak = 0;
                if (parseFloat($("#taxAmount").val()) > 0) {
                    pajak = scp["pajak"];
                    var pajakSpan = $("<span>").html(pajak);
                    pajakSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                    var pjk = "Pajak (" + $("#taxAmount").val() + "%)";
                    
                    pjkText = pjk + separatorPrint(40 - (pjk + pajakSpan.html()).length) + pajakSpan.html() +"\n";
                }

                var discBill = hitungDiscBill();
                var discBillSpan = $("<span>").html(discBill);
                discBillSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                discBillSpan.html("(" + discBillSpan.html() + ")");

                var grandTotal = totalSubtotal + serviceCharge + pajak - hitungDiscBill();
                var grandTotalSpan = $("<span>").html(grandTotal);
                grandTotalSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});                        

                text += separatorPrint(40, "-") + "\n";   

                text += "Total item" + separatorPrint(40 - ("Total item" + totalQty).length) + totalQty +"\n";
                text += "Total" + separatorPrint(40 - ("Total" + totalSubtotalSpan.html()).length) + totalSubtotalSpan.html() +"\n";
                text += scText;
                text += pjkText;
                text += "Discount Bill" + separatorPrint(40 - ("Discount Bill" + discBillSpan.html()).length) + discBillSpan.html() +"\n";

                text += separatorPrint(40, "-") + "\n"; 

                text += "Grand Total" + separatorPrint(40 - ("Grand Total" + grandTotalSpan.html()).length) + grandTotalSpan.html() +"\n";

                text += separatorPrint(40, "-") + "\n";         

                var totalDisc = parseFloat($("input#total-disc-input").val());
                var totalDiscSpan = $("<span>").html(totalDisc);
                totalDiscSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                text += "*Total Discount Menu" + separatorPrint(40 - ("*Total Discount Menu" + totalDiscSpan.html()).length) + totalDiscSpan.html() +"\n";

                text += separatorPrint(40, "-") + "\n"; 

                text += $("textarea#strukInvoiceFooter").val() + "\n";                    

                var content = [];

                $("input#printerKasir").each(function() {
                    content[$(this).val()] = text;
                });                

                var tagihanPrinted = function() {
                    var inputBillPrinted = $("<input>").attr("type", "hidden").attr("name", "billPrinted").val("1");
                    $("form#formMenuOrder").append(inputBillPrinted);
                    $("form#formMenuOrder").submit();
                };

                printContentToServer("", "", content, false, tagihanPrinted);                
            } else {
                $("#modalAlert #modalAlertBody").html("Tidak bisa melakukan print tagihan karena data belum disave");
                $("#modalAlert").modal();
            }
        };
        
        if ($("input#billPrinted").val() != 1) {
            functionPrintInvoice();
        } else {
            $("#modalAlert #modalAlertBody").html("Tidak bisa melakukan print tagihan karena tagihan sudah diprint");
            $("#modalAlert").modal();    
            
            $("#modalAlert").on("hidden.bs.modal", function (e) {
                $.ajax({
                    cache: false,
                    type: "POST",
                    url: thisObj.attr("href"),
                    success: function(response) {
                        $("#modalInfo #modalInfoBody").html("User dikenali system.<br>Fungsi print tagihan akan dilanjutkan");
                        $("#modalInfo").modal();
                        
                        $("#modalInfo").on("hidden.bs.modal", function (e) {
                            functionPrintInvoice();
                            
                            $("#modalInfo").off("hidden.bs.modal");
                        });
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        if (xhr.status == "403") {                
                            showModalUserPass(functionPrintInvoice, "print-invoice");
                        }
                    }
                });
                
                $("#modalAlert").off("hidden.bs.modal");
            });            
        }
        
        return false;
    });
    
    $("button#btnAntrianMenu").on("click", function(event) {
        if ($(document).find("input#sessionMtable").hasClass("sessionMtable")) {            
            
            if ($("tbody#tbodyOrderMenu").children("tr#menuRow").hasClass("highlight")) {
                var menu = "";
                $("tbody#tbodyOrderMenu").children("tr#menuRow.highlight").each(function() {
                    if ($(this).find("input.inputId").length == 0) {
                        menu += "(" + $(this).find("td#menu span").text() + ") ";                            
                    } else {
                        $("form#formMenuQueue").append($(this).html());
                    }
                });
                
                $("form#formMenuQueue").append($("input#sessionMtable"));

                if (menu != "") {
                    $("#modalAlert #modalAlertBody").html("<b>" + menu + "</b><br>Tidak bisa kirim antrian menu karena data belum disave");
                    $("#modalAlert").modal();
                } else {
                    $("#modalConfirmation #modalConfirmationTitle").html("Antrian Menu");
                    $("#modalConfirmation #modalConfirmationBody").html("Kirim ke antrian menu di dapur?");
                    $("#modalConfirmation").modal();
                    
                    $("#modalConfirmation #submitConfirmation").on("click", function() {          
                        $("form#formMenuQueue").append($("input#billPrinted"));      
                        $("form#formMenuQueue").submit();
                        $(this).off("click");
                    });
                }
            } else {
                $("#modalAlert #modalAlertBody").html("Tidak ada yang dipilih");
                $("#modalAlert").modal();
            }                                   
        } else {
            $("#modalAlert #modalAlertBody").html("Tidak bisa kirim antrian menu karena data belum disave");
            $("#modalAlert").modal();
        }
    });
    
    $("button#btnJumlahTamu").on("click", function(event) {        
        
        var jmlTamu = $("<input>").attr("class", "form-control jmlTamu2").val($("input#inputJumlahTamu").val());
        ' . $virtualKeyboard->keyboardNumeric('jmlTamu', true) . '        
        
        var namaTamu = $("<input>").attr("class", "form-control keyboard namaTamu2").val($("input#inputNamaTamu").val());
        ' . $virtualKeyboard->keyboardQwerty('namaTamu', true) . '

        var label = $("<label>").html("Jumlah Tamu");
        var label2 = $("<label>").html("Nama Tamu");

        var submit = $("<button>").on("click", function(event) {
            $("input#inputJumlahTamu").val($(this).parent().find("input.jmlTamu2").val());
            $("input#inputNamaTamu").val($(this).parent().find("input.namaTamu2").val());
            $("#modalCustom").modal("hide");
            
            $("form#formJumlahGuest").append($("input#sessionMtable"));
            
             $.ajax({
                type: "POST",
                url: $("form#formJumlahGuest").attr("action"),
                data: $("form#formJumlahGuest").serialize(),
                success: function(data) {
                    if (!data) {
                        $("#modalAlert #modalAlertBody").html("Error !<br>Terjadi kesalahan saat menyimpan data");
                        $("#modalAlert").modal();
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    if (xhr.status == "403") {
                        $("#modalAlert #modalAlertBody").html("Error.<br>Forbidden (#403): You are not allowed to perform this action.");
                        $("#modalAlert").modal();
                    }
                }
            });
        })
        .attr("class", "btn btn-primary").append("<i class=\"fa fa-check\"></i>&nbsp; Submit");                                                

        $("#modalCustom #modalCustomTitle").text("Informasi Tamu");
        $("#modalCustom #modelCustomBody #content").html("").append(label).append(jmlTamu).append("<br>").append(label2).append(namaTamu).append("<br>").append(submit);
        $("#modalCustom").modal();                            
    });
    
    $("a#btnUnlockBill").on("click", function(event) {
        $("form#formJumlahGuest").attr("action", $(this).attr("href"));
        $("form#formJumlahGuest").append($("input#sessionMtable"));
        $("form#formJumlahGuest").submit();
        
        return false;
    });

    $("a#btnPayment").on("click", function(event) {
        var payment = $("<input>").attr("type", "hidden").attr("id", "inputPayment").attr("class", "inputPayment").attr("name", "inputPayment").attr("value", 1);
        $("form#formMenuOrder").append(payment);
        $("form#formMenuOrder").submit();

        return false;
    });    

    $("button.btnQty").on("click", function(event) {
        var menu = "";
        var btnQty = $(this);
        $("tbody#tbodyOrderMenu").children("tr#menuRow.highlight").find("input.inputMenuQty").each(function() {

            if ($(this).attr("name").indexOf("FreeMenu") >= 0) {
                menu += "(" + $(this).parent().parent().find("td#menu span").text() + ") ";  
            } else {

                var qty = 0;
                var op = 0;

                if (btnQty.attr("id") == "btnQtyPlus") {
                    qty = parseFloat($(this).val()) + 1;
                    op = 1;
                } else if (btnQty.attr("id") == "btnQtyMinus") {
                    qty = parseFloat($(this).val()) - 1;
                    op = -1;
                }
                
                var discountType = $(this).parent().parent().find("input.inputMenuDiscountType").val();
                var discount = parseFloat($(this).parent().parent().find("input.inputMenuDiscount").val());
                var harga = parseFloat($(this).parent().parent().find("input.inputMenuHarga").val());
                
                if (discountType == "percent")
                    harga = harga - (discount * 0.01 * harga);                    
                else if (discountType == "value")
                    harga = harga - discount; 
                    
                var jmlHarga = harga * qty;                    

                if (qty > 0) {
                    $(this).val(qty);
                    $(this).parent().find("span").html(qty);
                    $(this).parent().parent().find("#subtotal span#spanSubtotal").html(jmlHarga);
                    $(this).parent().parent().find("#subtotal span#spanSubtotal").currency({' . Yii::$app->params['currencyOptions'] . '});
                        
                    var subtotalFreeMenu = 0;
                        
                    if (parseFloat($(this).parent().parent().find("input.inputMenuFreeMenu").val()) == 1) {
                        
                        var totalFreeMenu = parseFloat($("input#total-free-menu-input").val()) + (harga * op);                         
                        subtotalFreeMenu = harga * op;

                        $("input#total-free-menu-input").val(totalFreeMenu);
                        $("#total-free-menu").html($("input#total-free-menu-input").val());
                        $("#total-free-menu").currency({' . Yii::$app->params['currencyOptions'] . '});
                    }

                    $("#total-harga-input").val(((harga * op) + parseFloat($("#total-harga-input").val())) - subtotalFreeMenu);
                    $("#total-harga").html($("#total-harga-input").val());
                    $("#total-harga").currency({' . Yii::$app->params['currencyOptions'] . '});
                        
                    var scp = hitungServiceChargePajak($("#total-harga-input").val(), $("#serviceChargeAmount").val(), $("#taxAmount").val());           
                        
                    var serviceCharge = scp["serviceCharge"];
                    $("#service-charge-amount").html(serviceCharge);
                    $("#service-charge-amount").currency({' . Yii::$app->params['currencyOptions'] . '});

                    var pajak = scp["pajak"];
                    $("#tax-amount").html(pajak);
                    $("#tax-amount").currency({' . Yii::$app->params['currencyOptions'] . '});

                    var grandTotal = parseFloat($("#total-harga-input").val()) + serviceCharge + pajak - hitungDiscBill();
                    $("#grand-harga").html(grandTotal);
                    $("#grand-harga").currency({' . Yii::$app->params['currencyOptions'] . '});
                }
            }
        });

        if (menu != "") {
            $("#modalAlert #modalAlertBody").html("<b>" + menu + "</b><br>Tidak bisa melakukan penambahan atau pengurangan pada free menu");
            $("#modalAlert").modal();
        }
    });
    
    $("button#btnDeleteOrder").on("click", function(event) {
        if ($("tbody#tbodyOrderMenu").children("tr#menuRow").hasClass("highlight")) {
            var menu = "";
            $("tbody#tbodyOrderMenu").children("tr#menuRow.highlight").each(function() {
                if ($(this).find("input.inputId").length == 0) {
                    var discount = $(this).find("input.inputMenuDiscount");
                    var qty = parseFloat($(this).find("input.inputMenuQty").val());
                    var harga = parseFloat($(this).find("input.inputMenuHarga").val());      

                    var hargaTemp = 0;

                    if ($(this).find("input.inputMenuDiscountType").val() == "percent") {
                        hargaTemp = harga - (parseFloat(discount.val()) * 0.01 * harga);
                    } else if ($(this).find("input.inputMenuDiscountType").val() == "value") {
                        hargaTemp = harga - parseFloat(discount.val());
                    }

                    var jmlHargaTemp = hargaTemp * qty;   

                    $("#total-harga-input").val(parseFloat($("#total-harga-input").val()) - jmlHargaTemp);
                    $("#total-harga").html($("#total-harga-input").val());
                    $("#total-harga").currency({' . Yii::$app->params['currencyOptions'] . '}); 

                    var scp = hitungServiceChargePajak($("#total-harga-input").val(), $("#serviceChargeAmount").val(), $("#taxAmount").val());           

                    var serviceCharge = scp["serviceCharge"];
                    $("#service-charge-amount").html(serviceCharge);
                    $("#service-charge-amount").currency({' . Yii::$app->params['currencyOptions'] . '});

                    var pajak = scp["pajak"];
                    $("#tax-amount").html(pajak);
                    $("#tax-amount").currency({' . Yii::$app->params['currencyOptions'] . '});

                    var grandTotal = parseFloat($("#total-harga-input").val()) + serviceCharge + pajak - hitungDiscBill;
                    $("#grand-harga").html(grandTotal);
                    $("#grand-harga").currency({' . Yii::$app->params['currencyOptions'] . '});

                    $(this).fadeOut(500, function() {
                        $(this).remove();
                    });
                } else {
                    menu += "(" + $(this).find("td#menu span").text() + ") ";
                }
            });

            if (menu != "") {
                $("#modalAlert #modalAlertBody").html("<b>" + menu + "</b><br>Item order tidak bisa didelete karena data sudah disave.<br>Pakai fungsi void untuk mengcancel item order yang telah disave.");
                $("#modalAlert").modal();
            }
        } else {
            $("#modalAlert #modalAlertBody").html("Tidak ada yang dipilih");
            $("#modalAlert").modal();
        }                                   
    });
    
    $(document).on("click", "tbody#tbodyOrderMenu > tr#menuRow", function(event) {
        if ($(this).hasClass("highlight")) {
            $(this).removeClass("highlight");
            
            if (parseFloat($(this).find("input.inputMenuFreeMenu").val()) == 1) {
                $(this).addClass("free-menu");
            }
        } else if ($(this).hasClass("free-menu")) {
            $(this).removeClass("free-menu");
            $(this).addClass("highlight");            
        } else if (!$(this).hasClass("voided")) {
            $(this).addClass("highlight");
        }
    });
    
    $("button#btnSelectAll").on("click", function(event) {
        $("tbody#tbodyOrderMenu").children("tr#menuRow").each(function() {
            if ($(this).hasClass("free-menu")) {
                $(this).removeClass("free-menu");
                $(this).addClass("highlight");            
            } else if (!$(this).hasClass("voided")) {
                $(this).addClass("highlight");
            }
        });
    });
    
    $("button#btnUnselectAll").on("click", function(event) {
        $("tbody#tbodyOrderMenu").children("tr#menuRow").each(function() {
            if ($(this).hasClass("highlight")) {
                $(this).removeClass("highlight");

                if (parseFloat($(this).find("input.inputMenuFreeMenu").val()) == 1) {
                    $(this).addClass("free-menu");
                }
            }
        });
    });    
    
    /* BUTTON SCROLL
    $("a#scrollUp").on("click", function(event) {
        $("html").animate({
            scrollTop: "-=" + 100 + "px"
        });

        return false;
    });
    
    $("a#scrollDown").on("click", function(event) {
        $("html").animate({
            scrollTop: "+=" + 100 + "px"
        });

        return false;
    });
    */
    
    ' . $virtualKeyboard->keyboardNumeric('.keyboardDisc') . '
    ' . $virtualKeyboard->keyboardQwerty('.keyboardUserPass') . '   

';

$this->registerJs(Tools::jsHitungServiceChargePajak() . $jscript . $jscriptOrderQueue . Yii::$app->params['checkbox-radio-script']()); ?>