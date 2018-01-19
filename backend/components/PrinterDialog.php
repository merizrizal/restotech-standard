<?php

namespace backend\components;

use Yii;
use yii\base\Widget;
use backend\models\Printer;
use backend\models\Settings;

class PrinterDialog extends Widget {
    
    public $status = '';
    public $message1 = '';
    public $message2 = '';
    
    public function theScript() {
        
        $settings = Settings::getSettingsByName(['print_server_url', 'print_server_cross_domain', 'print_paper_width']);
        
        $async = 'true';
        $crossDomain = 'false';
        $dataType = '';
        
        if ($settings['print_server_cross_domain']) {
            
            $async = 'false';
            $crossDomain = 'true';
            $dataType = 'dataType: "jsonp",';
        }
        
        $jscript = '                        
            
            var printContentToServer = function(header, footer, content, openCashdrawer, _otherFunction) {
              
                $("#modalLoading").modal("show");
                
                var printer;
                var string = "";
                var otherFunction = _otherFunction;
                
                for (printer in content) {
                    if (printer != "") {                                                
                        
                        string = header + content[printer] + footer;
                        
                        if (openCashdrawer !== undefined && openCashdrawer) {
                            //string += chr(27) + "\x70" + "\x30" + chr(25) + chr(25);
                            string += chr(27) + chr(112) + chr(48) + chr(25) + chr(250);
                        }
                        
                        if ($("input#printer." + printer).val() == 1) {
                            string += chr(27) + chr(105);
                        }
                        
                        $.ajax({
                            async: ' . $async . ',
                            crossDomain: ' . $crossDomain . ',' .
                            $dataType . '
                            type: "POST",
                            url: "' . $settings['print_server_url'] .'",
                            data: {
                                "message": string,
                                "printer": printer,
                            },
                            success: function(response) {
                                $("#modalLoading").modal("hide");
                                
                                if (response.flag) {                                    
                                    if (otherFunction !== undefined) {
                                        setTimeout(function() {
                                            otherFunction();
                                        }, 1300);                                        
                                    }
                                } else {
                                    $("#modalAlert #modalAlertBody").html("Print data accepted but return error");
                                    $("#modalAlert").modal();
                                }
                            },
                            error: function (xhr, ajaxOptions, thrownError) {                                
                                $("#modalLoading").modal("hide");
                                
                                if (xhr.status != "200") {
                                
                                    if (xhr.status == "403") {
                                        $("#modalAlert #modalAlertBody").html("Tidak punya kewenangan");
                                    } else if (xhr.status == "404") {
                                        $("#modalAlert #modalAlertBody").html("Print server tidak ditemukan");
                                    } else if (xhr.status == "500") {
                                        $("#modalAlert #modalAlertBody").html("Port belum diseting");
                                    }

                                    $("#modalAlert").modal();                                    
                                }
                                
                                if (otherFunction !== undefined) {
                                    setTimeout(function() {
                                        $("#modalAlert").modal("hide");
                                        
                                        setTimeout(function() {
                                            otherFunction();
                                        }, 500);
                                    }, 1300);                      
                                }
                            }
                        });
                    }
                }
            };
            
            var printContentCategoryToServer = function(header, footer, content, openCashdrawer, _otherFunction) {
                $("#modalLoading").modal("show");
                
                var printer;
                var string = "";
                var otherFunction = _otherFunction;
                
                for (printer in content) {
                    if (printer != "") {
                    
                        var category;
                        
                        for (category in content[printer]) {                                                        

                            var c = "KATEGORI: " + category.split(".")[1] + "\n\n" + content[printer][category];
                            
                            string = header + c + footer;                           

                            if (openCashdrawer !== undefined && openCashdrawer) {
                                //string += chr(27) + "\x70" + "\x30" + chr(25) + chr(25);
                                string += chr(27) + chr(112) + chr(48) + chr(25) + chr(250);
                            }

                            if ($("input#printer." + printer).val() == 1) {
                                string += chr(27) + chr(105);
                            }        
                            
                            $.ajax({
                                cache: false,
                                type: "POST",
                                url: "' . Yii::$app->urlManager->createUrl(['site/print']) .'",
                                data: {
                                    "message": string,
                                    "printer": printer,
                                },
                                success: function(response) {
                                    $("#modalLoading").modal("hide");

                                    if (response.flag) {                                    
                                        if (otherFunction !== undefined) {
                                            otherFunction();
                                        }
                                    } else {
                                        $("#modalAlert #modalAlertBody").html("Print data accepted but return error");
                                        $("#modalAlert").modal();
                                    }
                                },
                                error: function (xhr, ajaxOptions, thrownError) {
                                    $("#modalLoading").modal("hide");

                                    if (xhr.status == "403") {
                                        $("#modalAlert #modalAlertBody").html("Tidak punya kewenangan");
                                    } else if (xhr.status == "404") {
                                        $("#modalAlert #modalAlertBody").html("Print server tidak ditemukan");
                                    } else if (xhr.status == "500") {
                                        $("#modalAlert #modalAlertBody").html("Port belum diseting");
                                    }

                                    $("#modalAlert").modal();
                                }
                            });
                        }
                    }
                }
            };
            
            var chr = function(i) {
                return String.fromCharCode(i);
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
            
            var spaceLength = 14;
            var paperWidth = ' . $settings['print_paper_width'] . ';
        ';
        
        $this->getView()->registerJs($jscript);  
    }
    
    public function onHidden($script) {
        $jscript = '
            $("#modalPrinter").on("hidden.bs.modal", function(event) {
                ' . $script . '
            });
        ';
        
        $this->getView()->registerJs($jscript);
    }
    
    public function renderDialog($type) {
        
        $modelPrinter = Printer::find()
            ->andWhere(['not_active' => false])
            ->asArray()->all();
        
        $modelPrinterKasir = Printer::find()
            ->andWhere(['type' => 'cashier'])
            ->andWhere(['not_active' => false])
            ->asArray()->all();
        
        return $this->render('printerDialog' . $type, [
            'modelPrinter' => $modelPrinter,
            'modelPrinterKasir' => $modelPrinterKasir,
        ]);
    }
}
