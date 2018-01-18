<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\PjaxAsset;
use kartik\date\DatePicker;
use frontend\components\NotificationDialog;
use backend\components\VirtualKeyboard;

PjaxAsset::register($this);

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

$this->title = 'List Table (Layout)'; ?>

<div class="mt"></div>

<div class="col-lg-12" style="padding-right: 33px">

    <div class="row">

        <div class="col-lg-12">
            Keterangan warna pada kategori meja :
        </div>

    </div>

    <div style="margin: 10px"></div>

    <div class="row">
        <?php
        foreach ($model as $value): 
            $color = !empty($value->color) ? $value->color : '#000'; ?>

            <div class="col-lg-2">
                <?= Html::a($value->nama_category, Yii::$app->urlManager->createUrl(['page/index2', 'catid' => $value['id']]), ['class' => 'btn btn-block', 'style' => 'margin-top: 5px; color: white; background: ' . $color]) ?>
            </div>

        <?php
        endforeach; ?>
        
        <?php
        $jscriptTemp = 'var drawingTemp;';

        foreach ($model as $value):
            if ($catid == $value['id']):
                foreach ($value->mtables as $dataTable):

                    $color = !empty($value->color) ? $value->color : '#000'; 
                    if (!empty($dataTable->shape)) {
                        if ($dataTable->shape == 'circle')
                            $jscriptTemp .= 'drawingTemp = $("canvas#tableLayout").drawArc(drawingArc("' . $dataTable->id . '", ' . $dataTable->layout_x . ', ' . $dataTable->layout_y . ', "' . $value->color . '"));'; 
                        else if ($dataTable->shape == 'rectangle')
                            $jscriptTemp .= 'drawingTemp = $("canvas#tableLayout").drawRect(drawingRect("' . $dataTable->id . '", ' . $dataTable->layout_x . ', ' . $dataTable->layout_y . ', "' . $value->color . '"));';

                        $jscriptTemp .= 'drawingTemp.drawText(drawingText("' . $dataTable->id . '", ' . $dataTable->layout_x . ', ' . $dataTable->layout_y . '));';
                    } ?>

                    <div style="margin: 5px">                            
                        <?= Html::hiddenInput('color', $value->color, ['id' => 'color']) ?>
                        <?= Html::hiddenInput('shape', $dataTable->shape, ['id' => 'shape']) ?>
                        <?= Html::hiddenInput('idTable', $dataTable->id, ['id' => 'idTable']) ?>
                    </div>

                <?php
                endforeach;
            endif;
        endforeach; ?>

    </div>

    <div style="margin: 10px"></div>

    <div class="row">        
        <div class="col-lg-9">
            <canvas id="tableLayout" width="750" height="600" style="background: rgb(200, 200 ,200)">

            </canvas>   
        </div>
        
        <div class="col-lg-3">
            <div id="infoTable" style="width: 100%; height: 600px; background: rgb(200, 200 ,200)">                

            </div>
            <div class="overlay"></div>
            <div class="loading-img"></div>
        </div>
    </div>
    
</div>

<!-- Modal Booking -->
<div class="modal fade" id="modalBooking" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalBookingTitle">Booking Table</h4>
            </div>
            <div class="modal-body" id="modelBookingBody">                                
                <div id="content">
                    <?php 
                    $form = ActiveForm::begin([
                            'options' => [
                                'id' => 'bookingForm',                                
                            ],
                            'fieldConfig' => [
                                'parts' => [
                                    '{inputClass}' => 'col-lg-12'
                                ],
                                'template' => '<div class="row">'
                                                . '<div class="col-lg-3">'
                                                    . '{label}'
                                                . '</div>'
                                                . '<div class="col-lg-6">'
                                                    . '<div class="{inputClass}">'
                                                        . '{input}'
                                                    . '</div>'
                                                . '</div>'
                                                . '<div class="col-lg-3">'
                                                    . '{error}'
                                                . '</div>'
                                            . '</div>', 
                            ]
                    ]); ?>               
                    
                    <?= Html::hiddenInput('layout', true) ?>

                    <?= $form->field($modelBooking, 'mtable_id')->textInput(['maxlength' => 24, 'readonly' => 'readonly']) ?>

                    <?= $form->field($modelBooking, 'nama_pelanggan')->textInput(['maxlength' => 64]) ?>

                    <?= $form->field($modelBooking, 'date', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-9'
                            ],
                        ])->widget(DatePicker::className(), [
                            'pluginOptions' => Yii::$app->params['datepickerOptions'],
                        ]) ?>

                    <?= $form->field($modelBooking, 'time', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-7'
                            ],
                        ])->textInput() ?>

                    <?= $form->field($modelBooking, 'keterangan')->textarea(['rows' => 6]) ?>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-3"></div>
                            <div class="col-lg-6">
                                <?php
                                $icon = '<i class="fa fa-floppy-o"></i>&nbsp;&nbsp;&nbsp;';
                                echo Html::submitButton($icon . 'Save', ['class' => $modelBooking->isNewRecord ? 'btn btn-success' : 'btn btn-primary']); ?>

                            </div>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
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

<?php
$virtualKeyboard = new VirtualKeyboard();
$virtualKeyboard->registerCssFile();
$virtualKeyboard->registerJsFile();

$this->params['regCssFile'][] = function() {
    $this->registerCssFile(Yii::getAlias('@common-web') . '/css/timepicker/bootstrap-timepicker.css');
};

$this->params['regJsFile'][] = function() {
    $this->registerJsFile(Yii::getAlias('@common-web') . '/js/plugins/jcanvas/jcanvas.min.js');
    $this->registerJsFile(Yii::getAlias('@common-web') . '/js/plugins/input-mask/jquery.inputmask.js');
    $this->registerJsFile(Yii::getAlias('@common-web') . '/js/plugins/input-mask/jquery.inputmask.date.extensions.js');
    $this->registerJsFile(Yii::getAlias('@common-web') . '/js/plugins/input-mask/jquery.inputmask.extensions.js');
    $this->registerJsFile(Yii::getAlias('@common-web') . '/js/plugins/timepicker/bootstrap-timepicker.js');    
};

$jscript = '
    var nearest = function (number, n) {
        return Math.round(number / n) * n;
    }
      
    var gridSize = 20;
    
    $(".overlay").hide();
    $(".loading-img").hide();
    
    var tableClick = function(idTable) {
        $.ajax({
            cache: false,
            type: "POST",
            data: {
                "idTable": idTable
            },
            url: "' . Yii::$app->urlManager->createUrl(['page/get-info-mtable']) . '",
            beforeSend: function(xhr) {
                $(".overlay").show();
                $(".loading-img").show();
            },
            success: function(response) {
                $("#infoTable").html(response);   
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
    };
    
    var drawingArc = function(idTable, posX, posY, color) {
        return {
            draggable: true,
            layer: true,
            name: idTable,
            fillStyle: color,
            x: posX, 
            y: posY,
            radius: 25,
            data: {
                "shape": "circle",
                "idTable": idTable
            },
            click: function(layer) {
                tableClick(idTable);
            }
        };
    };
    
    var drawingRect = function(idTable, posX, posY, color) {
        return {
            draggable: true,
            layer: true,
            name: idTable,
            fillStyle: color,
            x: posX, 
            y: posY,
            width: 50,
            height: 50,
            data: {
                "shape": "rectangle",
                "idTable": idTable
            },
            click: function(layer) {
                tableClick(idTable);
            }
        };
    };
    
    var drawingText = function(idTable, posX, posY) {
        return {
            layer: true,
            name: idTable + "text",
            fillStyle: "#fff",
            fontStyle: "bold",
            fontSize: "10pt",
            fontFamily: "Trebuchet MS, sans-serif",
            x: posX, 
            y: posY,
            text: idTable
        };
    };
    
    $("a#table").on("click", function(event) {
        event.preventDefault();
        
        var thisObj = $(this).parent();
        var color = thisObj.find("input#color").val();
        var shape = thisObj.find("input#shape").val();
        var idTable = thisObj.find("input#idTable").val();
        
        $("#modalShape").modal();                
        
        $("#modalShape").find("button").off("click").on("click", function(event) {
        
            var drawing;
        
            if ($(this).attr("id") == "circle") {
                drawing = $("canvas#tableLayout").drawArc(drawingArc(idTable, 25, 25, color));
            } else if ($(this).attr("id") == "rectangle") {
                drawing = $("canvas#tableLayout").drawRect(drawingRect(idTable, 25, 25, color));
            }
            
            drawing.drawText(drawingText(idTable, 25, 25));
        });
    });
    
    $("button#btnSave").on("click", function(event) {
        var i = 0;
        $("canvas#tableLayout").getLayers(function(layer) {
            if (layer.name.indexOf("text") == -1) {
                var inputId = $("<input>").attr("type", "hidden").attr("name", "mtable[" + i + "][id]").val(layer.data.idTable);
                var inputX = $("<input>").attr("type", "hidden").attr("name", "mtable[" + i + "][layout_x]").val(layer.x);
                var inputY = $("<input>").attr("type", "hidden").attr("name", "mtable[" + i + "][layout_y]").val(layer.y);
                var inputShape = $("<input>").attr("type", "hidden").attr("name", "mtable[" + i + "][shape]").val(layer.data.shape);
                $("form#formSaveLayout").append(inputId).append(inputX).append(inputY).append(inputShape);
                
                i++;
            }
          });
    });
    
    $("#booking-date").inputmask("yyyy-mm-dd", {"placeholder": "yyyy-mm-dd"});  
    
    $("#booking-time").timepicker({
        showMeridian: false
    });' . $virtualKeyboard->keyboardQwerty('#booking-nama_pelanggan') . $virtualKeyboard->keyboardQwerty('#booking-keterangan') . '
';

$this->registerJs($jscript . $jscriptTemp); ?>

