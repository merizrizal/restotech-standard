<?php

use yii\helpers\Html;
use backend\components\GridView;
use backend\components\ModalDialog;
use backend\components\NotificationDialog;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\EmployeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

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

$this->title = 'Karyawan';
$this->params['titleH1'] = '&nbsp;&nbsp;&nbsp;' . 
        Html::a('<i class="fa fa-upload"></i>&nbsp;&nbsp;&nbsp;' . 'Create New Data', ['create'], ['class' => 'btn btn-success']) .
        '&nbsp;&nbsp;&nbsp;' . 
        Html::a('<i class="fa fa-gear"></i>&nbsp;&nbsp;&nbsp;' . 'Update Limit Officer', ['update-limit-officer', 'id' => 'all'], ['class' => 'btn btn-primary', 'data-method' => 'post']);

$this->params['breadcrumbs'][] = $this->title; ?>

<div class="employee-index">

    <?php 
    $modalDialog = new ModalDialog([
        'clickedComponent' => 'a#delete',
        'modelAttributeId' => 'model-id',
        'modelAttributeName' => 'model-name',
    ]);

    $jscript = '$(\'[data-toggle="tooltip"]\').tooltip();'
            . Yii::$app->params['checkbox-radio-script']()
            . '$(".iCheck-helper").parent().removeClass("disabled");'
            . $modalDialog->getScript();
    
    $this->registerJs($jscript); 
    
    $jscript = '<script>' . $jscript . '</script>'; ?>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pjax' => true,
        'scriptAfterPjax' => $jscript,
        'bordered' => false,
        'floatHeader' => true,
        'panelHeadingTemplate' => '<div class="kv-panel-pager pull-right" style="text-align:right">'
                                    . '{pager}{summary}'
                                . '</div>'                                
                                . '<div class="clearfix"></div>'
        ,
        'panelFooterTemplate' => '<div class="kv-panel-pager pull-right" style="text-align:right">'
                                    . '{summary}{pager}'
                                . '</div>'
                                . '{footer}'
                                . '<div class="clearfix"></div>'
        ,
        'panel' => [
            'heading' => '',
        ],
        'toolbar' => [
            ['content'=>
                Html::a('<i class="fa fa-repeat"></i>', ['index'], [
                    'data-pjax'=>false, 
                    'class' => 'btn btn-success', 
                    'data-placement' => 'top',
                    'data-toggle' => 'tooltip',
                    'title' => 'Refresh'
                ])
            ],
        ],
        'filterModel' => $searchModel,            
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'kd_karyawan',
            'nama',
            'jenis_kelamin',
            'phone1',
            [
                'attribute' => 'not_active',
                'format' => 'raw',
                'filter' =>  [1 => 'True', 0 => 'False'],
                'value' => function ($model, $index, $widget) {                    
                    return Html::checkbox('not_active[]', $model->not_active, ['value' => $index, 'disabled' => 'disabled']);
                },
            ],
            
            [
                'class' => 'yii\grid\ActionColumn',
                'options' => [
                    'style' => 'width: 100px'
                ],
                'template' => '<div class="btn-group btn-group-xs" role="group" style="width: 100px">'
                                    . '{view}{update}{update-limit-officer}{delete}'
                            . '</div>',
                'buttons' => [
                    'view' =>  function($url, $model, $key) {
                        return Html::a('<i class="fa fa-search-plus"></i>', $url, [
                            'id' => 'view',
                            'class' => 'btn btn-primary',
                            'data-pjax' => '0',
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'title' => 'View',
                        ]);
                    },
                    'update' =>  function($url, $model, $key) {
                        return Html::a('<i class="fa fa-pencil"></i>', $url, [
                            'id' => 'update',
                            'class' => 'btn btn-success',
                            'data-pjax' => '0',
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'title' => 'Edit',
                        ]);
                    },
                    'update-limit-officer' =>  function($url, $model, $key) {
                        return Html::a('<i class="fa fa-wrench"></i>', $url, [
                            'id' => 'update-limit-officer',
                            'class' => 'btn btn-success',
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'data-method' => 'post',
                            'title' => 'Update Limit Officer',
                        ]);
                    },
                    'delete' =>  function($url, $model, $key) {
                        return Html::a('<i class="fa fa-trash"></i>', $url, [
                            'id' => 'delete',
                            'class' => 'btn btn-danger',                            
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'title' => 'Delete',
                            'model-id' => $model->kd_karyawan,
                            'model-name' => $model->nama,
                        ]);
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

<?= $modalDialog->renderDialog() ?>

<?php 
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/all.css', ['depends' => 'backend\assets\AppAsset']);
   
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/icheck.min.js', ['depends' => 'backend\assets\AppAsset']); ?>