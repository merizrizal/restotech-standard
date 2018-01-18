<?php

$jscript = '           
        
    
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
                text += "Tanggal/Jam Print" + separatorPrint(14 - "Tanggal/Jam Print".length) + ": " + datetime + "\n";
                text += separatorPrint(40, "-") + "\n";
                text += "Meja" + separatorPrint(14 - "Meja".length) + ": " + $("input#tableId").val() + "\n";
                text += "Tanggal/Jam Open" + separatorPrint(14 - "Tanggal/Jam Open".length) + ": " + $("input#tglJam").val() + "\n";
                text += "Kasir" + separatorPrint(14 - "Kasir".length) + ": " + $("input#userActive").val() + "\n";

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