<?php

use yii\helpers\Html;
use restotech\standard\backend\components\NotificationDialog;

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

$this->title = 'Inisialisasi Ruangan / Meja';
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="payment-method-init">
    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="box box-danger">
                <div class="box-body">
                    <div class="payment-method-form">
                        <br><br>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-6 col-lg-offset-3 text-center">
                                    <?php
                                    if (!$initialized) {
                                        echo Html::a('<i class="fa fa-check"></i>&nbsp;&nbsp;&nbsp;' . 'Inisialisasi', ['init'], ['class' => 'btn btn-primary btn-lg', 'data-method' => 'post']);
                                    } else {                                        
                                        echo '
                                            <div class="alert alert-success">
                                                <h4><i class="icon fa fa-check"></i> Data Sudah Di-inisialisasi</h4>
                                            </div>
                                        ';
                                    } ?>
                                </div>
                            </div>
                        </div>
                        <br><br>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>