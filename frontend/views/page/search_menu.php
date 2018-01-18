<?php
use yii\helpers\Html;
use backend\components\Tools;


if (count($modelMenu) > 0):
    foreach ($modelMenu as $menuData):
        
        $badgeStock = '';
    
        if (count($menuData['menuReceipts']) > 0) {         
            
            $noStock = '<div style="color:white;background-color:#C9302C">Stock Kritis</div>';
            
            foreach ($menuData['menuReceipts'] as $dataMenuReceipt) {
                if (count($dataMenuReceipt['itemSku']['stocks'] > 0)) {
                    $totalStok = 0;
                    foreach ($dataMenuReceipt['itemSku']['stocks'] as $dataStock) {
                        $totalStok += $dataStock['jumlah_stok'];
                    }
                    
                    if ($totalStok < $dataMenuReceipt['itemSku']['stok_minimal'])
                        $badgeStock = $noStock;
                    
                } else {
                    $badgeStock = $noStock;
                }
            }
        }
    
        $printer = '';                                  
        if (!empty($menuData['menuCategory']['printer']) && !$menuData['menuCategory']['printer0']['not_active'])
            $printer = $menuData['menuCategory']['printer'];                    
        else if (!empty($menuData['menuCategory']['parentCategory']['printer']) && !$menuData['menuCategory']['parentCategory']['printer0']['not_active'])                
            $printer = $menuData['menuCategory']['parentCategory']['printer']; ?>

        <a href="#" id="menu">
            <div class="col-md-3 col-sm-3 mb">
                <div class="product-panel-2" style="padding: 10px 0; height: 90px">
                    <?= $badgeStock ?>
                    <h5 class="mt" style="color: #000; font-weight: bold"><?= $menuData['nama_menu'] ?></h5>
                    <h6><?= Yii::$app->formatter->asCurrency($menuData['harga_jual']) ?></h6>                
                    <?= Html::hiddenInput('menu-id', $menuData['id'], ['id' => 'menu-id']) ?>
                    <?= Html::hiddenInput('menu-nama', $menuData['nama_menu'], ['id' => 'menu-nama']) ?>
                    <?= Html::hiddenInput('menu-harga', $menuData['harga_jual'], ['id' => 'menu-harga']) ?>
                    <?= Html::hiddenInput('menu-harga-formatted', Yii::$app->formatter->asCurrency($menuData['harga_jual']), ['id' => 'menu-harga-formatted']) ?>                
                    <?= Html::hiddenInput('menu-category-printer', $printer, ['id' => 'menu-category-printer']); ?>

                </div>
            </div>
        </a>

<?php
    endforeach; 
else: ?>
    <br><br><br><br>
    No Data Found
    <br><br><br><br><br>

<?php    
endif; ?>

<script>    

    <?= Tools::jsHitungServiceChargePajak(); ?>
        
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
        
    $("a#menu").on("click", function(event) {   
        event.preventDefault();
        
        var indexMenu = parseFloat($("input#indexMenu").val());
        
        var thisParent = $(this);
        var menuId = thisParent.find("input#menu-id");
        var menuNama = thisParent.find("input#menu-nama");
        var menuHarga = thisParent.find("input#menu-harga");
        var menuHargaFormatted = thisParent.find("input#menu-harga-formatted");
        var menuCategoryPrinter = thisParent.find("input#menu-category-printer");
        
        var harga = parseFloat(menuHarga.val());                
        
        $("#total-harga-input").val(harga + parseFloat($("#total-harga-input").val()));
        $("#total-harga").html($("#total-harga-input").val());
        $("#total-harga").currency({<?= Yii::$app->params['currencyOptions'] ?>});  
        
        var scp = hitungServiceChargePajak($("#total-harga-input").val(), $("#serviceChargeAmount").val(), $("#taxAmount").val());                                   
        
        var serviceCharge = scp["serviceCharge"];
        $("#service-charge-amount").html(serviceCharge);
        $("#service-charge-amount").currency({<?= Yii::$app->params['currencyOptions'] ?>});
        
        var pajak = scp["pajak"];
        $("#tax-amount").html(pajak);
        $("#tax-amount").currency({<?= Yii::$app->params['currencyOptions'] ?>});
        
        var grandTotal = parseFloat($("#total-harga-input").val()) + serviceCharge + pajak - hitungDiscBill();
        $("#grand-harga").html(grandTotal);
        $("#grand-harga").currency({<?= Yii::$app->params['currencyOptions'] ?>});
        
        var inputMenuQty = $("<input>").attr("type", "hidden").attr("id", "inputMenuQty").attr("class", "inputMenuQty").attr("name", "menu[" + indexMenu + "][inputMenuQty]").attr("value", 1);
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
        comp.children().find("#menu span").html(menuNama.val());
        comp.children().find("#menu").append(inputMenuId).append(inputCatatan).append(inputCategoryPrinter);
        comp.children().find("#qty").append(inputMenuQty).append(inputHarga).append(inputDiscount).append(inputVoid).append(inputFreeMenu).append(inputDiscountType);
        comp.children().find("#qty").find("span").html(1);
        comp.children().find("#subtotal span#spanSubtotal").append(menuHargaFormatted.val());                        
        $("tbody#tbodyOrderMenu").append(comp.children().html());             
    });
</script>


