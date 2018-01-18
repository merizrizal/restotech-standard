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

if ($status !== null): 
    $notif = new NotificationDialog([
        'status' => $status,
        'message1' => $message1,
        'message2' => $message2,
    ]);

    $notif->theScript();
    echo $notif->renderDialog();

endif;

$this->title = 'Payment';

$temp = $modelSettings;
$modelSettings = [];
foreach ($temp as $value) {
    $modelSettings[$value['setting_name']] = $value['setting_value'];
}

if ($modelTable->not_ppn)
    $modelSettings['tax_amount'] = 0;

if ($modelTable->not_service_charge)
    $modelSettings['service_charge_amount'] = 0;

if (!empty($settingsArray)) {
    echo Html::textarea('strukInvoiceHeader', $settingsArray['struk_invoice_header'], ['id' => 'strukInvoiceHeader', 'style' => 'display:none']);
    echo Html::textarea('strukInvoiceFooter', $settingsArray['struk_invoice_footer'], ['id' => 'strukInvoiceFooter', 'style' => 'display:none']); 
}

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
            <img src="' . Yii::getAlias('@backend-web') . '/img/mtable/thumb120x120' . $modelTable->image . '" class="img-circle" width="120">			
        </div>
        <div class="row data">
            <div class="col-sm-6 col-xs-6 goleft">
                <h4><b>' . $modelTable->nama_meja . '</b></h4>
                <h6>' . $modelTable->kapasitas . ' chair</h6>
            </div>
        </div>
    </div>                           
' ; ?>


<div class="col-lg-12">

    <div class="row mt">
        <div class="col-md-12 col-sm-12">
            <div class="weather-2 pn" style="height: auto; padding: 0 0 20px 0">
                <div class="overlay"></div>
                <div class="loading-img"></div>
                
                <div class="weather-2-header">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">                            
                            <p>
                                Table: <?= '(' . $modelTable->id . ') ' . $modelTable->nama_meja ?>
                            </p>
                        </div>
                    </div>                    
                </div>
                
                
                <div class="row data mt">
                    <?= Html::beginForm('', 'post', ['id' => 'formPayment']); ?>
                    
                    <?= Html::hiddenInput('tableId', $modelTable->id, ['id' => 'tableId']) ?>
                    <?= Html::hiddenInput('sessionId', $modelMtableSession->id) ?>
                    <?= Html::hiddenInput('userActive', Yii::$app->session->get('user_data')['employee']['nama'], ['id' => 'userActive']) ?>
                    <?= Html::hiddenInput('tglJamPesan', Yii::$app->formatter->asDatetime($modelMtableSession->opened_at), ['id' => 'tglJam']) ?>
                    
                    <?= Html::hiddenInput('isKoreksi', !empty($isKoreksi) ? 1 : 0, ['id' => 'isKoreksi']) ?>
                    
                    <div class="col-lg-5">
                        <div class="white-panel pn table-responsive" style="height: auto; color: #000">
                            <div class="white-header" style="padding: 10px">
                                <div class="goleft">
                                    <?php 
                                    $submitButton = Html::submitButton('<i class="fa fa-check" style="color: white"></i> Bayar', ['class' => 'btn btn-success', 'id' => 'btnBayar']);
                                    
                                    $backButton = '';
                                    if (Yii::$app->controller->action->id == 'koreksi-payment') {
                                        $backButton = Html::a('<i class="fa fa-undo" style="color: white"></i> Back', Yii::$app->urlManager->createUrl(['page/koreksi-faktur', 'id' => $modelInvoice->id]), ['class' => 'btn btn-danger']);
                                        echo Html::hiddenInput('oldInvoiceId', $modelInvoice->id);
                                        echo Html::hiddenInput('postFormPayment', Yii::$app->urlManager->createUrl(['page/koreksi-payment-submit']), ['id' => 'postFormPayment']);
                                    } else {
                                        $backButton = Html::a('<i class="fa fa-undo" style="color: white"></i> Back', Yii::$app->urlManager->createUrl(['page/view-table', 'id' => $modelMtableSession->id]), ['class' => 'btn btn-danger']); 
                                        echo Html::hiddenInput('postFormPayment', Yii::$app->urlManager->createUrl(['page/payment-submit']), ['id' => 'postFormPayment']);
                                    } 
                                    
                                    echo $submitButton;
                                    echo '&nbsp;&nbsp;&nbsp;';
                                    echo $backButton; ?>

                                </div>
                            </div>
                            <table class="table table-advance table-striped">
                                <thead>
                                    <tr>
                                        <th class="goleft">Menu</th>
                                        <th class="centered" style="width: 60px">Qty</th>
                                        <th class="goright" style="width: 40%">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyOrderMenu">
                                    <?php
                                    $discBill = empty($modelMtableSession->discount) ? 0 : $modelMtableSession->discount;
                                    $discBillType = $modelMtableSession->discount_type;
                                    $discBillValue = 0;

                                    if ($discBillType == 'percent') {                                                    
                                        $discBillValue = $discBill * 0.01 * $modelMtableSession->jumlah_harga; 
                                    } else if ($discBillType == 'value') {
                                        $discBillValue = $discBill;                                                
                                    }
                                        
                                    $i = 0;
                                    
                                    $totalDisc = 0;
                                    
                                    $jumlah_harga = 0;
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
                                            
                                                $freeMenu = $mtableOrderData['is_free_menu'] ? 'FreeMenu' : ''; 

                                                $subtotal = 0;

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

                                                <tr id="menuRow" class="<?= ($mtableOrderData->is_void ? 'voided' : '') . ' ' .  ($mtableOrderData->is_free_menu ? 'free-menu' : '') ?>">
                                                    <td id="menu" class="goleft">
                                                        <span><?= (!empty($mtableOrderData->parent_id) ? '<i class="fa fa-plus" style="color:green"></i>' : '') . $mtableOrderData->menu->nama_menu ?></span>
                                                        <?= Html::hiddenInput('menu[' . $i .'][inputMenuId]', $mtableOrderData->menu_id, ['id' => 'inputMenuId']) ?>
                                                        <?= Html::hiddenInput('menu[' . $i .'][inputId]', $mtableOrderData->id, ['id' => 'inputId', 'class' => 'inputId']) ?>
                                                        <?= Html::hiddenInput('menu[' . $i .'][inputMenuCatatan]', $mtableOrderData->catatan, ['id' => 'inputMenuCatatan', 'class' => 'inputMenuCatatan']) ?>
                                                    </td>
                                                    <td id="qty" class="centered">
                                                        <?= Html::hiddenInput('menu[' . $i .'][inputMenuQty]', $mtableOrderData->jumlah, ['id' => 'inputMenuQty', 'class' => 'inputMenuQty']) ?>
                                                        <?= Html::hiddenInput('menu[' . $i .'][inputMenuHarga]', $mtableOrderData->harga_satuan, ['id' => 'inputMenuHarga', 'class' => 'inputMenuHarga']) ?>
                                                        <?= Html::hiddenInput('menu[' . $i .'][inputMenuDiscountType]', $mtableOrderData->discount_type, ['id' => 'inputMenuDiscountType', 'class' => 'inputMenuDiscountType']) ?>
                                                        <?= Html::hiddenInput('menu[' . $i .'][inputMenuDiscount]', $mtableOrderData->discount, ['id' => 'inputMenuDiscount', 'class' => 'inputMenuDiscount']) ?>
                                                        <?= Html::hiddenInput('menu[' . $i .'][inputMenuVoid]', $mtableOrderData->is_void, ['id' => 'inputMenuVoid', 'class' => 'inputMenuVoid']) ?>
                                                        <?= Html::hiddenInput('menu[' . $i .'][inputMenuVoidAt]', $mtableOrderData->void_at, ['id' => 'inputMenuVoidAt', 'class' => 'inputMenuVoidAt']) ?>
                                                        <?= Html::hiddenInput('menu[' . $i .'][inputMenuUserVoid]', $mtableOrderData->user_void, ['id' => 'inputMenuUserVoid', 'class' => 'inputMenuUserVoid']) ?>
                                                        <?= Html::hiddenInput('menu[' . $i .'][inputMenuFreeMenu]', $mtableOrderData->is_free_menu, ['id' => 'inputMenuFreeMenu', 'class' => 'inputMenuFreeMenu']) ?>
                                                        <?= Html::hiddenInput('menu[' . $i .'][inputMenuFreeMenuAt]', $mtableOrderData->free_menu_at, ['id' => 'inputMenuFreeMenuAt', 'class' => 'inputMenuFreeMenuAt']) ?>
                                                        <?= Html::hiddenInput('menu[' . $i .'][inputMenuUserFreeMenu]', $mtableOrderData->user_free_menu, ['id' => 'inputMenuUserFreeMenu', 'class' => 'inputMenuUserFreeMenu']) ?>
                                                        <span><?= $mtableOrderData->jumlah ?></span>
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
                                            
                                    <?= Html::hiddenInput('orderCount', $i, ['id' => 'orderCount']) ?>
                                    
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
                                        <?= Html::hiddenInput('paymentJumlahHarga', $jumlah_harga, ['id' => 'paymentJumlahHarga']) ?>
                                    </tr>                                    
                                    <tr>
                                        <td class="goleft">Service (<?= $modelSettings['service_charge_amount'] ?> %)</td>
                                        <td colspan="2" id="service-charge-amount" class="goright"><?= $serviceCharge ?></td>
                                        <?= Html::hiddenInput('paymentServiceChargeAmount', $modelSettings['service_charge_amount'], ['id' => 'paymentServiceChargeAmount']) ?>
                                    </tr>
                                    <tr>
                                        <td class="goleft">Ppn (<?= $modelSettings['tax_amount'] ?> %)</td>
                                        <td colspan="2" id="tax-amount" class="goright"><?= $pajak ?></td>
                                        <?= Html::hiddenInput('paymentTaxAmount', $modelSettings['tax_amount'], ['id' => 'paymentTaxAmount']) ?>
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
                                        <?= Html::hiddenInput('paymentGrandHargaInput', $grandTotal, ['id' => 'paymentGrandHargaInput']) ?>
                                        <?= Html::hiddenInput('total-disc-input', $totalDisc, ['id' => 'total-disc-input']) ?>
                                    </tr>
                                </tfoot>
                            </table>
                            
                            <table class="table table-advance">
                                <thead>
                                    <tr style="font-weight: bold; font-size: 16px">
                                        <th class="goleft">
                                            <?= Html::hiddenInput('paymentJumlahBayar', 0, ['id' => 'paymentJumlahBayar']) ?>
                                            Total Bayar
                                        </th>
                                        <th id="totalBayar" class="goright" style="width: 45%">0</th>
                                    </tr>                                    
                                </thead>
                                <tbody id="tbodyPayment">
                                    
                                </tbody>
                                <tfoot>
                                    <tr style="font-weight: bold; font-size: 16px">
                                        <td class="goleft">
                                            <?= Html::hiddenInput('paymentJumlahKembali', 0, ['id' => 'paymentJumlahKembali']) ?>
                                            Kembali
                                        </td>
                                        <td id="totalKembali" class="goright">0</td>
                                    </tr>
                                </tfoot>
                            </table>
                            
                            <div class="goleft" style="margin: 10px">                                
                                <?php
                                echo $submitButton;
                                echo '&nbsp;&nbsp;&nbsp;';
                                echo $backButton; ?>
                                
                            </div>
                        </div>
                    </div>
                    
                    <?= Html::endForm(); ?>
                    
                    <div class="col-lg-7">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="btn-group btn-block">
                                    <button class="btn btn-danger btn-block btn-lg dropdown-toggle" data-toggle="dropdown" type="button" style="height: 83px">Payment<br>Method <span class="caret"></span></button>
                                    <ul class="dropdown-menu" role="menu">
                                        <?php
                                         foreach ($modelPaymentMethod as $paymentMethodData): ?>
                                             
                                            <li><a id="btnPaymentMethod" data-id="<?= $paymentMethodData['id'] ?>" class="btn-block btn-lg" href="javascript:;" style="font-size: 18px"><?= $paymentMethodData['nama_payment'] ?></a></li>
                                        
                                        <?php
                                         endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="col-lg-9">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span style="font-size: 30px">Total</span>
                                    </div>
                                    <div class="col-lg-8">
                                        <?= MaskMoney::widget(['name' => 'total', 'value' => $grandTotal, 'options' => [
                                            'id' => 'inputTotal',
                                            'class' => 'input-payment', 
                                            'style' => 'text-align: right; font-size: 24px; width:100%',
                                            'readonly' => 'readonly'
                                        ]]) ?>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span style="font-size: 30px">Bayar</span>
                                    </div>
                                    <div class="col-lg-8">
                                        <?= MaskMoney::widget(['name' => 'bayar', 'value' => 0, 'options' => [
                                            'id' => 'inputBayar',
                                            'class' => 'input-payment', 
                                            'style' => 'text-align: right; font-size: 24px; width:100%'
                                        ]]) ?>
                                    </div>
                                </div>
                            </div>                                                          
                        </div>                                                
                        
                        <div class="row mt">
                            <div class="col-lg-3" style="margin-top: 5px">
                                <button id="btnRp" data-rp="1000" class="btn btn-success btn-block btn-lg" type="button">1.000</button>
                                <button id="btnRp" data-rp="5000" class="btn btn-success btn-block btn-lg" type="button">5.000</button>
                                <button id="btnRp" data-rp="10000" class="btn btn-success btn-block btn-lg" type="button">10.000</button>
                                <button id="btnRp" data-rp="20000" class="btn btn-success btn-block btn-lg" type="button">20.000</button>
                                <button id="btnRp" data-rp="50000" class="btn btn-success btn-block btn-lg" type="button">50.000</button>
                                <button id="btnRp" data-rp="100000" class="btn btn-success btn-block btn-lg" type="button">100.000</button>
                                <button id="btnAll" class="btn btn-danger btn-block btn-lg" type="button" style="height: 85px">Pay All</button>
                            </div>
                            <div class="col-lg-9">
                                <button id="btnNumber" data-number="7" class="btn btn-primary btn-lg" type="button" style="height: 90px">7</button>
                                <button id="btnNumber" data-number="8" class="btn btn-primary btn-lg" type="button" style="height: 90px">8</button>
                                <button id="btnNumber" data-number="9" class="btn btn-primary btn-lg" type="button" style="height: 90px">9</button>
                                <button id="btnNumber" data-number="4" class="btn btn-primary btn-lg" type="button" style="height: 90px">4</button>
                                <button id="btnNumber" data-number="5" class="btn btn-primary btn-lg" type="button" style="height: 90px">5</button>
                                <button id="btnNumber" data-number="6" class="btn btn-primary btn-lg" type="button" style="height: 90px">6</button>
                                <button id="btnNumber" data-number="1" class="btn btn-primary btn-lg" type="button" style="height: 90px">1</button>
                                <button id="btnNumber" data-number="2" class="btn btn-primary btn-lg" type="button" style="height: 90px">2</button>
                                <button id="btnNumber" data-number="3" class="btn btn-primary btn-lg" type="button" style="height: 90px">3</button>
                                <button id="btnClearAll" class="btn btn-theme02 btn-lg" type="button">Clear All</button>
                                <button id="btnNumber" data-number="0" class="btn btn-primary btn-lg" type="button" style="height: 90px">0</button>
                                <button id="btnClear" class="btn btn-theme02 btn-lg" type="button">Clear</button>
                            </div>                            
                        </div>
                    </div>                                        
                    
                </div>
            </div>
        </div>
    </div>
</div>

<table id="tempRowPay" style="display: none">
    <tr>
        <td id="paymentMethod" class="goleft">
            <span id="text"></span>
        </td>
        <td id="totalPayment" class="goright"></td>
    </tr>
</table>

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

<!-- Modal KeteranganPayment -->
<div class="modal fade" id="modalKeteranganPayment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Keterangan Payment</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <?= Html::label('Keterangan Payment', 'keteranganPayment', ['class' => 'control-label']) ?>
                        <?= Html::textarea('keteranganPayment', '', ['id' => 'keteranganPayment', 'class' => 'form-control keyboard']) ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> &nbsp; Close</button>
                <button id="submitKeteranganPayment" type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-check"></i> &nbsp;Submit</button>
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


<?php
$printerDialog = new PrinterDialog();
$printerDialog->theScript();
echo $printerDialog->renderDialog('pos');

$virtualKeyboard = new VirtualKeyboard();
$virtualKeyboard->registerCssFile();
$virtualKeyboard->registerJsFile();

$this->params['regJsFile'][] = function() {
    $this->registerJsFile(Yii::getAlias('@common-web') . '/js/plugins/jquery-currency/jquery.currency.js');
    $this->registerJsFile(Yii::getAlias('@common-web') . '/js/plugins/iCheck/icheck.min.js');
};

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
            
            var hitungDiscBill = function() {
                var discountType = $("input#discBillType").val();
                var discount = $("input#discBill");                            
                var harga = parseFloat($("#paymentJumlahHarga").val());

                var hargaDisc = 0; 

                if (discountType == "percent") {
                    hargaDisc = parseFloat(discount.val()) * 0.01 * harga 
                } else if (discountType == "value") {
                    hargaDisc = parseFloat(discount.val()); 
                }

                return hargaDisc;
            };
            
            $("input.inputMenuDiscountType").each(function() {
                if ($(this).val() == "percent") {
                
                } else if ($(this).val() == "value") {
                    $(this).parent().parent().find("td#subtotal #spanDiscount #valDiscount").currency({' . Yii::$app->params['currencyOptions'] . '});
                }
            });
            
            $("tbody#tbodyOrderMenu tr#menuRow td#subtotal span#spanSubtotal").currency({' . Yii::$app->params['currencyOptions'] . '});   
            $("#total-free-menu").currency({' . Yii::$app->params['currencyOptions'] . '});
            $("#total-void").currency({' . Yii::$app->params['currencyOptions'] . '});
            $("#total-harga").currency({' . Yii::$app->params['currencyOptions'] . '});
            $("#discbill").currency({' . Yii::$app->params['currencyOptions'] . '});
            $("#service-charge-amount").currency({' . Yii::$app->params['currencyOptions'] . '});
            $("#tax-amount").currency({' . Yii::$app->params['currencyOptions'] . '});
            $("#grand-harga").currency({' . Yii::$app->params['currencyOptions'] . '});
            
            $("th#totalBayar").currency({' . Yii::$app->params['currencyOptions'] . '});
            $("td#totalKembali").currency({' . Yii::$app->params['currencyOptions'] . '});
                
            $(".input-payment").removeClass("form-control");
            $("#inputTotal-disp").off("keypress");
            $("#inputTotal-disp").off("keydown");
            
            $("button#btnRp").on("click", function(event) {
                var value = parseFloat($("#inputBayar").val()) + parseFloat($(this).attr("data-rp"));
                $("#inputBayar").val(value);
                $("#inputBayar-disp").val(value);
                $("#inputBayar-disp").maskMoney("mask", value);
            });
            
            $("button#btnNumber").on("click", function(event) {
                var value = $("#inputBayar").val() + $(this).attr("data-number");
                value = parseFloat(value);
                $("#inputBayar").val(value);
                $("#inputBayar-disp").val(value);
                $("#inputBayar-disp").maskMoney("mask", value);
            });
            
            $("button#btnAll").on("click", function(event) {
                var value = parseFloat($("#inputTotal").val());
                $("#inputBayar").val(value);
                $("#inputBayar-disp").val(value);
                $("#inputBayar-disp").maskMoney("mask", value);
            });
            
            var clearInputBayar = function() {
                var value = parseFloat(0);
                $("#inputBayar").val(value);
                $("#inputBayar-disp").val(value);
                $("#inputBayar-disp").maskMoney("mask", value);
            }
            
            $("button#btnClear").on("click", function(event) {
                var str = $("#inputBayar").val();
                if (str.length > 1) {
                    var value = str.substring(0, str.length - 1);
                    value = parseFloat(value);
                    $("#inputBayar").val(value);
                    $("#inputBayar-disp").val(value);
                    $("#inputBayar-disp").maskMoney("mask", value);
                } else {
                    clearInputBayar();
                }
            });
            
            $("button#btnClearAll").on("click", function(event) {
                clearInputBayar();
            });
            
            var paymentMethodFunction = function(thisObj, other, kode) {
                var value = parseFloat($("#inputBayar").val());
                var inputPaymentMethod = $("<input>").attr("type", "hidden").attr("name", "payment[" + i + "][paymentMethod]").attr("class", "paymentMethod").attr("value", thisObj.attr("data-id"));
                var inputPaymentValue = $("<input>").attr("type", "hidden").attr("name", "payment[" + i + "][paymentValue]").attr("class", "paymentValue").attr("value", value);
                var inputPaymentKeterangan = $("<input>").attr("type", "hidden").attr("name", "payment[" + i + "][paymentKeterangan]").attr("class", "paymentKeterangan").attr("value", "");
                var inputPaymentKode = (other != "") ? $("<input>").attr("type", "hidden").attr("name", "payment[" + i + "][paymentKode]").attr("class", "paymentKode").attr("value", kode) : "";
                
                if (other != "") {
                    if (inputPaymentMethod.val() == "XLIMIT")
                        inputPaymentKeterangan.val("Kode karyawan = " + inputPaymentKode.val());
                    else if (inputPaymentMethod.val() == "XVCHR")
                        inputPaymentKeterangan.val("Kode voucher = " + inputPaymentKode.val());
                }

                var element = $("#tempRowPay").clone();
                element.children().find("#paymentMethod").append(inputPaymentMethod).append(inputPaymentValue).append(inputPaymentKeterangan).append(inputPaymentKode);

                var btnDelete = $("<button>").attr("id", "btnDeletePayment" + i).attr("class", "btn btn-danger btn-xs").attr("type", "button");
                btnDelete.append($("<i>").attr("class", "fa fa-minus-circle").attr("style", "color:white"));

                var btnKeterangan = $("<button>").attr("id", "btnKeteranganPayment" + i).attr("class", "btn btn-primary btn-xs").attr("type", "button");
                btnKeterangan.append($("<i>").attr("class", "fa fa-pencil-square-o").attr("style", "color:white"));

                var spanText = element.children().find("#paymentMethod span#text");
                spanText.html(thisObj.text() + other);
                element.children().find("#paymentMethod").append(btnDelete);                                        
                element.children().find("#paymentMethod").append("&nbsp; &nbsp;");
                element.children().find("#paymentMethod").append(spanText);
                element.children().find("#paymentMethod").append("&nbsp; &nbsp;");                    

                element.children().find("#totalPayment").html(value);
                element.children().find("#totalPayment").currency({' . Yii::$app->params['currencyOptions'] . '});
                element.children().find("#totalPayment").append("&nbsp; &nbsp;").append(btnKeterangan);
                $("tbody#tbodyPayment").append(element.children().html()); 

                $("tbody#tbodyPayment").find("button#btnDeletePayment" + i).on("click", function(event) {
                    var thisObjTemp = $(this);
                    
                    var value = parseFloat(thisObjTemp.parent().parent().find("input.paymentValue").val());
                    var inputTotal = parseFloat($("input#inputTotal").val()) + value;
                    inputTotal = (inputTotal >= parseFloat($("input#paymentGrandHargaInput").val())) ? parseFloat($("input#paymentGrandHargaInput").val()) : inputTotal;

                    var kembali = parseFloat($("input#paymentJumlahKembali").val()) - value;
                    kembali = (kembali >= 0) ? kembali : 0;
                    $("input#paymentJumlahKembali").val(kembali);
                    $("td#totalKembali").html(kembali);
                    $("td#totalKembali").currency({' . Yii::$app->params['currencyOptions'] . '});

                    if (kembali <=0 ) { 
                        $("input#inputTotal").val(inputTotal);
                        $("#inputTotal-disp").val(inputTotal);
                        $("#inputTotal-disp").maskMoney("mask", inputTotal);
                    }

                    $("input#paymentJumlahBayar").val(parseFloat($("#paymentJumlahBayar").val()) - value);
                    $("th#totalBayar").html(parseFloat($("#paymentJumlahBayar").val()));
                    $("th#totalBayar").currency({' . Yii::$app->params['currencyOptions'] . '});

                    var kembali = parseFloat($("input#paymentJumlahKembali").val()) - value;
                    kembali = (kembali >= 0) ? kembali : 0;
                    $("input#paymentJumlahKembali").val(kembali);
                    $("td#totalKembali").html(kembali);
                    $("td#totalKembali").currency({' . Yii::$app->params['currencyOptions'] . '});

                    thisObjTemp.parent().parent().fadeOut(500, function() {
                        $(this).remove();
                    }); 
                });

                $("tbody#tbodyPayment").find("button#btnKeteranganPayment" + i).on("click", function(event) {
                    var thisObjTemp = $(this);

                    $("#modalKeteranganPayment").modal();
                    $("#modalKeteranganPayment #keteranganPayment").val(thisObjTemp.parent().parent().find("input.paymentKeterangan").val());

                    $("#submitKeteranganPayment").on("click", function() {                                                  
                        thisObjTemp.parent().parent().find("input.paymentKeterangan").val($("#modalKeteranganPayment #keteranganPayment").val());

                        $(this).off("click");
                    });                        
                });

                var inputTotal = parseFloat($("input#inputTotal").val()) - value;
                inputTotal = (inputTotal < 0) ? 0 : inputTotal;

                $("input#inputTotal").val(inputTotal);
                $("#inputTotal-disp").val(inputTotal);
                $("#inputTotal-disp").maskMoney("mask", inputTotal);

                $("input#paymentJumlahBayar").val(parseFloat($("#paymentJumlahBayar").val()) + value);
                $("th#totalBayar").html(parseFloat($("#paymentJumlahBayar").val()));
                $("th#totalBayar").currency({' . Yii::$app->params['currencyOptions'] . '});

                var kembali = parseFloat($("input#paymentJumlahBayar").val()) - parseFloat($("input#paymentGrandHargaInput").val());
                if (kembali >= 0) {
                    $("input#paymentJumlahKembali").val(kembali);
                    $("td#totalKembali").html(kembali);
                    $("td#totalKembali").currency({' . Yii::$app->params['currencyOptions'] . '});
                }

                i++;
                clearInputBayar();
            };
            
            var i = 0;
            $("a#btnPaymentMethod").on("click", function(event) {                
                var thisObj = $(this);                                
                var value = parseFloat($("#inputBayar").val());
                if (value > 0) {                                        
                    if ($(this).attr("data-id") == "XLIMIT" || $(this).attr("data-id") == "XVCHR") {                          
                        
                        var kode = $("<input>").attr("class", "form-control keyboardKode").val("");
                        ' . $virtualKeyboard->keyboardQwerty('kode', true) . '
                        
                        if ($(this).attr("data-id") == "XLIMIT") {
                            var submit = $("<button>").on("click", function(event) {
                                var kdKaryawan = $(this).parent().find("input").val();
                                var jmlLimit = 0;
                                $(".paymentMethod").each(function() {
                                    if ($(this).val() == "XLIMIT" && $(this).parent().find(".paymentKode").val() == kdKaryawan) {
                                        jmlLimit += parseFloat($(this).parent().find(".paymentValue").val())
                                    }
                                });
                                
                                $.ajax({
                                    type: "POST",
                                    url: "' . Yii::$app->urlManager->createUrl(['page/verify-employee']) . '",
                                    data: {
                                        "kdKaryawan": kdKaryawan,
                                        "jmlBayar" : (value + jmlLimit)
                                    },
                                    success: function(data) {
                                        if (data == true) {
                                            paymentMethodFunction(thisObj, " (" + kdKaryawan + ")", kdKaryawan);
                                        } else if (data == "jmlBayar") {
                                            $("#modalAlert #modalAlertBody").html("Sisa limit karyawan " + kdKaryawan + " tidak mencukupi !");
                                            $("#modalAlert").modal();
                                        } else if (data == false) {
                                            $("#modalAlert #modalAlertBody").html("Kode karyawan " + kdKaryawan + " tidak ditemukan !");
                                            $("#modalAlert").modal();
                                        }
                                    }
                                });  
                                
                                $("#modalCustom").modal("hide");
                            })
                            .attr("class", "btn btn-primary").append("<i class=\"fa fa-check\"></i>&nbsp; Submit");
                            
                            $("#modalCustom #modalCustomTitle").text("Limit Karyawan");
                            $("#modalCustom #modelCustomBody #content").html("").append("Kode Karyawan").append(kode).append(submit);
                        } else if ($(this).attr("data-id") == "XVCHR") {
                            var submit = $("<button>").on("click", function(event) {
                                var flag = true;
                                var kdVoucher = $(this).parent().find("input").val();

                                $(".paymentMethod").each(function() {
                                    if ($(this).val() == "XVCHR" && $(this).parent().find(".paymentKode").val() == kdVoucher) {
                                        flag = false;
                                        return false;
                                    }
                                });
                                
                                if (!flag) {
                                    $("#modalAlert #modalAlertBody").html("Kode Voucher " + kdVoucher + " sudah diinputkan.\nKode voucher tidak boleh sama !");
                                    $("#modalAlert").modal();
                                    $("#modalCustom").modal("hide");
                                    return false;
                                }
                                
                                $.ajax({
                                    type: "POST",
                                    url: "' . Yii::$app->urlManager->createUrl(['page/verify-voucher']) . '",
                                    data: {
                                        "kdVoucher": kdVoucher,
                                        "jmlBayar" : value
                                    },
                                    success: function(data) {
                                        if (data == true) {
                                            paymentMethodFunction(thisObj, " (" + kdVoucher + ")", kdVoucher);
                                        } else if (data == "date") {
                                            $("#modalAlert #modalAlertBody").html("Masa voucher " + kdVoucher + " sudah tidak berlaku !");
                                            $("#modalAlert").modal();
                                        } else if (data == "not_active") {
                                            $("#modalAlert #modalAlertBody").html("Kode voucher " + kdVoucher + " sudah pernah dipakai atau tidak berlaku !");
                                            $("#modalAlert").modal();
                                        } else if (data == "exceed") {
                                            $("#modalAlert #modalAlertBody").html("Pembayaran melebihi jumlah batas nominal Voucher " + kdVoucher + "!");
                                            $("#modalAlert").modal();
                                        } else if (data == false) {
                                            $("#modalAlert #modalAlertBody").html("Kode Voucher " + kdVoucher + " tidak ditemukan !");
                                            $("#modalAlert").modal();
                                        }
                                    }
                                });
                                
                                $("#modalCustom").modal("hide");
                            })
                            .attr("class", "btn btn-primary").append("<i class=\"fa fa-check\"></i>&nbsp; Submit");
                            
                            $("#modalCustom #modalCustomTitle").text("Voucher");
                            $("#modalCustom #modelCustomBody #content").html("").append("Kode Voucher").append(kode).append(submit);
                        }
                                                    
                        $("#overlayModalCustom").hide();
                        $("#loadingModalCustom").hide();
                        $("#modalCustom").modal();                                                    
                        
                        return false;
                    }
                    
                    paymentMethodFunction(thisObj, "");
                }
            });
            
            $("form#formPayment").on("submit", function() {     

                var executePayment = function(thisObj) {
                    $(".overlay").show();
                    $(".loading-img").show();

                    //var thisObj = $(this);                

                    var bayar = parseFloat($("input#paymentJumlahBayar").val());
                    var jmlHarga = parseFloat($("input#paymentGrandHargaInput").val());
                    var kembali = bayar - jmlHarga;

                    var countFreemenu = 0;
                    $("input.inputMenuFreeMenu").each(function() {
                        if ($(this).val() == 1)
                            countFreemenu++;
                    });                

                    if ((bayar > 0 && kembali >= 0) || parseFloat($("input#orderCount").val()) == countFreemenu) {
                        $.ajax({
                            type: "POST",
                            url: $("input#postFormPayment").val(),
                            dataType: "json",
                            data: thisObj.serialize(),
                            success: function(data) {
                                if (data.flag) {
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
                                    text += "Faktur" + separatorPrint(spaceLength - "Faktur".length) + ": " + data.noFaktur + "\n";
                                    text += "Kasir" + separatorPrint(spaceLength - "Kasir".length) + ": " + $("input#userActive").val() + "\n";

                                    text += separatorPrint(40, "-") + "\n"
                                    text += separatorPrint(13) + "Pembayaran \n";                        
                                    text += separatorPrint(40, "-") + "\n"            

                                    $("tbody#tbodyOrderMenu").children("tr#menuRow").each(function() {
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

                                    var scp = hitungServiceChargePajak(totalSubtotal, $("#paymentServiceChargeAmount").val(), $("#paymentTaxAmount").val());     
                                    
                                    var scText = "";
                                    var serviceCharge = 0;
                                    if (parseFloat($("#paymentServiceChargeAmount").val()) > 0) {
                                        serviceCharge = scp["serviceCharge"];
                                        var serviceChargeSpan = $("<span>").html(serviceCharge);
                                        serviceChargeSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                                        var sc = "Service Charge (" + $("#paymentServiceChargeAmount").val() + "%)";

                                        scText = sc + separatorPrint(40 - (sc + serviceChargeSpan.html()).length) + serviceChargeSpan.html() +"\n";
                                    }

                                    var pjkText = "";
                                    var pajak = 0;
                                    if (parseFloat($("#paymentTaxAmount").val()) > 0) {
                                        pajak = scp["pajak"];
                                        var pajakSpan = $("<span>").html(pajak);
                                        pajakSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                                        var pjk = "Pajak (" + $("#paymentTaxAmount").val() + "%)";

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
                                    text += "Total item" + separatorPrint(40 - ("Total item" + totalQty).length) + totalQty + "\n";
                                    text += "Total" + separatorPrint(40 - ("Total" + totalSubtotalSpan.html()).length) + totalSubtotalSpan.html() + "\n";
                                    text += scText
                                    text += pjkText
                                    text += "Discount Bill ' . $discBillText . '" + separatorPrint(40 - ("Discount Bill ' . $discBillText . '" + discBillSpan.html()).length) + discBillSpan.html() +"\n";
                                    
                                    text += separatorPrint(40, "-") + "\n"; 
                                    
                                    text += "Grand Total" + separatorPrint(40 - ("Grand Total" + grandTotalSpan.html()).length) + grandTotalSpan.html() +"\n";                                                                        

                                    text += separatorPrint(40, "-") + "\n"; 

                                    var jumlahKembali = grandTotal;

                                    $("tbody#tbodyPayment tr").each(function() {
                                        var paymentMethod = $(this).find("td#paymentMethod span#text").html();
                                        var keterangan = $(this).find("input.paymentKeterangan").val();

                                        var paymentValue = parseFloat($(this).find("input.paymentValue").val());
                                        var spanPaymentValue = $("<span>").html(paymentValue);
                                        spanPaymentValue.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});   

                                        text += paymentMethod + separatorPrint(40 - (paymentMethod + spanPaymentValue.html()).length) + spanPaymentValue.html() + "\n";
                                        text += keterangan + "\n";

                                        jumlahKembali -= paymentValue;
                                    });

                                    if (jumlahKembali >= 0)
                                        jumlahKembali = 0;
                                    else
                                        jumlahKembali = jumlahKembali * -1;

                                    var jumlahKembaliSpan = $("<span>").html(jumlahKembali);
                                    jumlahKembaliSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});

                                    text += separatorPrint(40, "-") + "\n";     
                                    text += "Kembali" + separatorPrint(40 - ("Kembali" + jumlahKembaliSpan.html()).length) + jumlahKembaliSpan.html() + "\n";
                                    
                                    text += separatorPrint(40, "-") + "\n";         
            
                                    var totalDisc = parseFloat($("input#total-disc-input").val());
                                    var totalDiscSpan = $("<span>").html(totalDisc);
                                    totalDiscSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                                    text += "*Total Discount Menu" + separatorPrint(40 - ("*Total Discount Menu" + totalDiscSpan.html()).length) + totalDiscSpan.html() +"\n";
                                    
                                    if (thisObj.find("input.keterangan").val() !== undefined) {
                                        text += "**Koreksi Invoice: " + thisObj.find("input.keterangan").val() + "\n\n";
                                    }

                                    text += $("textarea#strukInvoiceFooter").val() + "\n";                                       
                                    
                                    var locationFunction = function() {
                                        $(location).attr("href","' . Yii::$app->urlManager->createUrl(['page/index']) . '");
                                    };

                                    var content = [];

                                    $("input#printerKasir").each(function() {
                                        content[$(this).val()] = text;
                                    });                                                                                                                  
                                    
                                    printContentToServer("", "", content, true, locationFunction);
                                } else {
                                    $("#modalAlert #modalAlertBody").html("Error. Data pembayaran gagal disimpan. Silakan cek data resep menu, item sku storage");
                                    $("#modalAlert").modal();
                                }
                                
                                $(".overlay").hide();
                                $(".loading-img").hide();
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                                $(".overlay").hide();
                                $(".loading-img").hide();
                                
                                if (xhr.status == "403") {
                                    $("#modalAlert #modalAlertBody").html("Error.<br>Forbidden (#403): You are not allowed to perform this action.");
                                    $("#modalAlert").modal();
                                }
                            }
                        });                    
                    } else {
                        $("#modalAlert #modalAlertBody").html("Harap lunasi pembayaran terlebih dahulu sebelum submit");
                        $("#modalAlert").modal();
                    }
                };
                
                var thisObj = $(this);

                if ($("input#isKoreksi").val() == 1) {       
                    
                    var keterangan = $("<input>").attr("class", "form-control keyboard keterangan").val("");
                    ' . $virtualKeyboard->keyboardQwerty('keterangan', true) . '

                    var submit = $("<button>").on("click", function(event) {
                        $("#modalCustom").modal("hide");

                        var inputKeterangan = $("<input>").attr("type", "hidden").attr("name", "keterangan").attr("class", "keterangan").attr("value", $(this).parent().find("input").val());                        
                        $("form#formPayment").append(inputKeterangan);
                        
                        executePayment(thisObj);
                    })
                    .attr("class", "btn btn-primary").append("<i class=\"fa fa-check\"></i>&nbsp; Submit");                                                

                    $("#modalCustom #modalCustomTitle").text("Keterangan Koreksi");
                    $("#modalCustom #modelCustomBody #content").html("").append(keterangan).append(submit);
                    
                    $("#overlayModalCustom").hide();
                    $("#loadingModalCustom").hide();
                    $("#modalCustom").modal();    
                } else {
                    executePayment(thisObj);
                }
                
                return false;
            });            
            
            ' . $virtualKeyboard->keyboardQwerty('textarea.keyboard') . '
        
            $(".overlay").hide();
            $(".loading-img").hide();
';

$this->registerJs(Tools::jsHitungServiceChargePajak() . $jscript . Yii::$app->params['checkbox-radio-script']()); ?>