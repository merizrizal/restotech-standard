<?php

use yii\helpers\Html;
use backend\components\GridView; ?>


<div id="pjax-container">
    <div class="row get-purchase-order-trx">
        <div class="col-md-12">

            <?= GridView::widget([
                    'options' => [
                        'id' => 'get-list-booking'
                    ],
                    'dataProvider' => $dataProvider,
                    'condensed' => true,
                    'panelHeadingTemplate' => '',
                    'panelFooterTemplate' => '',
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                        ],

                        'id',
                        'mtable_id',
                        'nama_pelanggan',
                        'date:date',                               
                        'time',

                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{check}',
                            'buttons' => [
                                'check' =>  function($url, $model, $key) {
                                    $str = '';
                                    if(count($model->mtable->mtableSessions) > 0) {
                                        $str .= Html::hiddenInput('isOpen', true, ['id' => 'isOpen']);
                                    } else {
                                        $str .= Html::hiddenInput('isOpen', false, ['id' => 'isOpen']);
                                    }
                                    
                                    $str .= Html::hiddenInput('bookingId', $model->id, ['id' => 'bookingId']);

                                    return $str . 
                                            '<div class="btn-group btn-group-xs" role="group" style="width: 75px">' .
                                                Html::a('<i class="fa fa-check"></i>', $url, [
                                                    'id' => 'check',
                                                    'class' => 'btn btn-success',
                                                    'data-toggle' => 'tooltip',
                                                    'data-placement' => 'left',
                                                    'title' => 'Confirm',
                                                    'data-mtableid' => $model->mtable_id,
                                                ]) . 
                                            '</div>';
                                },
                            ]
                        ],
                    ],
                    'pager' => [
                        'firstPageLabel' => '<i class="fa fa-angle-double-left"></i>',
                        'prevPageLabel' => '<i class="fa fa-angle-left"></i>',
                        'lastPageLabel' => '<i class="fa fa-angle-double-right"></i>',
                        'nextPageLabel' => '<i class="fa fa-angle-right"></i>',
                    ],
                ]); ?>

        </div>
    </div>
</div>


<script>
    $(document).pjax('a', '#pjax-container');
    
    $(document).on('pjax:send', function() {
        $("#overlayModalCustom").show();
        $("#loadingModalCustom").show();
    })
      $(document).on('pjax:complete', function() {
        $("#overlayModalCustom").hide();
        $("#loadingModalCustom").hide();
    })
    
    $("a#check").tooltip();
            
    $("a#check").click(function(event) {
        event.preventDefault();    
        var thisObj = $(this);
        
        $("#modalConfirmation #modalConfirmationTitle").html("Confirm &amp; Open Table");
        $("#modalConfirmation #modalConfirmationBody").html("Confirm booking &amp; open table <b>" + $(this).attr("data-mtableid") + "</b> ?");
        $("#modalConfirmation").modal();
        
        $("#modalConfirmation #submitConfirmation").on("click", function(event) {
            
            if (thisObj.parent().parent().find("input#isOpen").val() == true) {
                $("#modalAlert #modalAlertBody").html("Tidak bisa melakukan open table karena meja sudah terisi");
                $("#modalAlert").modal();
            } else {
                $(location).attr("href","<?= Yii::$app->urlManager->createUrl('page/confirm-booking?id='); ?>" + thisObj.parent().parent().find("input#bookingId").val());
            }
            
            $(this).off("click");
        });
    });
</script>
