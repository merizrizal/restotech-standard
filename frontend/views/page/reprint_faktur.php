<?php
use yii\helpers\Html; 
use backend\components\Tools;
use backend\components\PrinterDialog;

Yii::$app->formatter->timeZone = 'Asia/Jakarta';

Tools::loadIsIncludeScp();

$this->title = 'Reprint Faktur';

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
                    <div class="col-lg-3"></div>     
                    
                    <?= Html::beginForm('', 'post', ['id' => 'formPayment']); ?>
                    
                    <?= Html::hiddenInput('tableId', $modelTable->id, ['id' => 'tableId']) ?>
                    <?= Html::hiddenInput('sessionId', $modelMtableSession->id) ?>
                    <?= Html::hiddenInput('userActive', Yii::$app->session->get('user_data')['employee']['nama'], ['id' => 'userActive']) ?>
                    <?= Html::hiddenInput('tglJamPesan', Yii::$app->formatter->asDatetime($modelMtableSession->opened_at), ['id' => 'tglJam']) ?>
                    
                    <?php
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
                    
                    <?= Html::hiddenInput('invoiceId', $modelSaleInvoice->id , ['id' => 'invoiceId']) ?>
                    
                    <div class="col-lg-5">
                        <div class="white-panel pn table-responsive" style="height: auto; color: #000">
                            <div class="white-header" style="padding: 10px">
                                
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
                                    $i = 0;
                                    
                                    $totalDisc = 0;
                                    
                                    $jumlah_harga = 0;
                                    $totalFreeMenu = 0;
                                    $totalVoid = 0;
                                    
                                    if (count($modelSaleInvoiceDetails) > 0): 
                                        
                                        foreach ($modelSaleInvoiceDetails as $saleInvoiceDetailData): 
                                            $freeMenu = $saleInvoiceDetailData['is_free_menu'] ? 'FreeMenu' : ''; 
                                    
                                            $subtotal = 0;
                                            
                                            $subtotal = $saleInvoiceDetailData->jumlah * $saleInvoiceDetailData->harga;
                                            
                                            if ($saleInvoiceDetailData->is_free_menu)
                                                $totalFreeMenu += $subtotal;
                                            
                                            if ($saleInvoiceDetailData->is_void)
                                                $totalVoid += $subtotal;                                                                                        

                                            if ($saleInvoiceDetailData->discount_type == 'percent') {                                                    
                                                $disc = $saleInvoiceDetailData->discount * 0.01 * $subtotal;
                                                $subtotal = $subtotal - $disc; 
                                                $totalDisc += $disc;
                                            } else if ($saleInvoiceDetailData->discount_type == 'value') {
                                                $disc = $saleInvoiceDetailData->jumlah * $saleInvoiceDetailData->discount;
                                                $subtotal = $subtotal - $disc;                                                
                                                $totalDisc += $disc;
                                            }

                                            if (!$saleInvoiceDetailData->is_free_menu && !$saleInvoiceDetailData->is_void) 
                                                $jumlah_harga += $subtotal; ?>
                                        
                                            <tr id="menuRow" class="<?= ($saleInvoiceDetailData->is_void ? 'voided' : '') . ' ' .  ($saleInvoiceDetailData->is_free_menu ? 'free-menu' : '') ?>">
                                                <td id="menu" class="goleft">
                                                    <span><?= $saleInvoiceDetailData->menu->nama_menu ?></span>
                                                    <?= Html::hiddenInput('menu[' . $i .'][inputMenuId]', $saleInvoiceDetailData->menu_id, ['id' => 'inputMenuId']) ?>
                                                    <?= Html::hiddenInput('menu[' . $i .'][inputId]', $saleInvoiceDetailData->id, ['id' => 'inputId', 'class' => 'inputId']) ?>
                                                    <?= Html::hiddenInput('menu[' . $i .'][inputMenuCatatan]', $saleInvoiceDetailData->catatan, ['id' => 'inputMenuCatatan', 'class' => 'inputMenuCatatan']) ?>
                                                </td>
                                                <td id="qty" class="centered">
                                                    <?= Html::hiddenInput('menu[' . $i .'][inputMenuQty]', $saleInvoiceDetailData->jumlah, ['id' => 'inputMenuQty', 'class' => 'inputMenuQty']) ?>
                                                    <?= Html::hiddenInput('menu[' . $i .'][inputMenuHarga]', $saleInvoiceDetailData->harga, ['id' => 'inputMenuHarga', 'class' => 'inputMenuHarga']) ?>
                                                    <?= Html::hiddenInput('menu[' . $i .'][inputMenuDiscountType]', $saleInvoiceDetailData->discount_type, ['id' => 'inputMenuDiscountType', 'class' => 'inputMenuDiscountType']) ?>
                                                    <?= Html::hiddenInput('menu[' . $i .'][inputMenuDiscount]', $saleInvoiceDetailData->discount, ['id' => 'inputMenuDiscount', 'class' => 'inputMenuDiscount']) ?>
                                                    <?= Html::hiddenInput('menu[' . $i .'][inputMenuVoid]', $saleInvoiceDetailData->is_void, ['id' => 'inputMenuVoid', 'class' => 'inputMenuVoid']) ?>
                                                    <?= Html::hiddenInput('menu[' . $i .'][inputMenuVoidAt]', $saleInvoiceDetailData->void_at, ['id' => 'inputMenuVoidAt', 'class' => 'inputMenuVoidAt']) ?>
                                                    <?= Html::hiddenInput('menu[' . $i .'][inputMenuUserVoid]', $saleInvoiceDetailData->user_void, ['id' => 'inputMenuUserVoid', 'class' => 'inputMenuUserVoid']) ?>
                                                    <?= Html::hiddenInput('menu[' . $i .'][inputMenuFreeMenu]', $saleInvoiceDetailData->is_free_menu, ['id' => 'inputMenuFreeMenu', 'class' => 'inputMenuFreeMenu']) ?>
                                                    <span><?= $saleInvoiceDetailData->jumlah ?></span>
                                                </td>
                                                <td id="subtotal" class="goright">
                                                    <span id="spanDiscount">Disc: <span id="valDiscount"><?= $saleInvoiceDetailData->discount ?></span></span>
                                                    <br>
                                                    <span id="spanSubtotal"><?= $subtotal ?></span>
                                                </td>
                                            </tr>
                                    
                                            <?php
                                            $i++;
                                        endforeach;
                                        
                                        $scp = Tools::hitungServiceChargePajak($jumlah_harga, $modelSaleInvoice->service_charge, $modelSaleInvoice->pajak);                                        
                                        $serviceCharge = $scp['serviceCharge'];
                                        $pajak = $scp['pajak']; 
                                        $grandTotal = $jumlah_harga + $serviceCharge + $pajak;
                                        
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
                                        <td class="goleft">Service (<?= $modelSaleInvoice->service_charge ?> %)</td>
                                        <td colspan="2" id="service-charge-amount" class="goright"><?= $serviceCharge ?></td>
                                        <?= Html::hiddenInput('paymentServiceChargeAmount', $modelSaleInvoice->service_charge, ['id' => 'paymentServiceChargeAmount']) ?>
                                    </tr>
                                    <tr>
                                        <td class="goleft">Ppn (<?= $modelSaleInvoice->pajak ?> %)</td>
                                        <td colspan="2" id="tax-amount" class="goright"><?= $pajak ?></td>
                                        <?= Html::hiddenInput('paymentTaxAmount', $modelSaleInvoice->pajak, ['id' => 'paymentTaxAmount']) ?>
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
                                            <?= Html::hiddenInput('paymentJumlahBayar', $modelSaleInvoice->jumlah_bayar, ['id' => 'paymentJumlahBayar']) ?>
                                            Total Bayar
                                        </th>
                                        <th id="totalBayar" class="goright" style="width: 45%"><?= $modelSaleInvoice->jumlah_bayar ?></th>
                                    </tr>                                    
                                </thead>
                                <tbody id="tbodyPayment">
                                    
                                </tbody>
                                <tfoot>
                                    <tr style="font-weight: bold; font-size: 16px">
                                        <td class="goleft">
                                            <?= Html::hiddenInput('paymentJumlahKembali', $modelSaleInvoice->jumlah_kembali, ['id' => 'paymentJumlahKembali']) ?>
                                            Kembali
                                        </td>
                                        <td id="totalKembali" class="goright"><?= $modelSaleInvoice->jumlah_kembali ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                            
                            <div class="goleft" style="margin: 10px">
                                <?= Html::button('<i class="fa fa-check" style="color: white"></i> Reprint', ['id' => 'btnReprint', 'class' => 'btn btn-success']) ?>
                                &nbsp; &nbsp;
                                <?= Html::a('<i class="fa fa-undo" style="color: white"></i> Back', Yii::$app->urlManager->createUrl(['page/reprint-faktur']), ['class' => 'btn btn-danger']) ?>
                            </div>
                        </div>
                    </div>
                    
                    <?= Html::endForm(); ?>
                    
                    <div class="col-lg-4"></div>                                        
                    
                </div>
            </div>
        </div>
    </div>
</div>

<table id="tempRowPay" style="display: none">
    
    <?php
    foreach ($modelSaleInvoice->saleInvoicePayments as $dataSaleInvoicePayment): ?>
         
        <tr>
            <td id="paymentMethod" class="goleft">
                <?= Html::hiddenInput(null, $dataSaleInvoicePayment->jumlah_bayar, ['class' => 'paymentValue']) ?>
                <?= Html::hiddenInput(null, $dataSaleInvoicePayment->keterangan, ['class' => 'paymentKeterangan']) ?>
                <span id="text"><?= $dataSaleInvoicePayment->paymentMethod->nama_payment ?></span>
            </td>
            <td id="totalPayment" class="goright"><?= $dataSaleInvoicePayment->jumlah_bayar ?></td>
        </tr>
        
    <?php
    endforeach; ?>
        
</table>


<?php

$printerDialog = new PrinterDialog();
$printerDialog->theScript();
echo $printerDialog->renderDialog('pos');

$this->params['regCssFile'][] = function() {
    $this->registerCssFile(Yii::getAlias('@common-web') . '/css/keyboard/keyboard.min.css');
}; 

$this->params['regJsFile'][] = function() {
    $this->registerJsFile(Yii::getAlias('@common-web') . '/js/plugins/keyboard/js/jquery.keyboard.min.js');
    $this->registerJsFile(Yii::getAlias('@common-web') . '/js/plugins/keyboard/js/jquery.keyboard.extension-typing.min.js');
    $this->registerJsFile(Yii::getAlias('@common-web') . '/js/plugins/jquery-currency/jquery.currency.js');
    $this->registerJsFile(Yii::getAlias('@common-web') . '/js/plugins/iCheck/icheck.min.js');
};

$jscript = '                       
            
            var chr = function(i) {
                return String.fromCharCode(i);
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

            var separatorPrint = function(length, char) {
                var separator = "";   
                for (i = 0; i < length; i++) {
                    if (char) {
                        separator += char;
                    } else {
                        separator += " ";
                    }
                }

                return separator;
            };

            $("input.inputMenuDiscountType").each(function() {
                if ($(this).val() == "percent") {
                
                } else if ($(this).val() == "value") {
                    $(this).parent().parent().find("td#subtotal #spanDiscount #valDiscount").currency({' . Yii::$app->params['currencyOptions'] . '});
                }
            });
            
            $("tbody#tbodyOrderMenu tr#menuRow td#subtotal span#spanSubtotal").currency({' . Yii::$app->params['currencyOptions'] . '});      
            $("#total-harga").currency({' . Yii::$app->params['currencyOptions'] . '});
            $("#discbill").currency({' . Yii::$app->params['currencyOptions'] . '});
            $("#service-charge-amount").currency({' . Yii::$app->params['currencyOptions'] . '});
            $("#tax-amount").currency({' . Yii::$app->params['currencyOptions'] . '});
            $("#grand-harga").currency({' . Yii::$app->params['currencyOptions'] . '});
            
            $("th#totalBayar").currency({' . Yii::$app->params['currencyOptions'] . '});
            $("td#totalKembali").currency({' . Yii::$app->params['currencyOptions'] . '});  
                
            $("tbody#tbodyPayment").append($("#tempRowPay tbody").html());
            $("tbody#tbodyPayment td#totalPayment").currency({' . Yii::$app->params['currencyOptions'] . '});            
            
            $("form#formPayment #btnReprint").on("click", function() {
                var text = "";
                var totalQty = 0;
                var totalSubtotal = 0;

                text += "\n" + $("textarea#strukInvoiceHeader").val() + "\n";
                text += separatorPrint(40, "-") + "\n";            
                text += "Meja" + separatorPrint(14 - "Meja".length) + ": " + $("input#tableId").val() + "\n";
                text += "Tanggal/Jam" + separatorPrint(14 - "Tanggal/Jam".length) + ": " + $("input#tglJam").val() + "\n";
                text += "Faktur" + separatorPrint(14 - "Faktur".length) + ": " + $("input#invoiceId").val() + "\n";
                text += "Kasir" + separatorPrint(14 - "Kasir".length) + ": " + $("input#userActive").val() + "\n";

                text += separatorPrint(40, "-") + "\n"
                text += separatorPrint(13) + "Pembayaran \n";                        
                text += separatorPrint(40, "-") + "\n"            

                $("tbody#tbodyOrderMenu tr#menuRow").each(function() {
                    var discountType = $(this).find("input.inputMenuDiscountType").val();
                    var discount = parseFloat($(this).find("input.inputMenuDiscount").val());
                    var harga = parseFloat($(this).find("input.inputMenuHarga").val());                                    

                    var menu = $(this).find("td#menu span").html();
                    var qty = $(this).find("td#qty input.inputMenuQty").val();

                    var textDisc = "";

                    if ($(this).find("input.inputMenuVoid").val() == 1) {
                        textDisc = "Void";
                    } else if ($(this).find("input.inputMenuFreeMenu").val() == 1) {
                        textDisc = "Free";
                    } else {
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
                text += "Total item" + separatorPrint(40 - ("Total item" + totalQty).length) + totalQty + "\n";
                text += "Total" + separatorPrint(40 - ("Total" + totalSubtotalSpan.html()).length) + totalSubtotalSpan.html() + "\n";
                text += scText;
                text += pjkText;
                text += "Discount Bill ' . $discBillText . '" + separatorPrint(40 - ("Discount Bill" + discBillSpan.html()).length) + discBillSpan.html() +"\n";

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
                text += "*Total Discount" + separatorPrint(40 - ("*Total Discount" + totalDiscSpan.html()).length) + totalDiscSpan.html() +"\n";
                
                text += separatorPrint(40, "-") + "\n";                
                text += "**Reprint Invoice\n\n";
                text += separatorPrint(40, "-") + "\n";

                text += $("textarea#strukInvoiceFooter").val() + "\n";                      

                var locationFunction = function() {
                    $(location).attr("href","' . Yii::$app->urlManager->createUrl(['page/index']) . '");
                };

                var content = [];

                $("input#printerKasir").each(function() {
                    content[$(this).val()] = text;
                });                
                
                printContentToServer("", "", content, true, locationFunction);                                                                                         
            });           
';

$this->registerJs(Tools::jsHitungServiceChargePajak() . $jscript . Yii::$app->params['checkbox-radio-script']()); ?>