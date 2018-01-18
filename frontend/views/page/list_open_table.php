<?php

use yii\helpers\Html;
use backend\components\GridView; 

Yii::$app->formatter->timeZone = 'Asia/Jakarta';

$this->title = 'Opened Table'; ?>

<div class="col-lg-12">

    <div class="content-panel mt">
        
        <h4><i class="fa fa-angle-right"></i> List Opened Table</h4>
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

                            'id',
                            'nama_meja',
                            'mtableCategory.nama_category',
                            [
                                'attribute' => 'opened_at',
                                'format' => 'raw',
                                'value' => function ($model, $index, $widget) {  
                                    $str = '';
                                    if (count($model->mtableSessions) > 0) {
                                        $str = Yii::$app->formatter->asDatetime($model->mtableSessions[0]->opened_at);
                                    }
                                    
                                    return $str;
                                },
                            ],
                                        
                            [
                                'attribute' => 'opened_by',
                                'format' => 'raw',
                                'value' => function ($model, $index, $widget) {  
                                    $str = '';
                                    
                                    if (count($model->mtableSessions) > 0) {
                                        $str = $model->mtableSessions[0]->userOpened->kdKaryawan->nama;
                                    }
                                    
                                    return $str;
                                },
                            ],                                        
                            [
                                'attribute' => 'tamu',
                                'format' => 'raw',
                                'value' => function ($model, $index, $widget) {  
                                    $str = '';
                                    
                                    if (count($model->mtableSessions) > 0) {
                                        
                                        foreach ($model->mtableSessions as $mtableSession) {
                                            $str .= $mtableSession->nama_tamu . '<br>asd';
                                        }
                                    }
                                    
                                    return $str;
                                },
                            ],                           
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{view}',
                                'buttons' => [
                                    'view' =>  function($url, $model, $key) {         
                                        $url = '';
                                        if (count($model->mtableSessions) > 0) {                                            
                                            if ($model->mtableSessions[0]['is_join_mtable'])
                                                $url = Yii::$app->urlManager->createUrl(['page/view-session', 'id' => $model->mtableSessions[0]->mtableJoin->activeMtableSession->mtable_id]);
                                            else 
                                                $url = Yii::$app->urlManager->createUrl(['page/view-session', 'id' => $model->id]);
                                        }
                                        
                                        
                                        return '<div class="btn-group btn-group-xs" role="group" style="width: 75px">' .
                                                    Html::a('<i class="fa fa-external-link-square"></i>', 
                                                    $url, 
                                                    [
                                                        'id' => 'check',
                                                        'class' => 'btn btn-success',
                                                        'data-toggle' => 'tooltip',
                                                        'data-placement' => 'right',
                                                        'title' => 'View Table',
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

$jscript = '
    $("a#check").tooltip();   
    
    setTimeout(function() {
        $(location).attr("href","");
    }, 5 * 1000);
';

$this->registerJs($jscript); ?>