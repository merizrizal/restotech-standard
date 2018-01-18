<?php
use yii\helpers\Html; ?>

<div class="row" style="padding: 0 15px">

    <?php    

    $badge = '';
    $tableStatus = '';
    $joinStatus = '';
    if (count($modelMtable['mtableSessions']) > 0) {
        $badge = 'bg-important';
        $tableStatus = 'Not Available';
        $joinStatus = $modelMtable['mtableSessions'][0]['is_join_mtable'] ? '<br>Joined' : '';
    } else {
        $badge = 'bg-success';
        $tableStatus = 'Available'; 
    } ?>

    <!-- WEATHER-2 PANEL -->
    <div class="col-lg-12 mt">
        <div id="mtable" class="weather-2 pn" style="height: 260px">     
            
            <?php
            $jmlTamu = '';
            $namaTamu = '';
            $aMenu = '';
            if ($tableStatus === 'Not Available') {
                if ($joinStatus != '')
                    $aMenu .= Html::a('View Table', 
                            Yii::$app->urlManager->createUrl(['page/view-session', 'id' => $modelMtable['mtableSessions'][0]['mtableJoin']['activeMtableSession']['mtable_id']]),
                            ['class' => 'btn btn-primary btn-block']);
                else 
                    $aMenu .= Html::a('View Table', 
                            Yii::$app->urlManager->createUrl(['page/view-session', 'id' => $modelMtable['id']]),
                            ['class' => 'btn btn-primary btn-block']);

                $jmlTamu = 'Tamu: ' . $modelMtable['mtableSessions'][0]['jumlah_guest'];
                $namaTamu = $modelMtable['mtableSessions'][0]['nama_tamu'];
            } else if ($tableStatus === 'Available') {
                $aMenu .= Html::a('Open Table', 
                        Yii::$app->urlManager->createUrl(['page/open-table', 'id' => $modelMtable['id']]),
                        ['class' => 'btn btn-primary btn-block']);                                                                                        
            } 

            $aMenu .= Html::a('Booking', 
                    Yii::$app->urlManager->createUrl(['page/booking', 'id' => $modelMtable['id']]), 
                    ['id' => 'aBooking', 'data-mtableid' => $modelMtable['id'], 'class' => 'btn btn-primary btn-block']);

            if (count($modelMtable['bookings']) > 0) {
                $aMenu .= Html::a('List Booking', 
                        Yii::$app->urlManager->createUrl(['page/list-booking', 'id' => $modelMtable['id']]), 
                        ['id' => 'aListBooking', 'class' => 'btn btn-primary btn-block']);
            } ?>
            
            <div class="badge-name">
                <h4><b><?= $modelMtable['nama_meja'] ?></b></h4>
            </div>
            <div class="weather-2-header">
                <div class="row">
                    <div class="col-sm-6 col-xs-6 goleft">
                        <span class="badge" style="margin-left: 5px; font-size: 20px"><?= $modelMtable['id'] ?></span>                                                                                                   
                    </div>
                    <div class="col-sm-6 col-xs-6 goright">
                        <p class="small"><span class="badge <?= $badge ?>"><?= $tableStatus . $joinStatus ?></span></p>
                    </div>
                </div>
            </div><!-- /weather-2 header -->
            <div class="row centered">
                <img src="<?= Yii::getAlias('@backend-web') . '/img/mtable/thumb120x120' . $modelMtable['image'] ?>" class="img-circle" width="120">			
            </div>
            <div class="row data">
                <div class="col-sm-6 col-xs-6 goleft">                                        
                    <h6>
                        Chair: <?= $modelMtable['kapasitas'] ?>
                        <br><br>
                        <?= $jmlTamu ?>
                    </h6>   

                    <?php 
                    if (count($modelMtable['bookings']) > 0)
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

</div><!-- /row -->

<div class="row" style="padding: 0 15px">
    <div class="col-lg-12 mt">
        <?= $aMenu ?>
    </div>
</div>

<script>
    var clearBookingForm = function(mtableid) {
        $("input#booking-mtable_id").val(mtableid);
        $("input#booking-nama_pelanggan").val("");
        $("input#booking-date").val("");
        $("input#booking-time").val("");        
        $("textarea#booking-keterangan").val("");
        
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
</script>