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

$this->title = 'List Table'; ?>

<div class="col-lg-12">	
    
    <div class="row mt">
        <div class="col-lg-12 col-md-12 mb">
            <div class="darkblue-panel">
                <div class="darkblue-header" style="background-color: <?= $modelMtable['color'] ?>">
                    <div class="row">
                        <div class="col-lg-2 col-md-2" style="text-align: left">
                            <h5 style="padding-left: 5px">
                                <?= Html::a('<i class="fa fa-chevron-circle-left"></i> Back', Yii::$app->urlManager->createUrl(['page/index']), ['class' => 'btn btn-primary']) ?>
                            </h5>
                        </div>
                        <div class="col-lg-10 col-md-10">
                            <h5 style="font-size: 20px;">                        
                                <?= $modelMtable['nama_category'] ?>
                            </h5>
                        </div>
                    </div>                                        
                </div>

                <div class="row" style="padding: 0 15px">

                    <?php
                    foreach ($modelMtable['mtables'] as $modelMtableData): 
                        
                        $badge = '';
                        $tableStatus = '';
                        $joinStatus = '';
                        if (count($modelMtableData['mtableSessions']) > 0) {
                            $badge = 'bg-important';
                            $tableStatus = 'Not Available';
                            $joinStatus = $modelMtableData['mtableSessions'][0]['is_join_mtable'] ? '<br>Joined' : '';
                        } else {
                            $badge = 'bg-success';
                            $tableStatus = 'Available'; 
                        } ?>

                        <!-- WEATHER-2 PANEL -->
                        <div class="col-lg-3 col-md-4 col-sm-4 mb">
                            <div id="mtable" class="weather-2 pn" style="height: 260px; cursor: pointer">
                                <div id="popupContent" style="display:none">                                                                             
                                        <?php
                                        $jmlTamu = '';
                                        $namaTamu = '';
                                        if ($tableStatus === 'Not Available') {
                                            if ($joinStatus != '')
                                                echo Html::a('View Table', 
                                                        Yii::$app->urlManager->createUrl(['page/view-session', 'id' => $modelMtableData['mtableSessions'][0]['mtableJoin']['activeMtableSession']['mtable_id']]),
                                                        ['class' => 'btn btn-primary btn-block']);
                                            else 
                                                echo Html::a('View Table', 
                                                        Yii::$app->urlManager->createUrl(['page/view-session', 'id' => $modelMtableData['id']]),
                                                        ['class' => 'btn btn-primary btn-block']);
                                            
                                            $jmlTamu = 'Tamu: ' . $modelMtableData['mtableSessions'][0]['jumlah_guest'];
                                            $namaTamu = $modelMtableData['mtableSessions'][0]['nama_tamu'];
                                        } else if ($tableStatus === 'Available') {
                                            echo Html::a('Open Table', 
                                                    Yii::$app->urlManager->createUrl(['page/open-table', 'id' => $modelMtableData['id']]),
                                                    ['class' => 'btn btn-primary btn-block']);                                                                                        
                                        } 

                                        echo Html::a('Booking', 
                                                Yii::$app->urlManager->createUrl(['page/booking', 'id' => $modelMtableData['id']]), 
                                                ['id' => 'aBooking', 'data-mtableid' => $modelMtableData['id'], 'class' => 'btn btn-primary btn-block']);

                                        if (count($modelMtableData['bookings']) > 0) {
                                            echo Html::a('List Booking', 
                                                    Yii::$app->urlManager->createUrl(['page/list-booking', 'id' => $modelMtableData['id']]), 
                                                    ['id' => 'aListBooking', 'class' => 'btn btn-primary btn-block']);
                                        }

                                        ?>
                                </div>
                                <div class="badge-name">
                                    <h4><b><?= $modelMtableData['nama_meja'] ?></b></h4>
                                </div>
                                <div class="weather-2-header">
                                    <div class="row">
                                        <div class="col-sm-6 col-xs-6 goleft">
                                            <span class="badge" style="margin-left: 5px; font-size: 20px"><?= $modelMtableData['id'] ?></span>                                                                                                   
                                        </div>
                                        <div class="col-sm-6 col-xs-6 goright">
                                            <p class="small"><span class="badge <?= $badge ?>"><?= $tableStatus . $joinStatus ?></span></p>
                                        </div>
                                    </div>
                                </div><!-- /weather-2 header -->
                                <div class="row centered">
                                    <img src="<?= Yii::getAlias('@backend-web') . '/img/mtable/thumb120x120' . $modelMtableData['image'] ?>" class="img-circle" width="120">			
                                </div>
                                <div class="row data">
                                    <div class="col-sm-6 col-xs-6 goleft">                                        
                                        <h6>
                                            Chair: <?= $modelMtableData['kapasitas'] ?>
                                            <br><br>
                                            <?= $jmlTamu ?>
                                        </h6>   
                                        
                                        <?php 
                                        if (count($modelMtableData['bookings']) > 0)
                                            echo '<span class="badge bg-warning">Booked</span>';
                                        else
                                            echo ''; ?>
                                        
                                    </div>

                                    <div class="col-sm-6 col-xs-6 goright">                 
                                        <h6>
                                            Atas Nama:<br>
                                            <?= $namaTamu ?>
                                       </h6>
                                    </div>
                                </div>
                            </div>
                        </div><! --/col-md-4 -->

                    <?php
                    endforeach; ?>

                </div><!-- /row -->
            </div>
        </div>
    </div>     
    

</div><!-- /col-lg-9 END SECTION MIDDLE -->


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
                    
                    <?= Html::hiddenInput('cid', $cid) ?>

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
    $this->registerJsFile(Yii::getAlias('@common-web') . '/js/plugins/input-mask/jquery.inputmask.js');
    $this->registerJsFile(Yii::getAlias('@common-web') . '/js/plugins/input-mask/jquery.inputmask.date.extensions.js');
    $this->registerJsFile(Yii::getAlias('@common-web') . '/js/plugins/input-mask/jquery.inputmask.extensions.js');
    $this->registerJsFile(Yii::getAlias('@common-web') . '/js/plugins/timepicker/bootstrap-timepicker.js');    
};

$jscript = '
    $("#booking-date").inputmask("yyyy-mm-dd", {"placeholder": "yyyy-mm-dd"});  
    
    $("#booking-time").timepicker({
        showMeridian: false
    });' . $virtualKeyboard->keyboardQwerty('#booking-nama_pelanggan') . $virtualKeyboard->keyboardQwerty('#booking-keterangan') . '
    
    var clearBookingForm = function(mtableid) {
        $("input#booking-mtable_id").val(mtableid);
        $("input#booking-nama_pelanggan").val("");
        $("input#booking-date").val("");
        $("input#booking-time").val("");        
        $("textarea#booking-keterangan").val("");
        
        $("#booking-time").timepicker({
        showMeridian: false
    });
        
        if ($("#bookingForm .form-group").hasClass("has-error")) {
            $("#bookingForm .form-group").removeClass("has-error");
            $("#bookingForm .help-block").empty();
        }
        if ($("#bookingForm .form-group").hasClass("has-success")) $("#bookingForm .form-group").removeClass("has-success");
    };

    $("a#aBooking").on("click", function(event) {
        event.preventDefault();      
        clearBookingForm($(this).attr("data-mtableid"));
        $("#bookingForm").attr("action", $(this).attr("href"));
        $("#modalBooking").modal();        
    });
    
    $("a#aListBooking").on("click", function(event) {
        event.preventDefault();
        var thisObj = $(this);
        
        $("#modalCustom #modalCustomTitle").text("List Booking");
        $("#modalCustom").modal();
        $("#overlayModalCustom").show();
        $("#loadingModalCustom").show();       

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
            }
        });
    });
    
    $("div#mtable").popover({
        html: true,
        placement: "bottom",
        trigger: "manual",
        content: $(this).find("div#popupContent").html()
    }).on("click", function (event) {
        var thisObj = $(this);
        
        $("div#mtable").each(function() {
            if (!$(this).is(thisObj))
                $(this).popover("hide");
        });        
                
        thisObj.popover("toggle");
        thisObj.parent().find(".popover-content").html(thisObj.find("div#popupContent").html());
        
        thisObj.parent().find(".popover-content").find("a#aBooking").on("click", function(event) {
            event.preventDefault();      
            clearBookingForm($(this).attr("data-mtableid"));
            $("#bookingForm").attr("action", $(this).attr("href"));
            $("#modalBooking").modal();               
            
            $("div#mtable").popover("hide");
        });
        
        thisObj.parent().find("a#aListBooking").on("click", function(event) {
            event.preventDefault();
            var thisObjListBooking = $(this);

            $("#modalCustom #modalCustomTitle").text("List Booking");
            $("#modalCustom").modal();
            $("#overlayModalCustom").show();
            $("#loadingModalCustom").show();       

            $.ajax({
                cache: false,
                type: "POST",
                data: {
                    "type": "close"
                },
                url: thisObjListBooking.attr("href"),
                beforeSend: function(xhr) {
                    $("div#mtable").popover("hide");
                },
                success: function(response) {
                    $("#modalCustom #modelCustomBody #content").html(response);   
                    $("#overlayModalCustom").hide();
                    $("#loadingModalCustom").hide();                    
                }
            });                        
        });
        
        var y = (parseFloat($(this).css("height")) / 2) - 40;        
        thisObj.parent().find("div.popover").css("top", y + "px");
    });
     
';

$this->registerJs($jscript); ?>

