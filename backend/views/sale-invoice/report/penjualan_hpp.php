<?php

use yii\helpers\Html;
use kartik\date\DatePicker;
use restotech\standard\backend\components\NotificationDialog;

yii\widgets\MaskedInputAsset::register($this);

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

$this->title = 'Laporan Penjualan';
$this->params['breadcrumbs'][] = $this->title; ?>

<?= Html::beginForm() ?>

    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="box box-danger">
                <div class="box-body">
                    <div class="sale-invoice-form">                       
                        
                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-3">
                                    <label class="control-label" for="tanggal">Tanggal</label>
                                </div>
                                <div class="col-lg-4">
                                    <?= DatePicker::widget([
                                        'id' => 'tanggal',
                                        'name' => 'tanggal',
                                        'pluginOptions' => Yii::$app->params['datepickerOptions'],
                                    ]); ?>                                 
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-3"></div>
                                <div class="col-lg-6">
                                    <?= Html::submitButton('<i class="fa fa-file-pdf-o"></i> PDF', ['name' => 'print', 'value' => 'pdf', 'class' => 'btn btn-primary']) ?>
                                    &nbsp;&nbsp;
                                    <?= Html::submitButton('<i class="fa fa-file-excel-o"></i> Excel', ['name' => 'print', 'value' => 'excel', 'class' => 'btn btn-primary']) ?>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

<?= Html::endForm() ?>

<?php
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/all.css', ['depends' => 'yii\web\YiiAsset']);
 
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);


$jscript = '
    $("#tanggal").inputmask("yyyy-mm-dd", {"placeholder": "yyyy-mm-dd"});
';

$this->registerJs($jscript . Yii::$app->params['checkbox-radio-script']()); ?>