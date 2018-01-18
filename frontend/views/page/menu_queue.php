<?php

use yii\helpers\Html;
use backend\components\GridView; 
use frontend\components\NotificationDialog;


$status = Yii::$app->session->getFlash('status');
$message1 = Yii::$app->session->getFlash('message1');
$message2 = Yii::$app->session->getFlash('message2');

if ($status !== null) : 
    $notif = new NotificationDialog([
        'status' => $status,
        'message1' => $message1,
        'message2' => $message2,
    ]);

    $notif->theScript();
    echo $notif->renderDialog();

endif; 

$this->title = 'Antrian Menu'; ?>


<div class="col-lg-12">

    <div class="content-panel mt">
        
        <h4><i class="fa fa-angle-right"></i> List Antrian Menu</h4>
        <br>

        <div class="row"style="margin: 0 15px">
            <div class="col-md-12" >

                <?= GridView::widget([                    
                        'dataProvider' => $dataProvider,
                        'pjax' => true,
                        'panelHeadingTemplate' => '',
                        'panelFooterTemplate' => '',
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                            ],

                            'menu_id',
                            'menu.nama_menu',
                            'jumlah',
                            'keterangan',                               
                            'mtableOrder.mtableSession.mtable_id',

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{check}',
                                'buttons' => [
                                    'check' =>  function($url, $model, $key) {                                        
                                        $str = '';
                                        $str .= Html::hiddenInput('queueId', $model->id);
                                        
                                        return $str . 
                                                '<div class="btn-group btn-group-xs" role="group" style="width: 75px">' .
                                                    Html::a('<i class="fa fa-check"></i>', '', [
                                                        'id' => 'check',
                                                        'class' => 'btn btn-success',
                                                        'data-toggle' => 'tooltip',
                                                        'data-placement' => 'right',
                                                        'title' => 'Finish',
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
</div>

<?php

echo Html::beginForm('', 'post', ['id' => 'formMenuQueue', 'style' => 'display:none']);
echo Html::endForm();

$jscript = '
    $("a#check").tooltip();
    
    $("a#check").on("click", function(event) {
        event.preventDefault();
        $("form#formMenuQueue").append($(this).parent().parent().html());
        $("form#formMenuQueue").submit();
    });
    
    setTimeout(function() {
        $(location).attr("href","");
    }, 5 * 1000);
';

$this->registerJs($jscript); ?>