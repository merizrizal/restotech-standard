<?php

namespace restotech\standard\backend\components;

use Yii;
use yii\base\Widget;

class VirtualKeyboard extends Widget {   
    
    private $setting;


    public function init() {
        $this->setting = Yii::$app->session->get('showVirtualKeyboard', false);        
    }
    
    public function registerCssFile() {
        if ($this->setting) {
            $this->getView()->registerCssFile($this->getView()->params['assetCommon']->baseUrl . '/plugins/keyboard/css/keyboard.css', ['depends' => 'yii\web\YiiAsset']);
        }
    }
    
    public function registerCss() {
        if ($this->setting) {
            $css = '
                .btn-xl {
                    padding: 10px 16px;
                    font-size: 24px;
                    line-height: 1.33;
                    border-radius: 6px;
                }

                .keyboard-os {
                    position: absolute;
                    top: 30%;
                    left: 50%;
                    z-index: 1999;
                    display: none;
                    float: left;
                    min-width: 160px;
                    padding: 5px 0px;
                    margin-left: -25%;
                    text-align: left;
                    list-style: outside none none;
                    background-color: #FFF;
                    background-clip: padding-box;
                    border: 1px solid rgba(0, 0, 0, 0.15);
                    border-radius: 4px;
                    box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.176);
                }
            ';

            $this->getView()->registerCss($css);
        }
    }
    
    public function registerJsFile() {
        if ($this->setting) {
            $this->getView()->registerJsFile($this->getView()->params['assetCommon']->baseUrl . '/plugins/keyboard/js/jquery.keyboard.js', ['depends' => 'yii\web\YiiAsset']);
            $this->getView()->registerJsFile($this->getView()->params['assetCommon']->baseUrl . '/plugins/keyboard/js/jquery.keyboard.extension-typing.js', ['depends' => 'yii\web\YiiAsset']);            
        }
    }       
    
    public function keyboardQwerty($element, $isVar = false) {
        $jscript = '';
        
        if ($this->setting) {
            $object = $isVar ? $element : '$("' . $element . '")';
            
            $jscript = '
                ' . $object . '.keyboard({
                    openOn: null,                        
                    layout: "qwerty",
                    css: {
                        input: "form-control input-sm",
                        container: "center-block keyboard-os",
                        buttonDefault: "btn btn-default btn-xl btn-flat",
                        buttonHover: "btn-primary",
                        buttonAction: "active",
                        buttonDisabled: "disabled"
                    }
                }).on("click", function(event){
                    var kb = $(this).getkeyboard();
                    if (kb.isOpen) {
                        kb.close();
                    } else {
                        kb.reveal();
                    }
                });
            ';
        }
        
        return $jscript;
    }
    
    public function keyboardNumeric($element, $isVar = false) {
        $jscript = '';
        
        if ($this->setting) {
            $object = $isVar ? $element : '$("' . $element . '")';
            
            $jscript = '
                ' . $object . '.keyboard({
                    openOn: null,                        
                    layout: "custom",
                    customLayout: {
                            "default" : [                                
                                    "7 8 9",
                                    "4 5 6",
                                    "1 2 3",
                                    "  0  ",
                                    "{bksp} {a} {c}"
                            ]
                    },
                    css: {
                        input: "form-control input-sm",
                        container: "center-block keyboard-os",
                        buttonDefault: "btn btn-default btn-xl btn-flat",
                        buttonHover: "btn-primary",
                        buttonAction: "active",
                        buttonDisabled: "disabled"
                    }
                }).on("click", function(event){
                    var kb = $(this).getkeyboard();
                    if (kb.isOpen) {
                        kb.close();
                    } else {
                        kb.reveal();
                    }
                });
            ';
        }
        
        return $jscript;
    }
}
